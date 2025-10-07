<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    public function alunos()
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
}
