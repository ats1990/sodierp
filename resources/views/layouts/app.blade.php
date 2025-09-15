<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SODIERP</title>
    <!-- Tailwind CDN para estilo rápido -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    @include('layouts.navigation')

    <main class="p-6">
        @yield('content')
    </main>
</body>
</html>
