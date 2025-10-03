<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        return true; // Permite que qualquer pessoa tente fazer login
    }

    /**
     * Regras de validação para o login
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Autentica o usuário.
     */
    public function authenticate(): void
    {
        $credentials = $this->only('email', 'password');

        if (!Auth::attempt($credentials, $this->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('As credenciais fornecidas não correspondem aos nossos registros.'),
            ]);
        }
    }

    /**
     * Prepara os dados antes da validação (opcional)
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'email' => strtolower($this->email),
        ]);
    }
}
