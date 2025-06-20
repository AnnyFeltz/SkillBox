<?php

use App\Http\Controllers\CanvasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
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
});

// Rotas de usuários normais
Route::middleware(['auth', 'verified', IsNotAdmin::class])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rotas de admin
Route::middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // routes/web.php
    Route::get('/editor', [CanvasController::class, 'editor'])->name('canvas.editor');
    Route::resource('canvas', CanvasController::class)->except(['edit', 'create', 'show']);
    Route::get('/canvas', [CanvasController::class, 'index'])->name('canvas.index');
    Route::post('/canvas/salvar', [CanvasController::class, 'store'])->name('canvas.salvar');
    Route::get('/canvas/{id}', [CanvasController::class, 'show'])->name('canvas.show');
    Route::delete('/canvas/{id}', [CanvasController::class, 'destroy'])->name('canvas.destroy');
});

require __DIR__ . '/auth.php';
