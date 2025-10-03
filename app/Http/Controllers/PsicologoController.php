<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario; // ⚠️ IMPORTANTE: precisa importar Usuario

class PsicologoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check.status']);
        $this->middleware('role:psicologo');
    }

    // Painel do psicólogo
    public function dashboard()
    {
        return view('painel.psicologo'); // Certifique-se que esta view exista
    }

    // Formulário de cadastro
    public function create()
    {
        return view('psicologo.create'); // Certifique-se que esta view exista
    }

    // Salvar novo psicólogo
    public function store(Request $request)
    {
        $request->validate([
            'nomeCompleto' => 'required|string|max:255',
            'nomeSocial' => 'nullable|string|max:255',
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
            'status' => 'inativo', // precisa ativação pela coordenação
            'password' => $request->password,
        ]);

        return redirect()->route('painel.psicologo')
            ->with('success', 'Psicólogo cadastrado com sucesso! Aguarde ativação.');
    }
}
