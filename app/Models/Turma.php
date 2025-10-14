<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB; 

use App\Models\Aluno;
use App\Models\User;

class Turma extends Model
{
    use HasFactory;

    protected $fillable = [
        'periodo',
        'letra',
        'ano_letivo',
        'vagas',
        'data_inicio', 
        'data_fim',    
        'professor_id',
    ];

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function alunos(): HasMany
    {
        return $this->hasMany(Aluno::class, 'turma_id');
    }

    public function getNomeCompletoAttribute()
    {
        return "{$this->ano_letivo} - {$this->letra} ({$this->periodo})";
    }

    /**
     * Determina o próximo índice alfabético para um dado ano letivo.
     * 🚨 CHAVE: A busca pela última letra é GLOBAL para o ano, garantindo continuidade entre os períodos.
     * @param int $anoLetivo
     * @return int O índice alfabético (0 para 'A', 1 para 'B', etc.)
     */
    public static function getNextAlphaIndex(int $anoLetivo): int
    {
        // Encontra a turma com a letra de maior ordem (Z) para o ano, em QUALQUER período.
        $lastTurma = self::where('ano_letivo', $anoLetivo)
                          ->whereNotNull('letra')
                          ->orderBy('letra', 'desc') 
                          ->first();

        if (!$lastTurma) {
            return 0; // Começa em 'A' (índice 0)
        }

        // Converte a última letra encontrada para o próximo índice
        $lastIndex = ord(strtoupper($lastTurma->letra)) - ord('A');

        // Retorna o próximo índice (ex: se foi 'E', retorna 5 para 'F')
        return $lastIndex + 1;
    }
}
