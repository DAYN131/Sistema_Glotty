<?php
// app/Models/Horario.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo', 
        'dias',
        'hora_inicio',
        'hora_fin',
        'activo'
    ];

    protected $casts = [
        'dias' => 'array',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'activo' => 'boolean'
    ];



    public function horariosPeriodo()
    {
        return $this->hasMany(HorarioPeriodo::class, 'horario_base_id');
    }

    public function periodosActivos()
    {
        return $this->belongsToMany(Periodo::class, 'horarios_periodo', 'horario_base_id', 'periodo_id')
                    ->wherePivot('activo', true)
                    ->withTimestamps();
    }

    // 🎯 SCOPES ÚTILES
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeSemanales($query)
    {
        return $query->where('tipo', 'semanal');
    }

    public function scopeSabatinos($query)
    {
        return $query->where('tipo', 'sabatino');
    }

    // ✅ MÉTODOS DE UTILIDAD
    public function estaActivo()
    {
        return $this->activo;
    }

    public function getDiasFormateadosAttribute()
    {
        if (!$this->dias) return '';

        $diasMap = [
            'Lunes' => 'Lun',
            'Martes' => 'Mar', 
            'Miércoles' => 'Mié',
            'Jueves' => 'Jue',
            'Viernes' => 'Vie',
            'Sábado' => 'Sáb',
            'Domingo' => 'Dom'
        ];

        return collect($this->dias)->map(function ($dia) use ($diasMap) {
            return $diasMap[$dia] ?? $dia;
        })->implode(', ');
    }

    public function getHorarioCompletoAttribute()
    {
        return "{$this->dias_formateados} {$this->hora_inicio->format('H:i')} - {$this->hora_fin->format('H:i')}";
    }

    public function getDuracionAttribute()
    {
        return $this->hora_inicio->diffInHours($this->hora_fin);
    }

    public function sePuedeEliminar()
    {
        // No se puede eliminar si está siendo usado en algún periodo
        return $this->horariosPeriodo()->count() === 0;
    }

    public function enUsoEnPeriodosActivos()
    {
        return $this->horariosPeriodo()
                    ->whereHas('periodo', function($query) {
                        $query->where('estado', '!=', 'finalizado');
                    })
                    ->exists();
    }



}