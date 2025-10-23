@extends('layouts.app') 

@section('content')
{{-- INÍCIO DA ALTERAÇÃO NO page-header: Adicionar o botão "Gerar PDF" --}}
<div class="page-header d-flex justify-content-between align-items-center">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-calendar-check"></i>
        </span> Chamada de Frequência
    </h3>
    {{-- NOVO BOTÃO: Gerar PDF --}}
    {{-- O data-bs-target aponta para o modal definido abaixo --}}
    <button type="button" class="btn btn-outline-danger btn-icon-text" data-bs-toggle="modal" data-bs-target="#pdfModal" id="openPdfModalBtn">
        <i class="mdi mdi-file-pdf btn-icon-prepend"></i> Gerar PDF
    </button>
</div>
{{-- FIM DA ALTERAÇÃO NO page-header --}}

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Selecione a Turma</h4>
                
                {{-- REMOVIDO: O Seletor de Mês Único foi retirado daqui --}}
                
                <div class="row mt-4" id="turma-cards">
                    @forelse ($turmas as $turma)
                        <div class="col-md-4 mb-4">
                            <div class="card card-hover-shadow h-100">
                                {{-- Centralizando o conteúdo do card --}}
                                <div class="card-body d-flex flex-column text-center">
                                    
                                    {{-- TÍTULO DO CARD: Turma A --}}
                                    <h5 class="card-title">Turma {{ $turma->letra }}</h5>
                                    
                                    {{-- Contagem de Alunos --}}
                                    <p class="card-text text-muted">
                                        {{ $turma->alunos->count() }} Alunos
                                    </p>
                                    
                                    {{-- BOTÃO: Lançar Frequência --}}
                                    {{-- O LINK SERÁ CONSTRUÍDO PELO JAVASCRIPT/BLADE --}}
                                    <a href="#" 
                                        class="btn btn-gradient-primary mt-auto select-turma-btn mx-auto" 
                                        data-turma-id="{{ $turma->id }}"
                                    >
                                        Lançar Frequência 
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p>Nenhuma turma de Formação Básica encontrada.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>

{{-- NOVO: Modal de Geração de PDF (HTML FALTANTE) --}}
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="pdfModalLabel"><i class="mdi mdi-file-pdf me-2"></i> Gerar Relatório de Chamada (PDF)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="pdfForm" action="{{ route('chamada.pdf.generate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    
                    {{-- Seleção de Turma --}}
                    <div class="mb-3">
                        <label for="turmaSelect" class="form-label">Turma(s) (Selecione 1 ou mais)</label>
                        <select class="form-select" id="turmaSelect" name="turma_ids[]" multiple size="4">
                            <option value="" selected>Todas as Turmas</option> 
                            {{-- Opções carregadas via AJAX/JS --}}
                        </select>
                        <small class="form-text text-muted">Use CTRL/CMD para selecionar múltiplas turmas.</small>
                    </div>

                    {{-- Seleção de Mês --}}
                    <div class="mb-3">
                        <label for="mesSelect" class="form-label">Mês(es) de Referência (Selecione 1 ou mais)</label>
                        <select class="form-select" id="mesSelect" name="mes_anos[]" multiple size="4">
                            <option value="" selected>Todos os Meses (Últimos 12)</option>
                            {{-- Opções carregadas via AJAX/JS --}}
                        </select>
                        <small class="form-text text-muted">Use CTRL/CMD para selecionar múltiplos meses.</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger" id="generatePdfBtn">
                        <i class="mdi mdi-download me-1"></i> Gerar PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- FIM DO NOVO CÓDIGO HTML --}}

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const turmaCards = document.getElementById('turma-cards');
        const buttons = turmaCards.querySelectorAll('.select-turma-btn');

        // Mês atual é obtido em PHP para garantir o formato correto (YYYY-MM)
        const currentMonth = '{{ $mes_ano_atual }}';

        // 1. Lógica para atualizar os links (EXISTENTE)
        function updateTurmaLinks() {
            buttons.forEach(button => {
                const turmaId = button.getAttribute('data-turma-id');
                
                // O link agora aponta SEMPRE para a turma e para o mês atual
                const url = `{{ url('chamada') }}/${turmaId}/${currentMonth}`;
                button.setAttribute('href', url);
            });
        }
        updateTurmaLinks();

        // 2. Lógica do Modal PDF (CORRIGIDA)
        const turmaSelect = document.getElementById('turmaSelect');
        const mesSelect = document.getElementById('mesSelect');
        const openPdfModalBtn = document.getElementById('openPdfModalBtn');
        const generatePdfBtn = document.getElementById('generatePdfBtn');
        const pdfForm = document.getElementById('pdfForm');

        function loadPdfModalOptions() {
            // Evita recarregar se já houver opções além do padrão
            if (turmaSelect.children.length > 1 && mesSelect.children.length > 1) {
                return; 
            }

            // Chamada AJAX para o método showPdfForm
            fetch("{{ route('chamada.pdf.form') }}")
                .then(response => {
                    // A CORREÇÃO CRÍTICA ESTÁ AQUI: Checar o status da resposta (ex: 403 Forbidden)
                    if (!response.ok) {
                        if (response.status === 403) {
                            throw new Error('Permissão negada. Verifique suas credenciais na PresencaPolicy.');
                        } else if (response.status === 404) {
                            throw new Error('Rota de dados não encontrada. Limpe o cache de rotas.');
                        }
                        throw new Error('Erro do servidor (' + response.status + ') ao carregar dados. (Verifique o log do servidor)');
                    }
                    // Só tentamos analisar como JSON se a resposta for 200 OK
                    return response.json();
                })
                .then(data => {
                    // Popula Turmas
                    data.turmas.forEach(turma => {
                        const option = document.createElement('option');
                        option.value = turma.id;
                        option.textContent = `Turma ${turma.letra}`;
                        turmaSelect.appendChild(option);
                    });

                    // Popula Meses
                    data.meses.forEach(mes => {
                        const option = document.createElement('option');
                        option.value = mes.valor;
                        option.textContent = mes.nome;
                        mesSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Erro ao carregar dados do PDF:', error);
                    alert('Falha ao carregar opções do PDF: ' + error.message);
                });
        }
        
        if(openPdfModalBtn) {
            openPdfModalBtn.addEventListener('click', loadPdfModalOptions);
        }

        // Listener para submissão do formulário: gerencia o estado do botão
        pdfForm.addEventListener('submit', function(e) {
            // Lógica para desmarcar 'Todas as Turmas/Meses' se opções específicas forem escolhidas
            const selectedTurmas = Array.from(turmaSelect.options).filter(opt => opt.selected && opt.value !== "");
            if (selectedTurmas.length > 0) {
                 turmaSelect.querySelector('option[value=""]').selected = false;
            }
            
            const selectedMeses = Array.from(mesSelect.options).filter(opt => opt.selected && opt.value !== "");
            if (selectedMeses.length > 0) {
                 mesSelect.querySelector('option[value=""]').selected = false;
            }

            // Desabilita o botão e mostra o spinner
            generatePdfBtn.disabled = true;
            generatePdfBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Gerando PDF...';
        });
        
        // Habilita o botão em caso de fechamento do modal antes da submissão
        document.getElementById('pdfModal').addEventListener('hidden.bs.modal', function() {
            generatePdfBtn.disabled = false;
            generatePdfBtn.innerHTML = '<i class="mdi mdi-download me-1"></i> Gerar PDF';
        });
    });
</script>
@endpush