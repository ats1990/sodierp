<div class="modal fade" id="deleteAllConfirmationModal" tabindex="-1" aria-labelledby="deleteAllConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="deleteAllConfirmationModalLabel">Confirmação de Exclusão em Massa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Você está prestes a excluir **TODAS** as turmas registradas no sistema.</p>
                <p class="text-danger fw-bold">ESTA AÇÃO É EXTREMAMENTE PERIGOSA E IRREVERSÍVEL!</p>
                <p class="small">Todos os vínculos de alunos com turmas serão perdidos e os alunos se tornarão "disponíveis" novamente.</p>

                <div class="alert alert-danger mt-3" role="alert">
                    Para confirmar, digite o texto **CONFIRMAR EXCLUSÃO TOTAL** no campo abaixo:
                </div>
                
                {{-- Campo de confirmação de texto --}}
                <input type="text" id="confirmationText" class="form-control" placeholder="CONFIRMAR EXCLUSÃO TOTAL">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                
                {{-- Form que envia para a rota de exclusão em massa --}}
                <form id="deleteAllTurmasForm" action="{{ route('formacao.turmas.destroy.all') }}" method="POST">
                    @csrf
                    {{-- O @method('DELETE') não é necessário, pois a rota está definida como POST no web.php, conforme verificado anteriormente. --}}
                    <button type="submit" class="btn btn-danger" id="confirmDeleteAllButton" disabled>
                        Excluir TODAS as Turmas
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Lógica para habilitar o botão de exclusão em massa apenas após digitar o texto de confirmação
    document.addEventListener('DOMContentLoaded', function() {
        const confirmationInput = document.getElementById('confirmationText');
        const confirmDeleteAllButton = document.getElementById('confirmDeleteAllButton');
        const requiredText = 'CONFIRMAR EXCLUSÃO TOTAL';

        if (confirmationInput && confirmDeleteAllButton) {
            confirmationInput.addEventListener('input', function() {
                // Remove espaços em branco antes e depois da comparação
                const isConfirmed = confirmationInput.value.trim().toUpperCase() === requiredText;
                confirmDeleteAllButton.disabled = !isConfirmed;
            });
            
            // Adiciona listener ao formulário para prevenir o envio se o botão estiver desabilitado por algum motivo
            const deleteAllTurmasForm = document.getElementById('deleteAllTurmasForm');
            if (deleteAllTurmasForm) {
                deleteAllTurmasForm.addEventListener('submit', function(event) {
                    if (confirmDeleteAllButton.disabled) {
                        event.preventDefault();
                    }
                });
            }
        }
    });
</script>
@endpush
