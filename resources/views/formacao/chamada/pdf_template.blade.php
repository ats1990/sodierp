<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório de Chamada - Frequência</title>
    <style>
        /* Estilos básicos para o PDF */
        body { font-family: 'Arial', sans-serif; font-size: 10px; margin: 0; padding: 0; }
        .page-break { page-break-after: always; }
        .relatorio { width: 100%; margin: 10px 0; }
        .header-mes { text-transform: uppercase; font-size: 14px; font-weight: bold; margin-bottom: 5px; text-align: center;}
        .header-turma { font-size: 12px; font-weight: bold; margin-bottom: 10px; text-align: center;}

        /* Estilo da Tabela */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 2px 1px; text-align: center; height: 18px; line-height: 1.2; }
        
        /* Larguras das Colunas (Ajuste para A4 Landscape) */
        .col-nome { width: 25%; text-align: left !important; padding-left: 5px; font-size: 9px; }
        .col-dia { width: 1.8%; } /* Cada coluna de dia deve ter menos de 2% para caber 31 dias + totais */
        .col-total { width: 4%; font-weight: bold; }
        
        /* Cores de Status */
        .status-P { background-color: #28a745; color: white; } /* Presente (Verde) */
        .status-F { background-color: #dc3545; color: white; } /* Falta Injustificada (Vermelho) */
        .status-J { background-color: #ffc107; color: #343a40; } /* Falta Justificada (Amarelo) */
        .status-D { background-color: #f0f0f0; color: #6c757d; } /* Descanso/Fim de Semana */
        .status- { background-color: white; color: #000; } /* Não lançado */
    </style>
</head>
<body>

    @foreach ($turmasData as $data)
        <div class="relatorio">
            <div class="header-mes">{{ $data['mes_ano_formatado'] }}</div>
            <div class="header-turma">TURMA "{{ $data['turma_letra'] }}"</div>

            <table>
                <thead>
                    <tr>
                        <th class="col-nome" rowspan="2">Nome</th>
                        {{-- Cabeçalho: Dia da Semana (D, S, T, Q, Q, S, S) --}}
                        @for ($dia = 1; $dia <= $data['dias_no_mes']; $dia++)
                            @php
                                $data_dia = $data['data_referencia']->copy()->day($dia);
                                $dia_semana_curto = ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'][$data_dia->dayOfWeek];
                            @endphp
                            <th class="col-dia">{{ $dia_semana_curto }}</th>
                        @endfor
                        <th class="col-total" rowspan="2">Total P.</th>
                        <th class="col-total" rowspan="2">Total F.</th>
                    </tr>
                    <tr>
                        {{-- Cabeçalho: Número do Dia (1, 2, 3...) --}}
                        @for ($dia = 1; $dia <= $data['dias_no_mes']; $dia++)
                            <th class="col-dia">{{ $dia }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @php $contador = 1; @endphp
                    @forelse ($data['frequencias'] as $frequencia)
                        <tr>
                            <td class="col-nome">{{ $contador++ }}. {{ $frequencia['aluno_nome'] }}</td>
                            @for ($dia = 1; $dia <= $data['dias_no_mes']; $dia++)
                                @php
                                    $data_dia = $data['data_referencia']->copy()->day($dia);
                                    $is_dia_letivo = !in_array($data_dia->dayOfWeek, [0, 6]);
                                    // A chave é o número do dia
                                    $presenca = $frequencia['presencas_map']->get($dia); 

                                    $status_text = '';
                                    $status_class = '';

                                    if (!$is_dia_letivo) {
                                        $status_text = 'D'; // Descanso
                                        $status_class = 'status-D';
                                    } elseif ($presenca) {
                                        if ($presenca->presente) {
                                            $status_text = 'P'; // Presente
                                            $status_class = 'status-P';
                                        } elseif ($presenca->justificada) {
                                            $status_text = 'J'; // Falta Justificada
                                            $status_class = 'status-J';
                                        } else {
                                            $status_text = 'F'; // Falta Injustificada
                                            $status_class = 'status-F';
                                        }
                                    } else {
                                         // Dia letivo sem registro
                                        $status_text = '-'; 
                                        $status_class = 'status-';
                                    }
                                @endphp
                                <td class="col-dia {{ $status_class }}">{{ $status_text }}</td>
                            @endfor
                            <td class="col-total">{{ $frequencia['total_presencas'] }}</td>
                            <td class="col-total">{{ $frequencia['total_faltas'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $data['dias_no_mes'] + 3 }}" style="text-align: left; padding: 10px;">Nenhum aluno encontrado para a Turma "{{ $data['turma_letra'] }}" no mês de {{ $data['mes_ano_formatado'] }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Quebra de página se houver mais turmas/meses para exibir --}}
        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>
</html>