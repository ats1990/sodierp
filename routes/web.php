<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\CoordenacaoController;
use App\Http\Controllers\PsicologoController;
use App\Http\Controllers\AdministracaoController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\FormacaoController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. ROTAS DE ACESSO GERAL (Sem autenticação)
// =========================================================================

// 🔹 Tela inicial (GET)
Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');

// 🔹 Login (POST)
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');


// 🟢 ROTAS GET DE VISUALIZAÇÃO DE CADASTRO (Criar/Novo)
Route::get('/alunos/novo', [AlunoController::class, 'create'])->name('aluno.create');
Route::get('/professores/novo', [ProfessorController::class, 'create'])->name('professor.create');
Route::get('/coordenacao/novo', [CoordenacaoController::class, 'create'])->name('coordenacao.create');
Route::get('/psicologos/novo', [PsicologoController::class, 'create'])->name('psicologo.create');
Route::get('/administracao/novo', [AdministracaoController::class, 'create'])->name('administracao.create');
Route::get('/usuarios/novo', [UsuarioController::class, 'create'])->name('usuarios.create');


// 🟢 ROTAS POST DE SALVAMENTO DE CADASTRO (Store)
Route::post('/aluno', [AlunoController::class, 'store'])->name('aluno.store');
Route::post('/professor', [ProfessorController::class, 'store'])->name('professor.store');
Route::post('/coordenacao', [CoordenacaoController::class, 'store'])->name('coordenacao.store');
Route::post('/psicologo', [PsicologoController::class, 'store'])->name('psicologo.store');
Route::post('/administracao', [AdministracaoController::class, 'store'])->name('administracao.store');
Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');


// =========================================================================
// 2. ROTAS PROTEGIDAS (Apenas para usuários autenticados e ativos)
// =========================================================================
Route::middleware(['auth', 'check.status'])->group(function () {

    // 🔹 Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // 🔹 Painéis (Dashboards)
    Route::get('/painel/coordenacao', [CoordenacaoController::class, 'dashboard'])
        ->middleware('role:coordenacao')
        ->name('painel.coordenacao');
    // ... (o restante das rotas de painel) ...
    Route::get('/painel/administracao', [AdministracaoController::class, 'dashboard'])
        ->middleware('role:administracao')
        ->name('painel.administracao');
    Route::get('/painel/professor', [ProfessorController::class, 'dashboard'])
        ->middleware('role:professor')
        ->name('painel.professor');
    Route::get('/painel/psicologo', [PsicologoController::class, 'dashboard'])
        ->middleware('role:psicologo')
        ->name('painel.psicologo');


    // 🔹 Perfil do usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // 🔹 Listagem e Gerenciamento (CRUDs internos)
    Route::get('/alunos', [AlunoController::class, 'index'])->name('aluno.index');

    // ... (o restante das rotas de gerenciamento de usuários, programas, e formação) ...
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
    // Rota para EXIBIR o formulário de edição (GET)
    Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])
        ->middleware('role:coordenacao')
        ->name('usuarios.edit');
    // Rota para ATUALIZAR os dados (PATCH/PUT)
    Route::patch('/usuarios/{usuario}', [UsuarioController::class, 'update'])
        ->middleware('role:coordenacao')
        ->name('usuarios.update');

    // 🔹 Programas (CRUD completo)
    Route::resource('programas', ProgramaController::class);

    // 🚨 ROTAS DO MENU FORMAÇÃO 🚨
    Route::prefix('formacao')
        ->name('formacao.')
        ->middleware('role:coordenacao')
        ->group(function () {
            Route::get('/turmas', [FormacaoController::class, 'indexTurmas'])->name('turmas.index');
            Route::post('/turmas', [FormacaoController::class, 'storeTurmas'])->name('turmas.store');
            Route::delete('/turmas/{turma}', [FormacaoController::class, 'destroyTurma'])->name('turmas.destroy');
            // LINHA CORRIGIDA: Usa o caminho e nome relativos ao grupo 'formacao'
            Route::post('/turmas/apagar-tudo', [FormacaoController::class, 'destroyAllTurmas'])->name('turmas.destroy.all');
            // ... (o restante das rotas de formação) ...
            Route::get('/notas', [FormacaoController::class, 'indexNotas'])->name('notas.index');
            Route::get('/boletim', [FormacaoController::class, 'indexBoletim'])->name('boletim.index');
            Route::get('/certificado', [FormacaoController::class, 'indexCertificado'])->name('certificado.index');
            Route::get('/importar-dados', [FormacaoController::class, 'indexImportar'])->name('importar.index');
        });
});
