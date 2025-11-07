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
use Illuminate\Support\Facades\Log; // Adicionado para debug

class AlunoController extends Controller
{
    // ==========================================================
    // MÉTODOS CRUD BÁSICOS E ESSENCIAIS
    // ==========================================================
    
    /**
     * Exibe a listagem de alunos com paginação, filtragem e ordenação.
     * MÉTODO INDEX ATUALIZADO para usar filtros de Ano e Semestre.
     */
    public function index(Request $request)
    {
        $query = Aluno::query();

        // 1. FILTRAGEM POR ANO, PERÍODO OU TURMA
        
        $turmaId = $request->input('turma_id');
        $anoLetivo = $request->input('ano_letivo');
        $periodo = $request->input('periodo');

        // Se a turma_id específica for selecionada, ela tem prioridade.
        if ($turmaId) {
            $query->where('turma_id', $turmaId);
        } 
        // Caso contrário, filtra pelo ano letivo E/OU período, usando whereHas na relação 'turma'.
        elseif ($anoLetivo || $periodo) {
            $query->whereHas('turma', function ($q) use ($anoLetivo, $periodo) {
                if ($anoLetivo) {
                    $q->where('ano_letivo', $anoLetivo);
                }
                if ($periodo) {
                    // O valor de $periodo será '1' ou '2' (Semestre)
                    $q->where('periodo', $periodo);
                }
            });
        }
        
        // 2. ORDENAÇÃO
        // Pega os parâmetros da requisição ou usa padrões
        $sortColumn = $request->get('sort', 'id'); 
        $sortDirection = $request->get('direction', 'desc');

        // Colunas permitidas para ordenação (segurança)
        $allowedColumns = ['id', 'nomeCompleto', 'cpf', 'turma_id'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }
        
        // Aplica a ordenação
        if ($sortColumn === 'turma_id') {
            // Ordena pela Turma (o que geralmente significa ordenar por ano/letra da turma)
            $query->with(['turma' => function ($q) use ($sortDirection) {
                $q->orderBy('ano_letivo', $sortDirection)->orderBy('letra', $sortDirection);
            }])
            ->orderBy('turma_id', $sortDirection); 
        } else {
             // Se estiver ordenando por 'nomeCompleto' ou 'cpf', usa a ordenação direta
            $query->orderBy($sortColumn, $sortDirection);
        }

        // 3. PAGINAÇÃO E RESULTADOS
        $alunos = $query->paginate(20)->withQueryString();

        // 4. VARIÁVEIS DE FILTRO PARA A VIEW
        
        // Turmas (para o filtro Turma Específica)
        $turmas = Turma::orderBy('ano_letivo', 'desc')->orderBy('letra', 'asc')->get();
        
        // Anos Letivos (valores distintos existentes no banco)
        $anosLetivos = Turma::select('ano_letivo')
            ->distinct()
            ->orderBy('ano_letivo', 'desc')
            ->pluck('ano_letivo');

        // Períodos (APENAS Semestres '1' e '2') - Valores do DB
        $periodos = ['1', '2']; 

        return view('alunos.index', compact('alunos', 'turmas', 'anosLetivos', 'periodos'));
    }

    /**
     * Exibe o formulário de cadastro de um novo aluno (aluno.create).
     */
    public function create()
    {
        // Necessário para o dropdown de Turmas
        $turmas = Turma::orderBy('ano_letivo', 'desc')->orderBy('letra', 'asc')->get();
        // Necessário para o dropdown de Familiares (Opcional se for linkar aluno a familiar existente)
        $familiares = Familiar::orderBy('nomeCompleto', 'asc')->get(); 

        return view('alunos.create', compact('turmas', 'familiares'));
    }

    /**
     * Armazena um novo aluno no banco de dados.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'nomeCompleto' => 'required|string|max:255',
            'cpf' => 'nullable|string|max:14|unique:alunos,cpf',
            'rg' => 'nullable|string|max:20',
            'dataNascimento' => 'nullable|date',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:alunos,email',
            // Adicionar campos necessários do seu formulário
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'bolsa_familia' => 'nullable|string|max:10', // Exemplo
        ]);

        // 1. Encontra os dados da turma para gerar o código do aluno
        $turma = Turma::findOrFail($validatedData['turma_id']);
        
        // 2. Conta quantos alunos já existem nesta turma para determinar o sequencial
        $sequencial = $turma->alunos()->count() + 1;
        $sequencialFormatado = str_pad($sequencial, 3, '0', STR_PAD_LEFT);

        // 3. Gera o codigoAluno no formato AAAA-S-T-XXX
        $codigoAluno = "{$turma->ano_letivo}-{$turma->periodo}-{$turma->letra}-{$sequencialFormatado}";

        $validatedData['codigoAluno'] = $codigoAluno;
        $validatedData['user_id'] = auth()->id() ?? 1; // ID do usuário que criou o registro

        // Cria o aluno
        $aluno = Aluno::create($validatedData);

        return redirect()->route('aluno.index')->with('success', "Aluno {$aluno->nomeCompleto} cadastrado com sucesso na Turma {$turma->getNomeCompletoAttribute()}.");
    }

    /**
     * Exibe o perfil detalhado de um aluno, carregando todos os seus dados relacionados.
     * Implementa o 'aluno.show'
     */
    public function show(Aluno $aluno)
    {
        // Carrega os relacionamentos necessários para a view de dashboard/perfil
        $aluno->load([
            'turma', 
            'familiares', 
            'user' 
        ]);

        return view('alunos.show', compact('aluno'));
    }
    
