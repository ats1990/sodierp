@extends('layouts.app')

@section('styles')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel-stylesheet">

<style>
/* Estilos de Estrutura e Container */
.container-fluid {
    padding-top: 20px;
}

/* O wrapper da tabela deve controlar a rolagem X e Y */
.table-responsive-custom {
    overflow: auto;
    /* Permite rolagem horizontal e vertical conforme necessário */
    max-height: 500px;
    position: relative;
    width: 100%;
}

/* Estilo da Tabela */
.table-responsive-custom table {
    border-collapse: separate;
    border-spacing: 0;
    width: auto;
    min-width: 100%;
    /* Permite que a tabela se estenda e force a rolagem X */
    margin-bottom: 0;
}

/* Coluna Fixa (Aluno) */
.sticky-col {
    position: sticky;
    left: 0;
    background-color: #f8f9fa;
    z-index: 10;
    border-right: 1px solid #dee2e6;
    min-width: 120px;
    box-shadow: 2px 0 3px rgba(0, 0, 0, 0.05);
    padding-left: 1.25rem !important;
    padding-right: 1.25rem !important;
}

/* Cabeçalho Fixo (Dias) */
.sticky-header th {
    position: sticky;
    top: 0;
    background-color: #e9ecef;
    z-index: 20;
    padding: 3px 2px;
    line-height: 1;
}

/* Canto superior esquerdo fixo (intersecção) */
.sticky-header .sticky-col {
    background-color: #e9ecef;
    z-index: 30;
}

/* LARGURA DOS DIAS (MANTIDA EM 20PX PARA COMPACTAÇÃO) */
.sticky-header th:not(.sticky-col),
.status-cell {
    min-width: 20px;
    width: 20px;
    padding: 0;
}

/* Novas colunas de Média/Total */
.summary-col {
    min-width: 70px;
    /* Largura para as colunas de resultado */
    text-align: center;
}

/* Estilos de células de status para alinhamento */
.status-cell {
    text-align: center !important;
}

/* Estilos de Status de Frequência - MANTIDOS */
.status-cell {
    transition: background-color 0.15s;
    font-weight: bold;
}

.status-cell[data-can-click="true"] {
    cursor: pointer;
}

.status-cell[data-can-edit="true"] {
    cursor: cell !important;
}

.status-cell:hover[data-can-click="true"] {
    opacity: 0.85;
}

/* Cores dos Status - MANTIDOS */
.bg-success {
    background-color: #28a745 !important;
    color: white;
}

.bg-danger {
    background-color: #dc3545 !important;
    color: white;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #343a40;
}

.bg-light-day {
    background-color: #f0f0f0 !important;
    color: #6c757d;
}

.text-dark {
    color: #212529 !important;
}

