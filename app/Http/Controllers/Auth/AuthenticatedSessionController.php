<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Exibe a view de login (sua welcome.blade.php)
     */
    public function create(): View
    {
        // Aqui você mantém seu welcome.blade.php como tela de login
        return view('welcome');
    }

    /**
     * Processa o login
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Autentica o usuário
        $request->authenticate();

        // Regenera a sessão
        $request->session()->regenerate();

        // Redirecionamento por papel
        $user = Auth::user();

        if ($user->hasRole('coordenacao')) {
            return redirect()->route('painel.coordenacao');
        } elseif ($user->hasRole('administracao')) {
            return redirect()->route('painel.administracao');
        } elseif ($user->hasRole('professor')) {
            return redirect()->route('painel.professor');
        } elseif ($user->hasRole('psicologo')) {
            return redirect()->route('painel.psicologo');
        }

        // Fallback caso não tenha papel definido
        return redirect('/');
    }

    /**
     * Logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
