<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Lista de Usuários Cadastrados</h4>
                <p class="card-description"> Gerencie o status e as informações dos usuários. Clique no **Nome Completo** para editar.</p>

                {{-- Mensagens de feedback (Livewire usa session()->flash) --}}
                @if(session()->has('success'))
                <div class="alert alert-success mt-3">{{ session('success') }}</div>
                @endif
                @if(session()->has('error'))
                <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                @endif

                {{-- Exibe erros de validação --}}
                @error('editedData.*')
                <div class="alert alert-danger mt-3">{{ $message }}</div>
                @enderror

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nome Completo</th>
                                <th>E-mail</th>
                                <th>Tipo</th>
                                <th>Status Atual</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($usuarios as $usuario)
                            {{-- A chave 'wire:key' é essencial para o Livewire gerenciar listas --}}
                            <tr wire:key="{{ $usuario->id }}">

                                {{-- COLUNA NOME COMPLETO: Clique para entrar em modo de edição --}}
                                <td class="align-middle"
                                    style="cursor: pointer;"
                                    {{-- Só permite o clique se a linha não estiver sendo editada --}}
                                    @if ($editingUserId !==$usuario->id) wire:click="edit({{ $usuario->id }})" @endif>

                                    @if ($editingUserId === $usuario->id)
                                    {{-- Se estiver editando, mostra o input --}}
                                    <input type="text" wire:model.blur="editedData.nomeCompleto" class="form-control form-control-sm">
                                    @else
                                    {{-- Se não estiver editando, mostra o texto --}}
                                    <span style="font-weight: bold;">{{ $usuario->nomeCompleto }}</span>
                                    @endif
                                </td>

                                {{-- COLUNA E-MAIL: Edição In-line --}}
                                <td class="align-middle">
                                    @if ($editingUserId === $usuario->id)
                                    <input type="email" wire:model.blur="editedData.email" class="form-control form-control-sm">
                                    @else
                                    {{ $usuario->email }}
                                    @endif
                                </td>

                                {{-- COLUNA TIPO: Edição In-line --}}
                                <td class="align-middle">
                                    @if ($editingUserId === $usuario->id)
                                    <select wire:model.blur="editedData.tipo" class="form-control form-control-sm">
                                        <option value="coordenacao">Coordenação</option>
                                        <option value="administracao">Administração</option>
                                        <option value="professor">Professor</option>
                                        <option value="psicologo">Psicólogo</option>
                                    </select>
                                    @else
                                    {{ ucfirst($usuario->tipo) }}
                                    @endif
                                </td>

                                {{-- COLUNA STATUS --}}
                                <td class="align-middle">
                                    @if ($usuario->status === 'ativo')
                                    <label class="badge badge-success">Ativo</label>
                                    @else
                                    <label class="badge badge-danger">Inativo</label>
                                    @endif
                                </td>

                                {{-- COLUNA AÇÕES --}}
                                <td class="align-middle">
                                    @if ($editingUserId === $usuario->id)
                                    {{-- Se estiver editando, mostra Salvar e Cancelar --}}
                                    <button wire:click="save({{ $usuario->id }})" class="btn btn-sm btn-primary">
                                        Salvar
                                    </button>
                                    <button wire:click="cancelEdit" class="btn btn-sm btn-light">
                                        Cancelar
                                    </button>
                                    @else
                                    {{-- Se não estiver editando, mostra Ativar/Desativar --}}
                                    <button
                                        wire:click="toggleStatus({{ $usuario->id }})"
                                        class="btn btn-sm {{ $usuario->status === 'ativo' ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                        {{ $usuario->status === 'ativo' ? 'Desativar' : 'Ativar' }}
                                    </button>

                                    {{-- 
                                    ✅ ALTERADO: Usando window.confirm() para exigir confirmação. 
                                    O botão só chama deleteUser() se o usuário clicar em OK.
                                    --}}
                                    <button
                                        x-data
                                        @click="confirm('Tem certeza que deseja EXCLUIR o usuário {{ $usuario->nomeCompleto }}? Esta ação é irreversível!') ? $wire.deleteUser({{ $usuario->id }}) : false"
                                        class="btn btn-sm btn-danger"
                                        style="margin-left: 5px;">
                                        Excluir
                                    </button>
                                    
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Nenhum usuário encontrado.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>