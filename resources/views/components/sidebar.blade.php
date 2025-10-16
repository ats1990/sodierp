@php
// FIX para o erro "Cannot redeclare isActiveRoute()":
// Define a função apenas se ela ainda não existir.
if (!function_exists('isActiveRoute')) {
    /**
    * Verifica se a rota atual corresponde à rota fornecida.
    * @param string $route O nome da rota.
    * @return string 'active' se a rota for a atual, caso contrário, vazio.
    */
    function isActiveRoute($route) {
        try {
            // Verifica se alguma rota começa com o nome da rota fornecida (útil para submenus)
            return request()->routeIs($route . '*') ? 'active' : '';
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
        // Gerenciamento de Usuários
        ['title' => 'Usuários', 'route' => 'usuarios.index', 'icon' => 'mdi-account-circle'],

        // NOVO: MENU FORMAÇÃO (COM SUBMENUS)
        [
            'title' => 'Formação',
            'id' => 'formacao-menu', // ID para o collapse
            'icon' => 'mdi-school', // Ícone para Formação
            // Os submenus da Formação foram ajustados para seguir a sua nova estrutura:
            'submenus' => [
                ['title' => 'Turmas', 'route' => 'formacao.turmas.index'],
                // ✅ CORRIGIDO: Agora aponta para a rota universal: 'chamada.index'
                ['title' => 'Chamada', 'route' => 'chamada.index'], 
                ['title' => 'Notas', 'route' => 'formacao.notas.index'],
                ['title' => 'Boletim', 'route' => 'formacao.boletim.index'],
                ['title' => 'Certificado', 'route' => 'formacao.certificado.index'],
                ['title' => 'Importar Dados', 'route' => 'formacao.importar.index'],
            ]
        ],

        // Itens removidos mantidos como referência comentada
    ],
    'professor' => [
        ['title' => 'Dashboard', 'route' => 'painel.professor', 'icon' => 'mdi-home'],
        // Chamada para o Professor (usa a rota simples)
        ['title' => 'Chamada', 'route' => 'chamada.index', 'icon' => 'mdi-calendar-check'],
        // Item removido mantido como referência comentada
    ],

    // Estrutura correta para a role 'administracao' (array plano)
    'administracao' => [
        ['title' => 'Dashboard', 'route' => 'painel.administracao', 'icon' => 'mdi-home'],
        ['title' => 'Chamada', 'route' => 'chamada.index', 'icon' => 'mdi-calendar-check'], // Chamada para Administração
        // Itens removidos mantidos como referência comentada
    ],

    // 'psicologo' é uma chave de nível superior
    'psicologo' => [
        ['title' => 'Dashboard', 'route' => 'painel.psicologo', 'icon' => 'mdi-home'],
        // Item removido mantido como referência comentada
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
        {{-- Loop para renderizar os menus --}}
        @foreach($menus[$role] ?? [] as $menu)
            {{-- Lógica para itens com submenu --}}
            @if(isset($menu['submenus']))
                {{-- Verifica se algum submenu está ativo para manter o menu pai expandido --}}
                @php
                    $isAnySubmenuActive = collect($menu['submenus'])->contains(function($submenu) {
                        // Verifica se o submenu tem rota e se a rota está ativa
                        return isset($submenu['route']) && request()->routeIs($submenu['route'] . '*');
                    });
                @endphp

                <li class="nav-item {{ $isAnySubmenuActive ? 'active' : '' }}">
                    <a class="nav-link"
                        data-bs-toggle="collapse"
                        href="#{{ $menu['id'] }}"
                        aria-expanded="{{ $isAnySubmenuActive ? 'true' : 'false' }}"
                        aria-controls="{{ $menu['id'] }}">
                        <span class="menu-title">{{ $menu['title'] }}</span>
                        <i class="menu-arrow"></i>
                        <i class="mdi {{ $menu['icon'] }} menu-icon"></i>
                    </a>
                    <div class="collapse {{ $isAnySubmenuActive ? 'show' : '' }}" id="{{ $menu['id'] }}">
                        <ul class="nav flex-column sub-menu">
                            @foreach($menu['submenus'] as $submenu)
                                <li class="nav-item">
                                    {{-- Certifica-se de que a rota existe antes de tentar gerar o link --}}
                                    @if (isset($submenu['route']))
                                        <a class="nav-link {{ isActiveRoute($submenu['route']) }}" href="{{ route($submenu['route']) }}">{{ $submenu['title'] }}</a>
                                    @else
                                        <a class="nav-link disabled">{{ $submenu['title'] }}</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </li>
            {{-- Lógica para itens simples --}}
            @else
                <li class="nav-item">
                    {{-- Verifica se a rota existe para evitar erro de `route()` --}}
                    @if (isset($menu['route']) && Route::has($menu['route']))
                        <a class="nav-link {{ isActiveRoute($menu['route']) }}" href="{{ route($menu['route']) }}">
                            <span class="menu-title">{{ $menu['title'] }}</span>
                            <i class="mdi {{ $menu['icon'] }} menu-icon"></i>
                        </a>
                    @else
                        <a class="nav-link disabled">
                            <span class="menu-title">{{ $menu['title'] }} (Rota não definida)</span>
                            <i class="mdi {{ $menu['icon'] }} menu-icon"></i>
                        </a>
                    @endif
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