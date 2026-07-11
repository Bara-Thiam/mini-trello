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

        $toutesLesTaches = \App\Models\Tache::whereIn('projet_id', $projetIds)->get();

        $parProjet = $projets->map(fn ($projet) => [
            'id' => $projet->id,
            'nom' => $projet->nom,
            'total' => $projet->taches->count(),
            'done' => $projet->taches->where('statut', 'DONE')->count(),
            'progression' => $projet->progression(),
            'tauxPrioriteHaute' => $projet->tauxPrioriteHaute(),
        ]);

        $parMembre = User::whereHas('taches', fn ($q) => $q->whereIn('projet_id', $projetIds))
            ->withCount(['taches' => fn ($q) => $q->whereIn('projet_id', $projetIds)])
            ->get()
            ->map(fn ($u) => ['userId' => $u->id, 'name' => $u->name, 'tasksCount' => $u->taches_count]);

        return Inertia::render('Dashboard/Index', [
            'totalProjets' => $projets->count(),
            'totalTaches' => $toutesLesTaches->count(),
            'tachesTerminees' => $toutesLesTaches->where('statut', 'DONE')->count(),
            'tachesEnCours' => $toutesLesTaches->where('statut', 'DOING')->count(),
            'tachesATaire' => $toutesLesTaches->where('statut', 'TODO')->count(),
            'parProjet' => $parProjet,
            'parMembre' => $parMembre,
        ]);
    }
}