    /**
     * Exibe o formulário para edição do aluno.
     */
    public function edit(Aluno $aluno)
    {
        $turmas = Turma::orderBy('ano_letivo', 'desc')->orderBy('letra', 'asc')->get();
        $familiares = Familiar::orderBy('nomeCompleto', 'asc')->get(); 

        return view('alunos.edit', compact('aluno', 'turmas', 'familiares'));
    }

    /**
     * Atualiza o aluno no banco de dados.
     */
    public function update(Request $request, Aluno $aluno)
    {
        $validatedData = $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'nomeCompleto' => 'required|string|max:255',
            // O CPF e E-mail devem ser únicos, exceto para o aluno atual
            'cpf' => 'nullable|string|max:14|unique:alunos,cpf,' . $aluno->id, 
            'email' => 'nullable|email|max:255|unique:alunos,email,' . $aluno->id,
            'rg' => 'nullable|string|max:20',
            'dataNascimento' => 'nullable|date',
            'telefone' => 'nullable|string|max:20',
            // Adicionar campos necessários do seu formulário
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'bolsa_familia' => 'nullable|string|max:10', 
            // O codigoAluno não deve ser alterado manualmente aqui
        ]);
        
        $aluno->update($validatedData);

        return redirect()->route('aluno.index')->with('success', "Aluno {$aluno->nomeCompleto} atualizado com sucesso.");
    }
    
    /**
     * Remove o aluno do banco de dados.
     */
    public function destroy(Aluno $aluno)
    {
        try {
            $aluno->delete();
            return redirect()->route('aluno.index')->with('success', 'Aluno excluído com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('aluno.index')->with('error', 'Erro ao excluir o aluno: ' . $e->getMessage());
        }
    }


    // ==========================================================
    // MÉTODOS RELACIONADOS À IMPORTAÇÃO
    // ==========================================================

    /**
     * Exibe o formulário de importação (Upload CSV)
     * MÉTODO CORRIGIDO: showImportForm
     */
    public function showImportForm()
    {
        return view('alunos.import');
    }

    /**
     * Processa o arquivo CSV e salva os dados dos alunos.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();
        $errors = [];
        $successCount = 0;
        
        // Define as regras de validação para os campos do aluno
        $validationRules = [
            'nomeCompleto' => 'required|string|max:255',
            'cpf' => 'nullable|string|max:14', 
            'rg' => 'nullable|string|max:20',
            'dataNascimento' => 'nullable|date_format:Y-m-d', // Já formatada
            'email' => 'nullable|email|max:255', 
        ];

        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            // Lê o cabeçalho (primeira linha)
            $header = fgetcsv($handle, 1000, ',');
            
            // Mapeamento dos cabeçalhos do CSV para os campos do DB
            $columnMap = [
                'CODIGO ALUNO' => 'codigoAluno',
                'NOME ALUNO' => 'nomeCompleto',
                'CPF' => 'cpf',
                'RG' => 'rg',
                'DATA DE NASCIMENTO' => 'dataNascimento',
                'TELEFONE' => 'telefone',
                'EMAIL' => 'email',
                'SEXO' => 'sexo',
                'CEP' => 'cep',
                'ENDERECO' => 'endereco',
                'BAIRRO' => 'bairro',
                'CIDADE' => 'cidade',
                'UF' => 'uf',
                'BOLSA FAMILIA' => 'bolsa_familia',
                // Adicione outros campos da sua planilha aqui
            ];

            $mappedHeader = [];
            foreach ($header as $colIndex => $colName) {
                $standardizedName = Str::upper(str_replace(' ', '_', trim($colName)));
                if (isset($columnMap[$standardizedName])) {
                    $mappedHeader[$colIndex] = $columnMap[$standardizedName];
                }
            }

            $line = 1; 
            DB::beginTransaction();
            
            try {
                while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $line++;
                    
                    $rowData = [];
                    foreach ($row as $colIndex => $value) {
                        if (isset($mappedHeader[$colIndex])) {
                            $rowData[$mappedHeader[$colIndex]] = $value;
                        }
                    }

                    $result = $this->handleCsvRow($rowData, $validationRules, $line);
                    
                    if ($result['status'] === 'success') {
                        $successCount++;
                    } else {
                        $errors = array_merge($errors, $result['errors']);
                    }
                }
                
                fclose($handle);
                
                if (empty($errors)) {
                    DB::commit();
                    return redirect()->route('aluno.index')->with('success', "Importação concluída: {$successCount} alunos processados com sucesso.");
                } else {
                    DB::rollBack();
                    // Retorna com erros
                    return redirect()->route('aluno.index')->with('error', 'Importação concluída, mas com erros.')->with('import_errors', $errors);
                }

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Erro fatal na importação: ' . $e->getMessage());
                return redirect()->route('aluno.index')->with('error', 'Erro fatal durante a importação: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('aluno.index')->with('error', 'Não foi possível ler o arquivo CSV.');
    }

    /**
     * Lógica para processar cada linha do CSV.
     */
    protected function handleCsvRow(array $rowData, array $validationRules, int $lineNumber): array
    {
        $errors = [];

        // 1. OBTENÇÃO DA TURMA (CHAVE PRINCIPAL)
        $codigoAluno = $rowData['codigoAluno'] ?? null;
        if (!$codigoAluno) {
            $errors[] = "Linha {$lineNumber}: Código do aluno (codigoAluno) está faltando.";
            return ['status' => 'error', 'errors' => $errors];
        }

        // Usa o método estático do modelo Turma para encontrar o ID
        $turmaId = Turma::findTurmaIdByCodigoAluno($codigoAluno); 

        if (!$turmaId) {
            $errors[] = "Linha {$lineNumber}: Turma não encontrada para o código '{$codigoAluno}'.";
            return ['status' => 'error', 'errors' => $errors];
        }

        // 2. PREPARAÇÃO E TRATAMENTO DOS DADOS

        // Trata a data de nascimento (assume o formato dd/mm/aaaa)
        if (isset($rowData['dataNascimento']) && 
            !empty($rowData['dataNascimento']) &&
            preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', trim($rowData['dataNascimento']), $matches)) 
        {
             // Converte dd/mm/yyyy para yyyy-mm-dd
             $rowData['dataNascimento'] = "{$matches[3]}-{$matches[2]}-{$matches[1]}";
        }

        // Remove chaves do CSV que não estão no modelo Aluno (ex: cabeçalhos não mapeados)
        $alunoData = array_intersect_key($rowData, array_flip(array_keys($validationRules)));
        $alunoData['turma_id'] = $turmaId;
        $alunoData['codigoAluno'] = $codigoAluno;
        $alunoData['user_id'] = auth()->id() ?? 1;


        // 3. VALIDAÇÃO

        $alunoExistente = Aluno::where('codigoAluno', $codigoAluno)->first();

        $finalValidationRules = $validationRules;
        if ($alunoExistente) {
            // Se o aluno existe, a regra de unicidade do CPF e Email deve ignorar o ID dele
            $finalValidationRules['cpf'] = 'nullable|string|max:14|unique:alunos,cpf,' . $alunoExistente->id;
            $finalValidationRules['email'] = 'nullable|email|max:255|unique:alunos,email,' . $alunoExistente->id;
        } else {
             // Se for um novo aluno, aplica a regra de unicidade normalmente
            $finalValidationRules['cpf'] = 'nullable|string|max:14|unique:alunos,cpf';
            $finalValidationRules['email'] = 'nullable|email|max:255|unique:alunos,email';
        }

        $validator = Validator::make($alunoData, $finalValidationRules);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            foreach ($messages as $msg) {
                $errors[] = "Linha {$lineNumber}: " . $msg;
            }
            return ['status' => 'error', 'errors' => $errors];
        }

        // 4. Criação/Atualização do Aluno
        try {
            if ($alunoExistente) {
                // Atualiza (não mexe no user_id)
                unset($alunoData['user_id']); 
                $alunoExistente->update($alunoData);
            } else {
                // Cria um novo
                Aluno::create($alunoData);
            }

            return ['status' => 'success'];
        } catch (\Exception $e) {
            Log::error("Erro ao salvar aluno na linha {$lineNumber}: " . $e->getMessage());
            $errors[] = "Linha {$lineNumber}: Erro ao salvar o aluno: " . $e->getMessage();
            return ['status' => 'error', 'errors' => $errors];
        }
    }
}
