<?php

namespace Tests\Feature;

use App\Models\Projet;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TacheControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_membre_du_projet_peut_creer_une_tache(): void
    {
        $projet = Projet::factory()->create();
        $membre = User::factory()->create(['role' => 'membre']);
        $projet->users()->attach($membre);

        $response = $this->actingAs($membre)->post("/projects/{$projet->id}/taches", [
            'titre' => 'Nouvelle tâche',
        ]);

        $response->assertRedirect("/projects/{$projet->id}");
        $this->assertDatabaseHas('taches', [
            'projet_id' => $projet->id,
            'titre' => 'Nouvelle tâche',
            'statut' => 'TODO',
        ]);
    }

    public function test_un_membre_ne_peut_pas_modifier_la_tache_dun_autre(): void
    {
        $projet = Projet::factory()->create();
        $membre = User::factory()->create(['role' => 'membre']);
        $autreMembre = User::factory()->create(['role' => 'membre']);
        $tache = Tache::factory()->create([
            'projet_id' => $projet->id,
            'user_id' => $autreMembre->id,
        ]);

        $response = $this->actingAs($membre)->put("/taches/{$tache->id}", [
            'titre' => 'Titre modifié',
        ]);

        $response->assertForbidden();
    }

    public function test_le_statut_par_defaut_est_todo_a_la_creation(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);

        $this->actingAs($chef)->post("/projects/{$projet->id}/taches", [
            'titre' => 'Sans statut précisé',
        ]);

        $this->assertDatabaseHas('taches', [
            'titre' => 'Sans statut précisé',
            'statut' => 'TODO',
        ]);
    }

    public function test_le_drag_and_drop_change_le_statut_et_renvoie_du_json(): void
    {
        $projet = Projet::factory()->create();
        $membre = User::factory()->create(['role' => 'membre']);
        $tache = Tache::factory()->create([
            'projet_id' => $projet->id,
            'user_id' => $membre->id,
            'statut' => 'TODO',
        ]);

        $response = $this->actingAs($membre)->patch("/taches/{$tache->id}/statut", [
            'statut' => 'DOING',
        ]);

        $response->assertOk();
        $response->assertJson(['statut' => 'DOING']);
        $this->assertDatabaseHas('taches', ['id' => $tache->id, 'statut' => 'DOING']);
    }
}