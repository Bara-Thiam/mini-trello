<?php

namespace App\Notifications;

use App\Models\Projet;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ProjetInvitationNotification extends Notification
{
    public function __construct(public Projet $projet, public User $inviteur)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'kind' => 'invitation',
            'message' => "{$this->inviteur->name} vous invite à rejoindre « {$this->projet->nom} »",
            'projetId' => $this->projet->id,
            'projetNom' => $this->projet->nom,
            'inviteurId' => $this->inviteur->id,
            'statut' => 'en_attente',
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