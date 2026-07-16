<?php

namespace App\Policies;

use App\Models\Projet;
use App\Models\User;

class ProjetPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Projet $projet): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $projet->users()->where('users.id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Projet $projet): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Projet $projet): bool
    {
        return $user->role === 'admin';
    }

    public function manageMembers(User $user, Projet $projet): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'chef_projet' && $projet->users()->where('users.id', $user->id)->exists();
    }
}