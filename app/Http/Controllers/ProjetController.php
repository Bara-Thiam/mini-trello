<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjetResource;
use App\Models\Projet;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Resources\TacheResource;

class ProjetController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $projets = $user->role === 'admin'
            ? Projet::all()
            : $user->projets;

        return Inertia::render('Projects/Index', [
            'projets' => ProjetResource::collection($projets),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Projet::class);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $projet = Projet::create($validated);
        $projet->users()->attach($request->user()->id);

        return redirect('/projects');
    }

    public function show(Request $request, Projet $projet)
    {
        $this->authorize('view', $projet);

        return Inertia::render('Projects/Kanban', [
            'projet' => new ProjetResource($projet),
            'colonnes' => $projet->colonnes()->orderBy('ordre')->get(),
            'taches' => TacheResource::collection($projet->taches()->with('user')->get()),
        ]);
    }

    public function update(Request $request, Projet $projet)
    {
        $this->authorize('update', $projet);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $projet->update($validated);

        return redirect('/projects');
    }

    public function destroy(Projet $projet)
    {
        $this->authorize('delete', $projet);

        $projet->delete();

        return redirect('/projects');
    }
}