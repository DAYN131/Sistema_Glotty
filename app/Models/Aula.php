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

    // Relación con disponibilidad por horario
    public function disponibilidadHorarios()
    {
        return $this->hasMany(DisponibilidadAula::class);
    }

    // Relación con grupos (a través de disponibilidad)
    public function grupos()
    {
        return $this->hasManyThrough(Grupo::class, DisponibilidadAula::class, 'aula_id', 'id', 'id', 'grupo_id');
    }

    // Scope para aulas disponibles
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }

    // Scope por tipo de aula
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Scope por edificio
    public function scopePorEdificio($query, $edificio)
    {
        return $query->where('edificio', $edificio);
    }

    // Scope que combina múltiples filtros
    public function scopeFiltrar($query, $filtros)
    {
        if (isset($filtros['edificio']) && $filtros['edificio']) {
            $query->where('edificio', $filtros['edificio']);
        }
        
        if (isset($filtros['tipo']) && $filtros['tipo']) {
            $query->where('tipo', $filtros['tipo']);
        }
        
        if (isset($filtros['disponible']) && $filtros['disponible'] !== '') {
            $query->where('disponible', $filtros['disponible']);
        }
        
        return $query;
    }

    // Verificar si está disponible en un horario específico
    public function estaDisponibleEnHorario($horarioPeriodoId)
    {
        if (!$this->disponible) {
            return false; // No disponible globalmente
        }

        $disponibilidad = $this->disponibilidadHorarios()
            ->where('horario_periodo_id', $horarioPeriodoId)
            ->first();

        return $disponibilidad ? $disponibilidad->disponible : true;
    }

    // Accesor para nombre completo (sin redundancia)
    public function getNombreCompletoAttribute()
    {
        return "{$this->edificio}-{$this->nombre}";
    }

    // Accesor para tipo formateado
    public function getTipoFormateadoAttribute()
    {
        return self::TIPOS_AULA[$this->tipo] ?? $this->tipo;
    }

    // Accesor para mostrar información resumida
    public function getInfoResumidaAttribute()
    {
        return "{$this->nombre_completo} - {$this->tipo_formateado} ({$this->capacidad} pers.)";
    }

    // Método para obtener estadísticas
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