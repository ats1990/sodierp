<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
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

        // Ler primeira linha raw para detectar delimitador (tenta , ; ou tab)
        $firstLine = null;
        $fp = fopen($filePath, 'r');
        if ($fp === false) {
            return redirect()->back()->with('error', 'Não foi possível abrir o arquivo para leitura.');
        }
        $firstLine = fgets($fp);
        rewind($fp);

        if ($firstLine === false) {
            fclose($fp);
            return redirect()->back()->with('error', 'O arquivo CSV está vazio ou inválido.');
        }

        $delimiter = $this->detectDelimiter($firstLine);

        // Lê o cabeçalho com o delimitador detectado
        $header = fgetcsv($fp, 0, $delimiter);
        if (!$header) {
            fclose($fp);
            return redirect()->back()->with('error', 'Não foi possível ler o cabeçalho do CSV.');
        }

        // Normaliza cabeçalho (CORREÇÃO AQUI): Usamos Str::slug antes de Str::snake para limpar melhor
        $normalizedHeader = array_map(fn($col) => Str::snake(Str::slug(trim($col), '_')), $header); 
        
        // Mapeamento manual (com variações)
        $manualMap = [
            // CÓDIGO / MATRÍCULA
            'codigo_aluno'      => 'codigo_matricula',
            'codigo'            => 'codigo_matricula',
            'cod'               => 'codigo_matricula',
            'matricula'         => 'codigo_matricula',
            'cod_matricula'     => 'codigo_matricula',
            'codigo_matricula'  => 'codigo_matricula',
            'c_o_d_i_g_o_a_l_u_n_o' => 'codigo_matricula', 

            // NOME
            'nome'              => 'nomeCompleto',
            'nome_completo'     => 'nomeCompleto',
            'aluno'             => 'nomeCompleto',
            'n_o_m_e'           => 'nomeCompleto', 

            // NOME SOCIAL
            'nome_social'       => 'nomeSocial',
            'n_o_m_e_s_o_c_i_a_l' => 'nomeSocial',

            // STATUS / TURMA / NÚMERO
            'status'            => 'status',
            't_u_r_m_a'         => 'turma_id', // Para 'TURMA' (Item 1)
            'turma'             => 'turma_id',
            'n'                 => 'numero', // Para 'Nº' (Item 2)
            'numero'            => 'numero',

            // EMPRESAS / CONTRATO / E-MAIL / IDENTIDADE / SEXO
            'empresas_que_ja_foi_encaminhado' => 'empresa_encaminhada',
            'e_m_p_r_e_s_a_s_q_u_e_j_f_o_i_e_n_c_a_m_i_n_h_a_d_o' => 'empresa_encaminhada',
            'em_processo'       => 'em_processo',
            'em_processo_'      => 'em_processo', // Para 'EM PROCESSO?'
            'e_m_p_r_o_c_e_s_s_o' => 'em_processo',
            'contrato'          => 'contrato',
            'c_o_n_t_r_a_t_o'   => 'contrato',
            'e_mail'            => 'email',
            'e_m_a_i_l'         => 'email', // Para 'e_mail' e 'e-_m_a_i_l'
            'email'             => 'email',
            'identidade_de_genero' => 'identidade_genero',
            'i_d_e_n_t_i_d_a_d_e_d_e_g_e_n_e_r_o' => 'identidade_genero',
            'sexo'              => 'sexo',
            's_e_x_o'           => 'sexo',

            // ESCOLARIDADE / PERÍODO
            'escolaridade'      => 'escolaridade',
            'e_s_c_o_l_a_r_i_d_a_d_e' => 'escolaridade',
            'periodo_da_escola' => 'periodo_escolar',
            'p_e_r_o_d_o_d_a_e_s_c_o_l_a' => 'periodo_escolar',
            'periodo_escolar'   => 'periodo_escolar',

            // RG / CPF / DATA NASCIMENTO
            'rg'                => 'rg',
            'r_g'               => 'rg',
            'cpf'               => 'cpf',
            'c_p_f'             => 'cpf',
            'data_nasc'         => 'dataNascimento',
            'data_nascimento'   => 'dataNascimento',
            'dt_nasc'           => 'dataNascimento',
            'dtnasc'            => 'dataNascimento',
            'data_nasc_'        => 'dataNascimento', 
            'nascimento'        => 'dataNascimento',
            'nasc'              => 'dataNascimento',
            'd_a_t_a_n_a_s_c'   => 'dataNascimento', // <--- Mapeamento para 'd_a_t_a_n_a_s_c.' (Item 16)
            
            // IDADE
            'idade'             => 'idade',
            'i_d_a_d_e'         => 'idade',
            'idade_e_mes'       => 'idade_meses',
            'idade_e_meses'     => 'idade_meses',
            'i_d_a_d_e_e_m_s'   => 'idade_meses',

            // ENDEREÇO
            'cep'               => 'cep',
            'c_e_p'             => 'cep',
            'logradouro'        => 'logradouro',
            'l_o_g_r_a_d_o_u_r_o' => 'logradouro',
            'n_res'             => 'numero_residencia',
            'n_r_e_s'           => 'numero_residencia', // Para 'n_r_e_s.'
            'numero_residencia' => 'numero_residencia',
            'complemento'       => 'complemento',
            'c_o_m_p_l_e_m_e_n_t_o' => 'complemento',
            'localidade'        => 'cidade',
            'l_o_c_a_l_i_d_a_d_e' => 'cidade',
            'cidade'            => 'cidade',
            'bairro'            => 'bairro',
            'b_a_i_r_r_o'       => 'bairro',

            // CONTATOS / RESPONSÁVEL
            'celular'           => 'celular',
            'c_e_l_u_l_a_r'     => 'celular',
            'cel_responsavel'   => 'celular_responsavel',
            'c_e_l_r_e_s_p_o_n_s_v_e_l' => 'celular_responsavel', // Para 'c_e_l._r_e_s_p_o_n_s_v_e_l'
            'celular_responsavel'=> 'celular_responsavel',
            'nome_do_responsavel' => 'nome_responsavel',
            'n_o_m_e_d_o_r_e_s_p_o_n_s_v_e_l' => 'nome_responsavel',
            'nome_para_recado'  => 'nome_recado',
            'n_o_m_e_p_a_r_a_r_e_c_a_d_o' => 'nome_recado',
            'tel_recado'        => 'telefone_recado',
            't_e_l_r_e_c_a_d_o' => 'telefone_recado', // Para 't_e_l._r_e_c_a_d_o'
            'telefone_recado'   => 'telefone_recado',

            // CURSOS / PERGUNTAS (Estes já estão com a normalização bizarra)
            'voc_est_cursando_ou_j_fez_algum_curso_relacionado_as_disciplinas_da_sodiprom' => 'curso_relacionado_sodiprom',
            'possui_algum_curso_qual' => 'curso_extra',

            // NOTAS / AVALIAÇÕES
            'feira'             => 'nota_feira',
            'infor'             => 'nota_informatica',
            'i_n_f_o_r'         => 'nota_informatica', // Para 'infor.'
            'informatica'       => 'nota_informatica',
            'log'               => 'nota_logistica',
            'l_o_g'             => 'nota_logistica', // Para 'log.'
            'logistica'         => 'nota_logistica',
            'mat'               => 'nota_matematica',
            'm_a_t'             => 'nota_matematica',
            'matematica'        => 'nota_matematica',
            'rh'                => 'nota_rh',
            'r_h'               => 'nota_rh',
            'ta'                => 'nota_ta',
            't_a'               => 'nota_ta',
            'm_d_i_a_f_i_n_a_l_d_i_s_c_i_p_l_i_n_a_s' => 'media_final_disciplinas',
            'm_d_i_a_f_i_n_a_l_c_o_m_p_o_r_t_a_m_e_n_t_o' => 'media_final_comportamento',

            // OUTROS
            'coluna1'           => 'coluna1',
            's_e_l_o'           => 'selo',
            'observacoes_da_equipe_pedagogica' => 'observacoes_pedagogicas',
            'o_c_o_r_r_n_c_i_a_s' => 'ocorrencias',
            's_u_g_e_s_t_e_s_d_e_r_e_a_s_d_e_a_t_u_a_o' => 'areas_atuacao',
        ];

        // Construir mapeamento final índice -> coluna DB
        $columnMap = [];
        foreach ($normalizedHeader as $idx => $col) {
            $dbColName = $manualMap[$col] ?? $col;
            $columnMap[$idx] = $dbColName;
        }

        // GARANTIR que variações comuns sejam transformadas nos nomes obrigatórios esperados
        $required = ['codigo_matricula', 'nomeCompleto', 'dataNascimento'];

        // Reverse map: para cada nome DB, lista de possíveis normalized headers que o representam
        $reverse = $this->buildReverseMap($manualMap);

        // Tenta corrigir automaticamente columnMap quando existirem variações
        $columnMap = $this->normalizeRequiredColumns($columnMap, $normalizedHeader, $reverse, $required);

        // Verifica novamente
        $missing = array_diff($required, array_values($columnMap));
        if (!empty($missing)) {
            fclose($fp);
            return back()->with('error', 'O CSV está faltando colunas obrigatórias: ' . implode(', ', $missing));
        }

        // PROCESSAR LINHAS
        $results = ['success' => 0, 'updated' => 0, 'errors' => []];
        $lineNumber = 1;

        while (($row = fgetcsv($fp, 0, $delimiter)) !== false) {
            $lineNumber++;
            
            // 🚨 CORREÇÃO DE CODIFICAÇÃO: Converte a linha de ISO-8859-1 (Latin1) para UTF-8.
            // Isso resolve o erro 'Incorrect string value'.
            $row = array_map(function($value) {
                if (!empty($value) && !mb_check_encoding($value, 'UTF-8')) {
                    // Tenta converter de Latin1 (ISO-8859-1) para UTF-8
                    return iconv('ISO-8859-1', 'UTF-8//IGNORE', $value); 
                }
                return $value;
            }, $row);
            // --------------------------------------------------------------------------

            $data = [];
            foreach ($columnMap as $i => $dbColumn) {
                $data[$dbColumn] = trim($row[$i] ?? '');
            }
            if (empty(array_filter($data))) continue;

            $codigo = $data['codigo_matricula'] ?? null;
            if (!$codigo) {
                $results['errors'][] = "Linha {$lineNumber}: faltando código de matrícula.";
                continue;
            }

            $response = $this->processAlunoLine($data, $codigo, $lineNumber);
            if ($response['status'] === 'created') $results['success']++;
            if ($response['status'] === 'updated') $results['updated']++;
            if ($response['status'] === 'error') $results['errors'] = array_merge($results['errors'], $response['errors']);
        }

        fclose($fp);

        $msg = "Importação concluída: {$results['success']} criados, {$results['updated']} atualizados.";

        return !empty($results['errors'])
            ? back()->with('error', $msg)->with('import_errors', $results['errors'])
            : back()->with('success', $msg);
    }

    /**
     * Tenta vários formatos para a data e retorna Carbon ou null.
     */
    protected function parseDateFlexible(?string $value)
    {
        if (empty($value)) return null;

        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'd/m/y', 'Y/m/d'];
        foreach ($formats as $fmt) {
            try {
                $d = Carbon::createFromFormat($fmt, $value);
                if ($d !== false) return $d;
            } catch (\Exception $e) {
                // continua
            }
        }

        // Tenta strtotime como último recurso
        try {
            $ts = strtotime($value);
            if ($ts !== false) return Carbon::createFromTimestamp($ts);
        } catch (\Exception $e) {
            // nada
        }

        return null;
    }

    /**
     * Processa criação/atualização do aluno.
     */
    protected function processAlunoLine(array $data, string $codigoMatricula, int $lineNumber): array
    {
        $errors = [];
        try {
            // ✅ CÓDIGO CORRIGIDO: Encontra a turma existente ou CRIA a turma histórica automaticamente
            $turmaId = Turma::findOrCreateTurmaIdByCodigoAluno($codigoMatricula); 
            
            if (!$turmaId) {
                 $errors[] = "Linha {$lineNumber} (Código: {$codigoMatricula}): Falha na identificação/criação da Turma Histórica. Verifique se o código de matrícula é válido ou se há campos obrigatórios faltando na tabela Turmas.";
                 return ['status' => 'error', 'errors' => $errors];
            }
            $data['turma_id'] = $turmaId;

            // tratamento de data de nascimento
            $dataNascInput = $data['dataNascimento'] ?? '';
            
            // 🚨 CORREÇÃO PARA #N/D E OUTROS PLACEHOLDERS 🚨
            $cleanedDate = strtoupper(trim($dataNascInput));
            // Lista de strings que devem ser consideradas "vazias" ou "não informadas"
            $invalidPlaceholders = ['#N/D', 'N/D', 'N/A', 'NA', 'NI', 'NDA', 'NÃO INFORMADO', 'DESCONHECIDO', ''];

            if (in_array($cleanedDate, $invalidPlaceholders)) {
                $data['dataNascimento'] = null; // Trata o placeholder como valor vazio/nulo
            }
            // ----------------------------------------------------

            if (!empty($data['dataNascimento'])) {
                $date = $this->parseDateFlexible($data['dataNascimento']);
                if ($date) {
                    $data['dataNascimento'] = $date->format('Y-m-d');
                } else {
                    $errors[] = "Linha {$lineNumber}: Data de nascimento inválida ('{$data['dataNascimento']}').";
                }
            } 
            
            // Validação final de obrigatoriedade
            if (empty($data['dataNascimento'])) {
                $errors[] = "Linha {$lineNumber}: Data de nascimento obrigatória.";
            }

            if (!empty($errors)) return ['status' => 'error', 'errors' => $errors];

            $validator = Validator::make($data, [
                'codigo_matricula' => 'required|string|max:100',
                'nomeCompleto' => 'required|string|max:191',
                'dataNascimento' => 'required|date',
            ]);

            if ($validator->fails()) {
                $errors[] = "Linha {$lineNumber}: Erros de validação - " . implode(', ', $validator->errors()->all());
                return ['status' => 'error', 'errors' => $errors];
            }

            // remover campos que não existem na migration (exemplo)
            unset($data['idade']);

            $aluno = Aluno::where('codigo_matricula', $codigoMatricula)->first();

            if ($aluno) {
                $aluno->update($data);
                return ['status' => 'updated', 'errors' => []];
            } else {
                Aluno::create($data);
                return ['status' => 'created', 'errors' => []];
            }
        } catch (\Exception $e) {
            Log::error("Erro de Importação na Linha {$lineNumber} para Código {$codigoMatricula}: " . $e->getMessage());
            $errors[] = "Linha {$lineNumber} (Código: {$codigoMatricula}): Erro fatal ao salvar: " . $e->getMessage();
            return ['status' => 'error', 'errors' => $errors];
        }
    }

    /**
     * Detecta delimitador da primeira linha: tenta vírgula, ponto-e-vírgula e tab.
     */
    protected function detectDelimiter(string $line): string
    {
        $candidates = [',', ';', "\t"];
        $best = ',';
        $bestCount = 0;
        foreach ($candidates as $c) {
            $count = substr_count($line, $c);
            if ($count > $bestCount) {
                $bestCount = $count;
                $best = $c;
            }
        }
        return $best;
    }

    /**
     * Constrói um mapa reverso DBname => [possible normalized headers]
     */
    protected function buildReverseMap(array $manualMap): array
    {
        $reverse = [];
        foreach ($manualMap as $normalized => $dbName) {
            $reverse[$dbName] = $reverse[$dbName] ?? [];
            $reverse[$dbName][] = $normalized;
        }
        return $reverse;
    }

    /**
     * Garante que as colunas obrigatórias estejam disponíveis no columnMap,
     * tentando "casar" variações comuns encontradas no header.
     */
    protected function normalizeRequiredColumns(array $columnMap, array $normalizedHeader, array $reverseMap, array $required): array
    {
        $values = array_values($columnMap);

        foreach ($required as $req) {
            // Se a coluna obrigatória já está mapeada, pule
            if (in_array($req, $values, true)) {
                continue;
            }

            // 1. Tentar mapeamento reverso direto (mapeamentos manuais)
            $candidates = $reverseMap[$req] ?? [];
            foreach ($candidates as $cand) {
                $idx = array_search($cand, $normalizedHeader, true);
                if ($idx !== false) {
                    $columnMap[$idx] = $req;
                    $values = array_values($columnMap);
                    continue 2;
                }
            }

            // 2. Tentar heurística de substring (se falhou o mapeamento direto)
            foreach ($normalizedHeader as $idxH => $h) {
                if ($columnMap[$idxH] === $h) {

                    // Heurística para codigo_matricula (procura por cod ou matric)
                    if ($req === 'codigo_matricula' && (str_contains($h, 'cod') || str_contains($h, 'matric'))) {
                        $columnMap[$idxH] = $req;
                        $values = array_values($columnMap);
                        continue 2;
                    }

                    // Heurística para nomeCompleto (procura por nome)
                    if ($req === 'nomeCompleto' && str_contains($h, 'nome') && !str_contains($h, 'social')) {
                        $columnMap[$idxH] = $req;
                        $values = array_values($columnMap);
                        continue 2;
                    }

                    // Heurística para dataNascimento (procura por data e nasc)
                    if ($req === 'dataNascimento' && str_contains($h, 'data') && str_contains($h, 'nasc')) {
                        $columnMap[$idxH] = $req;
                        $values = array_values($columnMap);
                        continue 2;
                    }
                }
            }
        }

        return $columnMap;
    }
}