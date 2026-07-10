<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Projet extends Model
{
    use HasFactory;

    public function taches()
    {
        return $this->hasMany(Tache::class);
    }

    public function colonnes()
    {
        return $this->hasMany(Colonne::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    protected $fillable = ['nom', 'description'];

    public function progression(): int
    {
        $total = $this->taches->count();
        return $total > 0 ? (int) round($this->taches->where('statut', 'DONE')->count() / $total * 100) : 0;
    }

    public function tauxPrioriteHaute(): int
    {
        $total = $this->taches->count();
        return $total > 0 ? (int) round($this->taches->where('priorite', 'HIGH')->count() / $total * 100) : 0;
    }
}
