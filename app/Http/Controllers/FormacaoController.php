<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use App\Models\Aluno; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; 

class FormacaoController extends Controller
{
    /**
     * Exibe a lista de turmas (Index).
     */
    public function indexTurmas()
    {
        $turmas = Turma::orderBy('ano_letivo', 'desc')
                        ->orderBy('periodo', 'asc')
                        ->orderBy('letra', 'asc')
                        ->get();
        
        // NOVO: Pega os alunos que ainda não estão atribuídos a uma turma (turma_id IS NULL)
        // Isso simula o critério de "aprovado/disponível para formação básica"
        $alunosDisponiveis = Aluno::whereNull('turma_id')
                                  ->orderBy('nomeCompleto', 'asc')
                                  ->get();

        // Passa as turmas e os alunos disponíveis para a view
        return view('formacao.turmas.index', compact('turmas', 'alunosDisponiveis')); 
    }

    /**
     * Exibe a lista de alunos para atribuição de turmas e as turmas disponíveis.
     */
    public function indexAtribuicaoTurmas()
    {
        // Pega todos os alunos, com paginação e carregando a relação da turma
        $alunos = Aluno::with('turma')
                       ->orderBy('nomeCompleto', 'asc')
                       ->paginate(15); 

        // Turmas disponíveis para atribuição, ordenadas para o select
        $turmas = Turma::orderBy('ano_letivo', 'desc')
                       ->orderBy('periodo', 'asc')
                       ->get();

        // RETORNO ATUALIZADO: Usando a nova pasta 'atribuicao'
        return view('formacao.atribuicao.index', compact('alunos', 'turmas'));
    }

    /**
     * Atribui um aluno a uma turma. (Usado tanto pelo modal quanto pela view de atribuicao)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function atribuirTurma(Request $request)
    {
        $request->validate([
            'aluno_id' => 'required|exists:alunos,id',
            'turma_id' => 'required|exists:turmas,id',
        ]);

        try {
            // Busca o aluno e a turma
            $aluno = Aluno::findOrFail($request->aluno_id);
            $turma = Turma::findOrFail($request->turma_id);

            // Atualiza o aluno com o turma_id
            $aluno->turma_id = $request->turma_id;
            $aluno->save();

            // Redireciona com mensagem de sucesso
            // Verifica de qual rota veio o request para redirecionar corretamente
            $routeName = Str::contains($request->session()->previousUrl(), 'atribuicao') 
                            ? 'formacao.atribuicao.index' 
                            : 'formacao.turmas.index';

            return redirect()->route($routeName)
                             ->with('success', "Aluno {$aluno->nomeCompleto} atribuído à turma {$turma->nome_completo} com sucesso.");
        } catch (\Exception $e) {
            Log::error('Erro ao atribuir turma ao aluno: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocorreu um erro durante a atribuição de turma. Tente novamente.');
        }
    }


    /**
     * Armazena uma nova(s) turma(s) com sequenciamento automático de letras.
     */
    public function storeTurmas(Request $request)
    {
        // 1. Validação dos dados de entrada (incluindo as datas)
        $request->validate([
            'ano_letivo' => 'required|integer|digits:4',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'vagas_por_turma' => 'required|integer|min:1',
            'qtd_manha' => 'nullable|integer|min:0|max:10',
            'qtd_tarde' => 'nullable|integer|min:0|max:10',
            'qtd_noite' => 'nullable|integer|min:0|max:10',
        ]);

        $anoLetivo = $request->input('ano_letivo');
        $vagas = $request->input('vagas_por_turma');
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        // Mapa de períodos e suas quantidades
        $periodos = [
            'Manhã' => $request->input('qtd_manha', 0),
            'Tarde' => $request->input('qtd_tarde', 0),
            'Noite' => $request->input('qtd_noite', 0),
        ];

        $turmasCriadas = 0;

        // Garante que todas as turmas sejam criadas ou nenhuma seja (transação)
        DB::transaction(function () use ($periodos, $anoLetivo, $vagas, $dataInicio, $dataFim, &$turmasCriadas) {
            
            // LÓGICA DE SEQUENCIAMENTO (CONTINUAR DO ÚLTIMO PONTO):
            $lastLetter = Turma::where('ano_letivo', $anoLetivo)->max('letra'); 
            
            if ($lastLetter) {
                $startLetter = chr(ord($lastLetter) + 1);
            } else {
                $startLetter = 'A';
            }
            
            $letterCounter = 0; 
            
            foreach ($periodos as $periodo => $quantidade) {
                if ($quantidade > 0) {
                    
                    for ($i = 0; $i < $quantidade; $i++) {
                        $currentLetter = chr(ord($startLetter) + $letterCounter);
                        $letterCounter++;

                        if ($currentLetter > 'Z') { 
                            break; 
                        }

                        // Cria a turma no banco de dados, incluindo as datas
                        Turma::create([
                            'periodo' => $periodo,
                            'letra' => $currentLetter,
                            'ano_letivo' => $anoLetivo,
                            'data_inicio' => $dataInicio,
                            'data_fim' => $dataFim,
                            'vagas' => $vagas,
                            'professor_id' => null, 
                        ]);
                        $turmasCriadas++;
                    }
                }
            }
        });

        if ($turmasCriadas > 0) {
            $message = "Sucesso! " . $turmasCriadas . " turma(s) criada(s) para o ano letivo de " . $anoLetivo . ".";
            return redirect()->route('formacao.turmas.index')->with('success', $message);
        }

        return redirect()->back()->with('success', 'Nenhuma nova turma foi criada.');
    }

    /**
     * Remove a turma especificada do armazenamento.
     * @param \App\Models\Turma $turma
     */
    public function destroyTurma(Turma $turma)
    {
        try {
            $turmaNome = $turma->nome_completo;
            $turma->delete();
            return redirect()->route('formacao.turmas.index')
                             ->with('success', "Turma '$turmaNome' excluída com sucesso!");
        } catch (\Exception $e) {
            return redirect()->back()
                             ->withErrors(['delete_error' => 'Erro ao excluir a turma. Verifique se há alunos ou registros vinculados.']);
        }
    }

    /**
     * Remove todas as turmas do armazenamento.
     */
    public function destroyAllTurmas()
    {
        DB::beginTransaction();
        try {
            // Deleta todos os registros na tabela 'turmas'. 
            $count = Turma::count();
            Turma::query()->delete(); 
            DB::commit();
            return redirect()->route('formacao.turmas.index')
                             ->with('success', "$count turmas foram excluídas com sucesso.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao deletar todas as turmas: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Erro ao tentar excluir todas as turmas.');
        }
    }
}
