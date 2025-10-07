<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Usuario; // 🚨 VERIFIQUE SE O NOME DO SEU MODEL É 'Usuario' OU 'User' 🚨
use Illuminate\Validation\Rule;

class GerenciarUsuarios extends Component
{
    // Propriedades para controle de estado
    public $editingUserId = null; // ID do usuário atualmente em modo de edição
    public $editedData = []; // Dados sendo editados (Nome, Email, Tipo)

    // -----------------------------------------------------
    // MÉTODOS DE EDIÇÃO IN-LINE
    // -----------------------------------------------------

    // Entra no modo de edição de uma linha, preenchendo os dados
    public function edit($userId)
    {
        $user = Usuario::findOrFail($userId);
        
        $this->editingUserId = $userId;
        
        // Carrega os dados atuais do usuário para serem exibidos nos inputs
        $this->editedData = [
            'nomeCompleto' => $user->nomeCompleto,
            'email' => $user->email,
            'tipo' => $user->tipo,
        ];
    }

    // Salva as alterações feitas na edição in-line
    public function save($userId)
    {
        $user = Usuario::findOrFail($userId);

        // 1. Validação
        $this->validate([
            'editedData.nomeCompleto' => 'required|string|max:255',
            'editedData.email' => [
                'required', 
                'email', 
                'max:255', 
                // Ignora o ID do usuário atual para permitir que ele mantenha o mesmo email
                Rule::unique('usuarios', 'email')->ignore($userId)
            ],
            'editedData.tipo' => 'required|in:professor,coordenacao,administracao,psicologo',
        ]);

        // 2. Atualiza o banco de dados
        $user->update([
            'nomeCompleto' => $this->editedData['nomeCompleto'],
            'email' => $this->editedData['email'],
            'tipo' => $this->editedData['tipo'],
        ]);

        // 3. Reseta o estado
        $this->editingUserId = null;
        $this->editedData = [];
        
        session()->flash('success', 'Usuário ' . $user->nomeCompleto . ' atualizado com sucesso!');
    }

    // Cancela o modo de edição
    public function cancelEdit()
    {
        $this->editingUserId = null;
        $this->editedData = [];
    }

    // -----------------------------------------------------
    // MÉTODOS DE STATUS (Ativar/Desativar)
    // -----------------------------------------------------

    public function toggleStatus($userId)
    {
        $user = Usuario::findOrFail($userId);
        
        // Regra de segurança: impede o coordenador de se desativar
        if (auth()->id() === $user->id && $user->status === 'ativo' && $user->tipo === 'coordenacao') {
            session()->flash('error', 'Você não pode desativar sua própria conta de Coordenação.');
            return;
        }

        // Alterna o status
        $newStatus = ($user->status === 'ativo') ? 'inativo' : 'ativo';
        $user->status = $newStatus;
        $user->save();

        $message = ($newStatus === 'ativo') ? 'ativado' : 'desativado';
        session()->flash('success', "Usuário {$user->nomeCompleto} {$message} com sucesso!");
    }


    // O método 'render' carrega os dados e exibe a view.
    public function render()
    {
        $usuarios = Usuario::orderBy('nomeCompleto')->get();
        
        return view('livewire.gerenciar-usuarios', [
            'usuarios' => $usuarios,
        ]);
    }

     // -----------------------------------------------------
    // MÉTODO DE EXCLUSÃO
    // -----------------------------------------------------

    public function deleteUser($userId)
    {
        $user = Usuario::findOrFail($userId);
        
        // Regra de segurança: Não permita que o usuário logado se autoexclua.
        if (auth()->id() === $user->id) {
            session()->flash('error', 'Você não pode excluir sua própria conta.');
            return;
        }

        $userName = $user->nomeCompleto;
        
        // Remove o usuário do banco de dados
        $user->delete();

        // Limpa o modo de edição, caso estivesse editando o usuário excluído
        if ($this->editingUserId === $userId) {
            $this->cancelEdit();
        }

        // Não precisa recarregar explicitamente, pois o próximo render fará isso
        session()->flash('success', "Usuário '{$userName}' excluído permanentemente com sucesso.");
    }
}