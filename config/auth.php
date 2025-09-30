<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Esta opção controla o "guard" de autenticação padrão e as opções de
    | redefinição de senha da sua aplicação.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Aqui você pode definir todos os "guards" de autenticação da aplicação.
    | O padrão usa sessão e o provider definido abaixo.
    |
    | Drivers suportados: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Define como os usuários serão buscados no banco. Aqui estamos usando
    | o Model App\Models\Usuario no lugar do padrão App\Models\User.
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\Usuario::class,
        ],

        // Exemplo alternativo com banco direto:
        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'usuarios',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reset de Senhas
    |--------------------------------------------------------------------------
    |
    | Configuração para reset de senhas. Você pode ter múltiplas configurações
    | caso use mais de uma tabela/model de usuários.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60, // minutos de validade do token
            'throttle' => 60, // tempo mínimo entre tentativas
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tempo de expiração da confirmação de senha
    |--------------------------------------------------------------------------
    |
    | Define por quantos segundos a confirmação de senha é válida.
    | Padrão = 3 horas (10800 segundos).
    |
    */

    'password_timeout' => 10800,

];
