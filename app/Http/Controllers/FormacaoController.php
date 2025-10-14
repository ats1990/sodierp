<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection;

class FormacaoController extends Controller
{
    // =========================================================================
    // MÉTODOS DE VISUALIZAÇÃO
    // =========================================================================

    /**
     * Exibe a lista de alunos com filtro para atribuição de turma.
     * Rota: formacao.atribuicao.index
     */
    public function indexAtribuicaoTurmas(Request $request)
    {
        // 1. Obtém o ID da turma para filtro (padrão: 'todos')
        $filtroTurmaId = $request->input('turma_id', 'todos');

        // 2. Inicia a query nos alunos
        $queryAlunos = Aluno::query();

        // 3. Aplica a lógica de filtro
        if ($filtroTurmaId === 'nao_atribuidos') {
            // Filtra alunos onde turma_id é NULL
            $queryAlunos->whereNull('turma_id');
        } elseif ($filtroTurmaId !== 'todos') {
            // Filtra por uma turma específica
            $queryAlunos->where('turma_id', $filtroTurmaId);
        }

        // 4. Busca todos os dados necessários
        $turmas = Turma::withCount('alunos')->get(); // <--- Otimizado para contar alunos no DB
        // Busca os alunos, ordenando por nomeCompleto para melhor visualização
        $alunos = $queryAlunos->orderBy('nomeCompleto')->get();
        
        // 5. Define a Árvore de Navegação (Breadcrumb)
        $breadcrumbs = [
            ['name' => 'Formação', 'route' => 'formacao.turmas.index'], // Rota principal de Formação
            ['name' => 'Turmas', 'route' => 'formacao.turmas.index'], // Você pode ter uma tela de listagem
            ['name' => 'Atribuição Detalhada', 'route' => null] // Tela atual
        ];

        // Passa os dados para a view
        return view('formacao.atribuicao.index', [
            'turmas' => $turmas,
            'alunos' => $alunos,
            'filtroTurmaId' => $filtroTurmaId,
        ]);
    }

    /**
     * Exibe a lista principal de Turmas (Classes).
     * Rota: formacao.turmas.index
     */
    public function indexTurmas(Request $request)
    {
        // Busca todas as turmas, contando quantos alunos estão atribuídos a cada uma
        $turmas = Turma::withCount('alunos')
            ->orderBy('ano_letivo', 'desc')
            ->orderBy('periodo')
            ->orderBy('letra')
            ->get();

        // Retorna a view de índice de turmas, passando os dados
        return view('formacao.turmas.index', compact('turmas'));
    }

    // =========================================================================
    // MÉTODOS DE GERENCIAMENTO DE TURMAS (Criação e Exclusão)
    // =========================================================================

    /**
     * Adiciona uma nova turma. (Criação de Turma Única)
     * Rota: formacao.turmas.store
     */
    public function storeTurmas(Request $request)
    {
        $request->validate([
            'periodo' => 'required|string|max:191',
            'letra' => 'required|string|max:1',
            'ano_letivo' => 'required|integer|min:2020|max:2099',
            'vagas' => 'required|integer|min:1|max:200',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
        ]);

        try {
            Turma::create($request->all());

            return redirect()->route('formacao.turmas.index')->with('success', 'Turma criada com sucesso!');
        } catch (\Exception $e) {
            \Log::error("Erro ao criar turma: " . $e->getMessage());
            return redirect()->route('formacao.turmas.index')->with('error', 'Falha ao criar turma. Tente novamente.');
        }
    }

