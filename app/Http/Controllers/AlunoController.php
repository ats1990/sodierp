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
// Removidos os 'use's de Str, Log e Collection, que eram usados apenas na importação.

class AlunoController extends Controller
{
    // ==========================================================
    // 💡 MÉTODO HELPER
    // Necessário para garantir que os campos booleanos ausentes (checkboxes)
    // recebam o valor '0' (false) no Request antes da validação.
    // ==========================================================
    protected function prepareBooleanFields(Request $request)
    {
        $booleanFields = [
            'declaracao_consentimento', 'carteira_trabalho', 'ja_trabalhou', 'ctps_assinada',
            'concluido', 'beneficio', 'convenio', 'vacinacao', 'queixa_saude', 'alergia',
            'tratamento', 'uso_remedio', 'cirurgia', 'pcd', 'doenca_congenita', 'psicologo',
            'convulsao', 'familia_doenca', 'familia_depressao', 'medico_especialista',
            'familia_psicologico', 'familia_alcool', 'familia_drogas'
        ];

        foreach ($booleanFields as $field) {
            // Se o campo não está presente no request (checkbox desmarcado), defina-o como 0.
            if (!$request->has($field)) {
                $request->merge([$field => 0]);
            }
        }
    }
   
    // ==========================================================
    // MÉTODOS CRUD PADRÃO
    // ==========================================================

