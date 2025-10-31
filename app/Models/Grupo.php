<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nivel_ingles', // 1-5 (nivel del curso)
        'letra_grupo',  // A, B, C...
        'anio',
        'periodo',
        'id_horario',
        'id_aula',
        'rfc_profesor',
        'cupo_minimo',
        'cupo_maximo',
        'rfc_coordinador'
    ];

    protected $dates = ['deleted_at'];

    // Relación con profesor
    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'rfc_profesor', 'rfc_profesor');
    }

    // Relación con aula
    public function aula()
    {
        return $this->belongsTo(Aula::class, 'id_aula', 'id_aula');
    }

    // Relación con horario
    public function horario()
    {
        return $this->belongsTo(Horario::class, 'id_horario');
    }

    // Relación con inscripciones (HAS MANY)
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_grupo', 'id');
    }

    
    // Relación con alumnos a través de inscripciones
    public function alumnos()
    {
     
                   return $this->hasManyThrough(
                    Alumno::class,
                    Inscripcion::class,
                    'id_grupo', // FK en inscripciones
                    'no_control', // FK en alumnos
                    'id', // PK en grupos
                    'no_control' // PK en alumnos
                );
    }
    ///
    /**
    * Calcula cupo disponible para vista de alumnos (incluye pendientes)
    * @return int
    */

   public function cupoDisponibleParaAlumnos(): int
   {
       return max(0, $this->cupo_maximo - $this->inscripciones()->count());
   }

   /**
    * Calcula cupo real disponible (solo inscripciones aprobadas)
    * @return int
    */
   public function cupoDisponibleReal(): int
   {
       return max(0, $this->cupo_maximo - $this->inscripciones()
           ->where('estatus_inscripcion', 'Aprobada')
           ->count());
   }

   public function getPeriodoFormateadoAttribute()
    {
        return "{$this->periodo}-{$this->anio}";
    }

    // En el modelo Grupo.php
    public function tieneAlumnosInscritos(): bool
    {
        return $this->inscripciones()
                    ->where('estatus_inscripcion', 'Aprobada')
                    ->exists();
    }
}