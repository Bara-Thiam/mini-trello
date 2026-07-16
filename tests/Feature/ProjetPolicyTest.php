<?php

namespace Tests\Feature;

use App\Models\Projet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjetPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_membre_du_projet_peut_le_voir(): void
    {
        $projet = Projet::factory()->create();
        $user = User::factory()->create(['role' => 'membre']);
        $projet->users()->attach($user);

        $this->assertTrue($user->can('view', $projet));
    }

    public function test_un_non_membre_ne_peut_pas_voir_le_projet(): void
    {
        $projet = Projet::factory()->create();
        $user = User::factory()->create(['role' => 'membre']);

        $this->assertFalse($user->can('view', $projet));
    }

    public function test_un_admin_peut_voir_nimporte_quel_projet(): void
    {
        $projet = Projet::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($admin->can('view', $projet));
    }

    public function test_un_chef_de_projet_ne_peut_pas_creer_de_projet(): void
    {
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $this->assertFalse($chef->can('create', Projet::class));
    }

    public function test_un_admin_peut_creer_un_projet(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->assertTrue($admin->can('create', Projet::class));
    }

    public function test_un_membre_ne_peut_pas_creer_de_projet(): void
    {
        $membre = User::factory()->create(['role' => 'membre']);

        $this->assertFalse($membre->can('create', Projet::class));
    }

    public function test_seul_ladmin_peut_supprimer_un_projet(): void
    {
        $projet = Projet::factory()->create();
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertFalse($chef->can('delete', $projet));
        $this->assertTrue($admin->can('delete', $projet));
    }
}