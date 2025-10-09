<?php

namespace App\Http\Requests\Turma;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkTurmaRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     * Normalmente, você usaria Gates ou policies aqui.
     */
    public function authorize(): bool
    {
        // Altere para a sua lógica de autorização (ex: Gate::allows('create-turmas'))
        return true; 
    }

    /**
     * Obtém as regras de validação que se aplicam à requisição.
     */
    public function rules(): array
    {
        return [
            'ano_letivo' => ['required', 'digits:4', 'integer', 'min:' . date('Y')],
            'vagas_geral' => ['required', 'integer', 'min:1'],
            
            // CORREÇÃO: Garante que as quantidades sejam números inteiros >= 0
            'quantidade_manha' => ['required', 'integer', 'min:0'],
            'quantidade_tarde' => ['required', 'integer', 'min:0'],

            'data_inicio' => ['required', 'date'],
            'data_fim' => ['required', 'date', 'after:data_inicio'],
        ];
    }
}
