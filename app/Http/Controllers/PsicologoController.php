<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PsicologoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check.status']);
        $this->middleware('role:psicologo');
    }

    public function dashboard()
    {
        return view('painel.psicologo');
    }

    public function create()
    {
        return view('psicologo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomeCompleto' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'cpf' => 'nullable|string|unique:usuarios,cpf',
            'password' => 'required|string|min:6|confirmed',
        ]);

        Usuario::create([
            'nomeCompleto' => $request->nomeCompleto,
            'nomeSocial' => $request->nomeSocial,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'tipo' => 'psicologo',
            'status' => 'inativo', // precisa ativação
            'password' => $request->password,
        ]);

        return redirect()->route('painel.psicologo')->with('success', 'Psicólogo cadastrado com sucesso!');
    }
}
