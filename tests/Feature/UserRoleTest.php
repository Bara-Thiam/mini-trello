<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_admin_peut_promouvoir_un_membre_en_chef_de_projet(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $membre = User::factory()->create(['role' => 'membre']);

        $this->actingAs($admin)->patch("/users/{$membre->id}/role", ['role' => 'chef_projet']);

        $this->assertEquals('chef_projet', $membre->fresh()->role);
    }

    public function test_un_admin_ne_peut_pas_modifier_son_propre_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->patch("/users/{$admin->id}/role", ['role' => 'membre']);

        $response->assertForbidden();
        $this->assertEquals('admin', $admin->fresh()->role);
    }

    public function test_un_membre_ne_peut_pas_acceder_a_la_liste_des_utilisateurs(): void
    {
        $membre = User::factory()->create(['role' => 'membre']);

        $response = $this->actingAs($membre)->get('/users');

        $response->assertForbidden();
    }

    public function test_un_chef_de_projet_ne_peut_pas_changer_de_role(): void
    {
        $chef = User::factory()->create(['role' => 'chef_projet']);
        $membre = User::factory()->create(['role' => 'membre']);

        $response = $this->actingAs($chef)->patch("/users/{$membre->id}/role", ['role' => 'admin']);

        $response->assertForbidden();
        $this->assertEquals('membre', $membre->fresh()->role);
    }
}