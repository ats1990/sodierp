<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Aluno;

class AdministracaoController extends Controller
{
    /**
     * Aplica middleware para autenticação e status ativo.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'check.status']); // usuário ativo
        $this->middleware('role:administracao');    // apenas perfil administração
    }

    /**
     * Exibe o painel principal da administração
     */
    public function dashboard()
    {
        $totalAlunos = Aluno::count();
        $totalUsuarios = Usuario::count();
        $usuariosPendentes = Usuario::where('status', 'inativo')->count();

        return view('painel.administracao', compact('totalAlunos', 'totalUsuarios', 'usuariosPendentes'));
    }

    /**
     * Formulário para criar um novo usuário/colaborador
     */
    public function create()
    {
        return view('administracao.create'); // view de cadastro
    }

    /**
     * Salva um novo usuário/colaborador no banco
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomeCompleto' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'tipo' => 'required|in:professor,psicologo,colaborador',
            'password' => 'required|string|min:6|confirmed',
        ]);

        Usuario::create([
            'nomeCompleto' => $request->nomeCompleto,
            'email' => $request->email,
            'tipo' => $request->tipo,
            'status' => 'inativo', // todo usuário novo começa inativo
            'password' => $request->password,
        ]);

        return redirect()->route('painel.administracao')->with('success', 'Usuário cadastrado com sucesso! Aguarde ativação.');
    }
}
