<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Familiar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Turma;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class AlunoController extends Controller
{
    // ==========================================================
    // 💡 SOLUÇÃO: MÉTODO HELPER PARA TRATAR CAMPOS BOOLEANOS AUSENTES
    // Isso garante que todo checkbox desmarcado (ausente no Request) receba '0'.
    // Resolve: "The [field] field must be true or false."
    // ==========================================================

    /**
     * Injeta o valor '0' (false) no Request para todos os campos booleanos
     * que estão ausentes (porque não foram marcados/selecionados no formulário).
     */
   
    public function showImportForm()
{
    return view('alunos.import');
}
    public function import(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt|max:10240',
    ]);

    $file = $request->file('csv_file');
    $filePath = $file->getRealPath();

    $delimiter = ';';
    $handle = fopen($filePath, 'r');

    if (!$handle) {
        return redirect()->back()->with('error', 'Não foi possível abrir o arquivo para leitura.');
    }

    // ==============================
    // 1. LER CABEÇALHO DO CSV
    // ==============================
    $header = fgetcsv($handle, 0, $delimiter);
    if (!$header) {
        return redirect()->back()->with('error', 'O arquivo CSV está vazio ou inválido.');
    }

    // Normaliza cabeçalhos do CSV → snake_case sem acentos
    $normalizedHeader = [];
    foreach ($header as $col) {
        $normalizedHeader[] = Str::snake(Str::ascii(trim($col)));
    }

    // ============================================
    // COLUNAS QUE EXISTEM NO BANCO (fillable)
    // ============================================
    $dbColumns = [
        'codigo_matricula',
        'nomeCompleto',
        'nomeSocial',
        'dataNascimento',
        'cpf',
        'rg',
        'mao_dominante',
        'carteira_trabalho',
        'ja_trabalhou',
        'ctps_assinada',
        'qual_funcao',
        'cep',
        'rua',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'telefone',
        'celular',
        'email',
        'escola',
        'ano',
        'periodo',
        'concluido',
        'ano_conclusao',
        'curso_atual',
        'moradia',
        'moradia_porquem',
        'beneficio',
        'bolsa_familia',
        'bpc_loas',
        'pensao',
        'aux_aluguel',
        'renda_cidada',
        'outros',
        'agua',
        'alimentacao',
        'gas',
        'luz',
        'medicamento',
        'telefone_internet',
        'aluguel_financiamento',
        'observacoes',
        'ubs',
        'convenio',
        'qual_convenio',
        'vacinacao',
        'queixa_saude',
        'qual_queixa',
        'alergia',
        'qual_alergia',
        'tratamento',
        'qual_tratamento',
        'uso_remedio',
        'qual_remedio',
        'cirurgia',
        'motivo_cirurgia',
        'pcd',
        'qual_pcd',
        'necessidade_especial',
        'doenca_congenita',
        'qual_doenca_congenita',
        'psicologo',
        'quando_psicologo',
        'convulsao',
        'quando_convulsao',
        'familia_doenca',
        'qual_familia_doenca',
        'familia_depressao',
        'quem_familia_depressao',
        'medico_especialista',
        'qual_medico_especialista',
        'familia_psicologico',
        'quem_familia_psicologico',
        'familia_alcool',
        'quem_familia_alcool',
        'familia_drogas',
        'quem_familia_drogas',
        'declaracao_consentimento',
        'assinatura'
    ];

    // ====================================
    // MAPEAR CABEÇALHO → CAMPOS DO BANCO
    // ====================================
    $columnMap = [];
    foreach ($normalizedHeader as $idx => $col) {

        // se o CSV tem "nome_completo" → no banco é "nomeCompleto"
        $manualMap = [
    // Mapeamento das colunas reais do seu CSV
    'codigo_aluno' => 'codigo_matricula',
    'turma' => 'turma_id',
    'n' => 'numero',
    'nome' => 'nomeCompleto',
    'nome_social' => 'nomeSocial',
    'status' => 'status',
    'empresas_que_ja_foi_encaminhado' => 'empresa_encaminhada',
    'em_processo' => 'em_processo',
    'contrato' => 'contrato',
    'e_mail' => 'email',
    'identidade_de_genero' => 'genero',
    'sexo' => 'sexo',
    'escolaridade' => 'escola',
    'periodo_da_escola' => 'periodo',
    'rg' => 'rg',
    'cpf' => 'cpf',
    'data_nasc' => 'dataNascimento',
    'idade' => 'idade',
    'idade_e_mes' => 'idade_mes',
    'cep' => 'cep',
    'logradouro' => 'rua',
    'n_res' => 'numero',
    'complemento' => 'complemento',
    'localidade' => 'cidade',
    'bairro' => 'bairro',
    'celular' => 'celular',
    'cel_responsavel' => 'telefone',
    'nome_do_responsavel' => 'responsavel',
    'nome_para_recado' => 'nome_recado',
    'tel_recado' => 'telefone_recado',
    'voce_esta_cursando_ou_ja_fez_algum_curso_relacionado_as_disciplinas_da_sodiprom' => 'curso_relacionado_sodiprom',
    'possui_algum_curso_qual' => 'possui_curso',
    'feira' => 'feira',
    'infor' => 'informatica',
    'log' => 'logistica',
    'mat' => 'matematica',
    'rh' => 'rh',
    'ta' => 'tec_adm',
    'media_final_disciplinas' => 'media_disciplinas',
    'media_final_comportamento' => 'media_comportamento',
    'coluna1' => 'coluna1',
    'selo' => 'selo',
    'observacoes_da_equipe_pedagogica' => 'observacoes',
    'ocorrencias' => 'ocorrencias',
    'sugestoes_de_areas_de_atuacao' => 'areas_atuacao',
];


        if (isset($manualMap[$col])) {
            $columnMap[$idx] = $manualMap[$col];
            continue;
        }

        // Se existir igual no banco
        if (in_array($col, $dbColumns)) {
            $columnMap[$idx] = $col;
        }
    }

    // COLUNAS ESSENCIAIS
    $required = ['codigo_matricula', 'nomeCompleto', 'dataNascimento'];
    $found = array_values($columnMap);
    $missing = array_diff($required, $found);

    if (!empty($missing)) {
        return back()->with('error',
            'O CSV está faltando colunas obrigatórias: ' . implode(', ', $missing)
        );
    }

    // ====================================
    // 2. PROCESSAR AS LINHAS
    // ====================================
    $results = ['success' => 0, 'updated' => 0, 'errors' => []];
    $lineNumber = 1;

    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
        $lineNumber++;
        $data = [];

        foreach ($columnMap as $i => $dbColumn) {
            $data[$dbColumn] = trim($row[$i] ?? '');
        }

        if (empty(array_filter($data))) {
            continue;
        }

        $codigo = $data['codigo_matricula'] ?? null;

        if (!$codigo) {
            $results['errors'][] = "Linha {$lineNumber}: faltando código de matrícula.";
            continue;
        }

        $response = $this->processAlunoLine($data, $codigo, $lineNumber);

        if ($response['status'] === 'created') $results['success']++;
        if ($response['status'] === 'updated') $results['updated']++;
        if ($response['status'] === 'error')
            $results['errors'] = array_merge($results['errors'], $response['errors']);
    }

    fclose($handle);

    // ====================================
    // 3. RETORNO
    // ====================================
    $msg = "Importação concluída: {$results['success']} criados, {$results['updated']} atualizados.";

    if (!empty($results['errors'])) {
        return back()->with('error', $msg)
            ->with('import_errors', $results['errors']);
    }

    return back()->with('success', $msg);
}


    /**
     * Lógica para processar a criação ou atualização de um aluno.
     * @param array $data Dados da linha CSV.
     * @param string $codigoMatricula Código de Matrícula.
     * @param int $lineNumber Número da linha no CSV.
     * @return array Status e possíveis erros.
     */
    protected function processAlunoLine(array $data, string $codigoMatricula, int $lineNumber): array
    {
        $errors = [];
        $aluno = null;
        $status = 'error';

        try {
            // 1. Tentar encontrar Turma ID
            $turmaId = Turma::findTurmaIdByCodigoAluno($codigoMatricula);

            if (!$turmaId) {
                // Loga um erro se a turma não puder ser identificada/criada.
                $errors[] = "Linha {$lineNumber} (Código: {$codigoMatricula}): Turma não pôde ser identificada (código inválido) ou criada.";
                return ['status' => 'error', 'errors' => $errors];
            }

            // 2. Preparar dados para o Aluno (adiciona turma_id)
            $data['turma_id'] = $turmaId;

            // 3. Sanear dados (conversão de booleanos, datas, etc.)

            // Converte Data de Nascimento (Esperando formato dd/mm/yyyy ou yyyy-mm-dd)
            if (!empty($data['dataNascimento'])) {
                // Tenta primeiro dd/mm/yyyy
                try {
                    $date = Carbon::createFromFormat('d/m/Y', $data['dataNascimento']);
                } catch (\Exception $e) {
                    // Se falhar, tenta yyyy-mm-dd
                    try {
                        $date = Carbon::createFromFormat('Y-m-d', $data['dataNascimento']);
                    } catch (\Exception $e) {
                        $date = null; // Falha na conversão
                    }
                }

                if ($date) {
                    $data['dataNascimento'] = $date->format('Y-m-d');
                } else {
                    $errors[] = "Linha {$lineNumber} (Código: {$codigoMatricula}): Data de nascimento inválida (Formato esperado: dd/mm/aaaa ou aaaa-mm-dd).";
                }
            } else {
                $errors[] = "Linha {$lineNumber} (Código: {$codigoMatricula}): Data de nascimento é obrigatória.";
            }

            // Se houve erro na data, retorna antes de validar o resto
            if (!empty($errors)) {
                return ['status' => 'error', 'errors' => $errors];
            }


            // Validação simples e sanitização (o Mutator no Model Aluno fará o resto)
            $validator = Validator::make($data, [
                'codigo_matricula' => 'required|string|max:100',
                'nomeCompleto' => 'required|string|max:191',
                'dataNascimento' => 'required|date',
                'email' => 'nullable|email|max:191',
                'cpf' => 'nullable|string|max:14', // A sanitização ocorre no Model
            ]);

            if ($validator->fails()) {
                $validationErrors = $validator->errors()->all();
                $errors[] = "Linha {$lineNumber} (Código: {$codigoMatricula}): Erros de validação - " . implode(', ', $validationErrors);
                return ['status' => 'error', 'errors' => $errors];
            }

            // Remove campos que não estão no fillable ou que são apenas Accessors
            unset($data['idade']); // 'idade' é um Accessor no Model

            // 4. Criação ou Atualização do Aluno
            $aluno = Aluno::where('codigo_matricula', $codigoMatricula)->first();

            if ($aluno) {
                // Atualiza o aluno existente
                $aluno->update($data);
                $status = 'updated';
            } else {
                // Cria um novo aluno
                $aluno = Aluno::create($data);
                $status = 'created';
            }

            return ['status' => $status, 'errors' => $errors];
        } catch (ValidationException $e) {
            // Erros de validação
            $validationErrors = $e->errors();
            foreach ($validationErrors as $field => $messages) {
                $errors[] = "Linha {$lineNumber} (Código: {$codigoMatricula}): Erro no campo '{$field}' - " . implode(', ', $messages);
            }
            return ['status' => 'error', 'errors' => $errors];
        } catch (\Exception $e) {
            // Outros erros, como problemas no DB
            Log::error("Erro de Importação na Linha {$lineNumber} para Código {$codigoMatricula}: " . $e->getMessage());
            $errors[] = "Linha {$lineNumber} (Código: {$codigoMatricula}): Erro fatal ao salvar: " . $e->getMessage();
            return ['status' => 'error', 'errors' => $errors];
        }
    }

    // ==========================================================
    // MÉTODOS CRUD CORRIGIDOS
    // ==========================================================

    public function create()
    {
        // Retorna a view de criação de aluno.
        $turmas = Turma::orderBy('ano_letivo', 'desc')->orderBy('letra', 'asc')->get();
        return view('alunos.create', compact('turmas'));
    }

    public function store(Request $request)
    {
        // 🚨 PASSO ESSENCIAL: Prepara campos booleanos ausentes
        $this->prepareBooleanFields($request);

        // 1. Validação Completa
        $validatedData = $request->validate([
            // CAMPOS OBRIGATÓRIOS (Resolve: "The [field] field is required.")
            // 'turma_id' => 'required|exists:turmas,id',
            'nomeCompleto' => 'required|string|max:191',
            'codigo_matricula' => 'nullable',
            'dataNascimento' => 'required|date',
            'declaracao_consentimento' => 'required|boolean', // Agora garantido pelo prepareBooleanFields

            // Outros Campos (Booleanos marcados como 'boolean' após a injeção do 0)
            'cpf' => 'nullable|string|max:14|unique:alunos,cpf',
            'email' => 'nullable|email|max:191',
            'nomeSocial' => 'nullable|string|max:191',
            'rg' => 'nullable|string|max:20',
            'mao_dominante' => 'nullable|string|max:50',

            // Dados de Trabalho (jaTrabalhou é 'required' por ser um campo de escolha obrigatória)
            'jaTrabalhou' => 'required|boolean',
            'carteiraTrabalho' => 'nullable|boolean',
            'ctpsAssinada' => 'nullable|boolean',
            'qualFuncao' => 'nullable|string|max:191',

            // Endereço e Contato
            'cep' => 'nullable|string|max:10',
            'rua' => 'nullable|string|max:191',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:191',
            'bairro' => 'nullable|string|max:191',
            'cidade' => 'nullable|string|max:191',
            'uf' => 'nullable|string|max:2',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',

            // Escolaridade
            'escola' => 'nullable|string|max:191',
            'ano' => 'nullable|string|max:50',
            'concluido' => 'nullable|boolean',
            'periodo' => 'nullable|string|max:50',
            'anoConclusao' => 'nullable|integer|digits:4',
            'cursoAtual' => 'nullable|string|max:191',

            // Socioeconômico e Saúde (Booleanos)
            'moradia' => 'nullable|string|max:50',
            'moradia_porquem' => 'nullable|string|max:191',
            'beneficio' => 'nullable|boolean',
            'bolsa_familia' => 'nullable|numeric',
            'bpc_loas' => 'nullable|numeric',
            'pensao' => 'nullable|numeric',
            'aux_aluguel' => 'nullable|numeric',
            'renda_cidada' => 'nullable|numeric',
            'outros' => 'nullable|numeric',
            'agua' => 'nullable|numeric',
            'alimentacao' => 'nullable|numeric',
            'gas' => 'nullable|numeric',
            'luz' => 'nullable|numeric',
            'medicamento' => 'nullable|numeric',
            'telefone_internet' => 'nullable|numeric',
            'aluguel_financiamento' => 'nullable|numeric',
            'observacoes' => 'nullable|string',
            'familiares_json' => 'nullable|json',

            'ubs' => 'nullable|string|max:191',
            'convenio' => 'nullable|boolean',
            'qual_convenio' => 'nullable|string|max:191',
            'vacinacao' => 'nullable|boolean',
            'queixa_saude' => 'nullable|boolean',
            'qual_queixa' => 'nullable|string|max:191',
            'alergia' => 'nullable|boolean',
            'qual_alergia' => 'nullable|string|max:191',
            'tratamento' => 'nullable|boolean',
            'qual_tratamento' => 'nullable|string|max:191',
            'uso_remedio' => 'nullable|boolean',
            'qual_remedio' => 'nullable|string|max:191',
            'cirurgia' => 'nullable|boolean',
            'motivo_cirurgia' => 'nullable|string|max:191',
            'pcd' => 'nullable|boolean',
            'qual_pcd' => 'nullable|string|max:191',
            'necessidade_especial' => 'nullable|string|max:191',
            'doenca_congenita' => 'nullable|boolean',
            'qual_doenca_congenita' => 'nullable|string|max:191',
            'psicologo' => 'nullable|boolean',
            'quando_psicologo' => 'nullable|string|max:191',
            'convulsao' => 'nullable|boolean',
            'quando_convulsao' => 'nullable|string|max:191',

            // Psicossocial
            'familia_doenca' => 'nullable|boolean',
            'qual_familia_doenca' => 'nullable|string|max:191',
            'familia_depressao' => 'nullable|boolean',
            'quem_familia_depressao' => 'nullable|string|max:191',
            'medico_especialista' => 'nullable|boolean',
            'qual_medico_especialista' => 'nullable|string|max:191',
            'familia_psicologico' => 'nullable|boolean',
            'quem_familia_psicologico' => 'nullable|string|max:191',
            'familia_alcool' => 'nullable|boolean',
            'quem_familia_alcool' => 'nullable|string|max:191',
            'familia_drogas' => 'nullable|boolean',
            'quem_familia_drogas' => 'nullable|string|max:191',

            // Assinatura
            'assinatura' => 'nullable|string',
        ]);


        // 2. Criação do Aluno
        $aluno = Aluno::create($validatedData);

        // 3. Processamento de Familiares (mantido)
        if ($request->filled('familiares_json')) {
            $familiares = json_decode($request->familiares_json, true);
            if ($familiares) {
                $familiares = array_filter($familiares, function ($f) {
                    return array_filter($f);
                });

                foreach ($familiares as $familiarData) {
                    if (!empty($familiarData['nomeCompleto']) && !empty($familiarData['parentesco'])) {
                        $aluno->familiares()->create($familiarData);
                    }
                }
            }
        }

        return redirect()->route('aluno.index')->with('success', 'Aluno criado com sucesso.');
    }

    public function show(Aluno $aluno)
    {
        // Retorna a view de visualização do aluno.
        $aluno->load('familiares');
        return view('alunos.show', compact('aluno'));
    }

    public function edit(Aluno $aluno)
    {
        // Retorna a view de edição do aluno.
        $turmas = Turma::orderBy('ano_letivo', 'desc')->orderBy('letra', 'asc')->get();
        $aluno->load('familiares');
        return view('alunos.edit', compact('aluno', 'turmas'));
    }

    public function update(Request $request, Aluno $aluno)
    {
        // 🚨 PASSO ESSENCIAL: Prepara campos booleanos ausentes
        $this->prepareBooleanFields($request);

        // 1. Validação Completa (Ajustada para update com 'unique' ignorando o aluno atual)
        $validatedData = $request->validate([
            // CAMPOS OBRIGATÓRIOS (Resolve: "The [field] field is required.")
            'turma_id' => 'required|exists:turmas,id',
            'nomeCompleto' => 'required|string|max:191',
            // O código de matrícula e CPF devem ser únicos, exceto para o próprio aluno
            'codigo_matricula' => 'required|string|max:100|unique:alunos,codigo_matricula,' . $aluno->id,
            'dataNascimento' => 'required|date',
            'declaracao_consentimento' => 'required|boolean',

            // Outros Campos (Booleanos marcados como 'boolean' após a injeção do 0)
            'cpf' => 'nullable|string|max:14|unique:alunos,cpf,' . $aluno->id,
            'email' => 'nullable|email|max:191',
            'nomeSocial' => 'nullable|string|max:191',
            'rg' => 'nullable|string|max:20',
            'mao_dominante' => 'nullable|string|max:50',

            // Dados de Trabalho 
            'jaTrabalhou' => 'required|boolean',
            'carteiraTrabalho' => 'nullable|boolean',
            'ctpsAssinada' => 'nullable|boolean',
            'qualFuncao' => 'nullable|string|max:191',

            // Endereço e Contato
            'cep' => 'nullable|string|max:10',
            'rua' => 'nullable|string|max:191',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:191',
            'bairro' => 'nullable|string|max:191',
            'cidade' => 'nullable|string|max:191',
            'uf' => 'nullable|string|max:2',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',

            // Escolaridade
            'escola' => 'nullable|string|max:191',
            'ano' => 'nullable|string|max:50',
            'concluido' => 'nullable|boolean',
            'periodo' => 'nullable|string|max:50',
            'anoConclusao' => 'nullable|integer|digits:4',
            'cursoAtual' => 'nullable|string|max:191',

            // Socioeconômico e Saúde (Booleanos)
            'moradia' => 'nullable|string|max:50',
            'moradia_porquem' => 'nullable|string|max:191',
            'beneficio' => 'nullable|boolean',
            'bolsa_familia' => 'nullable|numeric',
            'bpc_loas' => 'nullable|numeric',
            'pensao' => 'nullable|numeric',
            'aux_aluguel' => 'nullable|numeric',
            'renda_cidada' => 'nullable|numeric',
            'outros' => 'nullable|numeric',
            'agua' => 'nullable|numeric',
            'alimentacao' => 'nullable|numeric',
            'gas' => 'nullable|numeric',
            'luz' => 'nullable|numeric',
            'medicamento' => 'nullable|numeric',
            'telefone_internet' => 'nullable|numeric',
            'aluguel_financiamento' => 'nullable|numeric',
            'observacoes' => 'nullable|string',
            'familiares_json' => 'nullable|json',

            'ubs' => 'nullable|string|max:191',
            'convenio' => 'nullable|boolean',
            'qual_convenio' => 'nullable|string|max:191',
            'vacinacao' => 'nullable|boolean',
            'queixa_saude' => 'nullable|boolean',
            'qual_queixa' => 'nullable|string|max:191',
            'alergia' => 'nullable|boolean',
            'qual_alergia' => 'nullable|string|max:191',
            'tratamento' => 'nullable|boolean',
            'qual_tratamento' => 'nullable|string|max:191',
            'uso_remedio' => 'nullable|boolean',
            'qual_remedio' => 'nullable|string|max:191',
            'cirurgia' => 'nullable|boolean',
            'motivo_cirurgia' => 'nullable|string|max:191',
            'pcd' => 'nullable|boolean',
            'qual_pcd' => 'nullable|string|max:191',
            'necessidade_especial' => 'nullable|string|max:191',
            'doenca_congenita' => 'nullable|boolean',
            'qual_doenca_congenita' => 'nullable|string|max:191',
            'psicologo' => 'nullable|boolean',
            'quando_psicologo' => 'nullable|string|max:191',
            'convulsao' => 'nullable|boolean',
            'quando_convulsao' => 'nullable|string|max:191',

            // Psicossocial
            'familia_doenca' => 'nullable|boolean',
            'qual_familia_doenca' => 'nullable|string|max:191',
            'familia_depressao' => 'nullable|boolean',
            'quem_familia_depressao' => 'nullable|string|max:191',
            'medico_especialista' => 'nullable|boolean',
            'qual_medico_especialista' => 'nullable|string|max:191',
            'familia_psicologico' => 'nullable|boolean',
            'quem_familia_psicologico' => 'nullable|string|max:191',
            'familia_alcool' => 'nullable|boolean',
            'quem_familia_alcool' => 'nullable|string|max:191',
            'familia_drogas' => 'nullable|boolean',
            'quem_familia_drogas' => 'nullable|string|max:191',

            // Assinatura
            'assinatura' => 'nullable|string',
        ]);

        // 2. Atualização do Aluno
        $aluno->update($validatedData);

        // 3. Processamento de Familiares (mantido)
        if ($request->filled('familiares_json')) {
            $aluno->familiares()->delete();

            $familiares = json_decode($request->familiares_json, true);
            if ($familiares) {
                $familiares = array_filter($familiares, function ($f) {
                    return array_filter($f);
                });

                foreach ($familiares as $familiarData) {
                    if (!empty($familiarData['nomeCompleto']) && !empty($familiarData['parentesco'])) {
                        $aluno->familiares()->create($familiarData);
                    }
                }
            }
        }

        return redirect()->route('aluno.edit', $aluno)->with('success', 'Aluno atualizado com sucesso.');
    }

    public function destroy(Aluno $aluno)
    {
        // A Turma e os Familiares associados devem ser tratados via Foreign Keys/Cascata no DB
        $aluno->delete();
        return redirect()->route('aluno.index')->with('success', 'Aluno excluído com sucesso.');
    }
}
