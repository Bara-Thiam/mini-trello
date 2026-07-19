<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{
    protected $fillable = ['tache_id', 'user_id', 'type', 'champ', 'ancienne_valeur', 'nouvelle_valeur'];

    public function tache()
    {
        return $this->belongsTo(Tache::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}