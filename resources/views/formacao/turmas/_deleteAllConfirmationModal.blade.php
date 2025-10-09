<div class="modal fade" id="deleteAllTurmasModal" tabindex="-1" aria-labelledby="deleteAllTurmasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAllTurmasModalLabel">Confirmação de Exclusão Total</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>ATENÇÃO:</strong> Esta é uma ação irreversível e perigosa!</p>
                <p>Você está prestes a **excluir TODAS as turmas** de formação da base de dados.</p>
                <p class="mt-3">Esta ação limpará a tabela de turmas e removerá a associação de todos os professores e alunos.</p>
                <p class="fw-bold text-danger">Confirme que deseja continuar. Todas as turmas serão perdidas.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                
                {{-- O FORMULÁRIO QUE CHAMA O CONTROLLER --}}
                <form id="deleteAllTurmasForm" method="POST" action="{{ route('formacao.turmas.destroyAll') }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    {{-- O botão de envio deve ser type="submit" para enviar o formulário --}}
                    <button type="submit" class="btn btn-danger">Sim, Excluir TODAS as Turmas</button>
                </form>
            </div>
        </div>
    </div>
</div>