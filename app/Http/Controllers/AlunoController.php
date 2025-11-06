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

class AlunoController extends Controller
{
    // ==========================================================
    // MÉTODOS CRUD BÁSICOS E ESSENCIAIS
    // ==========================================================
    
    /**
     * Exibe a listagem de alunos com paginação.
     */
    public function index()
    {
        // 1. Busca os alunos, ordenados por nome completo
        $alunos = Aluno::orderBy('nomeCompleto')->paginate(20); 
        
        // 2. Retorna a view de listagem
        return view('alunos.index', compact('alunos'));
    }

    /**
     * Exibe o perfil detalhado de um aluno, carregando todos os seus dados relacionados.
     * Implementa o 'aluno.show'
     * @param \App\Models\Aluno $aluno
     * @return \Illuminate\View\View
     */
    public function show(Aluno $aluno)
    {
        // Carrega os relacionamentos necessários para a view de dashboard/perfil
        $aluno->load([
            'turma.professor', // Turma e o Professor relacionado
            'familiares',      // Todos os Familiares
            'presencas'        // Dados de frequência/presença (relacionamento definido em Aluno.php)
        ]);
        
        return view('alunos.show', compact('aluno'));
    }

    /**
     * Exibe o formulário para editar um aluno.
     * Implementa o 'aluno.edit'
     * @param \App\Models\Aluno $aluno
     * @return \Illuminate\View\View
     */
    public function edit(Aluno $aluno)
    {
        // Carrega todas as turmas disponíveis para o campo de seleção
        $turmas = Turma::all()->mapWithKeys(function ($turma) {
            // Usa o Accessor nomeCompleto do Model Turma
            return [$turma->id => $turma->nomeCompleto]; 
        });
        
        // Carrega familiares para serem listados na tela de edição
        $aluno->load('familiares');

        return view('alunos.edit', compact('aluno', 'turmas'));
    }

