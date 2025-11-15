<?php
// app/Models/HorarioPeriodo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioPeriodo extends Model
{
    use HasFactory;

    protected $table = 'horarios_periodo';

    protected $fillable = [
        'periodo_id',
        'horario_base_id', 
        'nombre',           // ✅ COPIA del nombre
        'tipo',             // ✅ COPIA del tipo
        'dias',             // ✅ COPIA de días
        'hora_inicio',      // ✅ COPIA de hora inicio
        'hora_fin',         // ✅ COPIA de hora fin
        'activo'
    ];

    protected $casts = [
        'dias' => 'array', // ← Esto convierte JSON string a array automáticamente
        'hora_inicio' => 'datetime:H:i:s', // ← Formato correcto
        'hora_fin' => 'datetime:H:i:s', // ← Formato correcto
        'activo' => 'boolean',
    ];

    // Relaciones
    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function horarioBase()
    {
        return $this->belongsTo(Horario::class, 'horario_base_id');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'horario_periodo_id');
    }

    // 🎯 MÉTODOS DE LA INSTANTÁNEA
    public function getDiasFormateadosAttribute()
    {
        if (!$this->dias) return '';

        $diasMap = [
            'Lunes' => 'Lun',
            'Martes' => 'Mar', 
            'Miércoles' => 'Mié',
            'Jueves' => 'Jue',
            'Viernes' => 'Vie',
            'Sábado' => 'Sáb'
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

    // Scope para horarios activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Accessor para descripción completa
    public function getDescripcionCompletaAttribute()
    {
        return $this->nombre . ' - ' . $this->periodo->nombre_periodo;
    }
}