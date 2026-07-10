<?php

use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/projects');
});


Route::get('/projects/{id}', function ($id) {
    return Inertia::render('Projects/Kanban', [
        'projetId' => (int) $id,
    ]);
});