<?php

namespace App\Notifications;

use App\Models\Tache;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TacheAssigneeNotification extends Notification
{
    public function __construct(public Tache $tache)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => "Tu as été assigné à la tâche « {$this->tache->titre} »",
            'tacheId' => $this->tache->id,
            'projetId' => $this->tache->projet_id,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.User.' . $this->tache->user_id)];
    }
}