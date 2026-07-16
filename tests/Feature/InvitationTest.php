<?php

namespace Tests\Feature;

use App\Models\Projet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Tache;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_inviter_puis_accepter_ajoute_lutilisateur_au_projet(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $projet->users()->attach($chef);
        $invite = User::factory()->create(['email' => 'invite@mail.com']);

        $this->actingAs($chef)->post("/projects/{$projet->id}/invitations", ['email' => 'invite@mail.com']);

        $notification = $invite->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals('invitation', $notification->data['kind']);
        $this->assertEquals('en_attente', $notification->data['statut']);

        $this->actingAs($invite)->post("/notifications/{$notification->id}/accepter");

        $this->assertTrue($projet->fresh()->users->contains($invite));
        $this->assertEquals('acceptee', $notification->fresh()->data['statut']);
        $this->assertTrue($chef->fresh()->unreadNotifications->isNotEmpty());
    }

    public function test_refuser_une_invitation_najoute_pas_lutilisateur_au_projet(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $projet->users()->attach($chef);
        $invite = User::factory()->create(['email' => 'invite@mail.com']);

        $this->actingAs($chef)->post("/projects/{$projet->id}/invitations", ['email' => 'invite@mail.com']);
        $notification = $invite->notifications()->first();

        $this->actingAs($invite)->post("/notifications/{$notification->id}/refuser");

        $this->assertFalse($projet->fresh()->users->contains($invite));
        $this->assertEquals('refusee', $notification->fresh()->data['statut']);
        $this->assertTrue($chef->fresh()->unreadNotifications->isNotEmpty());
    }

    public function test_on_ne_peut_pas_inviter_un_email_inexistant(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $projet->users()->attach($chef);

        $response = $this->actingAs($chef)->post("/projects/{$projet->id}/invitations", [
            'email' => 'inconnu@mail.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_on_ne_peut_pas_inviter_quelquun_deja_membre(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $dejaMembre = User::factory()->create(['email' => 'deja@mail.com']);
        $projet->users()->attach([$chef->id, $dejaMembre->id]);

        $response = $this->actingAs($chef)->post("/projects/{$projet->id}/invitations", [
            'email' => 'deja@mail.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_un_non_membre_ne_peut_pas_inviter_dans_un_projet(): void
    {
        $projet = Projet::factory()->create();
        $exterieur = User::factory()->create(['role' => 'chef_projet']);
        $cible = User::factory()->create(['email' => 'cible@mail.com']);

        $response = $this->actingAs($exterieur)->post("/projects/{$projet->id}/invitations", [
            'email' => 'cible@mail.com',
        ]);

        $response->assertForbidden();
        $this->assertFalse($projet->fresh()->users->contains($cible));
    }

    public function test_un_membre_simple_ne_peut_pas_inviter(): void
    {
        $projet = Projet::factory()->create();
        $membre = User::factory()->create(['role' => 'membre']);
        $projet->users()->attach($membre);
        $cible = User::factory()->create(['email' => 'cible@mail.com']);

        $response = $this->actingAs($membre)->post("/projects/{$projet->id}/invitations", [
            'email' => 'cible@mail.com',
        ]);

        $response->assertForbidden();
        $this->assertFalse($projet->fresh()->users->contains($cible));
    }

    public function test_on_ne_peut_pas_inviter_deux_fois_la_meme_personne_en_attente(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $projet->users()->attach($chef);
        $cible = User::factory()->create(['email' => 'cible@mail.com']);

        $this->actingAs($chef)->post("/projects/{$projet->id}/invitations", ['email' => 'cible@mail.com']);
        $response = $this->actingAs($chef)->post("/projects/{$projet->id}/invitations", ['email' => 'cible@mail.com']);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, $cible->notifications()->count());
    }

    public function test_un_chef_de_projet_peut_retirer_un_membre(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $membre = User::factory()->create(['role' => 'membre']);
        $projet->users()->attach([$chef->id, $membre->id]);
        $tache = Tache::factory()->create(['projet_id' => $projet->id, 'user_id' => $membre->id]);

        $this->actingAs($chef)->delete("/projects/{$projet->id}/membres/{$membre->id}");

        $this->assertFalse($projet->fresh()->users->contains($membre));
        $this->assertNull($tache->fresh()->user_id);
    }

    public function test_on_ne_peut_pas_retirer_le_dernier_membre_dun_projet(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $projet->users()->attach($chef);

        $this->actingAs($chef)->delete("/projects/{$projet->id}/membres/{$chef->id}");

        $this->assertTrue($projet->fresh()->users->contains($chef));
    }
}