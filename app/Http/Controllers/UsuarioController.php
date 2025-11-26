<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Necessário para a validação unique()->ignore()
use Illuminate\Support\Facades\Hash; // Necessário para criptografar a senha

class UsuarioController extends Controller
{
    /**
     * Exibe a lista de usuários para gerenciamento.
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $usuarios = Usuario::orderBy('nomeCompleto')->get(); 
        return view('painel.usuarios.index', compact('usuarios')); 
    }
    
    // 🔹 Métodos de Cadastro (Rotas de Acesso Geral)
    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
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
            // 🚨 CORREÇÃO DE SEGURANÇA: Senha deve ser criptografada!
            'password' => Hash::make($request->password), 
            // Campos de programa e disciplinas (manutenção do seu código)
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


    // ==========================================================
    // 🆕 MÉTODOS DE EDIÇÃO E ATUALIZAÇÃO (IMPLEMENTADOS)
    // ==========================================================

    /**
     * Exibe o formulário para editar um usuário existente.
     * @param \App\Models\Usuario $usuario
     * @return \Illuminate\View\View
     */
    public function edit(Usuario $usuario)
    {
        // Garante que apenas coordenadores possam acessar
        if (!auth()->user()->isCoordenacao()) {
            abort(403, 'Ação não autorizada.');
        }
        // A view deve estar em resources/views/painel/usuarios/edit.blade.php
        return view('painel.usuarios.edit', compact('usuario'));
    }

    /**
     * Atualiza um usuário existente no banco de dados.
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Usuario $usuario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Usuario $usuario)
    {
        if (!auth()->user()->isCoordenacao()) {
             abort(403, 'Ação não autorizada.');
        }

        // 1. Validação dos dados
        $validatedData = $request->validate([
            'nomeCompleto' => 'required|string|max:255',
            // Validações com exceção do próprio usuário ($usuario->id)
            'email' => ['required', 'email', Rule::unique('usuarios', 'email')->ignore($usuario->id)],
            'cpf' => ['nullable', 'string', Rule::unique('usuarios', 'cpf')->ignore($usuario->id)],
            'password' => 'nullable|string|min:6|confirmed', 
            'tipo' => ['required', Rule::in(['professor', 'coordenacao', 'administracao', 'psicologo'])],
        ]);

        // 2. Preparação dos dados
        $usuarioData = [
            'nomeCompleto' => $validatedData['nomeCompleto'],
            'email' => $validatedData['email'],
            'cpf' => $validatedData['cpf'],
            'tipo' => $validatedData['tipo'],
            // Campos de programa e disciplinas (manutenção do seu código)
            'nomeSocial' => $request->nomeSocial,
            'programa_basica' => $request->has('programa_basica'),
            'programa_aprendizagem' => $request->has('programa_aprendizagem'),
            'programa_convivencia' => $request->has('programa_convivencia'),
            'disciplinas_basica' => $request->disciplinas_basica ?: [],
            'disciplinas_aprendizagem' => $request->disciplinas_aprendizagem ?: [],
            'disciplinas_convivencia' => $request->disciplinas_convivencia ?: [],
        ];
        
        // 3. Atualiza a senha APENAS se o campo não estiver vazio, criptografando-a
        if (!empty($validatedData['password'])) {
            $usuarioData['password'] = Hash::make($validatedData['password']);
        }

        // 4. Atualização no banco de dados
        $usuario->update($usuarioData);

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuário ' . $usuario->nomeCompleto . ' atualizado com sucesso!');
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