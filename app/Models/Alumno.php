<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Alumno extends Authenticatable
{
    protected $table = 'alumnos';
    protected $primaryKey = 'no_control'; // Clave primaria personalizada
    public $incrementing = false; // Indica que la clave primaria no es autoincremental
    protected $keyType = 'string'; // Tipo de la clave primaria

    protected $fillable = [
        'no_control', 
        'nombre_alumno',
        'apellidos_alumno',
        'carrera',
        'correo_institucional',
        'contraseña'
    ];

    protected $hidden = [
        'contraseña', // Oculta la contraseña en las respuestas
    ];

    /**
     * Obtiene el nombre de la columna que se utiliza como contraseña.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->contraseña; // Indica que la columna "contraseña" es la que almacena la contraseña
    }

    // Relación con inscripciones (historial completo)
    public function inscripciones() {
        return $this->hasMany(Inscripcion::class, 'no_control', 'no_control');
    }


    /**
     * Método para obtener el nivel recomendado (simplificado para MVP)
     */

     public function nivelRecomendado()
     {
         $ultimoNivel = $this->inscripciones()
             ->where('estatus_inscripcion', 'Aprobada')
             ->latest('fecha_inscripcion')
             ->value('nivel_solicitado') ?? 0;
     
         return min($ultimoNivel + 1, 5); // Nivel máximo: 5
     }

   
    // Relación con grupos a través de inscripciones
    public function grupos()
    {
        return $this->hasManyThrough(
            Grupo::class,
            Inscripcion::class,
            'no_control', // FK en inscripciones
            'id', // FK en grupos
            'no_control', // PK en alumnos
            'id_grupo' // PK en grupos
        );
    }



}

    

    
