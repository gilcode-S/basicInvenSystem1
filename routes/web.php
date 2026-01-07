<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/locations', function () {
    return Inertia::render('Locations/Index');
})->middleware(['auth', 'verified'])->name('locations');

Route::get('/items', function () {
    return Inertia::render('Items/Index');
})->middleware(['auth', 'verified'])->name('items');

Route::get('/transfers', function () {
    return Inertia::render('Transfers/Index');
})->middleware(['auth', 'verified'])->name('transfers');

Route::get('/reports', function () {
    return Inertia::render('Reports/Index');
})->middleware(['auth', 'verified'])->name('reports');