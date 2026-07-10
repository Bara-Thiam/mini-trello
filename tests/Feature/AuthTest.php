<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_peut_creer_un_compte(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@mail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'email' => 'test@mail.com',
            'role' => 'membre',
        ]);
        $this->assertAuthenticated();
    }

    public function test_un_utilisateur_peut_se_connecter_avec_les_bons_identifiants(): void
    {
        $user = User::factory()->create([
            'email' => 'test@mail.com',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@mail.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_la_connexion_echoue_avec_un_mauvais_mot_de_passe(): void
    {
        User::factory()->create([
            'email' => 'test@mail.com',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@mail.com',
            'password' => 'mauvais_mot_de_passe',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}