<?php

namespace Database\Seeders;

use App\Models\Projet;
use Illuminate\Database\Seeder;

class ProjetSeeder extends Seeder
{
    public function run(): void
    {
        Projet::create([
            'nom' => 'Refonte site vitrine',
            'description' => 'Migration du site vers le nouveau template',
        ]);

        Projet::create([
            'nom' => 'App mobile interne',
            'description' => 'Application de suivi des livraisons',
        ]);
    }
}