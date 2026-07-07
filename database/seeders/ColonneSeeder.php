<?php

namespace Database\Seeders;

use App\Models\Colonne;
use App\Models\Projet;
use Illuminate\Database\Seeder;

class ColonneSeeder extends Seeder
{
    public function run(): void
    {
        $projets = Projet::all();

        foreach ($projets as $projet) {
            Colonne::create(['projet_id' => $projet->id, 'nom' => 'ToDo', 'ordre' => 1]);
            Colonne::create(['projet_id' => $projet->id, 'nom' => 'Doing', 'ordre' => 2]);
            Colonne::create(['projet_id' => $projet->id, 'nom' => 'Done', 'ordre' => 3]);
        }
    }
}