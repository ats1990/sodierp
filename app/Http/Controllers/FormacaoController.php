<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use App\Models\Aluno;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller responsável pela gestão das Turmas, Notas, Boletim e Certificados.
 */
class FormacaoController extends Controller
{
    /**
     * Exibe a tela principal de gestão de turmas. (formacao.turmas.index)
     */
    public function indexTurmas()
    {
        // Puxa todos os usuários do tipo 'professor' para o dropdown
        // Nota: Assumindo que a model Usuario está configurada para mapear a tabela 'usuarios'.
        $professores = Usuario::where('tipo', 'professor')
                                ->where('status', 'ativo')
                                ->orderBy('nomeCompleto')
                                ->get();

        // Carrega todas as turmas, ordenadas pelo ano e período
        $turmas = Turma::with('professor') // Carrega o relacionamento com professor
                       ->orderBy('ano_letivo', 'desc')
                       ->orderBy('periodo')
                       ->orderBy('letra')
                       ->get();

        // Carrega os alunos que ainda não estão atribuídos a nenhuma turma
        $alunosNaoAtribuidos = Aluno::whereNull('turma_id')
                                   ->orderBy('nomeCompleto')
                                   ->get();

        return view('formacao.turmas.index', compact('professores', 'turmas', 'alunosNaoAtribuidos'));
    }

