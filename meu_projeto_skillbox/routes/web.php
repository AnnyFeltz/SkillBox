<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CanvasProjetoController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsNotAdmin;

// Página pública
Route::get('/', function () {
    return view('home');
});

// Rotas para todos os usuários autenticados
Route::middleware(['auth'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/editor', [CanvasProjetoController::class, 'editor'])->name('editor');

    Route::get('/canvas', [CanvasProjetoController::class, 'index'])->name('canvas.index');
    Route::post('/canvas/salvar', [CanvasProjetoController::class, 'salvar'])->name('canvas.salvar');
    Route::get('/canvas/carregar', [CanvasProjetoController::class, 'carregar'])->name('canvas.carregar');
    Route::delete('/canvas/{id}', [CanvasProjetoController::class, 'destroy'])->name('canvas.destroy');
});

// Rotas para usuários comuns
Route::middleware(['auth', 'verified', IsNotAdmin::class])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Rotas para admin
Route::middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

require __DIR__ . '/auth.php';
