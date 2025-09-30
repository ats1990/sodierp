<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('welcome'); // sua tela de login
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // tenta autenticar
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // se o usuário estiver inativo
            if ($user->status === 'inativo') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Usuário ainda não foi ativado pela coordenação.',
                ]);
            }

            // Redirecionar conforme o tipo
            if ($user->tipo === 'coordenacao') {
                return redirect()->route('painel.coordenacao');
            } elseif ($user->tipo === 'administracao') {
                return redirect()->route('painel.administracao');
            } else {
                return redirect()->route('painel.colaborador');
            }
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
