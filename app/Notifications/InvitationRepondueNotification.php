<?php

namespace App\Notifications;

use App\Models\Projet;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class InvitationRepondueNotification extends Notification
{
    public function __construct(public Projet $projet, public User $invite, public bool $accepte)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $verbe = $this->accepte ? 'a accepté' : 'a refusé';

        return [
            'kind' => 'reponse_invitation',
            'message' => "{$this->invite->name} {$verbe} votre invitation pour « {$this->projet->nom} »",
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.User.' . $this->id)];
    }
}