    /**
     * Cria múltiplas turmas de uma vez (Bulk Store).
     * Rota: formacao.turmas.storeBulk
     */
    public function storeBulk(Request $request)
    {
        // 1. Validação dos campos consolidados
        $request->validate([
            'ano_letivo' => 'required|integer|min:2000|max:2050',
            'vagas_geral' => 'required|integer|min:1|max:200',
            'quantidade_manha' => 'nullable|integer|min:0',
            'quantidade_tarde' => 'nullable|integer|min:0',
            'quantidade_noite' => 'nullable|integer|min:0',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
        ]);

        // 2. Coleta e default dos valores 
        $qManha = (int) $request->input('quantidade_manha', 0);
        $qTarde = (int) $request->input('quantidade_tarde', 0);
        $qNoite = (int) $request->input('quantidade_noite', 0);

        // 3. Verifica se pelo menos uma turma será criada
        if ($qManha + $qTarde + $qNoite === 0) {
            throw ValidationException::withMessages([
                'quantidade_manha' => 'Você deve especificar a criação de pelo menos uma turma em qualquer período (Manhã, Tarde ou Noite).',
            ]);
        }

        $turmasData = [];
        $letras = range('A', 'Z');
        $letrasIndex = 0;

        $baseData = [
            'ano_letivo' => $request->ano_letivo,
            'vagas' => $request->vagas_geral,
            'data_inicio' => $request->data_inicio,
            'data_fim' => $request->data_fim,
        ];

        // Função auxiliar para gerar turmas por período
        $gerarTurmas = function ($periodo, $quantidade, &$letrasIndex, $baseData) use ($letras, &$turmasData) {
            for ($i = 0; $i < $quantidade; $i++) {
                if ($letrasIndex >= count($letras)) {
                    \Log::warning("Limite de letras atingido na criação em lote.");
                    break;
                }

                $turmasData[] = array_merge($baseData, [
                    'periodo' => $periodo,
                    'letra' => $letras[$letrasIndex++],
                ]);
            }
        };

        // 4. Gera os dados das turmas sequencialmente
        $gerarTurmas('Manhã', $qManha, $letrasIndex, $baseData);
        $gerarTurmas('Tarde', $qTarde, $letrasIndex, $baseData);
        $gerarTurmas('Noite', $qNoite, $letrasIndex, $baseData);


        // 5. Criação em Massa (usando transação)
        DB::beginTransaction();
        try {
            $turmasCriadas = 0;

            foreach ($turmasData as $data) {
                Turma::create($data);
                $turmasCriadas++;
            }

            DB::commit();

            return redirect()->route('formacao.turmas.index')
                ->with('success', "$turmasCriadas turmas foram criadas em lote com sucesso!");
        } catch (ValidationException $e) {
            DB::rollBack();
            // CORREÇÃO: Adiciona a flag de sessão para reabrir o modal SOMENTE em erro de validação
            return redirect()->back()->withErrors($e->errors())->withInput()->with('open_create_turma_modal', true);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Erro ao criar turmas em lote: " . $e->getMessage());

            return redirect()->route('formacao.turmas.index')
                ->with('error', 'Falha ao criar turmas em lote. Tente novamente.');
        }
    }


    /**
     * Exclui TODAS as turmas e desvincula os alunos.
     * Rota: formacao.turmas.destroyAll
     */
    public function destroyAllTurmas(Request $request)
    {
        DB::beginTransaction();
        try {
            // 1. Desvincula todos os alunos (seta turma_id para NULL)
            Aluno::whereNotNull('turma_id')->update(['turma_id' => null]);

            // 2. Exclui todas as turmas
            $count = Turma::count();
            // Turma::truncate(); // LINHA ORIGINAL (CAUSA O ERRO DE TRANSAÇÃO)
            Turma::query()->delete(); // CORREÇÃO: Usa DELETE FROM, respeitando o DB::beginTransaction()

            DB::commit();

            return redirect()->route('formacao.turmas.index')
                ->with('success', "Todas as $count turmas foram excluídas e os alunos desvinculados com sucesso.");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Falha ao excluir todas as turmas: " . $e->getMessage());

            return redirect()->route('formacao.turmas.index')
                ->with('error', 'Erro ao excluir todas as turmas. Detalhes: ' . $e->getMessage());
        }
    }

