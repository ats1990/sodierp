<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turma; 
use App\Models\Presenca; 
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Para gerar o PDF
use ZipArchive; // Para criar o arquivo ZIP
use Illuminate\Support\Facades\File; // Útil, mas ZipArchive nativo já é suficiente

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
            
            // Atribui as contagens ao objeto Aluno para uso na View
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

    /**
     * Retorna os dados necessários (Turmas e Meses) para popular o formulário de geração de PDF (via AJAX).
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showPdfForm(Request $request)
    {
        // 1. Pega todas as turmas
        $turmas = Turma::select('id', 'letra')->orderBy('letra')->get();
        
        // 2. Gera a lista de meses (Pega os últimos 12 meses como padrão)
        $meses = [];
        $data_referencia = now()->startOfMonth();
        
        for ($i = 0; $i < 12; $i++) { 
            $date = $data_referencia->copy()->subMonths($i);
            $meses[] = [
                'valor' => $date->format('Y-m'), 
                'nome' => $date->isoFormat('MMMM [de] YYYY') // Ex: Outubro de 2025
            ];
        }
        
        // Inverte a ordem para que o mês mais antigo venha primeiro
        $meses = array_reverse($meses); 

        return response()->json([
            'turmas' => $turmas,
            'meses' => $meses,
        ]);
    }

    /**
     * Gera o PDF das chamadas de frequência, compactando múltiplos meses em um ZIP.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function generatePdf(Request $request)
    {
        // 🏆 CORREÇÃO: AUMENTAR LIMITES PARA EVITAR TIMEOUT/MEMÓRIA
        set_time_limit(300); // 5 minutos (ajuste conforme necessário)
        ini_set('memory_limit', '512M'); // 512MB (ajuste conforme necessário)
        
        // 1. Validar a Requisição
        $request->validate([
            'turma_ids' => 'nullable|array', 
            'mes_anos' => 'nullable|array',
            'mes_anos.*' => 'nullable|string|date_format:Y-m',
        ]);

        $turmaIds = array_filter($request->input('turma_ids', []));
        $mesAnos = array_filter($request->input('mes_anos', []));
        
        if (empty($mesAnos)) {
             $mesAnos = [now()->format('Y-m')];
        }
        sort($mesAnos); 

        // ==========================================================
        // INÍCIO DA GERAÇÃO DO ZIP com ZipArchive
        // ==========================================================
        $zipFileName = 'frequencias_' . now()->format('YmdHis') . '.zip';
        // Pega o diretório temporário do sistema operacional
        $zipPath = sys_get_temp_dir() . '/' . $zipFileName;
        
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return back()->with('error', 'Não foi possível iniciar a criação do arquivo ZIP.');
        }

        $relatoriosGerados = 0; // Contador de PDFs gerados com sucesso

        // 2. Loop principal sobre MESES
        foreach ($mesAnos as $mesAno) {
            
            try {
                $dataReferencia = Carbon::createFromFormat('Y-m', $mesAno)->startOfMonth();
            } catch (\Exception $e) {
                continue; 
            }
            
            $diasNoMes = $dataReferencia->daysInMonth;
            $ano = $dataReferencia->year;
            $mes = $dataReferencia->month;

            // Filtra as turmas se IDs específicos foram passados, ou pega todas
            $turmas = Turma::when(!empty($turmaIds), function ($query) use ($turmaIds) {
                $query->whereIn('id', $turmaIds);
            })
            // Carrega alunos e, para cada aluno, carrega as presenças do mês/ano
            ->with(['alunos.presencas' => function ($query) use ($ano, $mes) {
                $query->whereYear('data', $ano)->whereMonth('data', $mes);
            }])
            ->orderBy('letra') 
            ->get();

            $turmasDataParaMes = [];

            // 3. Loop interno sobre TURMAS (Cálculo de Frequência)
            foreach ($turmas as $turma) {
                $frequencias = [];

                foreach ($turma->alunos->sortBy('nomeCompleto') as $aluno) {
                    $presencasMap = $aluno->presencas->keyBy(function($presenca) {
                        return (int) Carbon::parse($presenca->data)->day;
                    });

                    $total_presencas = 0;
                    $total_faltas = 0;
                    
                    for ($dia = 1; $dia <= $diasNoMes; $dia++) {
                        $data_dia = Carbon::createFromDate($ano, $mes, $dia);
                        $is_dia_letivo = !in_array($data_dia->dayOfWeek, [0, 6]);
                        
                        if ($is_dia_letivo) {
                            $registro = $presencasMap->get($dia); 

                            if ($registro) {
                                if ($registro->presente == 1) {
                                    $total_presencas++;
                                } else {
                                    $total_faltas++;
                                }
                            }
                        }
                    }

                    $frequencias[] = [
                        'aluno_nome' => $aluno->nomeCompleto,
                        'presencas_map' => $presencasMap,
                        'total_presencas' => $total_presencas,
                        'total_faltas' => $total_faltas,
                    ];
                }

                // Acumula os dados de todas as turmas para o MÊS atual
                $turmasDataParaMes[] = [
                    'turma_letra' => $turma->letra,
                    'mes_ano_formatado' => $dataReferencia->isoFormat('MMMM [de] YYYY'),
                    'dias_no_mes' => $diasNoMes,
                    'data_referencia' => $dataReferencia,
                    'frequencias' => $frequencias,
                ];
            }
            
            // 4. GERAÇÃO E ADIÇÃO DO PDF AO ZIP (UM PDF POR MÊS/Conjunto de Turmas)
            // 🏆 MUDANÇA: Verifica se turmas existem (isNotEmpty), garantindo relatório vazio se necessário
            if ($turmas->isNotEmpty()) { 
                
                // Define o nome do arquivo, incluindo a letra da turma se for uma única selecionada
                $turmaPrefix = count($turmaIds) === 1 ? 'Turma_' . $turmas->first()->letra . '_' : '';
                $pdfName = $turmaPrefix . $dataReferencia->isoFormat('MMMM_YYYY') . '.pdf';
                
                // Cria o PDF APENAS para os dados deste mês
                $pdf = Pdf::loadView('formacao.chamada.pdf_template', [
                    'turmasData' => $turmasDataParaMes, 
                ]);
                $pdf->setPaper('a4', 'landscape');
                
                // Salva o conteúdo na memória e adiciona ao ZIP
                $zip->addFromString($pdfName, $pdf->output());
                $relatoriosGerados++; // Incrementa o contador de sucesso
                
                // Limpar a variável do DomPDF ajuda a liberar memória para o próximo loop
                unset($pdf); 
            }
        } // Fim do loop de MESES

        // 5. Finaliza e fecha o arquivo ZIP
        $zip->close();
        
        // 6. Retorna o arquivo ZIP para download
        if ($relatoriosGerados > 0 && file_exists($zipPath)) {
             // download() com deleteFileAfterSend(true) remove o arquivo temporário após o download
            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        }

        // Se $relatoriosGerados for 0, as turmas/meses selecionados não existem ou não houve dados válidos para processar.
        return back()->with('error', 'Nenhum relatório foi gerado. Verifique se as turmas existem no período selecionado.');
    }
}