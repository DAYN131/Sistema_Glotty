<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Grupo extends Model
{
  

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'grupos';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nivel_ingles',
        'letra_grupo',
        'periodo_id',
        'horario_id',
        'aula_id',
        'profesor_id',
        'capacidad_maxima',
        'estudiantes_inscritos',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'nivel_ingles' => 'integer',
        'capacidad_maxima' => 'integer',
        'estudiantes_inscritos' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Estados posibles del grupo
     */
    const ESTADOS = [
        'planificado' => 'planificado',
        'con_profesor' => 'con_profesor', 
        'con_aula' => 'con_aula',
        'activo' => 'activo',
        'cancelado' => 'cancelado',
    ];

    /**
     * Relación con el periodo
     */
    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    /**
     * Relación con el horario
     */
    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class, 'horario_id');
    }

    /**
     * Relación con el aula
     */
    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class, 'aula_id', 'id_aula');
    }

    /**
     * Relación con el profesor
     */
    public function profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'profesor_id', 'id_profesor');
    }

    /**
     * Relación con los preregistros asignados
     */
    public function preregistros(): HasMany
    {
        return $this->hasMany(Preregistro::class, 'grupo_asignado_id');
    }

    /**
     * Scope para grupos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', self::ESTADOS['activo']);
    }

    /**
     * Scope para grupos por nivel
     */
    public function scopePorNivel($query, $nivel)
    {
        return $query->where('nivel_ingles', $nivel);
    }

    /**
     * Scope para grupos por periodo
     */
    public function scopePorPeriodo($query, $periodoId)
    {
        return $query->where('periodo_id', $periodoId);
    }

    /**
     * Scope para grupos con capacidad disponible
     */
    public function scopeConCapacidad($query)
    {
        return $query->whereRaw('estudiantes_inscritos < capacidad_maxima');
    }

    /**
     * Verificar si el grupo tiene capacidad disponible
     */
    public function tieneCapacidad(): bool
    {
        return $this->estudiantes_inscritos < $this->capacidad_maxima;
    }

    /**
     * Obtener capacidad disponible
     */
    public function getCapacidadDisponibleAttribute(): int
    {
        return $this->capacidad_maxima - $this->estudiantes_inscritos;
    }

    /**
     * Obtener el nombre completo del grupo (ej: "1-A")
     */
    public function getNombreCompletoAttribute(): string
    {
        return $this->nivel_ingles . '-' . $this->letra_grupo;
    }

    /**
     * Obtener el porcentaje de ocupación
     */
    public function getPorcentajeOcupacionAttribute(): float
    {
        if ($this->capacidad_maxima == 0) {
            return 0;
        }
        
        return ($this->estudiantes_inscritos / $this->capacidad_maxima) * 100;
    }

    /**
     * Verificar si el grupo está completo
     */
    public function getEstaCompletoAttribute(): bool
    {
        return $this->estudiantes_inscritos >= $this->capacidad_maxima;
    }

    /**
     * Incrementar contador de estudiantes inscritos
     */
    public function incrementarInscritos(): bool
    {
        if ($this->esta_completo) {
            return false;
        }

        return $this->increment('estudiantes_inscritos');
    }

    /**
     * Decrementar contador de estudiantes inscritos
     */
    public function decrementarInscritos(): bool
    {
        if ($this->estudiantes_inscritos <= 0) {
            return false;
        }

        return $this->decrement('estudiantes_inscritos');
    }

    /**
     * Obtener el estado legible
     */
    public function getEstadoLegibleAttribute(): string
    {
        $estadosLegibles = [
            'planificado' => 'Planificado',
            'con_profesor' => 'Con Profesor',
            'con_aula' => 'Con Aula',
            'activo' => 'Activo',
            'cancelado' => 'Cancelado',
        ];

        return $estadosLegibles[$this->estado] ?? 'Desconocido';
    }

    /**
     * Obtener la clase CSS para el estado
     */
    public function getClaseEstadoAttribute(): string
    {
        $clases = [
            'planificado' => 'bg-yellow-100 text-yellow-800',
            'con_profesor' => 'bg-blue-100 text-blue-800',
            'con_aula' => 'bg-purple-100 text-purple-800',
            'activo' => 'bg-green-100 text-green-800',
            'cancelado' => 'bg-red-100 text-red-800',
        ];

        return $clases[$this->estado] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Verificar si el grupo puede recibir más estudiantes
     */
    public function puedeRecibirEstudiantes(): bool
    {
        return $this->estado === 'activo' && $this->tieneCapacidad();
    }

    /**
     * Cancelar el grupo
     */
    public function cancelar(): bool
    {
        // Liberar preregistros asignados
        $this->preregistros()->update([
            'grupo_asignado_id' => null,
            'estado' => 'preregistrado'
        ]);

        return $this->update([
            'estado' => 'cancelado',
            'estudiantes_inscritos' => 0
        ]);
    }

    /**
     * Obtener información resumida del grupo
     */
    public function getInformacionResumidaAttribute(): array
    {
        return [
            'nombre' => $this->nombre_completo,
            'nivel' => $this->nivel_ingles,
            'letra' => $this->letra_grupo,
            'periodo' => $this->periodo->nombre ?? 'N/A',
            'horario' => $this->horario->nombre ?? 'N/A',
            'profesor' => $this->profesor ? $this->profesor->nombre_profesor . ' ' . $this->profesor->apellidos_profesor : 'Por asignar',
            'aula' => $this->aula ? $this->aula->id_aula : 'Por asignar',
            'estado' => $this->estado_legible,
            'ocupacion' => $this->estudiantes_inscritos . '/' . $this->capacidad_maxima,
            'porcentaje_ocupacion' => $this->porcentaje_ocupacion,
        ];
    }
}