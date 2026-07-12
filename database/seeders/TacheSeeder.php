<?php

namespace Database\Seeders;

use App\Models\Projet;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Database\Seeder;

class TacheSeeder extends Seeder
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

        Tache::create([
            'projet_id' => $refonte->id,
            'user_id' => $awa->id,
            'titre' => 'Intégrer la maquette Kanban',
            'description' => 'Découpage des colonnes ToDo/Doing/Done',
            'statut' => 'DOING',
            'priorite' => 'HIGH',
            'echeance' => '2026-08-01',
        ]);

        Tache::create([
            'projet_id' => $refonte->id,
            'user_id' => $mactar->id,
            'titre' => "Mettre en place l'authentification",
            'description' => 'Login/register avec rôles',
            'statut' => 'TODO',
            'priorite' => 'MEDIUM',
            'echeance' => '2026-08-05',
        ]);

        Tache::create([
            'projet_id' => $refonte->id,
            'user_id' => $fatou->id,
            'titre' => 'Rédiger le README',
            'description' => "Instructions d'installation",
            'statut' => 'DONE',
            'priorite' => 'LOW',
            'echeance' => '2026-07-20',
        ]);

        Tache::create([
            'projet_id' => $appMobile->id,
            'user_id' => $awa->id,
            'titre' => 'Concevoir le schéma de suivi',
            'description' => 'Modélisation des livraisons',
            'statut' => 'TODO',
            'priorite' => 'HIGH',
            'echeance' => '2026-08-10',
        ]);

        Tache::create([
            'projet_id' => $appMobile->id,
            'user_id' => $mactar->id,
            'titre' => "Développer l'API de suivi",
            'description' => 'Endpoints REST pour le tracking des livraisons',
            'statut' => 'TODO',
            'priorite' => 'MEDIUM',
            'echeance' => '2026-08-12',
        ]);

        Tache::create([
            'projet_id' => $appMobile->id,
            'user_id' => $fatou->id,
            'titre' => 'Créer les maquettes UI',
            'description' => 'Wireframes des écrans principaux',
            'statut' => 'DOING',
            'priorite' => 'MEDIUM',
            'echeance' => '2026-07-25',
        ]);

        Tache::create([
            'projet_id' => $refonte->id,
            'user_id' => $bara->id,
            'titre' => "Configurer l'authentification API",
            'description' => 'Middleware et policies par rôle',
            'statut' => 'DONE',
            'priorite' => 'HIGH',
            'echeance' => '2026-07-15',
        ]);

        Tache::create([
            'projet_id' => $refonte->id,
            'user_id' => $abdoulaye->id,
            'titre' => 'Réviser les permissions admin',
            'description' => 'Vérification des accès par rôle',
            'statut' => 'TODO',
            'priorite' => 'LOW',
            'echeance' => '2026-08-15',
        ]);

        Tache::create([
            'projet_id' => $appMobile->id,
            'user_id' => $awa->id,
            'titre' => 'Tester la synchronisation offline',
            'description' => 'Vérifier le comportement hors ligne',
            'statut' => 'TODO',
            'priorite' => 'HIGH',
            'echeance' => '2026-08-20',
        ]);

        Tache::create([
            'projet_id' => $appMobile->id,
            'user_id' => $abdoulaye->id,
            'titre' => 'Valider le déploiement',
            'description' => 'Checklist de mise en production',
            'statut' => 'TODO',
            'priorite' => 'MEDIUM',
            'echeance' => '2026-08-25',
        ]);
    }
}