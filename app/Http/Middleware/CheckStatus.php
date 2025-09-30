<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStatus
{
    /**
     * Verifica se o usuário está ativo.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->status !== 'ativo') {
            Auth::logout();
            return redirect()->route('welcome')->with('error', 'Sua conta ainda não foi ativada. Aguarde a coordenação.');
        }

        return $next($request);
    }
}
