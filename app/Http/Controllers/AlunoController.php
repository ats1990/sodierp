<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use Illuminate\Http\Request;

class AlunoController extends Controller
{
    public function create()
    {
        return view('alunos.create'); // Formulário de cadastro do aluno
    }

    public function store(Request $request)
    {
        // 1️⃣ Separar os dados do aluno dos familiares
        $alunoData = $request->except('familiares_json'); 

        // 2️⃣ Lista de todos os campos booleanos definidos na migration
        $booleanFields = [
            'carteiraTrabalho', 'jaTrabalhou', 'ctpsAssinada',
            'concluido', 'beneficio', 'convenio', 'vacinacao', 'queixa_saude',
            'alergia', 'tratamento', 'uso_remedio', 'cirurgia', 'pcd',
            'doenca_congenita', 'psicologo', 'convulsao', 'familia_doenca',
            'familia_depressao', 'medico_especialista', 'familia_psicologico',
            'familia_alcool', 'familia_drogas', 'declaracao_consentimento'
        ];

        // 3️⃣ Converter checkboxes/radios em 0 ou 1
        foreach ($booleanFields as $field) {
            $alunoData[$field] = $request->has($field) ? 1 : 0;
        }

        // 4️⃣ Se tiver algum campo múltiplo (ex: benefícios em array)
        if (isset($alunoData['beneficios']) && is_array($alunoData['beneficios'])) {
            $alunoData['beneficios'] = json_encode($alunoData['beneficios']);
        }

        // 5️⃣ Criar o aluno
        $aluno = Aluno::create($alunoData);

        // 6️⃣ Criar os familiares vinculados ao aluno (se houver)
        $familiaresJson = $request->input('familiares_json');
        if ($familiaresJson) {
            $familiares = json_decode($familiaresJson, true); // transforma JSON em array
            $aluno->familiares()->createMany($familiares);    // cria todos de uma vez
        }

        return redirect()->back()->with('success', 'Aluno e familiares cadastrados com sucesso!');
    }
}
