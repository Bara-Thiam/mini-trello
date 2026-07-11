<?php

use Inertia\Inertia;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\TacheController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return redirect('/projects');
});

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth')->group(function () {
    Route::get('/projects', [ProjetController::class, 'index']);
    Route::post('/projects', [ProjetController::class, 'store']);
    Route::get('/projects/{projet}', [ProjetController::class, 'show']);
    Route::put('/projects/{projet}', [ProjetController::class, 'update']);
    Route::delete('/projects/{projet}', [ProjetController::class, 'destroy']);

    Route::post('/projects/{projet}/taches', [TacheController::class, 'store']);
    Route::put('/taches/{tache}', [TacheController::class, 'update']);
    Route::delete('/taches/{tache}', [TacheController::class, 'destroy']);
    Route::patch('/taches/{tache}/statut', [TacheController::class, 'updateStatus']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/tasks/mine', [TacheController::class, 'mine']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/lue', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/tout-lu', [NotificationController::class, 'markAllAsRead']);
});