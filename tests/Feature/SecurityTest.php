<?php

namespace Tests\Feature;

use App\Models\Projet;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_visiteur_non_connecte_est_redirige_vers_login(): void
    {
        $response = $this->get('/projects');

        $response->assertRedirect('/login');
    }

    public function test_le_register_ne_permet_jamais_de_creer_un_admin(): void
    {
        $this->post('/register', [
            'name' => 'Tentative Admin',
            'email' => 'tentative@mail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'tentative@mail.com',
            'role' => 'membre',
        ]);
    }

    public function test_un_membre_dun_autre_projet_ne_peut_pas_modifier_une_tache_ici(): void
    {
        $projetA = Projet::factory()->create();
        $projetB = Projet::factory()->create();
        $userSeulementSurA = User::factory()->create(['role' => 'membre']);
        $projetA->users()->attach($userSeulementSurA);
        $tacheDansB = Tache::factory()->create(['projet_id' => $projetB->id]);

        $response = $this->actingAs($userSeulementSurA)->put("/taches/{$tacheDansB->id}", [
            'titre' => 'Modification non autorisée',
        ]);

        $response->assertForbidden();
    }

    public function test_on_ne_peut_pas_marquer_comme_lue_la_notification_dun_autre(): void
    {
        $victime = User::factory()->create();
        $attaquant = User::factory()->create();
        $projet = Projet::factory()->create();
        $tache = Tache::factory()->create(['projet_id' => $projet->id, 'user_id' => $victime->id]);
        $victime->notify(new \App\Notifications\TacheAssigneeNotification($tache));
        $notification = $victime->notifications()->first();

        $response = $this->actingAs($attaquant)->post("/notifications/{$notification->id}/lue");

        $response->assertNotFound();
        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_on_ne_peut_pas_accepter_une_invitation_qui_ne_nous_est_pas_destinee(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $projet->users()->attach($chef);
        $destinataire = User::factory()->create(['email' => 'destinataire@mail.com']);
        $intrus = User::factory()->create();

        $this->actingAs($chef)->post("/projects/{$projet->id}/invitations", ['email' => 'destinataire@mail.com']);
        $notification = $destinataire->notifications()->first();

        $response = $this->actingAs($intrus)->post("/notifications/{$notification->id}/accepter");

        $response->assertNotFound();
        $this->assertFalse($projet->fresh()->users->contains($intrus));
    }

    public function test_un_membre_ne_peut_pas_supprimer_un_projet_meme_sil_en_fait_partie(): void
    {
        $projet = Projet::factory()->create();
        $membre = User::factory()->create(['role' => 'membre']);
        $projet->users()->attach($membre);

        $response = $this->actingAs($membre)->delete("/projects/{$projet->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('projets', ['id' => $projet->id]);
    }

    public function test_un_chef_de_projet_dun_autre_projet_ne_peut_pas_inviter_ici(): void
    {
        $projetA = Projet::factory()->create();
        $projetB = Projet::factory()->create();
        $chefSeulementSurA = User::factory()->create(['role' => 'chef_projet']);
        $projetA->users()->attach($chefSeulementSurA);
        $cible = User::factory()->create(['email' => 'cible@mail.com']);

        $response = $this->actingAs($chefSeulementSurA)->post("/projects/{$projetB->id}/invitations", [
            'email' => 'cible@mail.com',
        ]);

        $response->assertForbidden();
    }

    public function test_on_ne_peut_pas_assigner_une_tache_a_quelquun_hors_du_projet(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $projet->users()->attach($chef);
        $exterieur = User::factory()->create();

        $response = $this->actingAs($chef)->post("/projects/{$projet->id}/taches", [
            'titre' => 'Tâche test',
            'user_id' => $exterieur->id,
        ]);

        $response->assertSessionHasErrors('user_id');
        $this->assertDatabaseMissing('taches', ['titre' => 'Tâche test']);
    }
}