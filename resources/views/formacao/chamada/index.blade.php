@extends('layouts.app') 

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-calendar-check"></i>
        </span> Chamada de Frequência
    </h3>
</div>

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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const turmaCards = document.getElementById('turma-cards');
        const buttons = turmaCards.querySelectorAll('.select-turma-btn');

        // Mês atual é obtido em PHP para garantir o formato correto (YYYY-MM)
        // Usamos uma variável Blade, que agora não precisa de JS dinâmico
        const currentMonth = '{{ $mes_ano_atual }}';

        // Função para atualizar os links dos botões
        function updateTurmaLinks() {
            buttons.forEach(button => {
                const turmaId = button.getAttribute('data-turma-id');
                
                // O link agora aponta SEMPRE para a turma e para o mês atual
                // Estrutura da URL: /chamada/{turma_id}/{YYYY-MM_ATUAL}
                const url = `{{ url('chamada') }}/${turmaId}/${currentMonth}`;
                button.setAttribute('href', url);
            });
        }

        // 1. Atualiza ao carregar a página
        updateTurmaLinks();

        // 2. O listener de 'change' no mês foi removido, pois o input não existe mais.
    });
</script>
@endpush