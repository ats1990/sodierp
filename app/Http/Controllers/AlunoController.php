<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use Illuminate\Http\Request;

class AlunoController extends Controller
{
    public function create()
    {
        return view('alunos.create'); // coloque o formulário aqui
    }

    public function store(Request $request)
    {
        $data = $request->all();

        // converter checkbox de benefícios para JSON
        if(isset($data['beneficios'])){
            $data['beneficios'] = json_encode($data['beneficios']);
        }

        // salvar aluno
        Aluno::create($data);

        return redirect()->back()->with('success', 'Aluno cadastrado com sucesso!');
    }
}
