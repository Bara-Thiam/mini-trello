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
            'email' => 'bara@gmail.com',
            'password' => 'password',
            'role' => 'chef_projet',
        ]);

        User::create([
            'name' => 'Joanelle Aholou-Kotiko',
            'email' => 'joanelle@gmail.com',
            'password' => 'password',
            'role' => 'membre',
        ]);

        User::create([
            'name' => 'Mactar Camara',
            'email' => 'mactar@gmail.com',
            'password' => 'password',
            'role' => 'membre',
        ]);

        User::create([
            'name' => 'Fatou Sarr',
            'email' => 'fatou@gmail.com',
            'password' => 'password',
            'role' => 'membre',
        ]);

        User::create([
            'name' => 'Abdoulaye Diallo',
            'email' => 'abdoulaye@gmail.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
    }
}