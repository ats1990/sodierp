@php
// FIX para o erro "Cannot redeclare isActiveRoute()":
// Define a função apenas se ela ainda não existir.
if (!function_exists('isActiveRoute')) {
/*** Verifica se a rota atual corresponde à rota fornecida.
* @param string $route O nome da rota.
* @return string 'active' se a rota for a atual, caso contrário, vazio.
*/
function isActiveRoute($route) {
try {
return Route::is($route) ? 'active' : '';
} catch (\Exception $e) {
return ''; // Retorna vazio em caso de erro de rota (mas não trava a aplicação)
}
}
}

// Obtém a role do usuário logado ou usa 'guest' se não estiver logado
$role = strtolower(auth()->user()->tipo ?? 'guest');

// Mapeamento dos menus por role
$menus = [
'coordenacao' => [
// Dashboard
['title' => 'Dashboard', 'route' => 'painel.coordenacao', 'icon' => 'mdi-home'],
// NOVO: Gerenciamento de Usuários
['title' => 'Usuários', 'route' => 'usuarios.index', 'icon' => 'mdi-account-circle'],
// ['title' => 'Turmas', 'route' => 'turmas.index', 'icon' => 'mdi-account-multiple'], // Removido
// ['title' => 'Professores', 'route' => 'professores.index', 'icon' => 'mdi-school'], // Removido
],
'professor' => [
['title' => 'Dashboard', 'route' => 'painel.professor', 'icon' => 'mdi-home'],
// ['title' => 'Minhas Turmas', 'route' => 'professor.turmas', 'icon' => 'mdi-book-open-page-variant'], // Removido
],
'administracao' => [
['title' => 'Dashboard', 'route' => 'painel.administracao', 'icon' => 'mdi-home'],
// ['title' => 'Usuários', 'route' => 'usuarios.index', 'icon' => 'mdi-account-circle'], // Removido
// ['title' => 'Configurações', 'route' => 'configuracoes.index', 'icon' => 'mdi-settings'], // Removido
],
'psicologo' => [
['title' => 'Dashboard', 'route' => 'painel.psicologo', 'icon' => 'mdi-home'],
// ['title' => 'Agenda', 'route' => 'agenda.index', 'icon' => 'mdi-calendar'], // Removido
],
];
@endphp

<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ auth()->user()->nomeCompleto }}</span>
                    <span class="text-secondary text-small">{{ ucfirst($role) }}</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>
        @foreach($menus[$role] ?? [] as $menu)
        {{-- A lógica de submenu e item simples é mantida --}}
        @if(isset($menu['submenus']))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#{{ $menu['id'] }}" aria-expanded="false" aria-controls="{{ $menu['id'] }}">
                <span class="menu-title">{{ $menu['title'] }}</span>
                <i class="menu-arrow"></i>
                <i class="mdi {{ $menu['icon'] }} menu-icon"></i>
            </a>
            <div class="collapse" id="{{ $menu['id'] }}">
                <ul class="nav flex-column sub-menu">
                    @foreach($menu['submenus'] as $submenu)
                    <li class="nav-item">
                        <a class="nav-link {{ isActiveRoute($submenu['route']) }}" href="{{ route($submenu['route']) }}">{{ $submenu['title'] }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </li>
        @else
        <li class="nav-item">
            {{-- O uso de route() só é feito aqui, se a rota não existir, o próximo erro será neste ponto. --}}
            <a class="nav-link {{ isActiveRoute($menu['route']) }}" href="{{ route($menu['route']) }}">
                <span class="menu-title">{{ $menu['title'] }}</span>
                <i class="mdi {{ $menu['icon'] }} menu-icon"></i>
            </a>
        </li>
        @endif
        @endforeach
        <li class="nav-item">
            <a class="nav-link" href="#" target="_blank">
                <span class="menu-title">Documentação</span>
                <i class="mdi mdi-file-document-box menu-icon"></i>
            </a>
        </li>
    </ul>
</nav>