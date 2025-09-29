<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\CoordenacaoController;
use App\Http\Controllers\PsicologoController;
use App\Http\Controllers\AdministracaoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// rota inicial nomeada -> permite usar redirect()->route('welcome')
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Rota para listar alunos
Route::get('/alunos', [AlunoController::class, 'index'])->name('aluno.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rotas GET para mostrar formulários de cadastro
Route::get('/alunos/novo', [AlunoController::class, 'create'])->name('aluno.create');
Route::get('/professores/novo', [ProfessorController::class, 'create'])->name('professor.create');
Route::get('/coordenacao/novo', [CoordenacaoController::class, 'create'])->name('coordenacao.create');
Route::get('/psicologos/novo', [PsicologoController::class, 'create'])->name('psicologo.create');
Route::get('/administracao/novo', [AdministracaoController::class, 'create'])->name('administracao.create');

// Rotas POST para salvar os dados
Route::post('/aluno', [AlunoController::class, 'store'])->name('aluno.store');
Route::post('/professor', [ProfessorController::class, 'store'])->name('professor.store');
Route::post('/coordenacao', [CoordenacaoController::class, 'store'])->name('coordenacao.store');
Route::post('/psicologo', [PsicologoController::class, 'store'])->name('psicologo.store');
Route::post('/administracao', [AdministracaoController::class, 'store'])->name('administracao.store');

require __DIR__.'/auth.php';
