<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    /**
     * Exibe a lista de usuários para gerenciamento (MÉTODO CHAMADO PELA ROTA)
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 1. Busca todos os usuários ordenados pelo nome completo
        $usuarios = Usuario::orderBy('nomeCompleto')->get(); 
        
        // 2. Retorna a view de listagem.
        return view('painel.usuarios.index', compact('usuarios')); 
    }
    
    // 🔹 Mantenha os métodos de Cadastro (seus originais)
    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        // ... (Seu código de validação e criação de usuário)
        $request->validate([
            'nomeCompleto' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'cpf' => 'nullable|string|unique:usuarios,cpf',
            'tipo' => 'required|in:professor,coordenacao,administracao,psicologo',
            'password' => 'required|string|min:6|confirmed',
        ]);

        Usuario::create([
            'nomeCompleto' => $request->nomeCompleto,
            'nomeSocial' => $request->nomeSocial,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'tipo' => $request->tipo,
            'status' => 'inativo', 
            'password' => $request->password,
            'programa_basica' => $request->has('programa_basica'),
            'programa_aprendizagem' => $request->has('programa_aprendizagem'),
            'programa_convivencia' => $request->has('programa_convivencia'),
            'disciplinas_basica' => $request->disciplinas_basica ?: [],
            'disciplinas_aprendizagem' => $request->disciplinas_aprendizagem ?: [],
            'disciplinas_convivencia' => $request->disciplinas_convivencia ?: [],
        ]);

        return redirect()->route('usuarios.create')
                          ->with('success', 'Usuário cadastrado com sucesso! Ele está inativo até ser ativado pela coordenação.');
    }


    /**
     * Método para ativar um usuário
     */
    public function ativar(Usuario $usuario)
    {
        if (auth()->user()->tipo !== 'coordenacao') {
            abort(403, 'Ação não autorizada.');
        }

        $usuario->status = 'ativo';
        $usuario->save();

        return redirect()->back()->with('success', 'Usuário ativado com sucesso!');
    }

    /**
     * Método para desativar um usuário
     */
    public function desativar(Usuario $usuario)
    {
        if (auth()->user()->tipo !== 'coordenacao') {
            abort(403, 'Ação não autorizada.');
        }
        
        // Impedir que o próprio coordenador se desative
        if (auth()->id() === $usuario->id) {
            return redirect()->back()->with('error', 'Você não pode desativar sua própria conta.');
        }

        $usuario->status = 'inativo';
        $usuario->save();

        return redirect()->back()->with('success', 'Usuário desativado com sucesso!');
    }
}