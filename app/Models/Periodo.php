<?php
// app/Models/Periodo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_periodo',
        'fecha_inicio', 
        'fecha_fin',
        'estado'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    // 🔥 RELACIONES
    public function horariosPeriodo()
    {
        return $this->hasMany(HorarioPeriodo::class);
    }

    public function horariosBase()
    {
        return $this->belongsToMany(
            Horario::class,
            'horarios_periodo',
            'periodo_id',
            'horario_base_id'
        )->withPivot('activo')->withTimestamps();
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    public function preregistros()
    {
        return $this->hasMany(Preregistro::class);
    }

    // 🎯 SCOPES ÚTILES
    public function scopeActivo($query)
    {
        return $query->where('estado', 'en_curso');
    }

    public function scopeConPreRegistrosActivos($query)
    {
        return $query->where('estado', 'preregistros_activos');
    }

    public function scopeFinalizados($query)
    {
        return $query->where('estado', 'finalizado');
    }

    // ✅ MÉTODOS DE ESTADO
    public function estaEnConfiguracion()
    {
        return $this->estado === 'configuracion';
    }

    public function aceptaPreRegistros()
    {
        return $this->estado === 'preregistros_activos';
    }

    public function estaEnCurso()
    {
        return $this->estado === 'en_curso';
    }

    public function estaFinalizado()
    {
        return $this->estado === 'finalizado';
    }

    public function getDiasDuracionAttribute(): int
    {
        return $this->fecha_inicio->diffInDays($this->fecha_fin);
    }


    public function puedeEliminarse()
    {
        return $this->estaEnConfiguracion() && 
            $this->grupos()->count() === 0 &&
            $this->preregistros()->count() === 0;
    }

    // ✅ Para usar en validaciones
    public function puedeCambiarA($nuevoEstado)
    {
        $transicionesPermitidas = [
            'configuracion' => ['preregistros_activos'],
            'preregistros_activos' => ['en_curso', 'configuracion'],
            'en_curso' => ['finalizado', 'preregistros_activos'],
            'finalizado' => []
        ];

        return in_array($nuevoEstado, $transicionesPermitidas[$this->estado]);
    }

}