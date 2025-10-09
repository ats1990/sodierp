<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Turma;
use App\Models\Aluno;
use App\Models\Usuario; // 💥 CORREÇÃO 1: Usando 'Usuario' que mapeia para a tabela 'usuarios' com a coluna 'tipo'.
use App\Models\Role; 
use App\Models\Familiar; 
use App\Models\Programa; 

class FormacaoController extends Controller
{
    // =========================================================================
    // Métodos para Gerenciamento de Turmas (CRUD)
    // =========================================================================

    /**
     * Exibe a lista de turmas e o formulário de criação.
     */
    public function indexTurmas()
    {
        // ATENÇÃO: Se o relacionamento 'professor' no modelo Turma usa 'App\Models\User',
        // isso pode causar falha. Assumindo que o relacionamento será corrigido ou 
        // que o modelo Usuario está configurado para ser o modelo 'User' da aplicação.
        $turmas = Turma::with('professor')->get();
        
        // Obtém apenas usuários com a role 'professor' para o formulário
        // 💥 CORREÇÃO 2: Usa a coluna 'tipo' da tabela 'usuarios' (Modelo Usuario).
        $professores = Usuario::where('tipo', 'professor')->get();
        
        return view('formacao.turmas.index', compact('turmas', 'professores'));
    }

    /**
     * Armazena uma nova turma no banco de dados.
     */
    public function storeTurmas(Request $request)
    {
        $request->validate([
            'periodo' => 'required|string|max:255',
            'letra' => 'required|string|max:1',
            'ano_letivo' => 'required|integer|min:' . (date('Y') - 1) . '|max:' . (date('Y') + 5), // Validação razoável
            'vagas' => 'required|integer|min:1',
            // 💥 CORREÇÃO 3: O exists deve procurar na tabela correta ('usuarios')
            'professor_id' => 'nullable|exists:usuarios,id', 
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
        ], [
            'periodo.required' => 'O campo Período é obrigatório.',
            'letra.required' => 'O campo Letra é obrigatório.',
            'ano_letivo.required' => 'O campo Ano Letivo é obrigatório.',
            'ano_letivo.integer' => 'O Ano Letivo deve ser um número inteiro.',
            'vagas.required' => 'O campo Vagas é obrigatório.',
            'vagas.min' => 'A turma deve ter pelo menos uma vaga.',
            'data_fim.after_or_equal' => 'A data final deve ser igual ou posterior à data de início.',
        ]);

        try {
            Turma::create($request->all());
            return redirect()->route('formacao.turmas.index')->with('success', 'Turma criada com sucesso!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') { // Código para erro de unicidade (duplicidade)
                return redirect()->back()->withInput()->with('error', 'Já existe uma turma com o mesmo Período, Letra e Ano Letivo.');
            }
            return redirect()->back()->withInput()->with('error', 'Erro ao criar turma: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erro inesperado ao criar turma.');
        }
    }
    
    // Método de Exclusão Única
    public function destroyTurma(Turma $turma)
    {
        // Garante que as associações em aluno_turma sejam removidas via cascade ou manualmente (melhor via cascade na migration)
        // Como o relacionamento é One-to-Many (turma_id em alunos), ao excluir a turma, o turma_id em alunos DEVE ser SET NULL
        
        try {
            $turma->delete();
            return redirect()->route('formacao.turmas.index')->with('success', 'Turma excluída com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('formacao.turmas.index')->with('error', 'Erro ao excluir a turma. Tente novamente.');
        }
    }


    /**
     * Exclui TODAS as turmas e limpa as associações.
     * Restrito apenas à Coordenação (via middleware na rota).
     */
    public function destroyAllTurmas()
    {
        // A permissão já é gerenciada pelo middleware 'role:coordenacao' na definição da rota.
        
        try {
            // Desabilita temporariamente as checagens de chave estrangeira
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // 1. Limpa o Foreign Key (turma_id) em todos os alunos, pois o relacionamento é One-to-Many.
            // Isso previne erros de chave estrangeira ao excluir as turmas.
            Aluno::whereNotNull('turma_id')->update(['turma_id' => null]);

            // 2. Limpa todas as turmas (truncate é mais rápido e eficiente)
            Turma::truncate();

            // Reabilita as checagens de chave estrangeira
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return redirect()->route('formacao.turmas.index')
                             ->with('success', 'Todas as turmas e suas associações com alunos foram excluídas com sucesso.');
                             
        } catch (\Exception $e) {
            // Garante que as checagens sejam reabilitadas mesmo em caso de falha
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            // Se houver qualquer erro, informa o usuário.
            return redirect()->route('formacao.turmas.index')
                             ->with('error', 'Erro crítico ao excluir todas as turmas. Detalhe: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // Métodos para Atribuição de Alunos a Turmas
    // =========================================================================

    /**
     * Salva a atribuição rápida de um aluno a uma turma (usado no modal de criação).
     */
    public function atribuirAlunoTurma(Request $request)
    {
        // Lógica de atribuição rápida... (Método POST)
    }

    /**
     * Exibe a tela detalhada de atribuição de alunos a turmas (GET).
     */
    public function indexAtribuicaoTurmas()
    {
        // Lógica para exibir a tela de atribuição... (Método GET)
    }

    /**
     * Atualiza a atribuição de turmas de um aluno (usado na tela detalhada).
     */
    public function updateAtribuicaoAluno(Request $request, Aluno $aluno)
    {
        // Lógica para atualizar a atribuição... (Método POST)
    }

    // =========================================================================
    // Métodos para Notas, Boletins, Certificados e Importação
    // =========================================================================
    
    public function indexNotas()
    {
        // Lógica para Notas
    }

    public function indexBoletim()
    {
        // Lógica para Boletim
    }

    public function indexCertificado()
    {
        // Lógica para Certificado
    }

    public function indexImportar()
    {
        // Lógica para Importar Dados
    }
}
