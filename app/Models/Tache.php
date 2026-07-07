<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = ['projet_id', 'user_id', 'titre', 'description', 'statut', 'priorite', 'echeance'];
}
