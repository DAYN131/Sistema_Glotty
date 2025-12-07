<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Preregistro;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumnoController extends Controller
{
    /**
     * Mostrar perfil del alumno (solo vista)
     */
    public function miPerfil()
    {
        $usuario = Auth::user();
        
        // Cargar preregistros del alumno
        $usuario->load([
            'preregistros' => function($query) {
                $query->with(['periodo', 'grupoAsignado', 'grupoAsignado.horario'])
                      ->orderBy('created_at', 'desc');
            }
        ]);
        
        // Estadísticas del alumno
        $preregistros = $usuario->preregistros;
        
        $estadisticas = [
            'total_preregistros' => $preregistros->count(),
            'preregistros_activos' => $preregistros->whereIn('estado', ['asignado', 'cursando'])->count(),
            'preregistros_finalizados' => $preregistros->where('estado', 'finalizado')->count(),
            'preregistros_cancelados' => $preregistros->where('estado', 'cancelado')->count(),
            'preregistros_pendientes' => $preregistros->where('estado', 'pendiente')->count(),
            'niveles_cursados' => $preregistros->where('estado', 'finalizado')
                                               ->pluck('nivel_solicitado')
                                               ->unique()
                                               ->sort()
                                               ->values(),
            'ultimo_preregistro' => $preregistros->first(),
        ];
        
        // Obtener periodo activo si existe
        $periodoActual = Periodo::where('estado', 'en_curso')->first();
        $preregistroActual = null;
        
        if ($periodoActual) {
            $preregistroActual = $usuario->preregistros()
                ->where('periodo_id', $periodoActual->id)
                ->whereIn('estado', ['asignado', 'cursando'])
                ->first();
        }
        
        return view('alumno.perfil', compact('usuario', 'estadisticas', 'preregistroActual'));
    }
}