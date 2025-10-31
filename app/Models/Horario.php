<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horario extends Model

{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'nombre',
        'tipo', // 'semanal' o 'sabado'
        'dias', // array de días para tipo semanal
        'hora_inicio',
        'hora_fin',
        'activo',
        'inicio_vigencia',
        'fin_vigencia'
    ];

    #protected $casts = [
        #'dias' => 'array',
        #'activo' => 'boolean',
        #'inicio_vigencia' => 'date:Y-m-d',
        #'fin_vigencia' => 'date:Y-m-d'
    #];

    protected $casts = [
        'activo' => 'boolean',
        'dias' => 'array', // Para convertir automáticamente el JSON
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'inicio_vigencia' => 'date',
        'fin_vigencia' => 'date'
    ];

    // Tipos permitidos (para validación)
    public static $tiposPermitidos = ['semanal', 'sabado'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Validar tipo
            if (!in_array($model->tipo, self::$tiposPermitidos)) {
                throw new \Exception("Tipo de horario debe ser: " . implode(', ', self::$tiposPermitidos));
            }

            // Validar días según tipo
            if ($model->tipo === 'semanal' && empty($model->dias)) {
                throw new \Exception("Horario semanal requiere días asignados");
            }

            if ($model->tipo === 'sabado' && !empty($model->dias)) {
                throw new \Exception("Horario sabatino no debe tener días asignados");
            }
        });
    }

    // Métodos útiles
    public function esSabatino()
    {
        return $this->tipo === 'sabado';
    }

    public function estaActivo()
    {
        return $this->activo && now()->between(
            $this->inicio_vigencia, 
            $this->fin_vigencia
        );
    }
}