    /**
     * Exclui uma turma específica e desvincula seus alunos.
     * Rota: formacao.turmas.destroy
     */
    public function destroyTurma(Turma $turma)
    {
        DB::beginTransaction();
        try {
            $turmaNome = "{$turma->letra} - {$turma->periodo} ({$turma->ano_letivo})";

            // 1. Desvincula os alunos desta turma (seta turma_id para NULL)
            Aluno::where('turma_id', $turma->id)->update(['turma_id' => null]);

            // 2. Exclui a turma
            $turma->delete();

            DB::commit();

            return redirect()->route('formacao.turmas.index')
                ->with('success', "A turma '{$turmaNome}' foi excluída e seus alunos foram desvinculados.");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Falha ao excluir a turma {$turma->id}: " . $e->getMessage());

            return redirect()->route('formacao.turmas.index')
                ->with('error', "Erro ao excluir a turma 'ID: {$turma->id}'. Detalhes: " . $e->getMessage());
        }
    }

    // =========================================================================
    // MÉTODOS DE ATRIBUIÇÃO
    // =========================================================================

    /**
     * Processa a submissão do formulário de atualização em massa (Bulk Update).
     * Rota: formacao.atribuicao.bulkUpdate
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate(['alunos' => 'required|array',]);

        $alunosParaAtualizar = $request->input('alunos');
        $alunosIds = array_keys($alunosParaAtualizar);
        $totalAtualizados = 0;

        $alunos = Aluno::whereIn('id', $alunosIds)->get()->keyBy('id');

        DB::beginTransaction();
        try {
            foreach ($alunosParaAtualizar as $alunoId => $turmaId) {
                $newTurmaId = ($turmaId == 0) ? null : (int)$turmaId;

                if ($aluno = $alunos->get($alunoId)) {
                    if ($aluno->turma_id !== $newTurmaId) {
                        $aluno->turma_id = $newTurmaId;
                        $aluno->save();
                        $totalAtualizados++;
                    }
                }
            }

            DB::commit();

            return redirect()->route('formacao.atribuicao.index', ['turma_id' => $request->input('turma_id')])
                ->with('success', "As atribuições de turma foram salvas com sucesso! Total de $totalAtualizados alunos atualizados.");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Falha na atualização em massa de atribuição de alunos: " . $e->getMessage());

            return redirect()->route('formacao.atribuicao.index', ['turma_id' => $request->input('turma_id')])
                ->with('error', 'Erro ao salvar as atribuições de turma. Detalhes: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------------------------
    // MÉTODOS DE ATRIBUIÇÃO INDIVIDUAL/LÓGICA
    // ---------------------------------------------------------------------------------

    /**
     * Auxiliar para determinar os períodos de contraturno para um turno atual.
     * @param string|null $currentTurno O turno atual do aluno ('Manhã', 'Tarde', 'Noite').
     * @return array Os períodos de contraturno elegíveis, priorizados por Manhã > Tarde > Noite.
     */
    private function getContraturnoPeriods(?string $currentTurno): array
    {
        $allPeriods = ['Manhã', 'Tarde', 'Noite'];

        // Se o turno atual for inválido ou nulo (o mais provável problema de atribuição), 
        // o aluno é elegível para TODOS os turnos, evitando ser barrado.
        if (empty($currentTurno) || !in_array($currentTurno, $allPeriods)) {
            if (!empty($currentTurno)) {
                \Log::warning("Aluno encontrado com turnoAtual ('{$currentTurno}') inválido/vazio. Aplicando fallback para todos os contraturnos.");
            }
            return $allPeriods;
        }

        // Filtra os contraturnos
        $contraturnoPeriods = [];
        foreach ($allPeriods as $period) {
            if ($period !== $currentTurno) {
                $contraturnoPeriods[] = $period;
            }
        }

        // Garante a ordem de prioridade (Manhã, Tarde, Noite) para homogeneidade
        usort($contraturnoPeriods, function ($a, $b) {
            $order = ['Manhã' => 1, 'Tarde' => 2, 'Noite' => 3];
            return ($order[$a] ?? 99) <=> ($order[$b] ?? 99);
        });

        return $contraturnoPeriods;
    }

