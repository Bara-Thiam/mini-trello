<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Sereigne Bara Thiam',
            'email' => 'bara@mail.com',
            'password' => 'password',
            'role' => 'chef_projet',
        ]);

        User::create([
            'name' => 'Joanelle Aholou-Kotiko',
            'email' => 'joanelle@mail.com',
            'password' => 'password',
            'role' => 'membre',
        ]);

        User::create([
            'name' => 'Mactar Camara',
            'email' => 'mactar@mail.com',
            'password' => 'password',
            'role' => 'membre',
        ]);

        User::create([
            'name' => 'Fatou Sarr',
            'email' => 'fatou@mail.com',
            'password' => 'password',
            'role' => 'membre',
        ]);

        User::create([
            'name' => 'Ibrahima Fall',
            'email' => 'ibrahima@mail.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
    }
}