<?php

use App\Models\Aluno;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\CoordenacaoController;
use App\Http\Controllers\PsicologoController;
use App\Http\Controllers\AdministracaoController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\FormacaoController;
use App\Http\Controllers\ChamadaController;
use App\Http\Controllers\FamiliarController; 
use App\Http\Controllers\TurmaController; // 💡 NOVO: Controller de Turma
use App\Models\Presenca;
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

// 🔹 Login
Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
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


    // ==========================================================
    // 🧑‍🎓 ROTAS DE GERENCIAMENTO DE ALUNOS (CRUD e Importação)
    // ==========================================================
    
    // 1. ROTAS ESTÁTICAS E ESPECÍFICAS
    Route::get('alunos/importar', [AlunoController::class, 'showImportForm'])
        ->middleware('role:coordenacao')
        ->name('aluno.import.form');
    Route::post('alunos/importar', [AlunoController::class, 'import'])
        ->middleware('role:coordenacao')
        ->name('aluno.import.store');
    Route::get('alunos/create', [AlunoController::class, 'create'])->name('aluno.create');

    // 2. ROTAS ESPECÍFICAS COM PARÂMETRO
    Route::get('alunos/{aluno}/edit', [AlunoController::class, 'edit'])->name('aluno.edit');

    // 3. ROTAS DE AÇÃO
    Route::post('alunos', [AlunoController::class, 'store'])->name('aluno.store');
    Route::put('alunos/{aluno}', [AlunoController::class, 'update'])->name('aluno.update');
    Route::delete('alunos/{aluno}', [AlunoController::class, 'destroy'])->name('aluno.destroy');

    // 4. ROTA DE VISUALIZAÇÃO GENÉRICA (DEVE SER A ÚLTIMA COM {aluno})
    Route::get('alunos/{aluno}', [AlunoController::class, 'show'])->name('aluno.show');

    // 5. ROTA DE LISTAGEM
    Route::get('alunos', [AlunoController::class, 'index'])->name('aluno.index');


    // ==========================================================
    // 👨‍👩‍👧‍👦 ROTAS DE GERENCIAMENTO DE FAMILIARES
    // ==========================================================
    Route::post('alunos/{aluno}/familiares', [FamiliarController::class, 'store'])->name('familiar.store');
    Route::delete('familiares/{familiar}', [FamiliarController::class, 'destroy'])->name('familiar.destroy');

    // ==========================================================
    // 👥 ROTAS DE GERENCIAMENTO DE USUÁRIOS
    // ==========================================================
    Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])
        ->middleware('role:coordenacao')
        ->name('usuarios.edit');
    Route::patch('/usuarios/{usuario}/ativar', [UsuarioController::class, 'ativar'])
        ->middleware('role:coordenacao')
        ->name('usuarios.ativar');
    Route::patch('/usuarios/{usuario}/desativar', [UsuarioController::class, 'desativar'])
        ->middleware('role:coordenacao')
        ->name('usuarios.desativar');
    Route::patch('/usuarios/{usuario}', [UsuarioController::class, 'update'])
        ->middleware('role:coordenacao')
        ->name('usuarios.update');
    Route::get('/usuarios', [UsuarioController::class, 'index'])
        ->middleware('role:coordenacao')
        ->name('usuarios.index');
    
    // ==========================================================
    // 🏫 ROTAS DE TURMAS (CRUD Completo) - Recurso que estava faltando!
    // ==========================================================
    // 💡 Substitui o bloco de 7 rotas com uma única linha (TurmaController).
    Route::resource('turmas', TurmaController::class);

    // 🔹 Programas (CRUD completo)
    Route::resource('programas', ProgramaController::class);

    // 🏆 GRUPO: ROTAS DE CHAMADA
    Route::controller(ChamadaController::class)->group(function () {
        Route::get('/chamada/pdf/form', 'showPdfForm')
            ->name('chamada.pdf.form')
            ->can('access', Presenca::class);
        Route::post('/chamada/pdf/generate', 'generatePdf')
            ->name('chamada.pdf.generate')
            ->can('access', Presenca::class);

        Route::get('/chamada', 'index')
            ->name('chamada.index')
            ->can('access', Presenca::class);

        Route::get('/chamada/{turma}/{mes_ano}', 'show')
            ->name('chamada.show')
            ->can('access', Presenca::class);
        Route::post('/chamada/{turma}/{mes_ano}', 'store')
            ->name('chamada.store')
            ->can('alter', Presenca::class);
    });

    // 🚨 ROTAS DO MENU FORMAÇÃO (RESTRITO APENAS À COORDENAÇÃO) 🚨
    Route::prefix('formacao')
        ->name('formacao.')
        ->middleware('role:coordenacao')
        ->group(function () {
            // Rotas de Turmas (CRUD Básico de FormacaoController)
            Route::get('/turmas', [FormacaoController::class, 'indexTurmas'])->name('turmas.index');
            Route::post('/turmas', [FormacaoController::class, 'storeTurmas'])->name('turmas.store');
            Route::post('/turmas/bulk', [FormacaoController::class, 'storeBulk'])->name('turmas.storeBulk');

            // Rotas de exclusão
            Route::delete('turmas/excluir-todas', [FormacaoController::class, 'destroyAllTurmas'])->name('turmas.destroyAll');
            Route::delete('/turmas/{turma}', [FormacaoController::class, 'destroyTurma'])->name('turmas.destroy');

            // Rotas de Atribuição
            Route::get('/turmas/atribuir/form', [FormacaoController::class, 'showAtribuicaoRapidaLogica'])->name('turmas.atribuicao_logica_form');
            Route::post('/turmas/atribuir', [FormacaoController::class, 'atribuirAlunoTurma'])->name('turmas.atribuir');
            Route::get('atribuicao', [FormacaoController::class, 'indexAtribuicaoTurmas'])->name('atribuicao.index');
            Route::put('atribuicao/salvar', [FormacaoController::class, 'bulkUpdate'])->name('atribuicao.bulkUpdate');
            Route::post('atribuicao/{aluno}', [FormacaoController::class, 'updateAtribuicaoAluno'])->name('atribuicao.update');
            Route::get('turmas/{turma}/alunos', [FormacaoController::class, 'getAlunosByTurma'])->name('turmas.alunos.ajax');

            // Outras Rotas de Formação
            Route::get('notas', [FormacaoController::class, 'indexNotas'])->name('notas.index');
            Route::get('boletim', [FormacaoController::class, 'indexBoletim'])->name('boletim.index');
            Route::get('certificado', [FormacaoController::class, 'indexCertificado'])->name('certificado.index');
            Route::get('importar-dados', [FormacaoController::class, 'indexImportar'])->name('importar.index');
        });

});