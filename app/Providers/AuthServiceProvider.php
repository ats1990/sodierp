<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate; // ⬅️ ESSENCIAL: Adicionando a classe Gate
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define a lógica de autorização (Gates) para checar o tipo/cargo do usuário.
        // 🚨 CORREÇÃO: Usando $user->tipo, conforme o banco de dados.
        
        // GATE PARA COORDENADORES
        Gate::define('isCoordenacao', function ($user) {
            // Retorna TRUE se o TIPO do usuário for 'coordenacao'
            return $user->tipo === 'coordenacao'; 
        });

        // GATE PARA ADMINISTRAÇÃO
        Gate::define('isAdministracao', function ($user) {
            // Retorna TRUE se o TIPO do usuário for 'administracao'
            return $user->tipo === 'administracao';
        });
    }
}