    /**
     * Atualiza os dados de um aluno no banco de dados.
     * Implementa o 'aluno.update'
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Aluno $aluno
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Aluno $aluno)
    {
        // 1. Definição das Regras de Validação para a Edição (incluindo unique do CPF)
        $rules = [
            'nomeCompleto' => 'required|string|max:191',
            // O CPF deve ser único, exceto para o aluno que está sendo editado (ignore)
            'cpf' => 'nullable|string|max:14|unique:alunos,cpf,' . $aluno->id, 
            'dataNascimento' => 'required|date_format:d/m/Y', // Espera o formato DD/MM/AAAA da view
            'email' => 'nullable|email|max:191', 
            'celular' => 'nullable|string|max:20',
            'turma_id' => 'nullable|exists:turmas,id',
            'observacoes' => 'nullable|string', 
            // Os campos booleanos devem ter validação de tipo adequada ao que a view envia.
            'jaTrabalhou' => 'nullable|boolean', // O Mutator no Model Aluno trata de strings p/ boolean
            'ctpsAssinada' => 'nullable|boolean',
            'bolsa_familia' => 'nullable|numeric|min:0',
        ];
        
        // 2. Valida os dados da requisição
        $validatedData = $request->validate($rules);
        
        // 3. Conversão da Data (dd/mm/aaaa para Y-m-d)
        if (isset($validatedData['dataNascimento'])) {
             try {
                // Tenta criar a data no formato d/m/Y e formatar para Y-m-d (MySQL)
                $validatedData['dataNascimento'] = Carbon::createFromFormat('d/m/Y', $validatedData['dataNascimento'])->format('Y-m-d');
             } catch (\Exception $e) {
                 throw ValidationException::withMessages(['dataNascimento' => 'A data de nascimento fornecida é inválida.']);
             }
        }
        
        // 4. Atualiza o Aluno (O Mutator no Model Aluno se encarrega de sanitizar CPF/RG/Booleanos)
        $aluno->update($validatedData);

        return redirect()->route('aluno.show', $aluno)
            ->with('success', 'Dados do aluno atualizados com sucesso!');
    }


    // ==========================================================
    // MÉTODOS DE IMPORTAÇÃO CSV (Corrigidos para Upsert)
    // ==========================================================

    public function showImportForm()
    {
        return view('alunos.import');
    }

    /**
     * Processa o upload do CSV e importa os dados.
     */
    public function import(Request $request)
    {
        // 1. Validação do arquivo
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('csv_file');
        
        if (($handle = fopen($file->getRealPath(), 'r')) !== FALSE) {
            
            // Tenta ler os cabeçalhos usando o delimitador ';'
            $header = fgetcsv($handle, 1000, ';');
            
            // Certifica-se de que os cabeçalhos estão em caixa alta e sem espaços extras
            $normalizedHeader = array_map(fn($h) => trim(Str::upper($h)), $header);
            
            $importedCount = 0;
            $rowCounter = 1; // 💡 CORREÇÃO: Contador para rastrear a linha atual no CSV
            $errorRows = [];

            DB::beginTransaction();

            try {
                // Loop por cada linha do CSV
                while (($row = fgetcsv($handle, 1000, ';')) !== FALSE) {
                    $rowCounter++; // Linha de dados (começa em 2)
                    
                    if (count($normalizedHeader) !== count($row)) {
                        $errorRows[] = "Linha " . $rowCounter . ": Contagem de colunas inválida. Cabeçalhos: " . count($normalizedHeader) . ", Dados: " . count($row) . ".";
                        continue;
                    }
                    
                    // Combina os cabeçalhos normalizados com os dados da linha
                    $data = array_combine($normalizedHeader, $row);
                    
                    // 1. Extração e Mapeamento dos dados do Aluno
                    $alunoData = $this->extractAlunoData($data);

                    // 2. Validação dos Dados Mapeados
                    // 💡 CORREÇÃO: As regras não têm mais a checagem 'unique' para permitir o Upsert.
                    $validator = Validator::make($alunoData, $this->getAlunoValidationRules());
                    
                    if ($validator->fails()) {
                        $errorRows[] = "Linha " . $rowCounter . ": " . implode('; ', $validator->errors()->all());
                        continue; 
                    }
                    
                    // Se o CPF estiver vazio ou for null, pula a linha, pois não temos chave de busca
                    if (empty($alunoData['cpf'])) {
                        $errorRows[] = "Linha " . $rowCounter . ": O CPF é obrigatório para importação/atualização.";
                        continue;
                    }


                    // 3. Criação/Atualização do Aluno (Upsert)
                    // 💡 CORREÇÃO CRÍTICA: Usa updateOrCreate para evitar erros de CPF duplicado.
                    $aluno = Aluno::updateOrCreate(
                        ['cpf' => $alunoData['cpf']], // Chave de busca (CPF)
                        $alunoData                       // Dados a serem atualizados/inseridos
                    );
                    
                    // 4. Criação dos Familiares
                    $familiaresData = $this->extractFamiliaresData($data);
                    foreach ($familiaresData as $familiarData) {
                        if (!empty($familiarData['nomeCompleto']) && $aluno) {
                             // A lógica de criação de familiares deve ser adicionada aqui se o CSV tiver dados de familiares
                             // $aluno->familiares()->create($familiarData);
                        }
                    }

                    $importedCount++;
                }

                DB::commit();
                fclose($handle);
                
                $message = "Importação concluída! {$importedCount} alunos foram importados/atualizados.";
                if (!empty($errorRows)) {
                    $message .= " Atenção: Houve erros em " . count($errorRows) . " linhas. Veja os detalhes abaixo.";
                }

                return redirect()->route('aluno.index')
                    ->with('success', $message)
                    ->with('import_errors', $errorRows); // Passa a lista de erros para a view

            } catch (\Exception $e) {
                DB::rollBack();
                fclose($handle);
                
                \Log::error('Erro na importação de CSV: ' . $e->getMessage());

                return redirect()->back()
                    ->with('error', "Erro fatal na linha: " . $rowCounter . " - " . $e->getMessage() . ". Nenhuma alteração foi salva.")
                    ->withInput();
            }
        }

        return redirect()->back()->with('error', 'Não foi possível ler o arquivo CSV.');
    }
    
