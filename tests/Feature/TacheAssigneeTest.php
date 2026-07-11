<?php

namespace Tests\Feature;

use App\Models\Projet;
use App\Models\Tache;
use App\Models\User;
use App\Notifications\TacheAssigneeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TacheAssigneeTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigner_une_tache_envoie_une_notification(): void
    {
        Notification::fake();

        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $membre = User::factory()->create(['role' => 'membre']);
        $tache = Tache::factory()->create(['projet_id' => $projet->id, 'user_id' => null]);

        $this->actingAs($chef)->put("/taches/{$tache->id}", [
            'user_id' => $membre->id,
        ]);

        Notification::assertSentTo($membre, TacheAssigneeNotification::class);
    }

    public function test_modifier_le_titre_sans_changer_lassignation_nenvoie_rien(): void
    {
        Notification::fake();

        $projet = Projet::factory()->create();
        $membre = User::factory()->create(['role' => 'membre']);
        $tache = Tache::factory()->create([
            'projet_id' => $projet->id,
            'user_id' => $membre->id,
        ]);

        $this->actingAs($membre)->put("/taches/{$tache->id}", [
            'titre' => 'Nouveau titre',
        ]);

        Notification::assertNothingSent();
    }
}