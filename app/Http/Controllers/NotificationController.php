<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Notifications/Index', [
            'notifications' => $request->user()->notifications->map(fn($n) => [
                'id' => $n->id,
                'kind' => $n->data['kind'] ?? 'assignation',
                'message' => $n->data['message'],
                'statut' => $n->data['statut'] ?? null,
                'lu' => $n->read_at !== null,
                'creeLe' => $n->created_at->diffForHumans(),
            ]),
        ]);
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return back();
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}