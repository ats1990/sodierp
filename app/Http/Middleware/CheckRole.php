<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed  ...$roles  // Permite passar múltiplos papéis como parâmetros
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        // Verifica se usuário está logado e se possui algum dos papéis permitidos
        if (!$user || !in_array($user->role->name, $roles)) {
            abort(403, 'Acesso negado.'); // Retorna erro 403 se não tiver permissão
        }

        return $next($request);
    }
}
