{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SODIERP')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="min-h-screen bg-gray-50 flex flex-col">

    {{-- Navbar --}}
    <header class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/logo.png') }}" alt="SODIERP Logo" class="h-10">
                <span class="font-bold text-xl text-gray-800">SODIERP</span>
            </div>

            <nav class="flex items-center space-x-4">
                @auth
                    <span class="text-gray-700">{{ Auth::user()->nomeCompleto }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-white bg-orange-600 hover:bg-orange-800 px-4 py-1 rounded-lg">Sair</button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="text-gray-700 hover:text-orange-600 font-semibold">Login</a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Conteúdo principal --}}
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white shadow-inner py-4 mt-auto text-center text-gray-500 text-sm">
        &copy; {{ date('Y') }} SODIPROM. Todos os direitos reservados.
        <p>Desenvolvido por Anderson Trajano.</p>
    </footer>

</body>

</html>
