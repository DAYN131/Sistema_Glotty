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
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Relación con Periodo
     */
    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    /**
     * Relación con Horario (base)
     */
    public function horarioBase()
    {
        return $this->belongsTo(Horario::class, 'horario_base_id');
    }

    /**
     * Relación con Grupos
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'horario_periodo_id');
    }

    /**
     * Scope para horarios activos por periodo
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Accessor para descripción completa
     */
    public function getDescripcionCompletaAttribute()
    {
        return $this->horarioBase->nombre . ' - ' . $this->periodo->nombre_periodo;
    }
}