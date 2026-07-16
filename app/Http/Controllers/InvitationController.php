<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\User;
use App\Notifications\InvitationRepondueNotification;
use App\Notifications\ProjetInvitationNotification;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function store(Request $request, Projet $projet)
    {
        $this->authorize('view', $projet);

        $this->authorize('manageMembers', $projet);

        $validated = $request->validate(['email' => 'required|email|max:255']);

        $invite = User::where('email', $validated['email'])->first();

        if (!$invite) {
            return back()->withErrors(['email' => 'Aucun utilisateur trouvé avec cet email.']);
        }

        if ($projet->users()->where('users.id', $invite->id)->exists()) {
            return back()->withErrors(['email' => 'Cette personne fait déjà partie du projet.']);
        }

        $invitationEnAttente = $invite->notifications()
            ->where('type', \App\Notifications\ProjetInvitationNotification::class)
            ->whereNull('read_at')
            ->get()
            ->first(fn($n) => ($n->data['projetId'] ?? null) === $projet->id && ($n->data['statut'] ?? null) === 'en_attente');

        if ($invitationEnAttente) {
            return back()->withErrors(['email' => 'Une invitation est déjà en attente pour cette personne.']);
        }

        $invite->notify(new ProjetInvitationNotification($projet, $request->user()));

        return back();
    }

    public function accepter(Request $request, string $notificationId)
    {
        $notification = $request->user()->notifications()->findOrFail($notificationId);
        $data = $notification->data;

        $projet = Projet::findOrFail($data['projetId']);
        $projet->users()->syncWithoutDetaching($request->user()->id);

        $inviteur = User::find($data['inviteurId']);
        $inviteur?->notify(new InvitationRepondueNotification($projet, $request->user(), true));

        $notification->update(['data' => array_merge($data, ['statut' => 'acceptee'])]);
        $notification->markAsRead();

        return back();
    }

    public function refuser(Request $request, string $notificationId)
    {
        $notification = $request->user()->notifications()->findOrFail($notificationId);
        $data = $notification->data;

        $projet = Projet::findOrFail($data['projetId']);
        $inviteur = User::find($data['inviteurId']);
        $inviteur?->notify(new InvitationRepondueNotification($projet, $request->user(), false));

        $notification->update(['data' => array_merge($data, ['statut' => 'refusee'])]);
        $notification->markAsRead();

        return back();
    }
}