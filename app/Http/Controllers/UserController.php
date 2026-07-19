<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        return Inertia::render('Users/Index', [
            'utilisateurs' => User::orderBy('name')->get(['id', 'name', 'email', 'role']),
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $this->authorize('updateRole', $user);

        $validated = $request->validate([
            'role' => ['required', Rule::in(['membre', 'chef_projet', 'admin'])],
        ]);

        $user->update($validated);

        return back();
    }
}