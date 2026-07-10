<?php

namespace Tests\Feature;

use App\Models\Projet;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TachePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_membre_peut_modifier_sa_propre_tache(): void
    {
        $projet = Projet::factory()->create();
        $membre = User::factory()->create(['role' => 'membre']);
        $tache = Tache::factory()->create([
            'projet_id' => $projet->id,
            'user_id' => $membre->id,
        ]);

        $this->assertTrue($membre->can('update', $tache));
    }

    public function test_un_membre_ne_peut_pas_modifier_la_tache_dun_autre_membre(): void
    {
        $projet = Projet::factory()->create();
        $membre = User::factory()->create(['role' => 'membre']);
        $autreMembre = User::factory()->create(['role' => 'membre']);
        $tache = Tache::factory()->create([
            'projet_id' => $projet->id,
            'user_id' => $autreMembre->id,
        ]);

        $this->assertFalse($membre->can('update', $tache));
    }

    public function test_un_chef_de_projet_peut_modifier_nimporte_quelle_tache(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $membre = User::factory()->create(['role' => 'membre']);
        $tache = Tache::factory()->create([
            'projet_id' => $projet->id,
            'user_id' => $membre->id,
        ]);

        $this->assertTrue($chef->can('update', $tache));
    }

    public function test_un_membre_ne_peut_pas_supprimer_une_tache(): void
    {
        $projet = Projet::factory()->create();
        $membre = User::factory()->create(['role' => 'membre']);
        $tache = Tache::factory()->create([
            'projet_id' => $projet->id,
            'user_id' => $membre->id,
        ]);

        $this->assertFalse($membre->can('delete', $tache));
    }

    public function test_un_chef_de_projet_peut_supprimer_une_tache(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $tache = Tache::factory()->create(['projet_id' => $projet->id]);

        $this->assertTrue($chef->can('delete', $tache));
    }
}