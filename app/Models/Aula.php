<?php
// app/Models/Aula.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'edificio', 
        'capacidad',
        'tipo',
        'equipamiento',
        'disponible'
    ];

    protected $casts = [
        'disponible' => 'boolean',
        'capacidad' => 'integer'
    ];

    // Tipos de aula disponibles
    const TIPOS_AULA = [
        'regular' => 'Aula Regular',
        'laboratorio' => 'Laboratorio',
        'computo' => 'Sala de Cómputo', 
        'audiovisual' => 'Aula Audiovisual',
    ];

    // ✅ RELACIÓN DIRECTA CON GRUPOS (más simple)
    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'aula_id');
    }

    // ✅ Scope para aulas disponibles
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }

    // ✅ Scope por tipo de aula
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // ✅ Scope por edificio
    public function scopePorEdificio($query, $edificio)
    {
        return $query->where('edificio', $edificio);
    }

    // ✅ NUEVO: Verificar disponibilidad en horario específico
    public function estaDisponibleEnHorario($horarioPeriodoId)
    {
        // Si el aula no está disponible globalmente
        if (!$this->disponible) {
            return false;
        }

        // Verificar si ya está ocupada en ese horario
        $ocupada = $this->grupos()
            ->where('horario_periodo_id', $horarioPeriodoId)
            ->whereNotIn('estado', ['cancelado'])
            ->exists();

        return !$ocupada;
    }

    // ✅ NUEVO: Obtener horarios ocupados
    public function obtenerHorariosOcupados()
    {
        return $this->grupos()
            ->whereNotIn('estado', ['cancelado'])
            ->with('horario')
            ->get()
            ->pluck('horario');
    }

    // ✅ NUEVO: Verificar si soporta capacidad
    public function soportaCapacidad($capacidadRequerida)
    {
        return $this->capacidad >= $capacidadRequerida;
    }

    // Accesor para nombre completo
    public function getNombreCompletoAttribute()
    {
        return "{$this->edificio}-{$this->nombre}";
    }

    // Accesor para tipo formateado
    public function getTipoFormateadoAttribute()
    {
        return self::TIPOS_AULA[$this->tipo] ?? $this->tipo;
    }

    // Accesor para información resumida
    public function getInfoResumidaAttribute()
    {
        $disponibilidad = $this->disponible ? 'Disponible' : 'No disponible';
        return "{$this->nombre_completo} - {$this->tipo_formateado} ({$this->capacidad} pers.) - {$disponibilidad}";
    }

    // ✅ NUEVO: Método para obtener aulas disponibles para un horario
    public static function disponiblesParaHorario($horarioPeriodoId, $capacidadRequerida = null)
    {
        $query = self::disponibles()
            ->whereDoesntHave('grupos', function ($q) use ($horarioPeriodoId) {
                $q->where('horario_periodo_id', $horarioPeriodoId)
                  ->whereNotIn('estado', ['cancelado']);
            });

        if ($capacidadRequerida) {
            $query->where('capacidad', '>=', $capacidadRequerida);
        }

        return $query->get();
    }

    // Método para obtener estadísticas (simplificado)
    public static function obtenerEstadisticas()
    {
        return [
            'total' => self::count(),
            'disponibles' => self::where('disponible', true)->count(),
            'por_tipo' => self::groupBy('tipo')
                            ->selectRaw('tipo, count(*) as total')
                            ->pluck('total', 'tipo'),
            'por_edificio' => self::groupBy('edificio')
                                ->selectRaw('edificio, count(*) as total')
                                ->pluck('total', 'edificio')
        ];
    }
}