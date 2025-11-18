<?php
// app/Models/DisponibilidadAula.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisponibilidadAula extends Model
{
    use HasFactory;

    protected $table = 'disponibilidad_aulas';

    protected $fillable = [
        'aula_id',
        'horario_periodo_id',
        'disponible',
        'grupo_id'
    ];

    protected $casts = [
        'disponible' => 'boolean'
    ];

    // Relación con el aula
    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    // Relación con el horario del período
    public function horarioPeriodo()
    {
        return $this->belongsTo(HorarioPeriodo::class);
    }

    // Relación con el grupo (si está ocupada)
    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    // Scope para disponibilidades activas
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }

    // Scope para un horario específico
    public function scopePorHorario($query, $horarioPeriodoId)
    {
        return $query->where('horario_periodo_id', $horarioPeriodoId);
    }

    // Método para verificar conflictos
    public static function tieneConflicto($aulaId, $horarioPeriodoId, $excluirId = null)
    {
        $query = self::where('aula_id', $aulaId)
                    ->where('horario_periodo_id', $horarioPeriodoId)
                    ->where('disponible', false);

        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }

        return $query->exists();
    }
}