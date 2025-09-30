<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario; // caso queira listar ou cadastrar usuários
use App\Models\Aluno;   // se precisar acessar alunos

class CoordenacaoController extends Controller
{
    // Aplica middleware para autenticação e status ativo
    public function __construct()
    {
        $this->middleware(['auth', 'check.status']); // garante que só usuários ativos acessem
        $this->middleware('role:coordenacao');       // garante que só coordenação acesse
    }

    /**
     * Exibe o painel principal da coordenação
     */
    public function dashboard()
    {
        // Exemplo de contagem de dados para o painel
        $totalAlunos = Aluno::count();
        $totalUsuarios = Usuario::count();
        $usuariosPendentes = Usuario::where('status', 'inativo')->count();

        return view('painel.coordenacao', compact('totalAlunos', 'totalUsuarios', 'usuariosPendentes'));
    }

    /**
     * Formulário para criar um novo usuário/colaborador
     */
    public function create()
    {
        return view('coordenacao.create'); // view de cadastro
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

        return redirect()->route('painel.coordenacao')->with('success', 'Usuário cadastrado com sucesso! Aguarde ativação.');
    }
}
