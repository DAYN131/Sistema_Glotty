<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Periodo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'anio',        // 🆕 Para control y visualización
        'fecha_inicio',
        'fecha_fin',
        'activo'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean'
    ];

    // 🆕 RELACIÓN CON GRUPOS
    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'periodo_id');
    }

    // 🆕 SCOPE PARA AÑO ACTUAL
    public function scopeAnioActual($query)
    {
        return $query->where('anio', date('Y'));
    }

    // 🆕 SCOPE PARA AÑO ESPECÍFICO
    public function scopeAnio($query, $anio)
    {
        return $query->where('anio', $anio);
    }

    // Solo un periodo activo a la vez
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    // 🆕 ACCESOR PARA NOMBRE COMPLETO
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->anio;
    }

    // 🆕 VALIDACIÓN: LAS FECHAS DEBEN COINCIDIR CON EL AÑO
    public function validarFechasConAnio()
    {
        return $this->fecha_inicio->year == $this->anio && 
               $this->fecha_fin->year == $this->anio;
    }

    // Scope para períodos futuros (próximos)
    public function scopeFuturos($query)
    {
        $hoy = Carbon::today();
        return $query->where('fecha_inicio', '>', $hoy)
                    ->where('activo', true);
    }

    // Verificar si el período está activo (basado en fechas REALES)
    public function estaActivo()
    {
        $hoy = Carbon::today();
        return $this->activo && 
               $hoy->between($this->fecha_inicio, $this->fecha_fin);
    }

    // Verificar si el período es futuro
    public function esFuturo()
    {
        $hoy = Carbon::today();
        return $this->fecha_inicio > $hoy;
    }

    // 🆕 DURACIÓN EN DÍAS
    public function getDuracionDiasAttribute()
    {
        return $this->fecha_inicio->diffInDays($this->fecha_fin);
    }

    // 🆕 VERIFICAR SI ESTÁ EN CURSO
    public function getEnCursoAttribute()
    {
        $hoy = Carbon::today();
        return $hoy->between($this->fecha_inicio, $this->fecha_fin);
    }
}