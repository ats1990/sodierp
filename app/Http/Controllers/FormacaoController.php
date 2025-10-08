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
     * @return \Illuminate\View\View
     */
    public function indexTurmas()
    {
        // Carrega todas as turmas ordenadas por ano, período e letra
        $turmas = Turma::orderBy('ano_letivo', 'desc')
                        ->orderBy('periodo', 'asc')
                        ->orderBy('letra', 'asc')
                        ->get();
        
        // Pega os alunos que ainda não estão atribuídos a uma turma (para o modal de atribuição rápida)
        $alunosDisponiveis = Aluno::whereNull('turma_id')
                                  ->orderBy('nomeCompleto', 'asc')
                                  ->get();

        // Passa as turmas e os alunos disponíveis para a view
        return view('formacao.turmas.index', compact('turmas', 'alunosDisponiveis')); 
    }

    /**
     * Exibe a lista de alunos para atribuição de turmas com filtros.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function indexAtribuicaoTurmas(Request $request)
    {
        // 1. Carrega todas as turmas para o campo de seleção (Turma e Tabela)
        $turmas = Turma::orderBy('ano_letivo', 'desc')
                       ->orderBy('periodo', 'asc')
                       ->orderBy('letra', 'asc')
                       ->get();
        
        // 2. Carrega os anos letivos distintos disponíveis nas turmas para o filtro
        $anosDisponiveis = Turma::select('ano_letivo')
                                ->distinct()
                                ->orderBy('ano_letivo', 'desc')
                                ->pluck('ano_letivo');

        // 3. Inicializa a query de Alunos, carregando a relação da turma
        $query = Aluno::with('turma')
                       ->orderBy('nomeCompleto', 'asc');
        
        // ===========================================
        // 4. APLICAÇÃO DOS FILTROS DINÂMICOS
        // ===========================================

        // a) Filtro por Turma
        if ($request->filled('filtro_turma')) {
            $filtroTurma = $request->input('filtro_turma');
            
            if ($filtroTurma === 'sem_turma') {
                // Filtra alunos que NÃO têm turma atribuída (turma_id IS NULL)
                $query->whereNull('turma_id');
            } else {
                // Filtra alunos por ID de turma específico
                $query->where('turma_id', $filtroTurma);
            }
        }
        
        // b) Filtro por Ano Letivo
        if ($request->filled('filtro_ano')) {
            $filtroAno = $request->input('filtro_ano');
            
            // Filtra alunos que pertencem a turmas naquele ano
            // Usa whereHas para verificar a relação 'turma'
            $query->whereHas('turma', function ($q) use ($filtroAno) {
                $q->where('ano_letivo', $filtroAno);
            });
        }

        // c) Filtro de Busca por Nome ou CPF
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                // Busca por nome completo ou CPF (case insensitive)
                $q->where(DB::raw('LOWER(nomeCompleto)'), 'like', "%" . Str::lower($search) . "%")
                  ->orWhere('cpf', 'like', "%{$search}%");
            });
        }
        
        // 5. Executa a query com paginação e mantém os filtros na URL
        $alunos = $query->paginate(15)->withQueryString();

        // 6. Passa todos os dados para a view
        return view('formacao.atribuicao.index', compact('alunos', 'turmas', 'anosDisponiveis')); 
    }

    /**
     * Lógica para Atribuir ou Desvincular um Aluno a uma Turma.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function atribuirTurma(Request $request)
    {
        $request->validate([
            'aluno_id' => 'required|exists:alunos,id',
            // turma_id pode ser vazio/nulo (para desvincular)
            'turma_id' => 'nullable|exists:turmas,id', 
        ]);
    
        $aluno = Aluno::findOrFail($request->aluno_id);
        $turmaId = $request->turma_id;
        $mensagem = '';
    
        // Se turma_id é vazio, desvincula o aluno
        if (empty($turmaId)) {
            $aluno->turma_id = null;
            $mensagem = "Aluno(a) **{$aluno->nomeCompleto}** desvinculado(a) de qualquer turma.";
        } else {
            // Atribui a nova turma
            $turma = Turma::findOrFail($turmaId);

            // 🚨 Verificação de Vagas Opcional: Descomente se quiser impedir atribuição após lotação
            // if ($turma->alunos()->count() >= $turma->vagas && $aluno->turma_id !== $turmaId) {
            //     return redirect()->back()->with('error', "Turma {$turma->nome_completo} está lotada. Vagas: {$turma->vagas}.");
            // }

            $aluno->turma_id = $turmaId;
            $mensagem = "Aluno(a) **{$aluno->nomeCompleto}** atribuído(a) à turma **{$turma->nome_completo}** com sucesso.";
        }
    
        $aluno->save();
        return redirect()->back()->with('success', $mensagem);
    }
    

    /**
     * Salva uma nova turma no armazenamento.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTurmas(Request $request)
    {
        // 1. Validação dos dados de entrada
        $validatedData = $request->validate([
            'ano_letivo' => 'required|integer|min:2020|max:2099',
            'periodo' => 'required|string|in:Manhã,Tarde',
            'letra' => 'required|string|max:1',
            'vagas' => 'required|integer|min:1',
            'professor_id' => 'nullable|exists:usuarios,id',
        ]);
    
        // 2. Verifica se a turma já existe
        $turmaExistente = Turma::where('ano_letivo', $validatedData['ano_letivo'])
                               ->where('periodo', $validatedData['periodo'])
                               ->where('letra', $validatedData['letra'])
                               ->first();
    
        if ($turmaExistente) {
            return redirect()->back()
                             ->with('error', "Já existe uma turma com a letra '{$validatedData['letra']}' no período '{$validatedData['periodo']}' para o ano de {$validatedData['ano_letivo']}.")
                             ->withInput();
        }
    
        // 3. Cria a nova turma
        $turma = Turma::create($validatedData);
    
        return redirect()->route('formacao.turmas.index')
                         ->with('success', "Turma **{$turma->nome_completo}** criada com sucesso!");
    }

    /**
     * Remove a turma especificada do armazenamento.
     * @param \App\Models\Turma $turma
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyTurma(Turma $turma)
    {
        DB::beginTransaction();
        try {
            // Desvincula todos os alunos desta turma (turma_id = NULL)
            Aluno::where('turma_id', $turma->id)->update(['turma_id' => null]);

            $turmaNome = $turma->nome_completo;
            $turma->delete();
            
            DB::commit();
            return redirect()->route('formacao.turmas.index')
                             ->with('success', "Turma **'$turmaNome'** excluída com sucesso! Alunos desvinculados.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir a turma: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Erro ao excluir a turma. Tente novamente.');
        }
    }

    /**
     * Remove todas as turmas do armazenamento.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyAllTurmas()
    {
        DB::beginTransaction();
        try {
            // Primeiro, desvincula todos os alunos de qualquer turma
            Aluno::whereNotNull('turma_id')->update(['turma_id' => null]);

            // Deleta todos os registros na tabela 'turmas'. 
            $count = Turma::count();
            Turma::query()->delete(); 
            
            DB::commit();
            return redirect()->route('formacao.turmas.index')
                             ->with('success', "**$count** turmas foram excluídas com sucesso. Todos os alunos foram desvinculados.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao deletar todas as turmas: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Erro ao tentar excluir todas as turmas.');
        }
    }
    
    // ... (Outros métodos como indexNotas, indexBoletim, etc., podem ser adicionados aqui) ...

    public function indexNotas()
    {
        // Lógica para a tela de Notas
        return view('formacao.notas.index');
    }

    public function indexBoletim()
    {
        // Lógica para a tela de Boletim
        return view('formacao.boletim.index');
    }

    public function indexCertificado()
    {
        // Lógica para a tela de Certificado
        return view('formacao.certificado.index');
    }
}