<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    {{-- 🚨 NOVO: CSRF Token para requisições AJAX seguras 🚨 --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Painel Admin')</title>
    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
</head>

<body>
    <div class="container-scroller">
        @include('components.navbar')
        <div class="container-fluid page-body-wrapper">
            @include('components.sidebar')
            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                    @yield('modals') {{-- Certifique-se de ter esta linha para o modal --}}
                </div>
                {{-- Linha 50: O footer fixo foi removido e substituído pelo @include --}}
                @include('components.footer')
            </div>
        </div>
    </div>
    
    <!-- Scripts base do tema (jQuery/Bootstrap e plugins) -->
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/vendors/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    
    <!-- Scripts de utilidade do tema -->
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    
    <!-- 🚨 NOVO: Incluído para resolver o erro '$.cookie' 🚨 -->
    <script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>
    
    <!-- 🚨 Reordenado: 'dashboard.js' DEVE vir após suas dependências 🚨 -->
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    
    {{-- Scripts específicos da página (como o da reabertura do modal) --}}
    @stack('scripts')
</body>

</html>
