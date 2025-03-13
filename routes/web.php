<?php

use App\Http\Controllers\UsuarioSetor\UsuarioSetorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::group(['prefix' => 'usuario-setor'], function () {
    Route::get('/', [UsuarioSetorController::class, 'index'])->name('usuario_setor.index');
    Route::get('/list', [UsuarioSetorController::class, 'list'])->name('usuario_setor.list');
    Route::get('/{id}', [UsuarioSetorController::class, 'edit'])->name('usuario_setor.edit');
    Route::post('/', [UsuarioSetorController::class, 'create'])->name('usuario_setor.create');
    Route::post('/{id}', [UsuarioSetorController::class, 'update'])->name('usuario_setor.update');
    Route::delete('/{id}', [UsuarioSetorController::class, 'delete'])->name('usuario_setor.delete');
});