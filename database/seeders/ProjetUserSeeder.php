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

        $bara = User::where('email', 'bara@mail.com')->firstOrFail();
        $awa = User::where('email', 'joanelle@mail.com')->firstOrFail();
        $mactar = User::where('email', 'mactar@mail.com')->firstOrFail();
        $fatou = User::where('email', 'fatou@mail.com')->firstOrFail();
        $ibrahima = User::where('email', 'ibrahima@mail.com')->firstOrFail();

        $refonte->users()->attach([$bara->id, $awa->id, $mactar->id, $fatou->id, $ibrahima->id]);
        $appMobile->users()->attach([$bara->id, $awa->id, $mactar->id, $fatou->id, $ibrahima->id]);
    }
}