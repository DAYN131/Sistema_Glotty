<?php
// app/Models/Grupo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nivel_ingles',
        'letra_grupo',
        'periodo_id',
        'horario_periodo_id',
        'aula_id',
        'profesor_id',
        'capacidad_maxima',
        'estudiantes_inscritos',
        'estado'
    ];

    protected $casts = [
        'nivel_ingles' => 'integer',
        'capacidad_maxima' => 'integer',
        'estudiantes_inscritos' => 'integer'
    ];

    // ESTADOS DEL GRUPO
    const ESTADOS = [
        'planificado' => 'Planificado',
        'con_profesor' => 'Con Profesor',
        'con_aula' => 'Con Aula',
        'activo' => 'Activo',
        'cancelado' => 'Cancelado'
    ];

    // NIVELES (para consistencia con Preregistro)
    const NIVELES = [
        1 => 'Nivel 1 - Principiante',
        2 => 'Nivel 2 - Básico', 
        3 => 'Nivel 3 - Intermedio',
        4 => 'Nivel 4 - Avanzado',
        5 => 'Nivel 5 - Conversación'
    ];

    // LETRAS DISPONIBLES
    const LETRAS_GRUPO = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

    // 🔗 RELACIONES
    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function horario()
    {
        return $this->belongsTo(HorarioPeriodo::class, 'horario_periodo_id');
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class, 'aula_id');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function preregistros()
    {
        return $this->hasMany(Preregistro::class, 'grupo_asignado_id');
    }

    public function estudiantesActivos()
    {
        return $this->preregistros()->whereIn('estado', ['asignado', 'cursando']);
    }

    // 🎯 SCOPES ÚTILES
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePlanificados($query)
    {
        return $query->where('estado', 'planificado');
    }

    public function scopePorNivel($query, $nivel)
    {
        return $query->where('nivel_ingles', $nivel);
    }

    public function scopePorPeriodo($query, $periodoId)
    {
        return $query->where('periodo_id', $periodoId);
    }

    public function scopeConCapacidad($query)
    {
        return $query->whereRaw('estudiantes_inscritos < capacidad_maxima');
    }

    // ✅ ACCESORES
    public function getNombreCompletoAttribute()
    {
        return "Nivel {$this->nivel_ingles}-{$this->letra_grupo}";
    }

    public function getNivelFormateadoAttribute()
    {
        return self::NIVELES[$this->nivel_ingles] ?? "Nivel {$this->nivel_ingles}";
    }

    public function getEstadoFormateadoAttribute()
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getCapacidadDisponibleAttribute()
    {
        return $this->capacidad_maxima - $this->estudiantes_inscritos;
    }

    public function getPorcentajeOcupacionAttribute()
    {
        if ($this->capacidad_maxima === 0) return 0;
        return round(($this->estudiantes_inscritos / $this->capacidad_maxima) * 100, 2);
    }

    // ✅ MÉTODOS DE NEGOCIO
    public function tieneCapacidad()
    {
        return $this->estudiantes_inscritos < $this->capacidad_maxima;
    }

    public function puedeSerActivo()
    {
        return $this->profesor_id && $this->aula_id && $this->tieneCapacidad();
    }

    public function puedeSerCancelado()
    {
        return $this->estudiantes_inscritos === 0 && $this->estado !== 'cancelado';
    }

    public function puedeEliminarse()
    {
        return $this->estudiantes_inscritos === 0 && $this->estado === 'planificado';
    }

    /**
     * Asignar estudiante al grupo
     */
    public function asignarEstudiante($preregistroId)
    {
        if (!$this->tieneCapacidad()) {
            return false;
        }

        $preregistro = Preregistro::find($preregistroId);
        if (!$preregistro || !$preregistro->puedeSerAsignado()) {
            return false;
        }

        // Actualizar preregistro
        $preregistro->update([
            'grupo_asignado_id' => $this->id,
            'estado' => 'asignado'
        ]);

        // Actualizar contador del grupo
        $this->increment('estudiantes_inscritos');

        return true;
    }

    /**
     * Remover estudiante del grupo
     */
    public function removerEstudiante($preregistroId)
    {
        $preregistro = Preregistro::find($preregistroId);
        if (!$preregistro || $preregistro->grupo_asignado_id !== $this->id) {
            return false;
        }

        // Actualizar preregistro
        $preregistro->update([
            'grupo_asignado_id' => null,
            'estado' => 'pendiente'
        ]);

        // Actualizar contador del grupo
        if ($this->estudiantes_inscritos > 0) {
            $this->decrement('estudiantes_inscritos');
        }

        return true;
    }

    /**
     * Obtener letras disponibles para un nivel y periodo
     */
    public static function letrasDisponibles($nivel, $periodoId)
    {
        $letrasOcupadas = self::where('nivel_ingles', $nivel)
            ->where('periodo_id', $periodoId)
            ->pluck('letra_grupo')
            ->toArray();

        return array_diff(self::LETRAS_GRUPO, $letrasOcupadas);
    }


    public function getClaseEstadoAttribute()
    {
        return match($this->estado) {
            'planificado' => 'bg-yellow-100 text-yellow-800',
            'con_profesor' => 'bg-blue-100 text-blue-800',
            'con_aula' => 'bg-purple-100 text-purple-800',
            'activo' => 'bg-green-100 text-green-800',
            'cancelado' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Estado en texto legible
     */
    public function getEstadoLegibleAttribute()
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }
}