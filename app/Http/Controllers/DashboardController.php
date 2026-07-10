<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $projets = $user->role === 'admin' ? Projet::all() : $user->projets;
        $projetIds = $projets->pluck('id');

        $parProjet = $projets->map(fn ($projet) => [
            'id' => $projet->id,
            'nom' => $projet->nom,
            'progression' => $projet->progression(),
            'tauxPrioriteHaute' => $projet->tauxPrioriteHaute(),
        ]);

        $parMembre = User::whereHas('taches', fn ($q) => $q->whereIn('projet_id', $projetIds))
            ->withCount(['taches' => fn ($q) => $q->whereIn('projet_id', $projetIds)])
            ->get()
            ->map(fn ($u) => ['userId' => $u->id, 'name' => $u->name, 'tasksCount' => $u->taches_count]);

        return Inertia::render('Dashboard/Index', [
            'parProjet' => $parProjet,
            'parMembre' => $parMembre,
        ]);
    }
}