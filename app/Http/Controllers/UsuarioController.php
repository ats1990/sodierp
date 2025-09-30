<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
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
            'status' => 'inativo', // <-- muda para inativo por padrão
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
     * Método para ativar um usuário (apenas coordenação pode usar)
     */
    public function ativar(Usuario $usuario)
    {
        // Aqui você pode checar se o usuário logado é coordenação
        if (auth()->user()->tipo !== 'coordenacao') {
            abort(403, 'Ação não autorizada.');
        }

        $usuario->status = 'ativo';
        $usuario->save();

        return redirect()->back()->with('success', 'Usuário ativado com sucesso!');
    }
}
