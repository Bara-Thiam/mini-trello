<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tache extends Model
{
    use HasFactory;

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = ['projet_id', 'user_id', 'titre', 'description', 'statut', 'priorite', 'echeance'];

    protected function casts(): array
    {
        return [
            'echeance' => 'date',
        ];
    }
}