    public function index(Request $request)
    {
        // 1. DADOS PARA FILTROS E VIEW
        // Garante que estas variáveis existem para popular os filtros da view
        $turmas = Turma::orderBy('ano_letivo', 'desc')->get();
        // Assume que anosLetivos e Periodos são coleções simples para o filtro
        $anosLetivos = Turma::select('ano_letivo')->distinct()->pluck('ano_letivo')->sortDesc();
        $periodos = Turma::select('periodo')->distinct()->pluck('periodo')->sort();


        // 2. INÍCIO DA QUERY
        // Carrega a relação 'turma' para evitar o problema de N+1
        $query = Aluno::query()->with('turma'); 

        // 3. FILTRAGEM
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nomeCompleto', 'like', "%{$search}%")
                  ->orWhere('codigo_matricula', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
        }

        if ($request->filled('turma_id')) {
            $query->where('turma_id', $request->input('turma_id'));
        }

        if ($request->filled('ano_letivo')) {
             // Filtra por Turmas que tenham o ano letivo selecionado
             $query->whereHas('turma', function ($q) use ($request) {
                 $q->where('ano_letivo', $request->input('ano_letivo'));
             });
        }
        
        if ($request->filled('periodo')) {
             // Filtra por Turmas que tenham o período selecionado
             $query->whereHas('turma', function ($q) use ($request) {
                 $q->where('periodo', $request->input('periodo'));
             });
        }


        // 4. ORDERING (Ajustado para segurança contra dados nulos)
        $sortColumn = $request->get('sort', 'codigo_matricula');
        $sortDirection = $request->get('direction', 'asc');
        $safeSortColumns = ['codigo_matricula', 'nomeCompleto', 'turma_id', 'status']; // dataNascimento removido por segurança inicial

        // Aplica a ordenação se for uma coluna segura. Caso contrário, usa o padrão.
        if (in_array($sortColumn, $safeSortColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('codigo_matricula', 'asc');
        }

        // 5. EXECUÇÃO DA QUERY E PAGINAÇÃO
        $alunos = $query->paginate(20)->withQueryString(); 
        // ->withQueryString() mantém os filtros ativos ao mudar de página

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
        $this->prepareBooleanFields($request);

        $validatedData = $request->validate([
            // CAMPOS PRINCIPAIS
            'turma_id' => 'required|exists:turmas,id',
            'nomeCompleto' => 'required|string|max:191',
            'codigo_matricula' => 'nullable|string|max:100|unique:alunos,codigo_matricula',
            'dataNascimento' => 'required|date',
            'declaracao_consentimento' => 'required|boolean',

            // CAMPOS PESSOAIS E DE CONTATO
            'cpf' => 'nullable|string|max:14|unique:alunos,cpf',
            'email' => 'nullable|email|max:191',
            'nomeSocial' => 'nullable|string|max:191',
            'rg' => 'nullable|string|max:20',
            'mao_dominante' => 'nullable|string|max:50',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            
            // DADOS DE TRABALHO
            'jaTrabalhou' => 'required|boolean',
            'carteiraTrabalho' => 'nullable|boolean',
            'ctpsAssinada' => 'nullable|boolean',
            'qualFuncao' => 'nullable|string|max:191',

            // ENDEREÇO
            'cep' => 'nullable|string|max:10', 'rua' => 'nullable|string|max:191',
            'numero' => 'nullable|string|max:20', 'complemento' => 'nullable|string|max:191',
            'bairro' => 'nullable|string|max:191', 'cidade' => 'nullable|string|max:191',
            'uf' => 'nullable|string|max:2',

            // ESCOLARIDADE
            'escola' => 'nullable|string|max:191', 'ano' => 'nullable|string|max:50',
            'concluido' => 'nullable|boolean', 'periodo' => 'nullable|string|max:50',
            'anoConclusao' => 'nullable|integer|digits:4', 'cursoAtual' => 'nullable|string|max:191',

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


        $aluno = Aluno::create($validatedData);

        // Processamento de Familiares (Criação)
        if ($request->filled('familiares_json')) {
            $familiares = json_decode($request->familiares_json, true);
            if ($familiares) {
                $familiares = array_filter($familiares, fn($f) => array_filter($f));

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
        $this->prepareBooleanFields($request);

        $validatedData = $request->validate([
            // CAMPOS PRINCIPAIS (Com regra de unique para IGNORAR o próprio aluno)
            'turma_id' => 'required|exists:turmas,id',
            'nomeCompleto' => 'required|string|max:191',
            'codigo_matricula' => 'required|string|max:100|unique:alunos,codigo_matricula,' . $aluno->id, 
            'dataNascimento' => 'required|date',
            'declaracao_consentimento' => 'required|boolean',

            // CAMPOS PESSOAIS E DE CONTATO
            'cpf' => 'nullable|string|max:14|unique:alunos,cpf,' . $aluno->id, 
            'email' => 'nullable|email|max:191',
            'nomeSocial' => 'nullable|string|max:191',
            'rg' => 'nullable|string|max:20',
            'mao_dominante' => 'nullable|string|max:50',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            
            // DADOS DE TRABALHO
            'jaTrabalhou' => 'required|boolean',
            'carteiraTrabalho' => 'nullable|boolean',
            'ctpsAssinada' => 'nullable|boolean',
            'qualFuncao' => 'nullable|string|max:191',

            // ENDEREÇO
            'cep' => 'nullable|string|max:10', 'rua' => 'nullable|string|max:191',
            'numero' => 'nullable|string|max:20', 'complemento' => 'nullable|string|max:191',
            'bairro' => 'nullable|string|max:191', 'cidade' => 'nullable|string|max:191',
            'uf' => 'nullable|string|max:2',

            // ESCOLARIDADE
            'escola' => 'nullable|string|max:191', 'ano' => 'nullable|string|max:50',
            'concluido' => 'nullable|boolean', 'periodo' => 'nullable|string|max:50',
            'anoConclusao' => 'nullable|integer|digits:4', 'cursoAtual' => 'nullable|string|max:191',

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

        $aluno->update($validatedData);

        // Processamento de Familiares (Deleta e recria para atualização)
        if ($request->filled('familiares_json')) {
            $aluno->familiares()->delete();

            $familiares = json_decode($request->familiares_json, true);
            if ($familiares) {
                $familiares = array_filter($familiares, fn($f) => array_filter($f));

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
        $aluno->delete();
        return redirect()->route('aluno.index')->with('success', 'Aluno excluído com sucesso.');
    }
}