    /**
     * Se esta rota fosse chamada (GET), ela provavelmente mostraria o modal.
     * Rota: formacao.turmas.logica
     */
    public function showAtribuicaoRapidaLogica(Request $request)
    {
        return redirect()->route('formacao.atribuicao.index', $request->query());
    }

    /**
     * Executa a lógica de Atribuição Rápida Inteligente.
     * Rota: formacao.turmas.atribuir (POST)
     */
    public function atribuirAlunoTurma(Request $request)
    {
        // 1. Validação dos inputs do modal
        $request->validate([
            'atribuir_canhotos_separadamente' => 'nullable|in:1,0',
            'turma_canhoto_manha' => 'nullable|exists:turmas,id',
            'turma_canhoto_tarde' => 'nullable|exists:turmas,id',
        ]);

        DB::beginTransaction();
        try {
            // 2. Coletar Turmas e Alunos
            $turmas = Turma::with('alunos')->get()->keyBy('id');

            // Busca alunos não atribuídos, ordenados alfabeticamente para homogeneidade
            $alunosNaoAtribuidos = Aluno::whereNull('turma_id')
                ->orderBy('nomeCompleto')
                ->get();

            $canhotos = $alunosNaoAtribuidos->where('canhoto', 1);
            $restantes = $alunosNaoAtribuidos->where('canhoto', 0);

            $alunosToSave = new Collection(); // Coleção para armazenar alunos modificados
            $turmasDisponiveis = $turmas->pluck('id')->toArray();

            // =======================================================
            // 3. ATRIBUIÇÃO DE CANHOTOS (Prioridade 1 - Turmas Reservadas)
            // =======================================================
            $atribuirCanhotosSeparadamente = (bool) $request->input('atribuir_canhotos_separadamente', false);

            if ($atribuirCanhotosSeparadamente && $canhotos->isNotEmpty()) {
                $turmasCanhotos = [];
                $reservadasIds = [];

                if ($turmaIdManha = $request->input('turma_canhoto_manha')) {
                    $turmasCanhotos[$turmaIdManha] = $turmas->get($turmaIdManha);
                    $reservadasIds[] = (int)$turmaIdManha;
                }
                if ($turmaIdTarde = $request->input('turma_canhoto_tarde')) {
                    $turmasCanhotos[$turmaIdTarde] = $turmas->get($turmaIdTarde);
                    $reservadasIds[] = (int)$turmaIdTarde;
                }

                $turmasDisponiveis = array_diff($turmasDisponiveis, $reservadasIds);

                if (!empty($turmasCanhotos)) {
                    $turmasCanhotosIndex = array_values($turmasCanhotos);
                    $turmaAtualIndex = 0;

                    foreach ($canhotos as $aluno) {
                        $turmaAtual = $turmasCanhotosIndex[$turmaAtualIndex];
                        $vagasOcupadas = $turmaAtual->alunos->count();

                        if ($vagasOcupadas < $turmaAtual->vagas) {
                            $aluno->turma_id = $turmaAtual->id;

                            // Adiciona para salvar e atualiza a contagem in-memory
                            $alunosToSave->push($aluno);
                            $turmaAtual->alunos->push($aluno);
                        } else {
                            // Turma reservada cheia: canhoto vai para o grupo de restantes
                            $restantes->push($aluno);
                        }

                        $turmaAtualIndex = ($turmaAtualIndex + 1) % count($turmasCanhotosIndex);
                    }
                } else {
                    // Nenhuma turma reservada selecionada
                    $restantes = $restantes->merge($canhotos);
                }
            } else {
                // Opção de separação não marcada
                $restantes = $restantes->merge($canhotos);
            }

            // =======================================================
            // 4. ATRIBUIÇÃO HOMOGÊNEA DOS RESTANTES (Contraturno e Homogeneidade)
            // =======================================================

            if ($restantes->isNotEmpty()) {
                $turmasRegulares = $turmas->only($turmasDisponiveis);

                foreach ($restantes as $aluno) {

                    // 4.1 Determinar o Contraturno (com fallback se turnoAtual estiver vazio)
                    $contraturnoPeriods = $this->getContraturnoPeriods($aluno->turnoAtual);

                    // 4.2 Encontra a turma regular que é Contraturno, tem vagas e a menor lotação
                    $turmaMenorLotacao = $turmasRegulares
                        ->filter(function ($turma) use ($contraturnoPeriods) {
                            return $turma->alunos->count() < $turma->vagas &&
                                in_array($turma->periodo, $contraturnoPeriods);
                        })
                        ->sortBy(function ($turma) {
                            return $turma->alunos->count(); // Ordena pela menor lotação (homogeneidade)
                        })
                        ->first();

                    if ($turmaMenorLotacao) {
                        $aluno->turma_id = $turmaMenorLotacao->id;

                        // Adiciona para salvar e atualiza a contagem in-memory
                        $alunosToSave->push($aluno);
                        $turmaMenorLotacao->alunos->push($aluno);
                    } else {
                        // Não encontrou turma (cheia ou sem contraturno adequado)
                        \Log::info("Aluno '{$aluno->nomeCompleto}' (Turno: {$aluno->turnoAtual}) não atribuído. Turmas lotadas ou sem contraturno disponível.");
                        continue;
                    }
                }
            }

            // =======================================================
            // 5. SALVAMENTO EM MASSA E COMMIT
            // =======================================================
            if ($alunosToSave->isNotEmpty()) {
                foreach ($alunosToSave as $aluno) {
                    $aluno->save(); // Salva todos os alunos atribuídos
                }
            }

            $alunosAtribuidosCount = $alunosToSave->count();
            DB::commit();

            if ($alunosAtribuidosCount > 0) {
                // CORREÇÃO: Volta para a página anterior, onde o modal foi aberto.
                return redirect()->back()
                    ->with('success', "Atribuição Rápida concluída! **$alunosAtribuidosCount** alunos foram distribuídos no contraturno.");
            } else {
                // CORREÇÃO: Volta para a página anterior, onde o modal foi aberto.
                return redirect()->back()
                    ->with('warning', "Atribuição Rápida concluída, mas nenhum aluno pôde ser distribuído (falta de vagas, conflito de contraturno, ou não haviam alunos sem turma).");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Erro CRÍTICO na Atribuição Lógica: " . $e->getMessage());

            // CORREÇÃO: Volta para a página anterior, onde o modal foi aberto.
            return redirect()->back()
                ->with('error', 'Falha grave na Atribuição Rápida Inteligente. Por favor, verifique o log para detalhes técnicos: ' . $e->getMessage());
        }
    }

    /**
     * Método de atualização individual (via AJAX/POST, mantido por referência).
     * CORRIGIDO: Removido caractere inválido no início da linha.
     */
    public function updateAtribuicaoAluno(Request $request, Aluno $aluno)
    {
        $newTurmaId = $request->input('turma_id');

        try {
            $aluno->turma_id = ($newTurmaId == '0' || $newTurmaId === null) ? null : (int)$newTurmaId;
            $aluno->save();

            $turmaNome = $aluno->turma ? $aluno->turma->nome : 'NÃO ATRIBUÍDO';
            $turmaIdRetorno = $aluno->turma_id ?? 0;

            return response()->json([
                'success' => true,
                'message' => 'Turma atribuída com sucesso.',
                'turma_nome' => $turmaNome,
                'turma_id' => $turmaIdRetorno
            ]);
        } catch (\Exception $e) {
            \Log::error("Erro ao salvar atribuição para aluno {$aluno->id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Verifique o log para mais detalhes.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // =========================================================================
    // MÉTODOS DE PLACEHOLDER PARA ROTAS (Formação)
    // =========================================================================
    public function indexNotas()
    {
        throw new \Exception('Método indexNotas não implementado.');
    }
    public function indexBoletim()
    {
        throw new \Exception('Método indexBoletim não implementado.');
    }
    public function indexCertificado()
    {
        throw new \Exception('Método indexCertificado não implementado.');
    }
    public function indexImportar()
    {
        throw new \Exception('Método indexImportar não implementado.');
    }
}