    // ==========================================================
    // REGRAS DE VALIDAÇÃO (Para a Importação)
    // ==========================================================

    /**
     * Retorna o array de regras de validação para o cadastro de aluno (usado na Importação).
     */
    private function getAlunoValidationRules(): array
    {
        return [
            // Requerido no DB
            'nomeCompleto' => 'required|string|max:191',
            'dataNascimento' => 'required|date_format:Y-m-d', // Assumindo o formato pós-conversão
            
            // A chave 'user_id' é essencial para o schema e é requerida
            'user_id' => 'required|integer', 
            
            // 💡 CORREÇÃO: Remove a regra 'unique' do CPF para permitir o updateOrCreate
            'cpf' => 'nullable|string|max:14', 
            'rg' => 'nullable|string|max:20', 
            
            // Regras de Endereço/Contato
            'cep' => 'nullable|string|max:10',
            'email' => 'nullable|string|max:191', // Retirado o 'email' para maior tolerância à importação.
            'celular' => 'nullable|string|max:20',
            
            // Regras Específicas
            'turma_id' => 'nullable|exists:turmas,id', 
            'mao_dominante' => 'nullable|in:destro,canhoto',
            
            // Regras de Valores (Decimais)
            'bolsa_familia' => 'nullable|numeric|min:0',
            // ... adicione regras para todos os seus campos decimais
        ];
    }

    // ==========================================================
    // MÉTODOS AUXILIARES PARA EXTRAÇÃO E MAPEAMENTO
    // ==========================================================
    
