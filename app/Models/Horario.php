<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'horarios';

    protected $fillable = [
        'nombre',
        'tipo',
        'dias',
        'hora_inicio',
        'hora_fin',
        'activo',
    ];

    protected $casts = [
        'dias' => 'array', // Esto automáticamente convierte el JSON a array
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relación con grupos
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'horario_id');
    }

    /**
     * Scope para horarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para horarios por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Obtener los días como string legible
     */
    public function getDiasLegiblesAttribute()
    {
        if (empty($this->dias)) {
            return '';
        }

        // Asegurarnos de que tenemos un array
        $dias = $this->dias;
        if (is_string($dias)) {
            $dias = json_decode($dias, true);
        }
        
        return is_array($dias) ? implode(', ', $dias) : '';
    }

    /**
     * Obtener el rango de horas legible
     */
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

    /**
     * Obtener la descripción completa del horario
     */
    public function getDescripcionCompletaAttribute()
    {
        return $this->nombre . ' (' . $this->dias_legibles . ' ' . $this->horario_legible . ')';
    }

    /**
     * Verificar si el horario está activo
     */
    public function estaActivo()
    {
        return $this->activo;
    }

    /**
     * Accesor para obtener los días como array
     */
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