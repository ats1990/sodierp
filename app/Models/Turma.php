<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB; // Adicionado, embora não estritamente necessário para o método, é útil para queries complexas

// 🚨 CORREÇÃO ESSENCIAL: Importa o Model Aluno (em português)
use App\Models\Aluno;
use App\Models\User;

class Turma extends Model
{
    use HasFactory;

    /**
     * Os atributos que são mass assignable.
     * Inclui os campos de data.
     */
    protected $fillable = [
        'periodo',
        'letra',
        'ano_letivo',
        'vagas',
        'data_inicio', // Mantido para o salvamento via Controller
        'data_fim',    // Mantido para o salvamento via Controller
        'professor_id',
    ];

    /**
     * Relação: Uma Turma pertence a um Professor (User)
     */
    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    /**
     * Relação: Uma Turma tem muitos Alunos (Aluno).
     * 🚨 CORRIGIDO: Usa a classe Aluno importada para resolver o erro "Student not found".
     */
    public function alunos(): HasMany
    {
        return $this->hasMany(Aluno::class, 'turma_id');
    }

    /**
     * Accessor para criar o nome completo da turma (ex: 2024 - B Tarde)
     */
    public function getNomeCompletoAttribute()
    {
        return "{$this->ano_letivo} - {$this->letra} ({$this->periodo})";
    }

    /**
     * 🆕 Determina o próximo índice alfabético para um dado ano letivo.
     * Retorna o índice (0 para 'A', 1 para 'B', etc.)
     * Ex: Se a última letra para o ano 2024 foi 'B' (índice 1), retorna 2 (para 'C').
     * * @param int $anoLetivo
     * @return int O índice alfabético (0 para 'A', 1 para 'B', etc.)
     */
    public static function getNextAlphaIndex(int $anoLetivo): int
    {
        // Encontra a turma com a letra de maior ordem (Z) para o ano
        $lastTurma = self::where('ano_letivo', $anoLetivo)
                         ->whereNotNull('letra')
                         ->orderBy('letra', 'desc')
                         ->first();

        if (!$lastTurma) {
            return 0; // Começa em 'A' (índice 0)
        }

        // Converte a última letra encontrada para o índice (ex: 'A' -> 0, 'B' -> 1)
        $lastIndex = ord(strtoupper($lastTurma->letra)) - ord('A');

        // Retorna o próximo índice
        return $lastIndex + 1;
    }
}
