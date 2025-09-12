<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\TurmaController;
use App\Http\Controllers\AprendizController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\CertificadoController;
use App\Http\Controllers\OcorrenciaController;
use App\Http\Controllers\AgendaPsicologicaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ConfigController;

/*
|--------------------------------------------------------------------------
| Web Routes - SODIERP
|--------------------------------------------------------------------------
|
| Todas as rotas internas exigem autenticação.
| Middleware de roles aplicado para admin e coord.
|
*/

// Página inicial → redireciona para login
Route::get('/', fn() => redirect()->route('login'));

// Rotas protegidas por autenticação
Route::middleware(['auth'])->group(function () {

    // Dashboard genérico
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Perfil do usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 🔹 Programas (resource)
    Route::resource('programas', ProgramaController::class);

    // 🔹 Turmas
    Route::prefix('turmas')->name('turmas.')->group(function () {
        Route::get('/', [TurmaController::class, 'index'])->name('index');
        Route::get('/{id}', [TurmaController::class, 'show'])->name('show');
    });

    // 🔹 Aprendizes
    Route::prefix('aprendizes')->name('aprendizes.')->group(function () {
        Route::get('/', [AprendizController::class, 'index'])->name('index');
        Route::get('/{id}', [AprendizController::class, 'show'])->name('show');
    });

    // 🔹 Avaliações (coord)
    Route::middleware(['role:coord'])->prefix('avaliacoes')->name('avaliacoes.')->group(function () {
        Route::get('/', [AvaliacaoController::class, 'index'])->name('index');
        Route::get('/{id}', [AvaliacaoController::class, 'show'])->name('show');
        Route::get('/create', [AvaliacaoController::class, 'create'])->name('create');
        Route::post('/', [AvaliacaoController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AvaliacaoController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AvaliacaoController::class, 'update'])->name('update');
        Route::delete('/{id}', [AvaliacaoController::class, 'destroy'])->name('destroy');
    });

    // 🔹 Certificados (admin + coord)
    Route::middleware(['role:admin|coord'])->prefix('certificados')->name('certificados.')->group(function () {
        Route::get('/', [CertificadoController::class, 'index'])->name('index');
        Route::get('/{id}', [CertificadoController::class, 'show'])->name('show');
        Route::get('/create', [CertificadoController::class, 'create'])->name('create');
        Route::post('/', [CertificadoController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CertificadoController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CertificadoController::class, 'update'])->name('update');
        Route::delete('/{id}', [CertificadoController::class, 'destroy'])->name('destroy');
    });

    // 🔹 Ocorrências (coord)
    Route::middleware(['role:coord'])->prefix('ocorrencias')->name('ocorrencias.')->group(function () {
        Route::get('/', [OcorrenciaController::class, 'index'])->name('index');
        Route::get('/{id}', [OcorrenciaController::class, 'show'])->name('show');
        Route::get('/create', [OcorrenciaController::class, 'create'])->name('create');
        Route::post('/', [OcorrenciaController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [OcorrenciaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [OcorrenciaController::class, 'update'])->name('update');
        Route::delete('/{id}', [OcorrenciaController::class, 'destroy'])->name('destroy');
    });

    // 🔹 Agenda Psicológica (coord)
    Route::middleware(['role:coord'])->prefix('agenda-psicologica')->name('agenda-psicologica.')->group(function () {
        Route::get('/', [AgendaPsicologicaController::class, 'index'])->name('index');
        Route::get('/{id}', [AgendaPsicologicaController::class, 'show'])->name('show');
        Route::get('/create', [AgendaPsicologicaController::class, 'create'])->name('create');
        Route::post('/', [AgendaPsicologicaController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AgendaPsicologicaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AgendaPsicologicaController::class, 'update'])->name('update');
        Route::delete('/{id}', [AgendaPsicologicaController::class, 'destroy'])->name('destroy');
    });

    // 🔹 Administração (admin)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios');
        Route::get('/config', [ConfigController::class, 'index'])->name('config');
    });

});

// 🔹 Autenticação (login, registro, senha, logout)
require __DIR__.'/auth.php';
