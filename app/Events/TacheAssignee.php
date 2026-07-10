<?php

namespace App\Events;

use App\Models\Tache;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TacheAssignee implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Tache $tache)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->tache->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'tache.assignee';
    }

    public function broadcastWith(): array
    {
        return [
            'tacheId' => $this->tache->id,
            'titre' => $this->tache->titre,
            'projetId' => $this->tache->projet_id,
        ];
    }
}