<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    // ✅ CORRECTO - La tabla coincide
    protected $table = 'horarios';

    // ✅ CORRECTO - Fillables están bien
    protected $fillable = [
        'nombre',
        'tipo',
        'dias',
        'hora_inicio',
        'hora_fin',
        'activo',
    ];

    protected $casts = [
        'dias' => 'array', // ✅ CORRECTO para JSON
        'activo' => 'boolean', // ✅ CORRECTO
        // ❌ FALTAN los casts para created_at y updated_at
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * ❌ CORREGIR: La relación con grupos es a través de horarios_periodo
     * Los grupos se relacionan con horarios_periodo, no directamente con horarios
     */
    public function horariosPeriodo()
    {
        return $this->hasMany(HorarioPeriodo::class, 'horario_base_id');
    }

    /**
     * Relación indirecta con grupos a través de horarios_periodo
     */
    public function grupos()
    {
        return $this->hasManyThrough(
            Grupo::class,
            HorarioPeriodo::class,
            'horario_base_id', // Foreign key en horarios_periodo
            'horario_periodo_id', // Foreign key en grupos
            'id', // Local key en horarios
            'id' // Local key en horarios_periodo
        );
    }

    // ✅ CORRECTO - Scopes están bien
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // ✅ CORRECTO - Accessors están bien
    public function getDiasLegiblesAttribute()
    {
        if (empty($this->dias)) {
            return '';
        }

        $dias = $this->dias;
        if (is_string($dias)) {
            $dias = json_decode($dias, true);
        }
        
        return is_array($dias) ? implode(', ', $dias) : '';
    }

    public function getHorarioLegibleAttribute()
    {
        $horaInicio = $this->hora_inicio instanceof \DateTime 
            ? $this->hora_inicio->format('H:i') 
            : $this->hora_inicio;
            
        $horaFin = $this->hora_fin instanceof \DateTime 
            ? $this->hora_fin->format('H:i') 
            : $this->hora_fin;
            
        return $horaInicio . ' - ' . $horaFin;
    }

    public function getDescripcionCompletaAttribute()
    {
        return $this->nombre . ' (' . $this->dias_legibles . ' ' . $this->horario_legible . ')';
    }

    public function estaActivo()
    {
        return $this->activo;
    }

    public function getDiasArrayAttribute()
    {
        if (empty($this->dias)) {
            return [];
        }

        if (is_array($this->dias)) {
            return $this->dias;
        }

        return json_decode($this->dias, true) ?? [];
    }
}