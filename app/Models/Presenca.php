<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presenca extends Model
{
    use HasFactory;

    protected $table = 'presencas'; // Certifique-se de que o nome da tabela está correto

    protected $fillable = [
        'aluno_id',
        'turma_id', 
        'data',
        'presente', // Campo booleano (true/false, 1/0)
        'professor_id',
    ];

    protected $casts = [
        'data' => 'date',
        'presente' => 'boolean',
    ];

    /**
     * Relação: Uma presença pertence a um aluno.
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }
    
    /**
     * Relação: Uma presença foi registrada por um professor.
     */
    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_id'); // Ajuste 'User::class' se seu model de usuário for diferente
    }
    
    /**
     * Relação: Uma presença pertence a uma turma.
     */
    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }
}
