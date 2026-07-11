<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Notifications/Index', [
            'notifications' => $request->user()->notifications->map(fn ($n) => [
                'id' => $n->id,
                'message' => $n->data['message'],
                'lu' => $n->read_at !== null,
                'creeLe' => $n->created_at->diffForHumans(),
            ]),
        ]);
    }

    public function markAsRead(Request $request, string $id)
    {
        $request->user()->notifications()->where('id', $id)->first()?->markAsRead();

        return back();
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}