<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $table = 'inscripciones';

    const ESTATUS_INSCRIPCION = [
        'Pendiente' => 'Pendiente',
        'Aprobada' => 'Aprobada', 
        'Expirada' => 'Expirada'
    ];

    const ESTATUS_PAGO = [
        'Pendiente' => 'Pendiente',
        'Aprobado' => 'Aprobado',
        'Rechazado' => 'Rechazado'
    ];
    
    
    protected $fillable = [
    'no_control',
    'id_grupo',
    'periodo_cursado',
    'fecha_inscripcion',
    'estatus_inscripcion',
    'calificacion_parcial_1',
    'calificacion_parcial_2',
    'calificacion_final',
    'nivel_solicitado'
    ];

    protected $casts = [
        'fecha_inscripcion' => 'datetime',
        'calificacion_parcial_1' => 'decimal:2',
        'calificacion_parcial_2' => 'decimal:2',
        'calificacion_final' => 'decimal:2'
    ];

        
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'no_control', 'no_control');
    }
    
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }

    public function calculateFinalGrade()
    {
        if (!is_null($this->calificacion_parcial_1) && !is_null($this->calificacion_parcial_2)) {
            $this->calificacion_final = ($this->calificacion_parcial_1 + $this->calificacion_parcial_2) / 2;
            $this->save();
        }
    }


}