    /**
     * Cria uma nova turma única. (formacao.turmas.store)
     */
    public function storeTurmas(Request $request)
    {
        $validated = $request->validate([
            'periodo' => ['required', 'string', 'max:191'],
            'letra' => ['required', 'string', 'max:1'],
            'ano_letivo' => ['required', 'integer', 'digits:4'],
            'vagas' => ['nullable', 'integer', 'min:1'],
            'professor_id' => ['nullable', 'exists:usuarios,id'],
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
        ]);

        try {
            Turma::create($validated);
            return back()->with('success', 'Turma criada com sucesso!');
        } catch (\Illuminate\Database\QueryException $e) {
            // Verifica se o erro é de chave única duplicada (ex: se já existe a turma)
            if (str_contains($e->getMessage(), 'Duplicate entry')) { 
                return back()->withErrors(['unique_violation' => 'Já existe uma turma cadastrada com o mesmo Período, Letra e Ano Letivo.'])->withInput();
            }
            return back()->with('error', 'Erro ao criar a turma: ' . $e->getMessage())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Erro inesperado: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Cria múltiplas turmas de uma vez (em massa), gerando-as a partir de parâmetros gerais do modal.
     * (formacao.turmas.storeBulk)
     */
    public function storeBulk(Request $request)
    {
        // 1. Valida os campos que SÃO enviados pelo seu modal
        $validated = $request->validate([
            'ano_letivo' => ['required', 'integer', 'digits:4'],
            'vagas_geral' => ['required', 'integer', 'min:1'],
            'quantidade_manha' => ['required', 'integer', 'min:0'],
            'quantidade_tarde' => ['required', 'integer', 'min:0'],
            'data_inicio' => ['required', 'date'],
            'data_fim' => ['required', 'date', 'after_or_equal:data_inicio'],
        ]);

        // 2. Verifica a quantidade total de turmas a serem criadas
        if ($validated['quantidade_manha'] + $validated['quantidade_tarde'] === 0) {
            return back()->withErrors(['quantidade' => 'É necessário criar pelo menos uma turma (Manhã ou Tarde).'])->withInput();
        }
        
        $turmasParaCriar = [];
        $letras = range('A', 'Z'); // Define as letras para as turmas (A, B, C...)
        
        $turmaBase = [
            'ano_letivo' => $validated['ano_letivo'],
            'vagas' => $validated['vagas_geral'],
            'data_inicio' => $validated['data_inicio'],
            'data_fim' => $validated['data_fim'],
            'professor_id' => null, // Assumindo que o professor será atribuído depois
        ];

        // 3. Geração das turmas da MANHÃ
        $qtdManha = $validated['quantidade_manha'];
        if ($qtdManha > 0) {
            for ($i = 0; $i < $qtdManha; $i++) {
                // Verifica se não vai exceder o alfabeto
                if (!isset($letras[$i])) {
                     return back()->withErrors(['quantidade_manha' => 'A quantidade de turmas da manhã excede o número de letras disponíveis (A-Z).'])->withInput();
                }
                $turmasParaCriar[] = array_merge($turmaBase, [
                    'periodo' => 'Manhã',
                    'letra' => $letras[$i],
                ]);
            }
        }

        // 4. Geração das turmas da TARDE
        $qtdTarde = $validated['quantidade_tarde'];
        if ($qtdTarde > 0) {
            for ($i = 0; $i < $qtdTarde; $i++) {
                // Verifica se não vai exceder o alfabeto
                if (!isset($letras[$i])) {
                     return back()->withErrors(['quantidade_tarde' => 'A quantidade de turmas da tarde excede o número de letras disponíveis (A-Z).'])->withInput();
                }
                $turmasParaCriar[] = array_merge($turmaBase, [
                    'periodo' => 'Tarde',
                    'letra' => $letras[$i], // Reinicia a letra A para o período da tarde
                ]);
            }
        }
        
        // 5. Inserção no banco de dados
        try {
            $count = count($turmasParaCriar);
            DB::beginTransaction();
            foreach ($turmasParaCriar as $data) {
                // Usar create() garante que os timestamps sejam preenchidos
                Turma::create($data); 
            }
            DB::commit();

            return back()->with('success', $count . ' turmas criadas em massa com sucesso!');
            
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Verifica se o erro é de chave única duplicada (Turma A Manhã já existe para aquele ano)
            if (str_contains($e->getMessage(), 'Duplicate entry')) { 
                return back()->withErrors(['unique_violation' => 'Já existe uma ou mais turmas com a mesma combinação de Período, Letra e Ano Letivo. Verifique se o ano letivo já foi iniciado.'])->withInput();
            }
            return back()->with('error', 'Erro ao criar turmas em massa: ' . $e->getMessage())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro inesperado: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Exclui todas as turmas. (formacao.turmas.destroyAll)
     */
    public function destroyAllTurmas()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // 1. Limpa o Foreign Key (turma_id) em todos os alunos
            Aluno::whereNotNull('turma_id')->update(['turma_id' => null]);

            // 2. Limpa todas as turmas
            Turma::truncate();

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return back()->with('success', 'Todas as turmas foram excluídas e as atribuições de alunos foram removidas.');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return back()->with('error', 'Erro ao excluir todas as turmas: ' . $e->getMessage());
        }
    }
    
    /**
     * Exclui uma única turma. (formacao.turmas.destroy)
     */
    public function destroyTurma(Turma $turma)
    {
        try {
            // Desvincula primeiro os alunos desta turma específica
            Aluno::where('turma_id', $turma->id)->update(['turma_id' => null]);

            $turma->delete();
            return back()->with('success', 'Turma excluída com sucesso! Os alunos foram desvinculados.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir a turma: ' . $e->getMessage());
        }
    }

    /**
     * Atribui um aluno a uma turma (usado no modal rápido). (formacao.turmas.atribuir)
     */
    public function atribuirAlunoTurma(Request $request)
    {
        $validated = $request->validate([
            'aluno_id' => ['required', 'exists:alunos,id'],
            'turma_id' => ['required', 'exists:turmas,id'],
        ]);

        try {
            $aluno = Aluno::find($validated['aluno_id']);
            $aluno->turma_id = $validated['turma_id'];
            $aluno->save();

            return back()->with('success', 'Aluno atribuído à turma com sucesso.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atribuir aluno: ' . $e->getMessage());
        }
    }

    /**
     * Exibe a tela de atribuição detalhada de turmas. (formacao.atribuicao.index)
     */
    public function indexAtribuicaoTurmas()
    {
        $turmas = Turma::orderBy('ano_letivo', 'desc')->orderBy('periodo')->get();
        $alunos = Aluno::orderBy('nomeCompleto')->get(); // Todos os alunos

        return view('formacao.atribuicao.index', compact('turmas', 'alunos'));
    }

    /**
     * Atualiza a atribuição de um aluno na tela detalhada. (formacao.atribuicao.update)
     */
    public function updateAtribuicaoAluno(Request $request, Aluno $aluno)
    {
        $validated = $request->validate([
            'turma_id' => ['nullable', 'exists:turmas,id'], 
        ]);

        try {
            $aluno->turma_id = $validated['turma_id'] ?? null;
            $aluno->save();

            // Tenta obter o nome da turma para a mensagem
            $turmaNome = $aluno->turma_id ? $aluno->turma->nomeCompleto : 'desvinculada';

            return back()->with('success', "Atribuição de {$aluno->nomeCompleto} atualizada para a turma: {$turmaNome}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar atribuição: ' . $e->getMessage());
        }
    }
    
    // Métodos stubs para as outras telas do menu Formação
    public function indexNotas() { return view('formacao.notas.index'); }
    public function indexBoletim() { return view('formacao.boletim.index'); }
    public function indexCertificado() { return view('formacao.certificado.index'); }
    public function indexImportar() { return view('formacao.importar.index'); }
}