    /**
     * Extrai os dados do Aluno do array combinado, mapeando os cabeçalhos do CSV.
     */
    private function extractAlunoData(array $data): array
    {
        // Define a codificação de origem mais provável para CSVs brasileiros não UTF-8
        $from_encoding = 'ISO-8859-1';

        // Helper para converter string e garantir que não é null
        $convert_string = function($value) use ($from_encoding) {
            if (is_null($value)) return null;
            $value = trim($value);
            // Tenta converter se não for UTF-8 válido (evita double-encoding)
            if (!mb_check_encoding($value, 'UTF-8')) {
                $value = mb_convert_encoding($value, 'UTF-8', $from_encoding);
            }
            return $value;
        };

        // 1. TRATAMENTO DA TURMA (Extração do CÓDIGO ALUNO)
        // 💡 CORREÇÃO: Retorno à lógica mais robusta de extração de Turma pelo CÓDIGO ALUNO.
        $codigoAluno = trim($data['CÓDIGO ALUNO'] ?? ''); 
        $turma_id = null;

        if (!empty($codigoAluno)) {
            // 1.1. Extração do Ano, Semestre e Letra usando RegEx (Ex: 251TA1)
            if (preg_match('/^(\d{2})(\d)([A-Z]+)/i', $codigoAluno, $matches)) {
                
                $anoCurto = $matches[1]; // Ex: 25
                $semestre = $matches[2]; // Ex: 1
                $turmaLetras = Str::upper($matches[3]); // Ex: TA 

                // 1.2. Sanitização da Letra
                if (Str::startsWith($turmaLetras, 'T') && strlen($turmaLetras) > 1) {
                    $letra = substr($turmaLetras, -1); // Ex: 'TA' -> 'A'
                } else {
                    $letra = $turmaLetras; 
                }
                
                // 1.3. Formatação do Ano (25 -> 2025)
                $anoLetivo = (int) $anoCurto + 2000;
                
                // 1.4. Busca no Banco de Dados (usando os 3 filtros)
                $turma = Turma::where('ano_letivo', $anoLetivo)
                            ->where('periodo', $semestre)
                            ->where('letra', $letra)
                            ->first(); 

                $turma_id = $turma->id ?? null;
            }
        }
        
        // 2. Mapeamento e Sanitização dos Dados
        $alunoData = [
            // Aplica a conversão em todos os campos de texto livre que podem ter acentuação
            'nomeCompleto' => $convert_string($data['NOME'] ?? null),
            'nomeSocial' => $convert_string($data['NOME SOCIAL'] ?? null),
            
            // CRITICAL FIX: Aplica a conversão ao e-mail para corrigir o erro 1366
            'email' => ($convertedEmail = $convert_string($data['E-MAIL'] ?? null)) ? $convertedEmail : null,
            
            'dataNascimento' => trim($data['DATA NASC.'] ?? null),
            
            'cpf' => preg_replace('/[^0-9]/', '', $data['CPF'] ?? ''), 
            
            'turma_id' => $turma_id,
            
            'rg' => $data['RG'] ?? null,
            // Aplica a conversão de encoding e sanitiza para manter apenas números e hífens
            'cep' => preg_replace('/[^0-9-]/', '', $convert_string($data['CEP'] ?? null)),
            
            'rua' => $convert_string($data['LOGRADOURO'] ?? null),
            'numero' => $data['Nº RES.'] ?? null,
            'complemento' => $convert_string($data['COMPLEMENTO'] ?? null),
            'bairro' => $convert_string($data['BAIRRO'] ?? null),
            'cidade' => $convert_string($data['LOCALIDADE'] ?? null),

            'celular' => $data['CELULAR'] ?? null,
            'telefone' => $data['CEL. RESPONSÁVEL'] ?? null,
            'cursoAtual' => $convert_string($data['ESCOLARIDADE'] ?? null), 
            'periodo' => $data['PERÍODO DA ESCOLA'] ?? null,
            
            'jaTrabalhou' => $data['EM PROCESSO?'] ?? 0, 
            'ctpsAssinada' => $data['CONTRATO'] ?? 0,
            
            'observacoes' => $convert_string($data['Observações da equipe pedagógica'] ?? null),
            // Ex: 'bolsa_familia' => $data['VALOR_BOLSA_FAMILIA'] ?? null, 

            // Inserir user_id obrigatório: Usando o ID do usuário logado (ou 1 se não houver)
            'user_id' => auth()->id() ?? 1, 
        ];
        
        // TRATAMENTO DA DATA DE NASCIMENTO (Formato dd/mm/aaaa -> YYYY-MM-DD)
        if (isset($alunoData['dataNascimento']) && 
            !empty($alunoData['dataNascimento']) &&
            preg_match('/^\d{2}\/\d{2}\/\d{4}$/', trim($alunoData['dataNascimento']))) 
        {
             try {
                $alunoData['dataNascimento'] = Carbon::createFromFormat('d/m/Y', trim($alunoData['dataNascimento']))->format('Y-m-d');
             } catch (\Exception $e) {
                 // A data inválida fará o Validator falhar mais tarde (comportamento desejado)
             }
        }
        
        // Garante que campos obrigatórios/internos que queremos validar ou que são vitais sejam mantidos (mesmo que nulos/vazios)
        return array_filter($alunoData, function ($value, $key) {
            if (in_array($key, ['user_id', 'dataNascimento', 'turma_id'])) {
                return true;
            }
            
            // Para todos os outros campos, remove se forem null ou string vazia.
            return !is_null($value) && $value !== '';
        }, ARRAY_FILTER_USE_BOTH);
    }
    
    /**
     * Extrai os dados dos Familiares. Retorna vazio.
     */
    private function extractFamiliaresData(array $data): array
    {
        return [];
    }
}