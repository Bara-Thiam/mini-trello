<?php

namespace Database\Seeders;

use App\Models\Projet;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjetUserSeeder extends Seeder
{
    public function run(): void
    {
        $refonte = Projet::where('nom', 'Refonte site vitrine')->firstOrFail();
        $appMobile = Projet::where('nom', 'App mobile interne')->firstOrFail();

        $bara = User::where('email', 'bara@gmail.com')->firstOrFail();
        $awa = User::where('email', 'joanelle@gmail.com')->firstOrFail();
        $mactar = User::where('email', 'mactar@gmail.com')->firstOrFail();
        $fatou = User::where('email', 'fatou@gmail.com')->firstOrFail();
        $abdoulaye = User::where('email', 'abdoulaye@gmail.com')->firstOrFail(); // ou abdoulaye@gmail.com — à confirmer

        $refonte->users()->attach([$bara->id, $awa->id, $mactar->id, $fatou->id, $abdoulaye->id]);
        $appMobile->users()->attach([$bara->id, $awa->id, $mactar->id, $fatou->id, $abdoulaye->id]);
    }
}