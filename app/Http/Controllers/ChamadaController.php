<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turma; 
use App\Models\Presenca; 
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChamadaController extends Controller
{
    /**
     * Exibe o índice com cards de turmas para seleção.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Turma::with('alunos');
        
        // Aplica o filtro de turmas com base na role do usuário
        if ($user->tipo === 'professor') {
            $turmas = $query->get();
        } else {
            $turmas = $query->get();
        }
        
        // CÁLCULO DAS DATAS MÍNIMA E MÁXIMA
        $min_data_inicio = $turmas->pluck('data_inicio')->filter()->min();
        $max_data_fim = $turmas->pluck('data_fim')->filter()->max();

        $min_month = $min_data_inicio 
            ? Carbon::parse($min_data_inicio)->format('Y-m') 
            : '2000-01'; 
            
        $max_month = $max_data_fim 
            ? Carbon::parse($max_data_fim)->format('Y-m') 
            : now()->format('Y-m');
        
        $mes_ano_atual = now()->format('Y-m'); 
        
        if ($mes_ano_atual > $max_month) {
            $mes_ano_atual = $max_month;
        }

        return view('formacao.chamada.index', compact(
            'turmas', 
            'mes_ano_atual', 
            'min_month',
            'max_month'
        ));
    }

    /**
     * Exibe o calendário de frequência para a Turma e Mês selecionados.
     */
    public function show(Turma $turma, string $mes_ano)
    {
        // 1. Processar Mês/Ano
        try {
            $data_referencia = Carbon::createFromFormat('Y-m', $mes_ano)->startOfMonth();
            
            // VALIDAÇÃO: Verifica se o mês está dentro do período da turma
            $data_referencia_dt = $data_referencia->toDateString();
            if (($turma->data_inicio && $data_referencia_dt < $turma->data_inicio) || 
                ($turma->data_fim && $data_referencia_dt > Carbon::parse($turma->data_fim)->startOfMonth()->toDateString())) {
                
                return redirect()->route('chamada.index')->with('error', 'O mês selecionado está fora do período de validade da turma.');
            }

            $dias_no_mes = $data_referencia->daysInMonth;
            $primeiro_dia = $data_referencia->dayOfWeek;
        } catch (\Exception $e) {
            return redirect()->route('chamada.index')->with('error', 'Mês ou Turma inválidos.');
        }

        // 2. Obter Alunos da Turma
        // Ordena pelo nome completo (coluna física correta)
        $alunos = $turma->alunos()->orderBy('nomeCompleto')->get();
        
        // 3. Obter Presenças do Mês
        $presencas = Presenca::where('turma_id', $turma->id)
            ->whereYear('data', $data_referencia->year)
            ->whereMonth('data', $data_referencia->month)
            ->get()
            ->keyBy(function($item) {
                // Key: aluno_id-dia_do_mes
                return $item->aluno_id . '-' . Carbon::parse($item->data)->day; 
            });

        // 4. CÁLCULO DA CONTAGEM DE PRESENÇAS E TOTAL DE FALTAS
        $total_dias_letivos = 0;
        $data_fim_mes = $data_referencia->copy()->endOfMonth();

        // 4.1. Determina o total de dias letivos (excluindo Sábados e Domingos)
        for ($dia = 1; $dia <= $dias_no_mes; $dia++) {
            $data_dia = Carbon::createFromDate($data_referencia->year, $data_referencia->month, $dia);
            // 0 = Domingo, 6 = Sábado. Dias úteis são 1 a 5.
            if (!in_array($data_dia->dayOfWeek, [0, 6])) {
                $total_dias_letivos++;
            }
        }
        
        // 4.2. Itera sobre os alunos para calcular as estatísticas
        foreach ($alunos as $aluno) {
            // Filtra as presenças do mês para o aluno atual
            $presencas_aluno = $presencas->filter(function ($presenca) use ($aluno) {
                return $presenca->aluno_id === $aluno->id;
            });
            
            $total_presencas = 0;
            $total_faltas = 0;
            $dias_registrados = 0;

            // Percorre todos os dias do mês para contabilizar P e F/J
            for ($dia = 1; $dia <= $dias_no_mes; $dia++) {
                $data_dia = Carbon::createFromDate($data_referencia->year, $data_referencia->month, $dia);
                $is_dia_letivo = !in_array($data_dia->dayOfWeek, [0, 6]);
                
                // Se for dia letivo, tentamos encontrar um registro
                if ($is_dia_letivo) {
                    $chave = $aluno->id . '-' . $dia;
                    $registro = $presencas->get($chave);

                    if ($registro) {
                        if ($registro->presente == 1) {
                            $total_presencas++;
                        } else {
                            // Conta Faltas (injustificadas 'F' ou justificadas 'J')
                            $total_faltas++;
                        }
                        $dias_registrados++;
                    } else {
                        // Se não há registro, consideramos como 'Não Lançado' (-) e não afeta a contagem
                    }
                }
            }
            
            // ATUALIZAÇÃO AQUI: Remove o cálculo da média e usa a contagem simples
            // O nome da propriedade é alterado de media_presencas para total_presencas
            $aluno->total_presencas = $total_presencas; 
            $aluno->total_faltas = $total_faltas;
        }

        // 5. Obter Último Registro (Professor e Horário)
        $ultimo_registro = Presenca::where('turma_id', $turma->id)
            ->whereYear('data', $data_referencia->year)
            ->whereMonth('data', $data_referencia->month)
            ->with('professor')
            ->latest('updated_at')
            ->first();

        // 6. Verificar permissão de ALTERAÇÃO para a View
        $can_alter = Auth::user()->can('alter', Presenca::class);

        // 7. Retorno da View
        return view('formacao.chamada.show', compact(
            'turma', 'mes_ano', 'alunos', 'presencas', 'dias_no_mes', 
            'data_referencia', 'ultimo_registro', 'can_alter'
        ));
    }

    /**
     * Salva a frequência (rota POST via AJAX ou formulário).
     */
    public function store(Request $request, Turma $turma, string $mes_ano)
    {
        // O Gate já garante que apenas 'coordenacao' e 'professor' cheguem aqui.

        $request->validate([
            'data' => 'required|date_format:Y-m-d',
            'aluno_id' => 'required|exists:alunos,id',
            'presente' => 'required|boolean',
            'justificada' => 'nullable|boolean',
            'motivo' => 'nullable|string|max:255',
        ]);
        
        $alunoId = $request->input('aluno_id');
        $data = $request->input('data');
        
        // Encontra ou cria o registro de presença para o dia
        $presenca = Presenca::updateOrCreate(
            [
                'aluno_id' => $alunoId,
                'data' => $data,
                'turma_id' => $turma->id, 
            ],
            [
                'presente' => $request->input('presente'),
                'justificada' => $request->input('justificada', 0), // Garante um valor padrão
                'motivo' => $request->input('motivo', null),
                'professor_id' => Auth::id(), // Quem está fazendo o registro
            ]
        );

        // Retorna uma resposta de sucesso (melhor se for AJAX)
        return response()->json([
            'success' => true,
            'message' => 'Frequência atualizada com sucesso.',
            'updated_at' => $presenca->updated_at->format('d/m/Y H:i:s'), // Formato mais completo para exibição
            'professor' => $presenca->professor->nomeCompleto ?? 'Atual',
        ]);
    }
}
