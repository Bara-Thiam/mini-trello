<?php

use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
});

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
});

Route::get('/projects', function () {
    return Inertia::render('Projects/Index');
});

Route::get('/tasks/mine', function () {
    return Inertia::render('Tasks/MyTasks');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard/Index');
});