<nav class="bg-white shadow mb-6 p-4 flex justify-between items-center">
    <div>
        <a href="{{ route('dashboard') }}" class="text-lg font-bold">SODIERP</a>
    </div>
    <div>
        @auth
            <span class="mr-4">{{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-red-500 hover:text-red-700">Sair</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-700">Login</a>
        @endauth
    </div>
</nav>
