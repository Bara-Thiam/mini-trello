<?php

namespace Tests\Feature;

use App\Events\TacheAssignee;
use App\Models\Projet;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TacheAssigneeTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigner_une_tache_declenche_levenement(): void
    {
        Event::fake();

        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $membre = User::factory()->create(['role' => 'membre']);
        $tache = Tache::factory()->create(['projet_id' => $projet->id, 'user_id' => null]);

        $this->actingAs($chef)->put("/taches/{$tache->id}", [
            'user_id' => $membre->id,
        ]);

        Event::assertDispatched(TacheAssignee::class, function ($event) use ($tache, $membre) {
            return $event->tache->id === $tache->id
                && $event->tache->user_id === $membre->id;
        });
    }

    public function test_modifier_le_titre_sans_changer_lassignation_ne_declenche_rien(): void
    {
        Event::fake();

        $projet = Projet::factory()->create();
        $membre = User::factory()->create(['role' => 'membre']);
        $tache = Tache::factory()->create([
            'projet_id' => $projet->id,
            'user_id' => $membre->id,
        ]);

        $this->actingAs($membre)->put("/taches/{$tache->id}", [
            'titre' => 'Nouveau titre',
        ]);

        Event::assertNotDispatched(TacheAssignee::class);
    }
}