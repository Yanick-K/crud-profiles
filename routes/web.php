<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileInertiaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard', ['isAuthenticated' => auth()->check(), 'storageUrl' => asset('storage')]);
})->name('dashboard');

Route::get('/create', [ProfileController::class, 'create'])->name('profile.create');
Route::get('/edit/{profile}', [ProfileController::class, 'edit'])->name('profile-entity.edit');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileInertiaController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileInertiaController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileInertiaController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
