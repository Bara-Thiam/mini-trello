<?php

namespace Tests\Feature;

use App\Models\Projet;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiviteTest extends TestCase
{
    use RefreshDatabase;

    public function test_creer_une_tache_enregistre_une_activite(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $projet->users()->attach($chef);

        $this->actingAs($chef)->post("/projects/{$projet->id}/taches", ['titre' => 'Tâche test']);

        $tache = Tache::where('titre', 'Tâche test')->first();
        $this->assertEquals('creation', $tache->activites()->first()->type);
    }

    public function test_modifier_le_titre_enregistre_lancienne_et_la_nouvelle_valeur(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $projet->users()->attach($chef);
        $tache = Tache::factory()->create(['projet_id' => $projet->id, 'titre' => 'Ancien titre']);

        $this->actingAs($chef)->put("/taches/{$tache->id}", ['titre' => 'Nouveau titre']);

        $activite = $tache->activites()->where('champ', 'titre')->first();
        $this->assertEquals('Ancien titre', $activite->ancienne_valeur);
        $this->assertEquals('Nouveau titre', $activite->nouvelle_valeur);
    }

    public function test_assigner_une_tache_enregistre_le_nom_de_lutilisateur(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $membre = User::factory()->create(['role' => 'membre', 'name' => 'Awa Ndiaye']);
        $projet->users()->attach([$chef->id, $membre->id]);
        $tache = Tache::factory()->create(['projet_id' => $projet->id, 'user_id' => null]);

        $this->actingAs($chef)->put("/taches/{$tache->id}", ['user_id' => $membre->id]);

        $activite = $tache->activites()->where('champ', 'user_id')->first();
        $this->assertNull($activite->ancienne_valeur);
        $this->assertEquals('Awa Ndiaye', $activite->nouvelle_valeur);
    }

    public function test_le_drag_and_drop_enregistre_le_changement_de_statut(): void
    {
        $projet = Projet::factory()->create();
        $membre = User::factory()->create(['role' => 'membre']);
        $projet->users()->attach($membre);
        $tache = Tache::factory()->create(['projet_id' => $projet->id, 'user_id' => $membre->id, 'statut' => 'TODO']);

        $this->actingAs($membre)->patch("/taches/{$tache->id}/statut", ['statut' => 'DOING']);

        $activite = $tache->activites()->where('champ', 'statut')->first();
        $this->assertEquals('TODO', $activite->ancienne_valeur);
        $this->assertEquals('DOING', $activite->nouvelle_valeur);
    }
}