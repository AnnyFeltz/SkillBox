<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CanvasProjetoController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsNotAdmin;

Route::get('/', function () {
    return view('home');
})->name('welcome');

Route::middleware(['auth'])->group(function () {
    // CanvasProjeto rotas
    Route::get('/canvas', [CanvasProjetoController::class, 'index'])->name('canvas.index');
    Route::post('/canvas/salvar', [CanvasProjetoController::class, 'salvar'])->name('canvas.salvar');
    Route::get('/canvas/carregar', [CanvasProjetoController::class, 'carregar'])->name('canvas.carregar');
    Route::delete('/canvas/{id}', [CanvasProjetoController::class, 'destroy'])->name('canvas.destroy');
    Route::get('/editor', [CanvasProjetoController::class, 'editor'])->name('editor');

    Route::post('/canvas/{canvas}/tools', [CanvasProjetoController::class, 'adicionarTool'])->name('canvas.tools.adicionar');
    Route::delete('/canvas/{canvas}/tools/{tool}', [CanvasProjetoController::class, 'removerTool'])->name('canvas.tools.remover');

    Route::post('/canvas/{id}/publicar', [CanvasProjetoController::class, 'publicar'])->name('canvas.publicar');
    Route::get('/canvas/{id}/visualizar', [CanvasProjetoController::class, 'visualizar'])->name('canvas.visualizar');
    Route::get('/canvas/publicados', [CanvasProjetoController::class, 'publicados'])->name('canvas.publicados');

    // Tarefas associadas ao CanvasProjeto
    Route::prefix('canvas/{canvas}/tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
        Route::patch('{task}/toggle', [TaskController::class, 'toggleStatus'])->name('tasks.toggle');
    });

    // Ferramentas (ToolController REST exceto show)
    Route::resource('tools', ToolController::class)->except(['show']);

    // Perfil (Breeze)
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Dashboards
Route::middleware(['auth', 'verified', IsNotAdmin::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

require __DIR__ . '/auth.php';
