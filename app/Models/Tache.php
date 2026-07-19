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

    public function activites()
    {
        return $this->hasMany(Activite::class)->latest();
    }

    protected static function booted(): void
    {
        static::created(function (Tache $tache) {
            $tache->activites()->create([
                'user_id' => auth()->id(),
                'type' => 'creation',
            ]);
        });

        static::updating(function (Tache $tache) {
            $champsSuivis = ['titre', 'description', 'statut', 'priorite', 'echeance', 'user_id'];

            foreach ($tache->getDirty() as $champ => $nouvelle) {
                if (!in_array($champ, $champsSuivis)) {
                    continue;
                }

                $ancienne = $tache->getOriginal($champ);

                if ($champ === 'user_id') {
                    $ancienne = $ancienne ? \App\Models\User::find($ancienne)?->name : null;
                    $nouvelle = $nouvelle ? \App\Models\User::find($nouvelle)?->name : null;
                }

                $tache->activites()->create([
                    'user_id' => auth()->id(),
                    'type' => 'modification',
                    'champ' => $champ,
                    'ancienne_valeur' => $ancienne,
                    'nouvelle_valeur' => $nouvelle,
                ]);
            }
        });
    }
}
