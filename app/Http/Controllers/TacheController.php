<?php

namespace App\Http\Controllers;

use App\Http\Resources\TacheResource;
use App\Models\Projet;
use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TacheController extends Controller
{
    public function store(Request $request, Projet $projet)
    {
        $this->authorize('create', [Tache::class, $projet->id]);

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
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
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'priorite' => ['sometimes', Rule::in(['LOW', 'MEDIUM', 'HIGH'])],
            'echeance' => 'nullable|date',
        ]);

        $tache->update($validated);

        if (isset($validated['user_id']) && $tache->wasChanged('user_id') && $tache->user_id) {
            event(new \App\Events\TacheAssignee($tache));
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
}