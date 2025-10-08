<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirmação de Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Você tem certeza que deseja excluir a turma **<span id="turmaNomeToDelete" class="fw-bold text-danger"></span>**?</p>
                <p class="text-danger small"><strong>Atenção:</strong> Esta ação é irreversível e irá desvincular quaisquer alunos desta turma (eles se tornarão alunos 'disponíveis' novamente).</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                {{-- O JS em index.blade.php (se existir) enviará o formulário DELETE --}}
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Excluir Permanentemente</button>
            </div>
        </div>
    </div>
</div>
