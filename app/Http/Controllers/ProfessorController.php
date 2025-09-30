<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aluno;

class ProfessorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check.status']);
        $this->middleware('role:professor');
    }

    public function dashboard()
    {
        $alunos = Aluno::where('professor_id', auth()->id())->get();
        return view('painel.professor', compact('alunos'));
    }

    public function create()
    {
        return view('professor.create');
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
            'tipo' => 'professor',
            'status' => 'inativo', // precisa ativação pela coordenação
            'password' => $request->password,
        ]);

        return redirect()->route('painel.professor')->with('success', 'Professor cadastrado com sucesso!');
    }
}