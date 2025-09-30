<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    // Valores padrão ao criar um novo usuário
    protected $attributes = [
        'status' => 'inativo', // todos novos usuários começam inativos
    ];

    protected $fillable = [
        'nomeCompleto',
        'nomeSocial',
        'email',
        'cpf',
        'tipo',
        'status',
        'password',
        'programa_basica',
        'programa_aprendizagem',
        'programa_convivencia',
        'disciplinas_basica',
        'disciplinas_aprendizagem',
        'disciplinas_convivencia',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'disciplinas_basica' => 'array',
        'disciplinas_aprendizagem' => 'array',
        'disciplinas_convivencia' => 'array',
    ];

    // Criptografar senha automaticamente
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
