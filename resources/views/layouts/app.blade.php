<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    {{-- 🚨 CSRF Token para requisições AJAX seguras 🚨 --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Painel Admin')</title>
    
    {{-- CSS Base do Tema --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />

    {{-- 🚨 NOVO: Injeta o CSS específico da página aqui (como o @section('styles') do seu Blade) 🚨 --}}
    @yield('styles') 

</head>

<body>
    <div class="container-scroller">
        @include('components.navbar')
        <div class="container-fluid page-body-wrapper">
            @include('components.sidebar')
            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                    @yield('modals')
                </div>
                @include('components.footer')
            </div>
        </div>
    </div>
    
        <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/vendors/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    
        <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    
        <script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>
    
        <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    
    {{-- 🚨 CORREÇÃO BLADE: Adiciona @yield('scripts') para aceitar injeção via @section('scripts') 🚨 --}}
    @yield('scripts') 
    
    {{-- Scripts específicos da página injetados via @push('scripts') --}}
    @stack('scripts')
</body>

</html>