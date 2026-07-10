<?php

namespace Tests\Feature;

use App\Models\Projet;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_progression_et_le_taux_de_priorite_sont_corrects(): void
    {
        $projet = Projet::factory()->create();
        $user = User::factory()->create(['role' => 'membre']);
        $projet->users()->attach($user);

        Tache::factory()->create(['projet_id' => $projet->id, 'statut' => 'DONE', 'priorite' => 'HIGH']);
        Tache::factory()->create(['projet_id' => $projet->id, 'statut' => 'DONE', 'priorite' => 'LOW']);
        Tache::factory()->create(['projet_id' => $projet->id, 'statut' => 'TODO', 'priorite' => 'LOW']);
        Tache::factory()->create(['projet_id' => $projet->id, 'statut' => 'TODO', 'priorite' => 'LOW']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Index', shouldExist: false)
            ->where('parProjet.0.progression', 50)
            ->where('parProjet.0.tauxPrioriteHaute', 25)
        );
    }

    public function test_le_nombre_de_taches_par_membre_est_correct(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $membre = User::factory()->create(['role' => 'membre']);
        $projet->users()->attach([$chef->id, $membre->id]);

        Tache::factory()->count(3)->create(['projet_id' => $projet->id, 'user_id' => $membre->id]);
        Tache::factory()->count(1)->create(['projet_id' => $projet->id, 'user_id' => $chef->id]);

        $response = $this->actingAs($chef)->get('/dashboard');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Index', shouldExist: false)
            ->has('parMembre', 2)
        );
    }
}