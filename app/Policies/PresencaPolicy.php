<?php

namespace App\Policies;

// ❌ ANTES: use App\Models\User; 
// ✅ DEPOIS: Importe o seu modelo de usuário real
use App\Models\Usuario; 
use App\Models\Presenca; // Se Presenca for um Model
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class PresencaPolicy
{
    use HandlesAuthorization;
    
    // NOTA: O método 'before' é opcional e serve para dar SUPER PODERES a certas roles
    // Se o Administrador (ou outra role) deve ignorar todas as outras checagens:
    // ❌ ANTES: public function before(User $user, string $ability)
    // ✅ DEPOIS:
    public function before(Usuario $user, string $ability)
    {
        if ($user->tipo === 'administracao') {
            return true; // Administrador pode fazer tudo
        }
    }

    /**
     * Determina se o usuário pode acessar (visualizar a lista ou a tela) a Chamada.
     * Esta checagem corresponde à sua rota 'chamada.index' e 'chamada.show'
     * com ->can('access', Presenca::class).
     * @param  \App\Models\Usuario  $user (Corrigido para Usuario)
     * @return \Illuminate\Auth\Access\Response|bool
     */
    // ❌ ANTES: public function access(User $user)
    // ✅ DEPOIS:
    public function access(Usuario $user)
    {
        // Acesso permitido para Coordenacao, Professor e Administracao
        return in_array($user->tipo, ['coordenacao', 'professor', 'administracao'])
                ? Response::allow()
                : Response::deny('Você não tem permissão para acessar o controle de Chamada.');
    }

    /**
     * Determina se o usuário pode alterar (salvar dados) a Chamada.
     * Esta checagem corresponde à sua rota 'chamada.store'
     * com ->can('alter', Presenca::class).
     * @param  \App\Models\Usuario  $user (Corrigido para Usuario)
     * @return \Illuminate\Auth\Access\Response|bool
     */
    // ❌ ANTES: public function alter(User $user)
    // ✅ DEPOIS:
    public function alter(Usuario $user)
    {
        // Apenas Coordenacao e Professor (quem precisa lançar) podem alterar
        return in_array($user->tipo, ['coordenacao', 'professor'])
                ? Response::allow()
                : Response::deny('Você não tem permissão para alterar os dados de Chamada.');
    }
    
    // Métodos padrões de Resource Policy (view, create, update, delete, restore, forceDelete)
    // Se você não usá-los nas rotas, pode deixá-los como 'false' ou removê-los se não forem necessários.
}