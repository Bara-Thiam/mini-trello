<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TacheResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'projetId' => $this->projet_id,
            'userId' => $this->user_id,
            'titre' => $this->titre,
            'description' => $this->description,
            'statut' => $this->statut,
            'priorite' => $this->priorite,
            'echeance' => $this->echeance?->format('Y-m-d'),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
        ];
    }
}