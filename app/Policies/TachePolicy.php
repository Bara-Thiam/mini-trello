<?php

namespace App\Policies;

use App\Models\Tache;
use App\Models\User;

class TachePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Tache $tache): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $tache->projet->users()->where('users.id', $user->id)->exists();
    }

    public function create(User $user, int $projetId): bool
    {
        if ($user->role === 'admin' || $user->role === 'chef_projet') {
            return true;
        }

        return \App\Models\Projet::find($projetId)
            ?->users()
            ->where('users.id', $user->id)
            ->exists() ?? false;
    }

    public function update(User $user, Tache $tache): bool
    {
        if (in_array($user->role, ['admin', 'chef_projet'])) {
            return true;
        }

        return $tache->user_id === $user->id;
    }

    public function delete(User $user, Tache $tache): bool
    {
        return in_array($user->role, ['admin', 'chef_projet']);
    }
}