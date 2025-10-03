{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SODIERP - Sistema de Gestão</title>
    @vite('resources/css/app.css')
    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/png">
</head>

<body class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-blue-50 flex flex-col">

    {{-- Container central --}}
    <div class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-2xl bg-white rounded-3xl shadow-xl flex flex-col md:flex-row overflow-hidden">

            {{-- Seção de boas-vindas --}}
            <div class="md:w-1/2 bg-gradient-to-br from-orange-200 via-orange-100 to-white p-8 flex flex-col justify-center items-start">
                <h1 class="text-3xl font-bold mb-3 text-gray-800">Bem-vindo ao SODI-ERP</h1>
                <p class="text-gray-700 mb-4">
                    Gerencie aprendizes, turmas e avaliações de forma simples e segura.
                </p>
                <<img src="{{ asset('assets/images/welcome-illustration.svg') }}" alt="Ilustração">
            </div>

            {{-- Seção de login/registro --}}
            <div class="md:w-1/2 p-8 flex flex-col justify-center items-center">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">Acesse sua conta</h2>

                {{-- Formulário de login --}}
                <form method="POST" action="{{ route('login.store') }}" class="space-y-4 w-full">
                    @csrf

                    {{-- Email --}}
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input
                        id="email"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                        class="w-full border-gray-300 rounded focus:border-[#fb6a28] focus:ring focus:ring-[#fb6a28]/30" />

                    {{-- Senha --}}
                    <x-input-label for="password" :value="__('Senha')" />
                    <x-text-input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full border-gray-300 rounded focus:border-[#fb6a28] focus:ring focus:ring-[#fb6a28]/30" />

                    {{-- Lembrar-me e esqueci a senha --}}
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox"
                                class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500"
                                name="remember">
                            <span class="ml-2 text-sm text-gray-600">{{ __('Lembrar-me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-gray-600 hover:text-[#fb6a28] underline text-sm">
                            {{ __('Esqueceu sua senha?') }}
                        </a>
                        @endif
                    </div>

                    {{-- Botões Login / Registrar --}}
                    <div class="flex flex-col sm:flex-row sm:justify-between gap-2 mt-4">
                        <x-primary-button class="w-full sm:w-auto"
                            style="background-color: #fb6a28;"
                            onmouseover="this.style.backgroundColor='#140c0b'"
                            onmouseout="this.style.backgroundColor='#fb6a28'">
                            {{ __('Login') }}
                        </x-primary-button>

                        <!-- Botão Registrar -->
                        <button type="button"
                            class="w-full sm:w-auto text-white font-semibold py-2 px-4 rounded-lg text-center"
                            style="background-color: #fb6a28;"
                            onmouseover="this.style.backgroundColor='#140c0b'"
                            onmouseout="this.style.backgroundColor='#fb6a28'"
                            onclick="openModal('aluno')">
                            {{ __('Registrar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="w-full text-center py-4 text-gray-500 text-sm">
        &copy; {{ date('Y') }} SODIPROM. Todos os direitos reservados.
        <p>Desenvolvido por Anderson Trajano.</p>
    </footer>

    {{-- Modal de cadastro --}}
    <div id="registerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 relative">
            <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            <div id="tipoUsuarioSelection" class="space-y-4">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Escolha o tipo de usuário</h2>
                <div class="flex flex-col gap-2">
                    <button onclick="window.location='{{ route('aluno.create') }}'"
                        class="w-full bg-[#fb6a28] hover:bg-[#140c0b] text-white py-2 px-4 rounded-lg">
                        Aluno
                    </button>
                    <button onclick="window.location='{{ route('usuarios.create') }}'"
                        class="w-full bg-[#fb6a28] hover:bg-[#140c0b] text-white py-2 px-4 rounded-lg">
                        Colaborador
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(tipo) {
            if (tipo === 'aluno') {
                document.getElementById('registerModal').classList.remove('hidden');
                document.getElementById('registerModal').classList.add('flex');
            }
        }

        function closeModal() {
            document.getElementById('registerModal').classList.remove('flex');
            document.getElementById('registerModal').classList.add('hidden');
        }
    </script>

</body>

</html>