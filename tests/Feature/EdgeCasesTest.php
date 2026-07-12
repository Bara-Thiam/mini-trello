<?php

namespace Tests\Feature;

use App\Models\Projet;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    public function test_supprimer_un_projet_supprime_ses_colonnes_et_taches(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $projet = Projet::factory()->create();
        $colonne = $projet->colonnes()->create(['nom' => 'ToDo', 'ordre' => 1]);
        $tache = Tache::factory()->create(['projet_id' => $projet->id]);

        $this->actingAs($admin)->delete("/projects/{$projet->id}");

        $this->assertDatabaseMissing('colonnes', ['id' => $colonne->id]);
        $this->assertDatabaseMissing('taches', ['id' => $tache->id]);
    }

    public function test_supprimer_un_utilisateur_rend_ses_taches_orphelines_sans_les_supprimer(): void
    {
        $projet = Projet::factory()->create();
        $user = User::factory()->create();
        $tache = Tache::factory()->create(['projet_id' => $projet->id, 'user_id' => $user->id]);

        $user->delete();

        $this->assertDatabaseHas('taches', ['id' => $tache->id, 'user_id' => null]);
    }

    public function test_supprimer_un_utilisateur_le_retire_de_ses_projets(): void
    {
        $projet = Projet::factory()->create();
        $user = User::factory()->create();
        $projet->users()->attach($user);

        $user->delete();

        $this->assertDatabaseMissing('projet_user', ['projet_id' => $projet->id, 'user_id' => $user->id]);
    }

    public function test_progression_est_100_pourcent_quand_toutes_les_taches_sont_done(): void
    {
        $projet = Projet::factory()->create();
        Tache::factory()->count(3)->create(['projet_id' => $projet->id, 'statut' => 'DONE']);

        $this->assertEquals(100, $projet->fresh()->progression());
    }

    public function test_progression_est_zero_sur_un_projet_sans_taches(): void
    {
        $projet = Projet::factory()->create();

        $this->assertEquals(0, $projet->progression());
    }

    public function test_le_champ_statut_est_ignore_a_la_creation_meme_sil_est_envoye(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $projet->users()->attach($chef);

        $this->actingAs($chef)->post("/projects/{$projet->id}/taches", [
            'titre' => 'Tentative de forcer le statut',
            'statut' => 'DONE',
        ]);

        $this->assertDatabaseHas('taches', [
            'titre' => 'Tentative de forcer le statut',
            'statut' => 'TODO',
        ]);
    }
}