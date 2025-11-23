<?php
// app/Models/Periodo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_periodo',
        'fecha_inicio', 
        'fecha_fin',
        'estado'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    // ✅ CONSTANTE DE ESTADOS FORMAL
    const ESTADOS = [
        'configuracion' => 'En Configuración',
        'preregistros_activos' => 'Preregistros Abiertos',
        'preregistros_cerrados' => 'Preregistros Cerrados',
        'en_curso' => 'En Curso',
        'finalizado' => 'Finalizado',
        'cancelado' => 'Cancelado'
    ];

    // 🔥 RELACIONES (se mantienen igual)
    public function horariosPeriodo() { return $this->hasMany(HorarioPeriodo::class); }
    public function horariosBase() { return $this->belongsToMany(Horario::class, 'horarios_periodo', 'periodo_id', 'horario_base_id')->withPivot('activo')->withTimestamps(); }
    public function grupos() { return $this->hasMany(Grupo::class); }
    public function preregistros() { return $this->hasMany(Preregistro::class); }

    // 🎯 SCOPES ÚTILES (actualizados)
    public function scopeActivo($query) { return $query->where('estado', 'en_curso'); }
    public function scopeConPreRegistrosActivos($query) { return $query->where('estado', 'preregistros_activos'); }
    public function scopeConPreRegistrosCerrados($query) { return $query->where('estado', 'preregistros_cerrados'); }
    public function scopeFinalizados($query) { return $query->where('estado', 'finalizado'); }
    public function scopeCancelados($query) { return $query->where('estado', 'cancelado'); }

    // ✅ MÉTODOS DE ESTADO (actualizados)
    public function estaEnConfiguracion() { return $this->estado === 'configuracion'; }
    public function preregistrosAbiertos() { return $this->estado === 'preregistros_activos'; }
    public function preregistrosCerrados() { return $this->estado === 'preregistros_cerrados'; }
    public function estaEnCurso() { return $this->estado === 'en_curso'; }
    public function estaFinalizado() { return $this->estado === 'finalizado'; }
    public function estaCancelado() { return $this->estado === 'cancelado'; }

    public function getDiasDuracionAttribute(): int
    {
        return $this->fecha_inicio->diffInDays($this->fecha_fin);
    }

    public function puedeEliminarse()
    {
        return $this->estaEnConfiguracion() && 
            $this->grupos()->count() === 0 &&
            $this->preregistros()->count() === 0;
    }

    // ✅ MÉTODO ACTUALIZADO: Flexibilidad práctica pero con sentido
    public function puedeCambiarA($nuevoEstado)
    {
        // Estados finales: no se puede cambiar desde ellos
        if (in_array($this->estado, ['finalizado', 'cancelado'])) {
            return false;
        }

        // Estados que no pueden ser destino desde cualquier estado
        if (in_array($nuevoEstado, ['finalizado', 'cancelado'])) {
            return true; // Permitir finalizar/cancelar desde cualquier estado activo
        }

        // Transiciones principales permitidas (con flexibilidad)
        $transicionesPermitidas = [
            'configuracion' => ['preregistros_activos', 'preregistros_cerrados', 'en_curso'],
            'preregistros_activos' => ['configuracion', 'preregistros_cerrados', 'en_curso'],
            'preregistros_cerrados' => ['preregistros_activos', 'en_curso', 'configuracion'],
            'en_curso' => ['preregistros_cerrados', 'preregistros_activos', 'configuracion'],
        ];

        return in_array($nuevoEstado, $transicionesPermitidas[$this->estado] ?? []);
    }

    // ✅ NUEVO: Obtener nombre legible del estado
    public function getEstadoLegibleAttribute()
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    // ✅ NUEVO: Verificar si acepta preregistros (para lógica de estudiantes)
    public function aceptaPreRegistros()
    {
        return $this->preregistrosAbiertos();
    }

    // ✅ NUEVO: Estados que permiten gestión de horarios
    public function permiteGestionHorarios()
    {
        return !$this->estaFinalizado() && !$this->estaCancelado();
    }

    // ✅ NUEVO: Estados que permiten preregistros de estudiantes
    public function permitePreRegistrosEstudiantes()
    {
        return $this->preregistrosAbiertos();
    }
}