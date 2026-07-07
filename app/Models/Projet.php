<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    public function taches()
    {
        return $this->hasMany(Tache::class);
    }

    public function colonnes()
    {
        return $this->hasMany(Colonne::class);
    }

    protected $fillable = ['nom', 'description'];
}
