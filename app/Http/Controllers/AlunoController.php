<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Familiar;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AlunoController extends Controller
{
    // ==========================================================
    // 💡 MÉTODO HELPER
    // Garante que checkboxes ausentes enviem 0, antes da validação.
    // Também converte 'on', '1', 'true' etc para 1 quando presente.
    // ==========================================================
    protected function prepareBooleanFields(Request $request): void
    {
        $booleanFields = [
            'declaracao_consentimento', 'carteira_trabalho', 'ja_trabalhou', 'ctps_assinada',
            'concluido', 'beneficio', 'convenio', 'vacinacao', 'queixa_saude', 'alergia',
            'tratamento', 'uso_remedio', 'cirurgia', 'pcd', 'doenca_congenita', 'psicologo',
            'convulsao', 'familia_doenca', 'familia_depressao', 'medico_especialista',
            'familia_psicologico', 'familia_alcool', 'familia_drogas'
        ];

        foreach ($booleanFields as $field) {
            // Se não enviado (checkbox desmarcado) -> garante 0
            if (!$request->has($field)) {
                $request->merge([$field => 0]);
            } else {
                // Se enviado, normalize para valores aceitos (0 ou 1)
                // $request->boolean() será usado depois na transformação final,
                // aqui mantemos para garantir consistência no request.
                $val = $request->input($field);
                // normalize common truthy strings
                if (in_array($val, ['on', 'true', '1', 1, true, 'sim'], true)) {
                    $request->merge([$field => 1]);
                } else {
                    // qualquer outro valor -> 0
                    $request->merge([$field => 0]);
                }
            }
        }
    }

    // ==========================================================
    // MÉTODOS CRUD PADRÃO
    // ==========================================================

    public function index(Request $request)
    {
        // 1. DADOS PARA FILTROS E VIEW
        $turmas = Turma::orderBy('ano_letivo', 'desc')->get();
        $anosLetivos = Turma::select('ano_letivo')->distinct()->pluck('ano_letivo')->sortDesc();
        $periodos = Turma::select('periodo')->distinct()->pluck('periodo')->sort();

        // 2. INÍCIO DA QUERY
        $query = Aluno::query()->with('turma');

        // 3. FILTRAGEM (agrupando ORs corretamente)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nomeCompleto', 'like', "%{$search}%")
                  ->orWhere('codigo_matricula', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        if ($request->filled('turma_id')) {
            $query->where('turma_id', $request->input('turma_id'));
        }

        if ($request->filled('ano_letivo')) {
            $query->whereHas('turma', function ($q) use ($request) {
                $q->where('ano_letivo', $request->input('ano_letivo'));
            });
        }

        if ($request->filled('periodo')) {
            $query->whereHas('turma', function ($q) use ($request) {
                $q->where('periodo', $request->input('periodo'));
            });
        }

        // 4. ORDERING
        $sortColumn = $request->get('sort', 'codigo_matricula');
        $sortDirection = $request->get('direction', 'asc');
        $safeSortColumns = ['codigo_matricula', 'nomeCompleto', 'turma_id', 'status'];

        if (in_array($sortColumn, $safeSortColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('codigo_matricula', 'asc');
        }

        // 5. EXECUÇÃO DA QUERY E PAGINAÇÃO
        $alunos = $query->paginate(20)->withQueryString();

        // 6. RETORNO DA VIEW
        return view('alunos.index', compact('alunos', 'turmas', 'anosLetivos', 'periodos'));
    }

    public function create()
    {
        $turmas = Turma::orderBy('ano_letivo', 'desc')->orderBy('letra', 'asc')->get();
        return view('alunos.create', compact('turmas'));
    }

    public function store(Request $request)
    {
        // Normaliza checkboxes ausentes
        $this->prepareBooleanFields($request);

        // VALIDAÇÃO (turma_id NÃO obrigatório)
        $validatedData = $request->validate([
            // CAMPOS PRINCIPAIS
            'turma_id' => 'nullable|exists:turmas,id',
            'nomeCompleto' => 'required|string|max:191',
            'codigo_matricula' => 'nullable|string|max:100|unique:alunos,codigo_matricula',
            'dataNascimento' => 'required|date',
            'declaracao_consentimento' => 'nullable|boolean',

            // CAMPOS PESSOAIS E DE CONTATO
            'cpf' => 'nullable|string|max:14|unique:alunos,cpf',
            'email' => 'nullable|email|max:191',
            'nomeSocial' => 'nullable|string|max:191',
            'rg' => 'nullable|string|max:20',
            'mao_dominante' => 'nullable|string|max:50',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',

            // DADOS DE TRABALHO
            'ja_trabalhou' => 'nullable|boolean',
            'carteira_trabalho' => 'nullable|boolean',
            'ctps_assinada' => 'nullable|boolean',
            'qualFuncao' => 'nullable|string|max:191',

            // ENDEREÇO
            'cep' => 'nullable|string|max:10', 'rua' => 'nullable|string|max:191',
            'numero' => 'nullable|string|max:20', 'complemento' => 'nullable|string|max:191',
            'bairro' => 'nullable|string|max:191', 'cidade' => 'nullable|string|max:191',
            'uf' => 'nullable|string|max:2',

            // ESCOLARIDADE
            'escola' => 'nullable|string|max:191', 'ano' => 'nullable|string|max:50',
            'concluido' => 'nullable|boolean', 'periodo' => 'nullable|string|max:50',
            'anoConclusao' => 'nullable|string|max:4|regex:/^\\d{4}$/',
            'cursoAtual' => 'nullable|string|max:191',

            // SOCIOECONÔMICO E SAÚDE
            'moradia' => 'nullable|string|max:50', 
            'moradia_porquem' => 'nullable|string|max:191',
            'beneficio' => 'nullable|boolean',
            'bolsa_familia' => 'nullable|numeric', 'bpc_loas' => 'nullable|numeric',
            'pensao' => 'nullable|numeric', 'aux_aluguel' => 'nullable|numeric',
            'renda_cidada' => 'nullable|numeric', 'outros' => 'nullable|numeric',
            'agua' => 'nullable|numeric', 'alimentacao' => 'nullable|numeric',
            'gas' => 'nullable|numeric', 'luz' => 'nullable|numeric',
            'medicamento' => 'nullable|numeric', 'telefone_internet' => 'nullable|numeric',
            'aluguel_financiamento' => 'nullable|numeric',
            'ubs' => 'nullable|string|max:191',
            'convenio' => 'nullable|boolean', 'qual_convenio' => 'nullable|string|max:191',
            'vacinacao' => 'nullable|boolean',
            'queixa_saude' => 'nullable|boolean', 'qual_queixa' => 'nullable|string|max:191',
            'alergia' => 'nullable|boolean', 'qual_alergia' => 'nullable|string|max:191',
            'tratamento' => 'nullable|boolean', 'qual_tratamento' => 'nullable|string|max:191',
            'uso_remedio' => 'nullable|boolean', 'qual_remedio' => 'nullable|string|max:191',
            'cirurgia' => 'nullable|boolean', 'motivo_cirurgia' => 'nullable|string|max:191',
            'pcd' => 'nullable|boolean', 'qual_pcd' => 'nullable|string|max:191',
            'necessidade_especial' => 'nullable|string|max:191',
            'doenca_congenita' => 'nullable|boolean', 'qual_doenca_congenita' => 'nullable|string|max:191',
            'psicologo' => 'nullable|boolean', 'quando_psicologo' => 'nullable|string|max:191',
            'convulsao' => 'nullable|boolean', 'quando_convulsao' => 'nullable|string|max:191',

            // PSICOSSOCIAL
            'familia_doenca' => 'nullable|boolean', 'qual_familia_doenca' => 'nullable|string|max:191',
            'familia_depressao' => 'nullable|boolean', 'quem_familia_depressao' => 'nullable|string|max:191',
            'medico_especialista' => 'nullable|boolean', 'qual_medico_especialista' => 'nullable|string|max:191',
            'familia_psicologico' => 'nullable|boolean', 'quem_familia_psicologico' => 'nullable|string|max:191',
            'familia_alcool' => 'nullable|boolean', 'quem_familia_alcool' => 'nullable|string|max:191',
            'familia_drogas' => 'nullable|boolean', 'quem_familia_drogas' => 'nullable|string|max:191',

            // OUTROS
            'observacoes' => 'nullable|string',
            'familiares_json' => 'nullable|json',
            'assinatura' => 'nullable|string',
        ]);

        // ==========================================================
        // NORMALIZAÇÕES E LIMPEZAS (após validação)
        // ==========================================================
        // Converte booleans para 0/1 usando $request->boolean (respeita prepareBooleanFields)
        $booleanFields = [
            'declaracao_consentimento', 'carteira_trabalho', 'ja_trabalhou', 'ctps_assinada',
            'concluido', 'beneficio', 'convenio', 'vacinacao', 'queixa_saude', 'alergia',
            'tratamento', 'uso_remedio', 'cirurgia', 'pcd', 'doenca_congenita', 'psicologo',
            'convulsao', 'familia_doenca', 'familia_depressao', 'medico_especialista',
            'familia_psicologico', 'familia_alcool', 'familia_drogas'
        ];

        foreach ($booleanFields as $f) {
            $validatedData[$f] = $request->boolean($f);
        }

        // Sanitiza CPF e RG se existirem
        if (isset($validatedData['cpf'])) {
            $validatedData['cpf'] = preg_replace('/[^0-9]/', '', $validatedData['cpf']);
        }
        if (isset($validatedData['rg'])) {
            $validatedData['rg'] = preg_replace('/[^a-zA-Z0-9]/', '', $validatedData['rg']);
        }

        // Normaliza data (garante formato Y-m-d)
        if (!empty($validatedData['dataNascimento'])) {
            try {
                $validatedData['dataNascimento'] = Carbon::parse($validatedData['dataNascimento'])->format('Y-m-d');
            } catch (\Exception $e) {
                // se parsing falhar, deixa como está (já passou na validação)
            }
        }

        // ==========================================================
        // SALVA ALUNO E FAMILIARES (TRANSAÇÃO)
        // ==========================================================
        DB::beginTransaction();
        try {
            $aluno = Aluno::create($validatedData);

            // Processamento de Familiares (Criação)
            if ($request->filled('familiares_json')) {
                $familiares = json_decode($request->familiares_json, true);
                if ($familiares && is_array($familiares)) {
                    $familiares = array_filter($familiares, fn ($f) => array_filter($f));

                    foreach ($familiares as $familiarData) {
                        if (!empty($familiarData['nomeCompleto']) && !empty($familiarData['parentesco'])) {
                            // sanitiza salario se vier como string
                            if (!empty($familiarData['salarioBase'])) {
                                $familiarData['salarioBase'] = (float) str_replace(',', '.', str_replace('.', '', $familiarData['salarioBase']));
                            }
                            $aluno->familiares()->create($familiarData);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('aluno.index')->with('success', 'Aluno criado com sucesso.');
        } catch (\Throwable $e) {
            DB::rollBack();
            // opcional: log do erro aqui
            return back()->with('error', 'Erro ao salvar aluno: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Aluno $aluno)
    {
        $aluno->load('familiares');
        return view('alunos.show', compact('aluno'));
    }

    public function edit(Aluno $aluno)
    {
        $turmas = Turma::orderBy('ano_letivo', 'desc')->orderBy('letra', 'asc')->get();
        $aluno->load('familiares');
        return view('alunos.edit', compact('aluno', 'turmas'));
    }

    public function update(Request $request, Aluno $aluno)
    {
        // Normaliza checkboxes ausentes
        $this->prepareBooleanFields($request);

        $validatedData = $request->validate([
            // CAMPOS PRINCIPAIS (turma_id NÃO obrigatório para update também)
            'turma_id' => 'nullable|exists:turmas,id',
            'nomeCompleto' => 'required|string|max:191',
            'codigo_matricula' => 'nullable|string|max:100|unique:alunos,codigo_matricula,' . $aluno->id,
            'dataNascimento' => 'required|date',
            'declaracao_consentimento' => 'nullable|boolean',

            // CAMPOS PESSOAIS E DE CONTATO
            'cpf' => 'nullable|string|max:14|unique:alunos,cpf,' . $aluno->id,
            'email' => 'nullable|email|max:191',
            'nomeSocial' => 'nullable|string|max:191',
            'rg' => 'nullable|string|max:20',
            'mao_dominante' => 'nullable|string|max:50',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',

            // DADOS DE TRABALHO
            'ja_trabalhou' => 'nullable|boolean',
            'carteira_trabalho' => 'nullable|boolean',
            'ctps_assinada' => 'nullable|boolean',
            'qualFuncao' => 'nullable|string|max:191',

            // ENDEREÇO
            'cep' => 'nullable|string|max:10', 'rua' => 'nullable|string|max:191',
            'numero' => 'nullable|string|max:20', 'complemento' => 'nullable|string|max:191',
            'bairro' => 'nullable|string|max:191', 'cidade' => 'nullable|string|max:191',
            'uf' => 'nullable|string|max:2',

            // ESCOLARIDADE
            'escola' => 'nullable|string|max:191', 'ano' => 'nullable|string|max:50',
            'concluido' => 'nullable|boolean', 'periodo' => 'nullable|string|max:50',
            'anoConclusao' => 'nullable|digits:4',
            'cursoAtual' => 'nullable|string|max:191',

            // SOCIOECONÔMICO E SAÚDE
            'moradia' => 'nullable|string|max:50', 'moradia_porquem' => 'nullable|string|max:191',
            'beneficio' => 'nullable|boolean',
            'bolsa_familia' => 'nullable|numeric', 'bpc_loas' => 'nullable|numeric',
            'pensao' => 'nullable|numeric', 'aux_aluguel' => 'nullable|numeric',
            'renda_cidada' => 'nullable|numeric', 'outros' => 'nullable|numeric',
            'agua' => 'nullable|numeric', 'alimentacao' => 'nullable|numeric',
            'gas' => 'nullable|numeric', 'luz' => 'nullable|numeric',
            'medicamento' => 'nullable|numeric', 'telefone_internet' => 'nullable|numeric',
            'aluguel_financiamento' => 'nullable|numeric',
            'ubs' => 'nullable|string|max:191',
            'convenio' => 'nullable|boolean', 'qual_convenio' => 'nullable|string|max:191',
            'vacinacao' => 'nullable|boolean',
            'queixa_saude' => 'nullable|boolean', 'qual_queixa' => 'nullable|string|max:191',
            'alergia' => 'nullable|boolean', 'qual_alergia' => 'nullable|string|max:191',
            'tratamento' => 'nullable|boolean', 'qual_tratamento' => 'nullable|string|max:191',
            'uso_remedio' => 'nullable|boolean', 'qual_remedio' => 'nullable|string|max:191',
            'cirurgia' => 'nullable|boolean', 'motivo_cirurgia' => 'nullable|string|max:191',
            'pcd' => 'nullable|boolean', 'qual_pcd' => 'nullable|string|max:191',
            'necessidade_especial' => 'nullable|string|max:191',
            'doenca_congenita' => 'nullable|boolean', 'qual_doenca_congenita' => 'nullable|string|max:191',
            'psicologo' => 'nullable|boolean', 'quando_psicologo' => 'nullable|string|max:191',
            'convulsao' => 'nullable|boolean', 'quando_convulsao' => 'nullable|string|max:191',

            // PSICOSSOCIAL
            'familia_doenca' => 'nullable|boolean', 'qual_familia_doenca' => 'nullable|string|max:191',
            'familia_depressao' => 'nullable|boolean', 'quem_familia_depressao' => 'nullable|string|max:191',
            'medico_especialista' => 'nullable|boolean', 'qual_medico_especialista' => 'nullable|string|max:191',
            'familia_psicologico' => 'nullable|boolean', 'quem_familia_psicologico' => 'nullable|string|max:191',
            'familia_alcool' => 'nullable|boolean', 'quem_familia_alcool' => 'nullable|string|max:191',
            'familia_drogas' => 'nullable|boolean', 'quem_familia_drogas' => 'nullable|string|max:191',

            // OUTROS
            'observacoes' => 'nullable|string',
            'familiares_json' => 'nullable|json',
            'assinatura' => 'nullable|string',
        ]);

        // ==========================================================
        // NORMALIZAÇÕES E LIMPEZAS (após validação)
        // ==========================================================
        $booleanFields = [
            'declaracao_consentimento', 'carteira_trabalho', 'ja_trabalhou', 'ctps_assinada',
            'concluido', 'beneficio', 'convenio', 'vacinacao', 'queixa_saude', 'alergia',
            'tratamento', 'uso_remedio', 'cirurgia', 'pcd', 'doenca_congenita', 'psicologo',
            'convulsao', 'familia_doenca', 'familia_depressao', 'medico_especialista',
            'familia_psicologico', 'familia_alcool', 'familia_drogas'
        ];

        foreach ($booleanFields as $f) {
            $validatedData[$f] = $request->boolean($f);
        }

        if (isset($validatedData['cpf'])) {
            $validatedData['cpf'] = preg_replace('/[^0-9]/', '', $validatedData['cpf']);
        }
        if (isset($validatedData['rg'])) {
            $validatedData['rg'] = preg_replace('/[^a-zA-Z0-9]/', '', $validatedData['rg']);
        }

        if (!empty($validatedData['dataNascimento'])) {
            try {
                $validatedData['dataNascimento'] = Carbon::parse($validatedData['dataNascimento'])->format('Y-m-d');
            } catch (\Exception $e) {
                // não fatal - já validado
            }
        }

        // ==========================================================
        // ATUALIZA ALUNO E FAMILIARES (TRANSAÇÃO)
        // ==========================================================
        DB::beginTransaction();
        try {
            $aluno->update($validatedData);

            // Processamento de Familiares (Deleta e recria para atualização)
            if ($request->filled('familiares_json')) {
                $aluno->familiares()->delete();

                $familiares = json_decode($request->familiares_json, true);
                if ($familiares && is_array($familiares)) {
                    $familiares = array_filter($familiares, fn ($f) => array_filter($f));

                    foreach ($familiares as $familiarData) {
                        if (!empty($familiarData['nomeCompleto']) && !empty($familiarData['parentesco'])) {
                            if (!empty($familiarData['salarioBase'])) {
                                $familiarData['salarioBase'] = (float) str_replace(',', '.', str_replace('.', '', $familiarData['salarioBase']));
                            }
                            $aluno->familiares()->create($familiarData);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('aluno.edit', $aluno)->with('success', 'Aluno atualizado com sucesso.');
        } catch (\Throwable $e) {
            DB::rollBack();
            // opcional: log do erro aqui
            return back()->with('error', 'Erro ao atualizar aluno: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Aluno $aluno)
    {
        $aluno->delete();
        return redirect()->route('aluno.index')->with('success', 'Aluno excluído com sucesso.');
    }
}
