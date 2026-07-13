# mini-Trello

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue](https://img.shields.io/badge/Vue-3-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![Inertia](https://img.shields.io/badge/Inertia.js-9553E9?style=for-the-badge&logo=inertia&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Tailwind](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Tests](https://img.shields.io/badge/Tests-48%20passing-00C48C?style=for-the-badge&logo=php&logoColor=white)

Application collaborative de gestion de tâches type Kanban, développée avec Laravel 12 (backend + API), Vue 3 et Inertia.js (frontend), Laravel Reverb (notifications temps réel) et MySQL.

## Sommaire

- [Stack technique](#stack-technique)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Lancer l'application](#lancer-lapplication)
- [Comptes de démonstration](#comptes-de-démonstration)
- [Lancer les tests](#lancer-les-tests)
- [Fonctionnalités](#fonctionnalités)
- [Rôles et permissions](#rôles-et-permissions)
- [Structure du projet](#structure-du-projet)
- [Équipe](#équipe)

---

## Stack technique

| Composant | Technologie |
|---|---|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Vue 3, Inertia.js, Tailwind CSS v4 |
| Base de données | MySQL |
| Temps réel | Laravel Reverb (WebSocket auto-hébergé) |
| Drag & drop | vuedraggable |
| Tests | PHPUnit (48 tests Feature) |

---

## Prérequis

- PHP 8.2 ou supérieur
- Composer 2+
- Node.js 18+ et npm
- MySQL (ou MariaDB)
- Un environnement local type XAMPP/Laragon/Herd, ou PHP + MySQL installés séparément

---

## Installation

### 1. Cloner le repo

```bash
git clone https://github.com/Bara-Thiam/mini-trello.git
cd mini-trello
```

### 2. Installer les dépendances

```bash
composer install
npm install
```

### 3. Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Ouvrez `.env` et configurez la connexion à la base de données :

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_trello
DB_USERNAME=root
DB_PASSWORD=
```

Créez la base `mini_trello` (via phpMyAdmin ou en ligne de commande) si elle n'existe pas encore.

### 4. Configurer Reverb (obligatoire — notifications temps réel)

L'application utilise Laravel Reverb pour les notifications en direct (assignation de tâche, invitations à un projet). Sans cette configuration, les notifications ne s'affichent qu'après un rechargement manuel de la page.

```bash
php artisan install:broadcasting
```

Répondez **Reverb** à la question posée par la commande. Elle configure automatiquement les variables suivantes dans `.env` :

```
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

Si ces valeurs sont déjà présentes dans votre `.env` (repo cloné après une première installation), passez cette étape.

### 5. Migrations et données de démonstration

```bash
php artisan migrate:fresh --seed
```

Cette commande crée toutes les tables et les peuple avec :
- 5 utilisateurs (1 admin, 1 chef de projet, 3 membres)
- 2 projets
- 10 tâches réparties sur les 2 projets
- Les colonnes ToDo / Doing / Done pour chaque projet

---

## Lancer l'application

Trois processus doivent tourner simultanément, dans trois terminaux séparés :

```bash
php artisan serve
```

```bash
npm run dev
```

```bash
php artisan reverb:start
```

Rendez-vous ensuite sur **http://127.0.0.1:8000**.

---

## Comptes de démonstration

Tous les comptes utilisent le mot de passe : **`password`**

| Email | Rôle | Nom |
|---|---|---|
| bara@gmail.com | Chef de projet | Sereigne Bara Thiam |
| joanelle@gmail.com | Membre | Joanelle Aholou-Kotiko |
| mactar@gmail.com | Membre | Mactar Camara |
| fatou@gmail.com | Membre | Fatou Sarr |
| abdoulaye@gmail.com | Admin | Abdoulaye |

---

## Lancer les tests

```bash
php artisan test
```

48 tests couvrent : authentification, policies de permissions par rôle, CRUD projets/tâches, calculs du dashboard, notifications, système d'invitation, cas limites des règles métier, et scénarios de sécurité (tentatives d'accès non autorisé, élévation de privilège, permissions croisées entre projets).

---

## Fonctionnalités

### MVP
- CRUD complet des projets (créer, lister, modifier, supprimer selon le rôle)
- CRUD complet des tâches (titre, description, statut, priorité, échéance, assignation)
- Tableau Kanban à 3 colonnes (ToDo / Doing / Done)
- Authentification (inscription, connexion, déconnexion)

### Bonus implémentés
- **Drag & drop** entre colonnes, avec mise à jour instantanée en base
- **Filtrage** des tâches par membre et par priorité
- **Notifications en temps réel** (Laravel Reverb) à l'assignation d'une tâche
- **Statistiques** : dashboard avec progression par projet, répartition des tâches, tâches par membre
- **Système d'invitation** : un chef de projet ou admin peut inviter un utilisateur existant par email ; l'invité reçoit une notification avec Accepter/Refuser, et l'inviteur est notifié de la réponse

---

## Rôles et permissions

| Action | Membre | Chef de projet | Admin |
|---|:---:|:---:|:---:|
| Voir un projet dont il est membre | ✅ | ✅ | ✅ |
| Voir tous les projets | ❌ | ❌ | ✅ |
| Créer un projet | ❌ | ✅ | ✅ |
| Supprimer un projet | ❌ | ❌ | ✅ |
| Inviter un membre à un projet | ❌ | ✅ | ✅ |
| Créer une tâche (dans un projet dont il est membre) | ✅ | ✅ | ✅ |
| Modifier/assigner n'importe quelle tâche du projet | ❌ (seulement les siennes) | ✅ | ✅ |
| Supprimer une tâche | ❌ | ✅ | ✅ |

La visibilité des projets repose sur la table pivot `projet_user` : un utilisateur ne voit que les projets auxquels il a été explicitement ajouté (par création ou par invitation acceptée), à l'exception de l'admin qui voit tout.

---

## Structure du projet

```
app/
├── Http/
│   ├── Controllers/       # ProjetController, TacheController, AuthController,
│   │                        DashboardController, NotificationController, InvitationController
│   └── Resources/          # ProjetResource, TacheResource (conversion camelCase)
├── Models/                 # User, Projet, Tache, Colonne
├── Policies/                # ProjetPolicy, TachePolicy
├── Notifications/           # TacheAssigneeNotification, ProjetInvitationNotification,
│                              InvitationRepondueNotification
└── Events/

database/
├── migrations/
├── seeders/
└── factories/

resources/js/
├── Pages/                  # Auth, Projects, Tasks, Dashboard, Notifications
├── Components/Kanban/       # TaskCard, KanbanColumn, TaskForm
└── Layouts/AppLayout.vue

tests/Feature/               # 48 tests couvrant l'ensemble de la logique métier
```

---

## Équipe

| | Membre | Responsabilités |
|:--:|---|---|
| 👨‍💻 | **Sereigne Bara Thiam** | Chef de projet & Backend — migrations, authentification, policies de permissions, API CRUD, calculs statistiques, notifications temps réel (Reverb), système d'invitation, tests automatisés |
| 👩‍💻 | **Yayra Joanelle Aholou-Kotiko** | Frontend Kanban — tableau Kanban, drag & drop, formulaire de tâche, composants réutilisables (TaskCard, KanbanColumn) |
| 👨‍💻 | **Mouhamadou Mactar Camara** | Frontend Projets & Dashboard — pages d'authentification, liste des projets, vue "mes tâches", tableau de bord statistique |

<div align="center">

**ESITEC — Groupe Sup de Co Dakar · L2 Génie Informatique · 2025-2026**

![Made with Laravel](https://img.shields.io/badge/Made%20with-Laravel-FF2D20?style=flat-square&logo=laravel)
![Made in Senegal](https://img.shields.io/badge/Made%20in-S%C3%A9n%C3%A9gal%20%F0%9F%87%B8%F0%9F%87%B3-00853F?style=flat-square)

</div>