<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\CoordenacaoController;
use App\Http\Controllers\PsicologoController;
use App\Http\Controllers\AdministracaoController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🔹 Tela inicial = login
Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');

// 🔹 Login e Logout
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// 🔹 Painéis (usuários autenticados e ativos)
Route::middleware(['auth', 'check.status'])->group(function () {

    // Coordenação
    Route::get('/painel/coordenacao', [CoordenacaoController::class, 'dashboard'])
        ->middleware('role:coordenacao')
        ->name('painel.coordenacao');

    // Administração
    Route::get('/painel/administracao', [AdministracaoController::class, 'dashboard'])
        ->middleware('role:administracao')
        ->name('painel.administracao');

    // Professor
    Route::get('/painel/professor', [ProfessorController::class, 'dashboard'])
        ->middleware('role:professor')
        ->name('painel.professor');

    // Psicólogo
    Route::get('/painel/psicologo', [PsicologoController::class, 'dashboard'])
        ->middleware('role:psicologo')
        ->name('painel.psicologo');

    // Perfil do usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 🔹 Cadastros (views de criação) - padronizado singular/plural igual ao seu original
    Route::get('/alunos/novo', [AlunoController::class, 'create'])->name('aluno.create');
    Route::get('/professores/novo', [ProfessorController::class, 'create'])->name('professor.create');
    Route::get('/coordenacao/novo', [CoordenacaoController::class, 'create'])->name('coordenacao.create');
    Route::get('/psicologos/novo', [PsicologoController::class, 'create'])->name('psicologo.create');
    Route::get('/administracao/novo', [AdministracaoController::class, 'create'])->name('administracao.create');
    Route::get('/usuarios/novo', [UsuarioController::class, 'create'])->name('usuarios.create');

    // 🔹 Salvamento (POST)
    Route::post('/aluno', [AlunoController::class, 'store'])->name('aluno.store');
    Route::post('/professor', [ProfessorController::class, 'store'])->name('professor.store');
    Route::post('/coordenacao', [CoordenacaoController::class, 'store'])->name('coordenacao.store');
    Route::post('/psicologo', [PsicologoController::class, 'store'])->name('psicologo.store');
    Route::post('/administracao', [AdministracaoController::class, 'store'])->name('administracao.store');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');

    // 🔹 Listagem
    Route::get('/alunos', [AlunoController::class, 'index'])->name('aluno.index');
    
    // NOVO: Listagem e Gerenciamento de Usuários
    Route::get('/usuarios', [UsuarioController::class, 'index'])
        ->middleware('role:coordenacao')
        ->name('usuarios.index');

    // Rota para ATIVAR Usuário
    Route::patch('/usuarios/{usuario}/ativar', [UsuarioController::class, 'ativar'])
        ->middleware('role:coordenacao')
        ->name('usuarios.ativar');

    // Rota para DESATIVAR Usuário
    Route::patch('/usuarios/{usuario}/desativar', [UsuarioController::class, 'desativar'])
        ->middleware('role:coordenacao')
        ->name('usuarios.desativar');

    // 🔹 Programas (CRUD completo)
    Route::resource('programas', ProgramaController::class);
});