<?php

// app/Models/Usuario.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable 
{
     use HasFactory, Notifiable;

    protected $table = 'usuarios';
    
    protected $fillable = [
        'correo_personal',
        'nombre_completo',
        'numero_telefonico',
        'genero',
        'fecha_nacimiento',
        'tipo_usuario',
        'correo_institucional',
        'numero_control',
        'especialidad',
        'contraseña'
    ];

    protected $hidden = [
        'contraseña'
    ];

    // Relaciones
    public function preregistros()
    {
        return $this->hasMany(Preregistro::class, 'usuario_id');
    }
}