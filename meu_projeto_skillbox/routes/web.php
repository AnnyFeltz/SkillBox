<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CanvasProjetoController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ToolController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsNotAdmin;

Route::get('/', function () {
    return view('home');
});

Route::middleware(['auth'])->group(function () {
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Canvas
    Route::get('/canvas', [CanvasProjetoController::class, 'index'])->name('canvas.index');
    Route::post('/canvas/salvar', [CanvasProjetoController::class, 'salvar'])->name('canvas.salvar');
    Route::get('/canvas/carregar', [CanvasProjetoController::class, 'carregar'])->name('canvas.carregar');
    Route::delete('/canvas/{id}', [CanvasProjetoController::class, 'destroy'])->name('canvas.destroy');
    Route::get('/editor', [CanvasProjetoController::class, 'editor'])->name('editor');

    // Tarefas associadas a Canvas
    Route::prefix('canvas/{canvas}/tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    });

    // Ferramentas (ToolController padrão REST exceto show, create, edit)
    Route::resource('tools', ToolController::class)->except(['show', 'create', 'edit']);

    // Adicionar e remover ferramenta em canvas
    Route::post('/canvas/{canvas}/tools', [CanvasProjetoController::class, 'adicionarTool'])->name('canvas.tools.adicionar');
    Route::delete('/canvas/{canvas}/tools/{tool}', [CanvasProjetoController::class, 'removerTool'])->name('canvas.tools.remover');
});

Route::middleware(['auth', 'verified', IsNotAdmin::class])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

require __DIR__ . '/auth.php';
