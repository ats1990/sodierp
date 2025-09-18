<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Familiar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlunoController extends Controller
{
    public function create()
    {
        return view('alunos.create');
    }

    public function store(Request $request)
    {
        // Usar uma transação é uma boa prática para garantir a consistência dos dados.
        // Ou tudo é salvo, ou nada é.
        DB::beginTransaction();

        try {
            // 1️⃣ Separar os dados do aluno dos familiares
            $alunoData = $request->except(['familiares_json', '_token']);

            // 2️⃣ Lista de campos booleanos (com base nos seus radios 'sim'/'nao')
            $booleanFields = [
                'carteiraTrabalho', 'jaTrabalhou', 'ctpsAssinada', 'concluido', 'beneficio',
                'convenio', 'vacinacao', 'queixa_saude', 'alergia', 'tratamento',
                'uso_remedio', 'cirurgia', 'pcd', 'doenca_congenita', 'psicologo',
                'convulsao', 'familia_doenca', 'familia_depressao', 'medico_especialista',
                'familia_psicologico', 'familia_alcool', 'familia_drogas'
            ];

            // 3️⃣ Converter radios 'sim'/'nao' em 1 ou 0
            foreach ($booleanFields as $field) {
                if ($request->has($field)) {
                    $alunoData[$field] = $request->input($field) === 'sim' ? 1 : 0;
                } else {
                    $alunoData[$field] = 0;
                }
            }
            
            // Tratamento especial para o checkbox de consentimento
            $alunoData['declaracao_consentimento'] = $request->has('declaracao_consentimento') ? 1 : 0;

            // 4️⃣ Criar o aluno
            $aluno = Aluno::create($alunoData);

            // 5️⃣ Criar os familiares
            $familiaresJson = $request->input('familiares_json');
            if ($familiaresJson) {
                $familiares = json_decode($familiaresJson, true);

                foreach ($familiares as $dadosFamiliar) {
                    if (empty($dadosFamiliar['nomeCompleto']) && empty($dadosFamiliar['parentesco'])) {
                        continue;
                    }

                    $dadosFamiliar['idade'] = !empty($dadosFamiliar['idade']) ? $dadosFamiliar['idade'] : null;
                    $dadosFamiliar['salarioBase'] = !empty($dadosFamiliar['salarioBase']) ? $dadosFamiliar['salarioBase'] : null;

                    $aluno->familiares()->create($dadosFamiliar);
                }
            }
            
            // Se tudo ocorreu bem, confirma a transação
            DB::commit();

            return redirect()->back()->with('success', 'Aluno e familiares cadastrados com sucesso!');

        } catch (\Exception $e) {
            // Se ocorreu qualquer erro, desfaz a transação
            DB::rollBack();
            // Retorna com a mensagem de erro detalhada para depuração
            return redirect()->back()->withInput()->with('error', 'Falha ao cadastrar: ' . $e->getMessage());
        }
    }
}