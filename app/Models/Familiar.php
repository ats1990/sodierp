<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Familiar extends Model
{
    use HasFactory;

    // Define explicitamente o nome da tabela
    protected $table = 'familiares';

    protected $fillable = [
        'aluno_id', 'parentesco', 'nomeCompleto', 'idade', 'profissao', 'empresa', 'salarioBase'
    ];

    // Relacionamento com Aluno
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }

    // Sanitização automática de salário
    protected static function booted()
    {
        static::saving(function ($familiar) {
            if (isset($familiar->salarioBase)) {
                // Substitui '.' por nada e ',' por '.' para converter para float
                $familiar->salarioBase = (float) str_replace(',', '.', str_replace('.', '', $familiar->salarioBase));
            }
        });
    }
}
