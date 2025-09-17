<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Familiar extends Model
{
    use HasFactory;

    protected $table = 'familiares';

    // Campos que podem ser preenchidos via mass assignment
    protected $fillable = [
        'aluno_id',
        'nomeCompleto',
        'parentesco',
        'idade',
        'profissao',
        'empresa',
        'salarioBase',
        'telefone',
    ];

    /**
     * Um Familiar pertence a um Aluno
     */
    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }
}
