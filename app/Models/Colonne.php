<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colonne extends Model
{
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    protected $fillable = ['projet_id', 'nom', 'ordre'];
}