.text-white {
    color: white !important;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h1><i class="bi bi-calendar-check me-2"></i> Chamada: Turma {{ $turma->letra }}</h1>
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Início</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('chamada.index') }}">Chamada</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Turma {{ $turma->letra }}</li>
                </ol>
            </nav>
        </div>
    </div>
    
    {{-- CARD: SEM CLASSE card-stretch-width - RESPEITA AS MARGENS DO TEMA --}}
    <div class="card shadow-sm mb-4">
        {{-- Removendo o padding padrão do card-body (p-0) --}}
        <div class="card-body p-0">
            
            {{-- Wrapper de navegação --}}
            <div class="p-4 pb-0">
                <div class="row align-items-center mb-4">
                    
                    {{-- Coluna de Navegação (Mês) --}}
                    <div class="col-12 col-md-8 d-flex align-items-center">
                        <a href="{{ route('chamada.show', ['turma' => $turma->id, 'mes_ano' => $data_referencia->copy()->subMonth()->format('Y-m')]) }}" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Mês Anterior
                        </a>
                        <span class="h4 mb-0 mx-3 text-center">{{ $data_referencia->isoFormat('MMMM [de] YYYY') }}</span>
                        <a href="{{ route('chamada.show', ['turma' => $turma->id, 'mes_ano' => $data_referencia->copy()->addMonth()->format('Y-m')]) }}" class="btn btn-sm btn-outline-secondary">
                            Mês Seguinte <i class="bi bi-arrow-right"></i>
                        </a>
                        
                    </div>

                    {{-- Coluna de Status (Última atualização) --}}
                    <div class="col-12 col-md-4 text-md-end mt-2 mt-md-0">
                        @if($ultimo_registro)
                            <small id="last-update" class="text-muted">
                                Última atualização por <strong>{{ $ultimo_registro->professor->nomeCompleto ?? 'Desconhecido' }}</strong> em {{ $ultimo_registro->updated_at->format('d/m/Y H:i:s') }}
                            </small>
                        @else
                            <small id="last-update" class="text-muted">Nenhum registro de frequência encontrado para este mês.</small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- O CONTAINER QUE CONTROLA A ROLAGEM HORIZONTAL E VERTICAL (DENTRO DO CARD) --}}
            <div class="table-responsive-custom">
                <table class="table table-bordered table-hover">
                    
                    {{-- CABEÇALHO (Fixado) --}}
                    <thead class="sticky-header">
                        <tr>
                            <th class="sticky-col text-center align-middle">Aluno</th>
                            @for ($dia = 1; $dia <= $dias_no_mes; $dia++)
                                @php
                                    $data_dia=\Carbon\Carbon::createFromDate($data_referencia->year, $data_referencia->month, $dia);
                                    $dias_semana_br = [0 => 'D', 1 => 'S', 2 => 'T', 3 => 'Q', 4 => 'Q', 5 => 'S', 6 => 'S'];
                                    $dia_index = $data_dia->dayOfWeek;
                                    $dia_semana_curto = $dias_semana_br[$dia_index] ?? '-';
                                    $is_weekend = in_array($dia_index, [0, 6]);
                                    $header_class = $is_weekend ? 'bg-secondary text-white' : 'bg-primary text-white';
                                @endphp
                                <th class="text-center {{ $header_class }}" title="{{ $data_dia->isoFormat('dddd') }}">
                                    {{ $dia_semana_curto }}<br>{{ $dia }}
                                </th>
                            @endfor
                            
                            {{-- ALTERAÇÃO AQUI: De Média Pres. para Total Pres. --}}
                            <th class="text-center bg-info text-white summary-col align-middle" style="min-width: 80px;">Total Pres.</th>
                            <th class="text-center bg-dark text-white summary-col align-middle" style="min-width: 80px;">Total Faltas</th>
                        </tr>
                        
                    </thead>

                    {{-- CORPO DA TABELA --}}
                    <tbody>
                        @forelse ($alunos as $aluno)
                            <tr>
                                {{-- COLUNA FIXA --}}
                                <td class="sticky-col align-middle">
                                    <strong title="{{ $aluno->nomeCompleto }}">{{ $aluno->nome_exibicao }}</strong>
                                </td>
                                
                                {{-- CÉLULAS DOS DIAS --}}
                                @for ($dia = 1; $dia <= $dias_no_mes; $dia++)
                                    @php
                                        // Variáveis de data e status... (MANTIDAS)
                                        $data_dia=\Carbon\Carbon::createFromDate($data_referencia->year, $data_referencia->month, $dia);
                                        $chave_presenca = $aluno->id . '-' . $dia;
                                        $presenca = $presencas->get($chave_presenca);
                                        $is_weekend = in_array($data_dia->dayOfWeek, [0, 6]);
                                        
                                        $status_presenca = $presenca ? ($presenca->presente ? 1 : 0) : null;
                                        $is_justificada = $presenca ? ($presenca->justificada ?? false) : false;

                                        $status_class = ''; $status_text = '-'; $can_click = 'true'; $can_edit = 'true';
                                        
                                        if ($status_presenca === 1) {
                                            $status_class = 'bg-success'; $status_text = 'P';
                                        } elseif ($status_presenca === 0 && $is_justificada) {
                                            $status_class = 'bg-warning text-dark'; $status_text = 'J'; $can_click = 'false';
                                        } elseif ($status_presenca === 0) {
                                            $status_class = 'bg-danger'; $status_text = 'F';
                                        } elseif ($is_weekend) {
                                            $status_class = 'bg-light-day text-dark'; $status_text = 'D'; $can_click = 'false'; $can_edit = 'false';
                                        } else {
                                            $status_class = ''; $status_text = '-';
                                        }
                                        $full_date = $data_dia->format('Y-m-d');
                                        
                                    @endphp
                                    
                                    <td class="status-cell align-middle {{ $status_class }}"
                                        data-aluno-id="{{ $aluno->id }}"
                                        data-data="{{ $full_date }}"
                                        data-aluno-nome="{{ $aluno->nome_exibicao }}"
                                        data-status="{{ $status_presenca }}"
                                        data-is-justificada="{{ $is_justificada ? '1' : '0' }}"
                                        data-can-click="{{ $can_click }}"
                                        data-can-edit="{{ $can_edit }}"
                                        title="{{ $can_click === 'true' ? 'Clique: alternar P/F | Duplo Clique: Abrir edição (J)' : 'Duplo Clique: Abrir edição (J)' }}"
                                    >
                                        {{ $status_text }}
                                    </td>
                                @endfor
                                
                                {{-- ALTERAÇÃO AQUI: Usa total_presencas em vez de media_presencas e remove formatação % --}}
                                <td class="summary-col align-middle text-info">
                                    <strong>{{ $aluno->total_presencas ?? 0 }}</strong>
                                </td>
                                <td class="summary-col align-middle text-danger">
                                    <strong>{{ $aluno->total_faltas ?? 0 }}</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                {{-- AJUSTE: dias_no_mes + 1 (aluno) + 2 (novas colunas) = +3 --}}
                                <td colspan="{{ $dias_no_mes + 3 }}" class="text-center">Nenhum aluno encontrado nesta turma.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Wrapper de Legenda --}}
            <div class="p-4 pt-4">
                <p class="mt-4 mb-0">
                    <span class="badge bg-success">P</span> Presença |
                    <span class="badge bg-danger">F</span> Falta Injustificada |
                    <span class="badge bg-warning text-dark">J</span> Falta Justificada (Atestado) |
                    <span class="badge bg-secondary">-</span> Não Lançado |
                    <span class="badge bg-light-day text-dark">D</span> Dia de Descanso / Não Letivo
                </p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editFrequencyModal" tabindex="-1" aria-labelledby="editFrequencyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editFrequencyModalLabel">Registrar Status: <span id="modalAlunoNome"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Data: <strong id="modalDataCompleta"></strong></p>

                <div class="mb-3">
                    <label for="modalStatusSelect" class="form-label">Status de Frequência</label>
                    <select class="form-select" id="modalStatusSelect">
                        <option value="1">P - Presença</option>
                        <option value="0-0">F - Falta Injustificada</option>
                        <option value="0-1">J - Falta Justificada (Com Atestado/Motivo)</option>
                        
                    </select>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="modalJustificadaCheck" disabled>
                    <label class="form-check-label" for="modalJustificadaCheck">
                        Falta Justificada (Marcado automaticamente com a opção 'J')
                    </label>
                </div>

                <div class="mb-3">
                    <label for="modalMotivo" class="form-label">Motivo/Observação (Opcional)</label>
                    <textarea class="form-control" id="modalMotivo" rows="2" placeholder="Ex: Atestado médico, viagem familiar."></textarea>
                </div>

                <input type="hidden" id="modalAlunoId">
                <input type="hidden" id="modalData">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="saveFrequencyButton">Salvar Status</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // CORREÇÃO DE SINTAXE BLADE: Usa o JSON para converter booleano PHP em boolean JavaScript
        const canAlter = {{ $can_alter ? 'true' : 'false' }};
        const turmaId = {{ $turma->id }};
        const mesAno = '{{ $mes_ano }}';
        const csrfToken = '{{ csrf_token() }}';
        const lastUpdateElement = document.getElementById('last-update');
        const modalElement = new bootstrap.Modal(document.getElementById('editFrequencyModal'));

        const statusCells = document.querySelectorAll('.status-cell');

        if (!canAlter) {
            document.querySelectorAll('.status-cell').forEach(cell => cell.style.cursor = 'default');
            return;
        }

        statusCells.forEach(cell => {
            if (cell.getAttribute('data-can-click') === 'true') {
                cell.addEventListener('click', function() {
                    const alunoId = this.getAttribute('data-aluno-id');
                    const data = this.getAttribute('data-data');
                    const currentStatus = this.getAttribute('data-status');

                    const newPresente = (currentStatus === '1') ? 0 : 1;

                    updateCellVisual(this, newPresente, 0);
                    savePresenca(alunoId, data, newPresente, 0, '');
                });
            }

            if (cell.getAttribute('data-can-edit') === 'true') {
                cell.classList.add('data-can-edit');
                cell.addEventListener('dblclick', function(e) {
                    e.preventDefault();
                    openJustificationModal(this);
                });
            }
        });

        document.getElementById('saveFrequencyButton').addEventListener('click', function() {
            const alunoId = document.getElementById('modalAlunoId').value;
            const data = document.getElementById('modalData').value;
            const statusValue = document.getElementById('modalStatusSelect').value;
            const motivo = document.getElementById('modalMotivo').value;

            const parts = statusValue.split('-');
            const presente = parseInt(parts[0]);
            const justificada = parts.length > 1 ? parseInt(parts[1]) : 0;

            const cellElement = document.querySelector(`.status-cell[data-aluno-id="${alunoId}"][data-data="${data}"]`);

            if (cellElement) {
                updateCellVisual(cellElement, presente, justificada);
                savePresenca(alunoId, data, presente, justificada, motivo);
            }

            modalElement.hide();
        });

        document.getElementById('modalStatusSelect').addEventListener('change', function() {
            const isJustificada = this.value === '0-1';
            document.getElementById('modalJustificadaCheck').checked = isJustificada;
        });


        function openJustificationModal(cell) {
            const alunoId = cell.getAttribute('data-aluno-id');
            const data = cell.getAttribute('data-data');
            const alunoNome = cell.getAttribute('data-aluno-nome');
            const currentStatus = cell.getAttribute('data-status');
            const isJustificada = cell.getAttribute('data-is-justificada');

            const dateObj = new Date(data + 'T00:00:00');
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('modalDataCompleta').textContent = dateObj.toLocaleDateString('pt-BR', options);

            document.getElementById('modalAlunoNome').textContent = alunoNome;
            document.getElementById('modalAlunoId').value = alunoId;
            document.getElementById('modalData').value = data;
            document.getElementById('modalMotivo').value = '';

            let selectValue = '0-0';
            if (currentStatus === '1') {
                selectValue = '1';
            } else if (currentStatus === '0' && isJustificada === '1') {
                selectValue = '0-1';
            }
            document.getElementById('modalStatusSelect').value = selectValue;
            document.getElementById('modalJustificadaCheck').checked = (selectValue === '0-1');

            modalElement.show();
        }

        function updateCellVisual(cellElement, newPresente, newJustificada) {
            cellElement.setAttribute('data-status', newPresente);
            cellElement.setAttribute('data-is-justificada', newJustificada);

            cellElement.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-light-day', 'text-dark');
            cellElement.textContent = '-';

            const isDescanso = cellElement.textContent === 'D';
            cellElement.setAttribute('data-can-click', isDescanso ? 'false' : 'true');

            if (newPresente === 1) {
                cellElement.classList.add('bg-success');
                cellElement.textContent = 'P';
            } else if (newPresente === 0 && newJustificada === 1) {
                cellElement.classList.add('bg-warning', 'text-dark');
                cellElement.textContent = 'J';
                cellElement.setAttribute('data-can-click', 'false');
            } else if (newPresente === 0) {
                cellElement.classList.add('bg-danger');
                cellElement.textContent = 'F';
            }
        }

        function savePresenca(alunoId, data, presente, justificada, motivo) {
            const url = `{{ url('chamada') }}/${turmaId}/${mesAno}`;

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        aluno_id: alunoId,
                        data: data,
                        presente: presente,
                        justificada: justificada,
                        motivo: motivo,
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Falha na resposta do servidor.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (lastUpdateElement) {
                        lastUpdateElement.innerHTML = `Última atualização por 
<strong>${data.professor}</strong> em ${data.updated_at}`;
                    }
                })
                .catch(error => {
                    console.error('Erro ao salvar frequência:', error);
                    // Use um modal ou notificação no lugar de alert()
                    // alert('Erro ao salvar frequência. A página será recarregada.');
                    // location.reload();
                });
        }
    });
</script>
@endpush