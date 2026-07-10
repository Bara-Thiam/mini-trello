<?php

namespace Database\Factories;

use App\Models\Tache;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tache>
 */
class TacheFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titre' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'statut' => 'TODO',
            'priorite' => 'MEDIUM',
            'echeance' => $this->faker->dateTimeBetween('now', '+2 months'),
        ];
    }
}