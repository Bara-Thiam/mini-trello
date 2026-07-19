<?php

namespace App\Http\Controllers;

use App\Http\Resources\TacheResource;
use App\Models\Projet;
use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TacheController extends Controller
{
    public function store(Request $request, Projet $projet)
    {
        $this->authorize('create', [Tache::class, $projet->id]);

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'user_id' => [
                'nullable',
                Rule::exists('projet_user', 'user_id')->where('projet_id', $projet->id),
            ],
            'priorite' => ['sometimes', Rule::in(['LOW', 'MEDIUM', 'HIGH'])],
            'echeance' => 'nullable|date',
        ]);

        $projet->taches()->create($validated);

        return redirect("/projects/{$projet->id}");
    }

    public function update(Request $request, Tache $tache)
    {
        $this->authorize('update', $tache);

        $validated = $request->validate([
            'titre' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'user_id' => [
                'nullable',
                Rule::exists('projet_user', 'user_id')->where('projet_id', $tache->projet_id),
            ],
            'priorite' => ['sometimes', Rule::in(['LOW', 'MEDIUM', 'HIGH'])],
            'echeance' => 'nullable|date',
        ]);

        $tache->update($validated);

        if (isset($validated['user_id']) && $tache->wasChanged('user_id') && $tache->user_id) {
            $tache->user->notify(new \App\Notifications\TacheAssigneeNotification($tache));
        }

        return redirect("/projects/{$tache->projet_id}");
    }

    public function destroy(Tache $tache)
    {
        $this->authorize('delete', $tache);

        $projetId = $tache->projet_id;
        $tache->delete();

        return redirect("/projects/{$projetId}");
    }

    public function updateStatus(Request $request, Tache $tache)
    {
        $this->authorize('update', $tache);

        $validated = $request->validate([
            'statut' => ['required', Rule::in(['TODO', 'DOING', 'DONE'])],
        ]);

        $tache->update($validated);

        return new TacheResource($tache);
    }

    public function mine(Request $request)
    {
        $taches = Tache::where('user_id', $request->user()->id)
            ->with('projet')
            ->get();

        return Inertia::render('Tasks/MyTasks', [
            'taches' => TacheResource::collection($taches),
        ]);
    }

    public function historique(Tache $tache)
    {
        $this->authorize('view', $tache);

        return $tache->activites()->with('user')->get()->map(fn($a) => [
            'id' => $a->id,
            'type' => $a->type,
            'champ' => $a->champ,
            'ancienneValeur' => $a->ancienne_valeur,
            'nouvelleValeur' => $a->nouvelle_valeur,
            'user' => $a->user?->name ?? 'Utilisateur supprimé',
            'creeLe' => $a->created_at->diffForHumans(),
        ]);
    }
}