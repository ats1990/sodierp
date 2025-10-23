<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Frequência</title>
    {{-- Para PDF, use estilos inline ou um bloco <style> --}}
    <style>
        /* Estilos gerais */
        body { font-family: sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0 0 0; font-size: 12px; }

        /* Estilos da Tabela (Crucial para o formato) */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 10px; /* Fonte menor para caber mais dias */
            table-layout: fixed; /* Ajuda a gerenciar a largura das colunas */
        }
        th, td { 
            border: 1px solid #000; 
            padding: 3px; 
            text-align: center; 
            height: 15px;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        th.nome { 
            width: 200px; /* Largura fixa para o nome */
            text-align: left; 
            font-size: 11px;
        }
        td.nome { 
            text-align: left; 
            padding-left: 5px;
            white-space: nowrap;
            overflow: hidden;
            font-size: 10px;
        }
        /* As demais colunas terão a largura dividida automaticamente pelo browser/DomPDF */

        /* Oculta o rodapé e cabeçalho do navegador na impressão */
        @media print {
            @page { margin: 1cm; size: landscape; }
            body { margin: 0; }
        }
    </style>
    
    {{-- Script para impressão --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.search.includes('action=print')) {
                window.print();
            }
        });
    </script>
</head>
<body>
    
    @php
        // Bloco de lógica para cálculo dos dias do mês (essencial para a tabela)
        
        $maxDia = 0;
        $periodoFormatado = 'Todos os Meses';
        
        if ($mesAno !== 'all' && strpos($mesAno, '-') !== false) {
            try {
                $dataObj = \Carbon\Carbon::createFromFormat('Y-m', $mesAno);
                $maxDia = $dataObj->daysInMonth;
                $periodoFormatado = strtoupper($dataObj->locale('pt_BR')->isoFormat('MMMM YYYY'));
            } catch (\Exception $e) {
                $maxDia = 31; 
                $periodoFormatado = 'Mês Inválido';
            }
        } else {
             $maxDia = 31;
        }
        
        // 2. Extrai a lista de dias que realmente possuem registros (para otimização de largura)
        $diasComRegistro = [];
        foreach ($frequencias as $aluno) {
            if (!empty($aluno->dias_frequencia)) {
                $diasComRegistro = array_merge($diasComRegistro, array_keys((array)$aluno->dias_frequencia));
            }
        }
        $diasComRegistro = array_unique($diasComRegistro);
        $diasComRegistroFormatado = array_map('intval', $diasComRegistro);
        sort($diasComRegistroFormatado);
        
        // Determina os dias a serem exibidos no relatório (1 a 31 ou apenas dias com registro)
        $diasRelatorio = [];
        if ($mesAno !== 'all') {
            for ($i = 1; $i <= $maxDia; $i++) {
                $diasRelatorio[] = $i;
            }
        } else {
             $diasRelatorio = $diasComRegistroFormatado;
        }
        
    @endphp

    <div class="header">
        <h1>Relatório de Frequência - Administrativo I</h1>
        <p>
            Turma: **{{ $turmaId === 'all' ? 'Todas' : 'Turma ' . $turmaId }}** @if ($mesAno !== 'all')
                | Mês: **{{ $periodoFormatado }}** @endif
        </p>
        <p>
            ADMINISTRATIVO I - SEGUNDA - {{ $periodoFormatado }}
        </p>
    </div>

    @if (empty($frequencias))
        <div style="text-align: center; margin-top: 50px; font-size: 12pt; color: #777;">
            Nenhum dado de frequência encontrado para os filtros selecionados.
        </div>
    @else
        
        <table>
            <thead>
                <tr>
                    <th class="nome" rowspan="2">Nome</th>
                    {{-- CORREÇÃO: Colspan deve ser o número de dias no relatório --}}
                    <th colspan="{{ count($diasRelatorio) }}">Dias do Mês</th>
                </tr>
                <tr>
                    {{-- Cabeçalho dos Dias (1, 2, 3, ...) --}}
                    @foreach ($diasRelatorio as $dia)
                        <th>{{ $dia }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($frequencias as $aluno)
                    <tr>
                        <td class="nome">{{ $aluno->nome ?? 'Aluno sem nome' }}</td>
                        
                        {{-- Células de Frequência por Dia --}}
                        @foreach ($diasRelatorio as $dia)
                            @php
                                $diaKey = str_pad($dia, 2, '0', STR_PAD_LEFT);
                                // Pega a marca de frequência ('P', 'F', 'E', ou nulo)
                                // Usamos (array) para garantir que possamos acessar dias_frequencia
                                $marca = ((array)($aluno->dias_frequencia ?? []))[$diaKey] ?? '';
                            @endphp
                            <td>{{ $marca }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 20px; font-size: 9px; page-break-inside: avoid;">
            <p><strong>Legenda:</strong> P = Presente | F = Falta | E = Falta Justificada</p>
            <p>Relatório Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    
    @endif

</body>
</html>
