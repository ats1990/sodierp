<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // ✅ importante importar Schema
use Illuminate\Support\Facades\Blade; // ✅ importar Blade para registrar componente

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define o tamanho padrão das strings para evitar erro de chave longa no MySQL
        Schema::defaultStringLength(191);

        // Registrar o componente Blade 'layouts.app'
        Blade::component('layouts.app', 'layouts.app');
    }
}
