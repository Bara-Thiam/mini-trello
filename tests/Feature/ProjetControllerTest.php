<?php

namespace Tests\Feature;

use App\Models\Projet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProjetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_membre_ne_voit_que_ses_projets(): void
    {
        $projetA = Projet::factory()->create();
        $projetB = Projet::factory()->create();
        $user = User::factory()->create(['role' => 'membre']);
        $projetA->users()->attach($user);

        $response = $this->actingAs($user)->get('/projects');

        $response->assertInertia(
            fn(Assert $page) => $page
                ->component('Projects/Index', shouldExist: false)
                ->has('projets', 1)
                ->where('projets.0.id', $projetA->id)
        );
    }

    public function test_un_admin_voit_tous_les_projets(): void
    {
        Projet::factory()->count(3)->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/projects');

        $response->assertInertia(
            fn(Assert $page) => $page
                ->component('Projects/Index', shouldExist: false)
                ->has('projets', 3)
        );
    }

    public function test_un_non_membre_ne_peut_pas_voir_le_detail_dun_projet(): void
    {
        $projet = Projet::factory()->create();
        $user = User::factory()->create(['role' => 'membre']);

        $response = $this->actingAs($user)->get("/projects/{$projet->id}");

        $response->assertForbidden();
    }

    public function test_un_admin_peut_creer_un_projet_et_en_devient_membre(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/projects', [
            'nom' => 'Nouveau projet',
            'description' => 'Test',
        ]);

        $response->assertRedirect('/projects');
        $this->assertDatabaseHas('projets', ['nom' => 'Nouveau projet']);

        $projet = Projet::where('nom', 'Nouveau projet')->first();
        $this->assertTrue($projet->users->contains($admin));
    }

    public function test_creer_un_projet_genere_les_trois_colonnes_par_defaut(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/projects', ['nom' => 'Test colonnes']);

        $projet = Projet::where('nom', 'Test colonnes')->first();
        $this->assertCount(3, $projet->colonnes);
        $this->assertEquals(['ToDo', 'Doing', 'Done'], $projet->colonnes()->orderBy('ordre')->pluck('nom')->toArray());
    }
}