<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\CoordenacaoController;
use App\Http\Controllers\PsicologoController;
use App\Http\Controllers\AdministracaoController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🔹 Tela inicial = login
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 🔹 Painéis administrativos (usuários ativos)
Route::middleware(['auth', 'check.status'])->group(function () {

    // Coordenação
    Route::middleware('role:coordenacao')->group(function () {
        Route::get('/painel/coordenacao', [CoordenacaoController::class, 'dashboard'])
            ->name('painel.coordenacao');
    });

    // Administração
    Route::middleware('role:administracao')->group(function () {
        Route::get('/painel/administracao', [AdministracaoController::class, 'dashboard'])
            ->name('painel.administracao');
    });

    // Colaboradores gerais (professor, psicologo)
    Route::middleware('role:professor,psicologo')->group(function () {
        Route::get('/painel/colaborador', [UsuarioController::class, 'dashboard'])
            ->name('painel.colaborador');
    });

    // Rotas de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 🔹 Rotas de cadastro
Route::get('/alunos/novo', [AlunoController::class, 'create'])->name('aluno.create');
Route::get('/professores/novo', [ProfessorController::class, 'create'])->name('professor.create');
Route::get('/coordenacao/novo', [CoordenacaoController::class, 'create'])->name('coordenacao.create');
Route::get('/psicologos/novo', [PsicologoController::class, 'create'])->name('psicologo.create');
Route::get('/administracao/novo', [AdministracaoController::class, 'create'])->name('administracao.create');
Route::get('/usuarios/novo', [UsuarioController::class, 'create'])->name('usuarios.create');

// 🔹 Rotas POST para salvar dados
Route::post('/aluno', [AlunoController::class, 'store'])->name('aluno.store');
Route::post('/professor', [ProfessorController::class, 'store'])->name('professor.store');
Route::post('/coordenacao', [CoordenacaoController::class, 'store'])->name('coordenacao.store');
Route::post('/psicologo', [PsicologoController::class, 'store'])->name('psicologo.store');
Route::post('/administracao', [AdministracaoController::class, 'store'])->name('administracao.store');
Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');

// 🔹 Listagem
Route::get('/alunos', [AlunoController::class, 'index'])->name('aluno.index');

// 🔹 Programas (CRUD completo)
Route::resource('programas', ProgramaController::class);

// 🔹 Importa rotas extras do Breeze/Fortify (se estiver usando)
require __DIR__.'/auth.php';
