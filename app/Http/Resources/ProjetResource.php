<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'progression' => $this->progression(),
            'repartition' => [
                'todo' => $this->taches->where('statut', 'TODO')->count(),
                'doing' => $this->taches->where('statut', 'DOING')->count(),
                'done' => $this->taches->where('statut', 'DONE')->count(),
            ],
        ];
    }
}