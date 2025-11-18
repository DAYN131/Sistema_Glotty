<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preregistro extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'periodo_id',
        'nivel_solicitado',
        'horario_preferido_id',
        'semestre_carrera',
        'grupo_asignado_id',
        'estado',
        'pago_estado'
    ];

    protected $casts = [
        'nivel_solicitado' => 'integer'
    ];

    // ESTADOS DEL PREREGISTRO
    const ESTADOS = [
        'pendiente' => 'Pendiente de Asignación',
        'asignado' => 'Asignado a Grupo',
        'cursando' => 'Cursando',
        'finalizado' => 'Finalizado',
        'cancelado' => 'Cancelado'
    ];

    // Estados de pago - ACTUALIZADO CON PRÓRROGA
    const PAGO_ESTADOS = [
        'pendiente' => 'Pendiente de Pago',
        'prorroga' => 'En Prórroga', // ✅ NUEVO
        'pagado' => 'Pagado',
        'rechazado' => 'Pago Rechazado'
    ];

    // Niveles disponibles
    const NIVELES = [
        1 => 'Nivel 1 - Principiante',
        2 => 'Nivel 2 - Básico', 
        3 => 'Nivel 3 - Intermedio',
        4 => 'Nivel 4 - Avanzado',
        5 => 'Nivel 5 - Conversación'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function horarioPreferido()
    {
        return $this->belongsTo(HorarioPeriodo::class, 'horario_preferido_id');
    }

    public function grupoAsignado()
    {
        return $this->belongsTo(Grupo::class, 'grupo_asignado_id');
    }

    // Scopes útiles - ACTUALIZADOS
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopePagados($query)
    {
        return $query->where('pago_estado', 'pagado');
    }

    public function scopePuedenAsignarse($query)
    {
        return $query->where('estado', 'pendiente')
                    ->whereIn('pago_estado', ['pagado', 'prorroga']); // ✅ INCLUYE PRÓRROGA
    }

    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['pendiente', 'asignado', 'cursando']);
    }

    public function scopeConProrroga($query)
    {
        return $query->where('pago_estado', 'prorroga');
    }

    // Accesores
    public function getNivelFormateadoAttribute()
    {
        return self::NIVELES[$this->nivel_solicitado] ?? "Nivel {$this->nivel_solicitado}";
    }

    public function getEstadoFormateadoAttribute()
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getPagoEstadoFormateadoAttribute()
    {
        return self::PAGO_ESTADOS[$this->pago_estado] ?? $this->pago_estado;
    }

    // ✅ MÉTODOS DE NEGOCIO ACTUALIZADOS CON PRÓRROGA

    /**
     * Puede ser asignado a grupo si:
     * - Está pendiente
     * - Pago está 'pagado' O 'prorroga' ✅
     * - El periodo acepta preregistros
     */
    public function puedeSerAsignado()
    {
        return $this->estado === 'pendiente' && 
               in_array($this->pago_estado, ['pagado', 'prorroga']) && // ✅ ACTUALIZADO
               $this->periodo->aceptaPreRegistros();
    }

    /**
     * Está listo para cursar (tiene grupo y pago en orden)
     */
    public function estaListoParaCursar()
    {
        return $this->estado === 'asignado' && 
               in_array($this->pago_estado, ['pagado', 'prorroga']); // ✅ ACTUALIZADO
    }

    /**
     * Puede ser cancelado solo si:
     * - Está pendiente o asignado
     * - El periodo no ha finalizado
     */
    public function puedeSerCancelado()
    {
        return in_array($this->estado, ['pendiente', 'asignado']) &&
               !$this->periodo->estaFinalizado();
    }

    /**
     * Está cursando activamente
     */
    public function estaCursando()
    {
        return $this->estado === 'cursando';
    }

    /**
     * Ha finalizado el curso
     */
    public function estaFinalizado()
    {
        return $this->estado === 'finalizado';
    }

    /**
     * Está cancelado
     */
    public function estaCancelado()
    {
        return $this->estado === 'cancelado';
    }

    /**
     * Tiene prórroga activa
     */
    public function tieneProrroga()
    {
        return $this->pago_estado === 'prorroga'; // ✅ NUEVO MÉTODO
    }

    /**
     * Verifica si el preregistro está vencido (no pagado a tiempo)
     * Ahora solo aplica para 'pendiente', no para 'prorroga'
     */
    public function estaVencido()
    {
        return $this->pago_estado === 'pendiente' && 
               $this->created_at->diffInDays(now()) > 7; // 7 días para pagar (solo pendientes)
    }

    /**
     * Cambios automáticos de estado cuando el periodo cambia
     */
    public function sincronizarConPeriodo()
    {
        if ($this->periodo->estaEnCurso() && $this->estaListoParaCursar()) {
            $this->update(['estado' => 'cursando']);
        }
        
        if ($this->periodo->estaFinalizado() && $this->estado === 'cursando') {
            $this->update(['estado' => 'finalizado']);
        }
    }

    /**
     * TRANSICIONES PERMITIDAS para preregistros
     */
    public function puedeCambiarA($nuevoEstado)
    {
        $transicionesPermitidas = [
            'pendiente' => ['asignado', 'cancelado'],
            'asignado' => ['cursando', 'cancelado'],
            'cursando' => ['finalizado'],
            'finalizado' => [],
            'cancelado' => []
        ];

        return in_array($nuevoEstado, $transicionesPermitidas[$this->estado]);
    }

    /**
     * Para mostrar colores en la interfaz
     */
    public function getColorPagoAttribute()
    {
        return match($this->pago_estado) {
            'pendiente' => 'yellow',
            'prorroga' => 'blue', // ✅ NUEVO COLOR
            'pagado' => 'green',
            'rechazado' => 'red',
            default => 'gray'
        };
    }
}