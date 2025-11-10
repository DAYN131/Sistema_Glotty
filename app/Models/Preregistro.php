<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Importación de modelos para las relaciones
use App\Models\Usuario;
use App\Models\Periodo;
use App\Models\Horario;
use App\Models\Grupo;
use App\Models\Calificacion;

class Preregistro extends Model
{
    // 1. Configuración de la tabla
    protected $table = 'preregistros';

    // 2. Definición de Constantes para ENUMs

    // Correspondiente al campo 'estado'
    const ESTADO = [
        'preregistrado' => 'preregistrado', // Solicitud inicial
        'asignado' => 'asignado',           // Grupo asignado por coordinador
        'cursando' => 'cursando',           // Ya inscrito formalmente (debe estar pagado)
        'finalizado' => 'finalizado',       // Curso concluido
        'cancelado' => 'cancelado',         // Cancelado por el alumno o coordinador
    ];

    // Correspondiente al campo 'pagado'
    const PAGO = [
        'pendiente' => 'pendiente',
        'pagado' => 'pagado',
    ];

    // 3. Campos Asignables Masivamente (fillable)
    protected $fillable = [
        'usuario_id',
        'periodo_id',
        'nivel_solicitado',
        'horario_solicitado_id',
        'semestre_carrera',
        'grupo_asignado_id',
        'estado',
        'pagado',
    ];

    // 4. Casting de Atributos
    protected $casts = [
        'nivel_solicitado' => 'integer',
        'usuario_id' => 'integer',
        'periodo_id' => 'integer',
        'horario_solicitado_id' => 'integer',
        'grupo_asignado_id' => 'integer',
    ];

    // 5. Relaciones de la Base de Datos

    /**
     * Relación con la tabla 'usuarios' (el alumno que hace el pre-registro).
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Relación con la tabla 'periodos'.
     */
    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    /**
     * Relación con la tabla 'horarios' (la preferencia solicitada).
     */
    public function horarioSolicitado(): BelongsTo
    {
        return $this->belongsTo(Horario::class, 'horario_solicitado_id');
    }

    /**
     * Relación con la tabla 'grupos' (el grupo asignado).
     */
    public function grupoAsignado(): BelongsTo
    {
        // El campo grupo_asignado_id puede ser NULL
        return $this->belongsTo(Grupo::class, 'grupo_asignado_id');
    }

    /**
     * Relación con la tabla 'calificaciones'.
     */
    public function calificacion()
    {
        // Un preregistro tiene una calificación (si está cursando/finalizado)
        return $this->hasOne(Calificacion::class, 'preregistro_id');
    }

    // 6. Métodos de ayuda/alcances personalizados (Scopes)

    /**
     * Scope para obtener pre-registros pendientes de asignación.
     */
    public function scopePendientesAsignacion($query)
    {
        return $query->where('estado', self::ESTADO['preregistrado']);
    }

    /**
     * Scope para obtener pre-registros pagados.
     */
    public function scopePagados($query)
    {
        return $query->where('pagado', self::PAGO['pagado']);
    }
}
