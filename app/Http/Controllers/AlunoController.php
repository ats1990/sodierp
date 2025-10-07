<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlunoRequest;
use App\Models\Aluno;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlunoController extends Controller
{
    /**
     * Exibe o formulário de criação de aluno.
     */
    public function create()
    {
        return view('alunos.create');
    }

    /**
     * Armazena um novo aluno e seus familiares no banco de dados.
     *
     * @param  \App\Http\Requests\StoreAlunoRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreAlunoRequest $request)
    {
        DB::beginTransaction();

        try {
            // Dados validados
            $data = $request->validated();

            // Lista de campos booleanos
            $booleanFields = [
                'carteiraTrabalho',
                'jaTrabalhou',
                'ctpsAssinada',
                'concluido',
                'beneficio',
                'convenio',
                'vacinacao',
                'queixa_saude',
                'alergia',
                'tratamento',
                'uso_remedio',
                'cirurgia',
                'pcd',
                'doenca_congenita',
                'psicologo',
                'convulsao',
                'familia_doenca',
                'familia_depressao',
                'medico_especialista',
                'familia_psicologico',
                'familia_alcool',
                'familia_drogas',
                'declaracao_consentimento',
            ];

            // Normaliza os booleans (se não vierem no request, assume false)
            foreach ($booleanFields as $field) {
                $data[$field] = $request->boolean($field);
            }

            // Criação do aluno
            $aluno = Aluno::create($data);

            // Criação dos familiares, se enviados
            if ($request->has('familiares_json') && !empty($request->familiares_json)) {
                $familiares = json_decode($request->familiares_json, true);

                // Sanitiza salário e cria cada familiar
                foreach ($familiares as &$familiar) {
                    if (isset($familiar['salarioBase'])) {
                        $familiar['salarioBase'] = str_replace(['.', ','], ['', '.'], $familiar['salarioBase']);
                    }
                }

                $aluno->familiares()->createMany($familiares);
            }

            DB::commit();

           return redirect()->route('login') // <-- LINHA CORRIGIDA: Usa a rota de login existente.
    ->with('success', 'Aluno e familiares cadastrados com sucesso! Faça seu login.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao cadastrar aluno: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocorreu um erro ao cadastrar o aluno. Por favor, tente novamente.');
        }
    }
}
