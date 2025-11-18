<?php

namespace App\Http\Controllers;

use App\Models\Preregistro;
use App\Models\Periodo;
use App\Models\Grupo;
use App\Models\HorarioPeriodo;
use Illuminate\Http\Request;

class CoordinadorPreregistroController extends Controller
{
    /**
     * Muestra el análisis de demanda (PÁGINA PRINCIPAL)
     */
    public function demanda()
    {
        // Obtener periodo activo para preregistros
        $periodoActivo = Periodo::conPreRegistrosActivos()->first();
        
        // Obtener preregistros pendientes
        $preregistrosPendientes = Preregistro::where('estado', 'pendiente')->count();
        $totalPreregistros = Preregistro::count();
        
        // Análisis por nivel
        $demandaPorNivel = Preregistro::where('estado', 'pendiente')
            ->groupBy('nivel_solicitado')
            ->selectRaw('nivel_solicitado, count(*) as total')
            ->pluck('total', 'nivel_solicitado')
            ->toArray();
        
        // Análisis por horario
        $demandaPorHorario = Preregistro::where('estado', 'pendiente')
            ->with('horarioPreferido')
            ->get()
            ->groupBy('horario_preferido_id')
            ->map(function ($group) {
                $horario = $group->first()->horarioPreferido;
                
                if (!$horario) {
                    return [
                        'nombre' => 'Horario no disponible',
                        'tipo' => 'N/A',
                        'dias' => [],
                        'hora_inicio' => 'N/A',
                        'hora_fin' => 'N/A',
                        'cantidad' => $group->count()
                    ];
                }

                // Formatear horas
                $horaInicio = $horario->hora_inicio instanceof \DateTime 
                    ? $horario->hora_inicio->format('H:i') 
                    : (\Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') ?? 'N/A');
                    
                $horaFin = $horario->hora_fin instanceof \DateTime 
                    ? $horario->hora_fin->format('H:i') 
                    : (\Carbon\Carbon::parse($horario->hora_fin)->format('H:i') ?? 'N/A');

                // Manejar días
                $diasArray = [];
                if (is_array($horario->dias)) {
                    $diasArray = $horario->dias;
                } elseif (is_string($horario->dias)) {
                    $decoded = json_decode($horario->dias, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $diasArray = $decoded;
                    } else {
                        $diasArray = array_map('trim', explode(',', $horario->dias));
                    }
                }
                $diasArray = array_filter($diasArray);

                return [
                    'nombre' => $horario->nombre ?? 'No disponible',
                    'tipo' => $horario->tipo ?? 'Sin tipo',
                    'dias' => $diasArray,
                    'hora_inicio' => $horaInicio,
                    'hora_fin' => $horaFin,
                    'cantidad' => $group->count()
                ];
            })
            ->sortByDesc('cantidad')
            ->toArray();
        
        // Generar sugerencias de grupos
        $gruposSugeridos = [];
        foreach ($demandaPorNivel as $nivel => $cantidad) {
            $gruposNecesarios = ceil($cantidad / 20); // 20 estudiantes por grupo
            
            // Obtener horarios populares para este nivel
            $horariosPopulares = Preregistro::where('estado', 'pendiente')
                ->where('nivel_solicitado', $nivel)
                ->with('horarioPreferido')
                ->get()
                ->groupBy('horario_preferido_id')
                ->map(function ($group) {
                    $horario = $group->first()->horarioPreferido;
                    return [
                        'nombre' => $horario->nombre ?? 'No disponible',
                        'cantidad' => $group->count()
                    ];
                })
                ->sortByDesc('cantidad')
                ->take(3)
                ->values()
                ->toArray();
            
            $gruposSugeridos[] = [
                'nivel' => $nivel,
                'descripcion_nivel' => Preregistro::NIVELES[$nivel] ?? "Nivel $nivel",
                'estudiantes' => $cantidad,
                'grupos_sugeridos' => $gruposNecesarios,
                'horarios_populares' => $horariosPopulares
            ];
        }
        
        $horariosDisponibles = HorarioPeriodo::where('activo', true)->get();

        // Variables adicionales para compact
        $nivelesUnicos = count($demandaPorNivel);
        $horariosUnicos = count($demandaPorHorario);
        
        return view('coordinador.preregistros.demanda', compact(
            'periodoActivo', 
            'totalPreregistros',
            'preregistrosPendientes',
            'demandaPorNivel',
            'demandaPorHorario',
            'gruposSugeridos',
            'nivelesUnicos',
            'horariosUnicos',
            'horariosDisponibles'
        ));
    }

    /**
     * Muestra lista detallada de preregistros (PARA GESTIÓN INDIVIDUAL)
     */
    public function index(Request $request)
    {
        $query = Preregistro::with([
            'usuario', 
            'periodo', 
            'horarioPreferido', 
            'grupoAsignado'
        ])->latest();

        // Aplicar filtros
        if ($request->filled('nivel')) {
            $query->where('nivel_solicitado', $request->nivel);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('pago_estado')) {
            $query->where('pago_estado', $request->pago_estado);
        }

        $preregistros = $query->paginate(20);

        $periodos = Periodo::all();
        $gruposDisponibles = Grupo::with(['horario', 'aula', 'profesor'])->get();

        return view('coordinador.preregistros.index', compact(
            'preregistros', 
            'periodos', 
            'gruposDisponibles'
        ));
    }

    /**
     * Muestra preregistros por estado
     */
    public function porEstado($estado)
    {
        $preregistros = Preregistro::with([
            'usuario', 
            'periodo', 
            'horarioPreferido', 
            'grupoAsignado'
        ])->where('estado', $estado)->latest()->paginate(20);

        return view('coordinador.preregistros.index', compact('preregistros'));
    }

    /**
     * Asigna un preregistro a un grupo - válido para 'pagado' y 'prorroga'
     */
    public function asignarGrupo(Request $request, $id)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id'
        ]);

        try {
            $preregistro = Preregistro::findOrFail($id);
            
            if (!$preregistro->puedeSerAsignado()) {
                return back()->with('error', 
                    $preregistro->pago_estado === 'pendiente' 
                        ? 'Estudiante no ha pagado. ¿Desea dar prórroga?'
                        : 'No se puede asignar grupo en el estado actual'
                );
            }

            // Verificar que el grupo tenga capacidad
            $grupo = Grupo::findOrFail($request->grupo_id);
            if ($grupo->estudiantes_inscritos >= $grupo->capacidad_maxima) {
                return back()->with('error', 'El grupo seleccionado no tiene capacidad disponible');
            }

            // Verificar que el nivel del grupo coincida
            if ($grupo->nivel_ingles != $preregistro->nivel_solicitado) {
                return back()->with('error', 'El nivel del grupo no coincide con el nivel solicitado');
            }

            $preregistro->update([
                'grupo_asignado_id' => $request->grupo_id,
                'estado' => 'asignado'
            ]);

            // Actualizar contador del grupo
            $grupo->increment('estudiantes_inscritos');

            return back()->with('success', 'Estudiante asignado al grupo exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al asignar preregistro: ' . $e->getMessage());
        }
    }

    /**
     * Cambia el estado de pago de un preregistro - ACTUALIZADO CON PRÓRROGA
     */
    public function cambiarEstadoPago(Request $request, $id)
    {
        $request->validate([
            'pago_estado' => 'required|in:pendiente,prorroga,pagado,rechazado' // ✅ ACTUALIZADO
        ]);

        try {
            $preregistro = Preregistro::findOrFail($id);
            
            $preregistro->update([
                'pago_estado' => $request->pago_estado
            ]);

            $mensaje = match($request->pago_estado) {
                'prorroga' => 'Prórroga concedida. Ya puede asignarse a grupo.',
                'pagado' => 'Pago confirmado. Ya puede asignarse a grupo.',
                'pendiente' => 'Estado de pago reiniciado a pendiente.',
                'rechazado' => 'Pago rechazado. No puede asignarse a grupo.'
            };

            return back()->with('success', $mensaje);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar estado de pago: ' . $e->getMessage());
        }
    }

    /**
     * Cambia el estado de un preregistro
     */
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,asignado,cursando,finalizado,cancelado'
        ]);

        try {
            $preregistro = Preregistro::findOrFail($id);
            
            // Validaciones adicionales para cancelación
            if ($request->estado === 'cancelado') {
                if (!$preregistro->puedeSerCancelado()) {
                    return back()->with('error', 'No se puede cancelar este preregistro en su estado actual.');
                }
                
                // Si tenía grupo asignado, liberarlo y actualizar contador
                if ($preregistro->grupo_asignado_id) {
                    $grupo = Grupo::find($preregistro->grupo_asignado_id);
                    if ($grupo && $grupo->estudiantes_inscritos > 0) {
                        $grupo->decrement('estudiantes_inscritos');
                    }
                    $preregistro->grupo_asignado_id = null;
                }
            }

            // Si se cambia de asignado a otro estado, liberar el grupo
            if ($preregistro->estado === 'asignado' && $request->estado !== 'asignado' && $request->estado !== 'cancelado') {
                if ($preregistro->grupo_asignado_id) {
                    $grupo = Grupo::find($preregistro->grupo_asignado_id);
                    if ($grupo && $grupo->estudiantes_inscritos > 0) {
                        $grupo->decrement('estudiantes_inscritos');
                    }
                    $preregistro->grupo_asignado_id = null;
                }
            }

            $preregistro->update([
                'estado' => $request->estado,
                'grupo_asignado_id' => $preregistro->grupo_asignado_id
            ]);

            return back()->with('success', 'Estado actualizado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }
    }

    /**
     * Cancela un preregistro específico (acción directa)
     */
    public function cancelarPreregistro($id)
    {
        try {
            $preregistro = Preregistro::findOrFail($id);
            
            // Verificar que pueda ser cancelado según las reglas del modelo
            if (!$preregistro->puedeSerCancelado()) {
                return back()->with('error', 'No se puede cancelar este preregistro en su estado actual.');
            }

            // Liberar grupo asignado si tenía y actualizar contador
            $grupoAsignado = null;
            if ($preregistro->grupo_asignado_id) {
                $grupo = Grupo::find($preregistro->grupo_asignado_id);
                if ($grupo && $grupo->estudiantes_inscritos > 0) {
                    $grupo->decrement('estudiantes_inscritos');
                }
                $grupoAsignado = $preregistro->grupo_asignado_id;
            }

            $preregistro->update([
                'estado' => 'cancelado',
                'grupo_asignado_id' => null
            ]);

            $mensaje = 'Preregistro cancelado exitosamente.';
            if ($grupoAsignado) {
                $mensaje .= ' Se liberó la asignación del grupo.';
            }

            return back()->with('success', $mensaje);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al cancelar preregistro: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene los estudiantes por nivel (para AJAX)
     */
    public function obtenerEstudiantesPorNivel($nivel)
    {
        $estudiantes = Preregistro::with(['usuario', 'horarioPreferido'])
            ->where('estado', 'pendiente')
            ->where('nivel_solicitado', $nivel)
            ->get()
            ->map(function ($preregistro) {
                $horario = $preregistro->horarioPreferido;
                
                // Procesar días del horario
                $diasArray = [];
                if ($horario) {
                    if (is_array($horario->dias)) {
                        $diasArray = $horario->dias;
                    } elseif (is_string($horario->dias)) {
                        $decoded = json_decode($horario->dias, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $diasArray = $decoded;
                        } else {
                            $diasArray = array_map('trim', explode(',', $horario->dias));
                        }
                    }
                }
                $diasArray = array_filter($diasArray);
                $diasTexto = !empty($diasArray) ? implode(', ', $diasArray) : 'No especificado';

                return [
                    'id' => $preregistro->id,
                    'nombre' => $preregistro->usuario->nombre_completo ?? 'No disponible',
                    'numero_control' => $preregistro->usuario->numero_control ?? 'N/A',
                    'correo' => $preregistro->usuario->correo_institucional ?? 'N/A',
                    'especialidad' => $preregistro->usuario->especialidad ?? 'N/A',
                    'horario_preferido' => $horario->nombre ?? 'No especificado',
                    'tipo_horario' => $horario->tipo ?? 'N/A',
                    'dias_horario' => $diasTexto,
                    'semestre_carrera' => $preregistro->semestre_carrera ?? 'No especificado',
                    'pago_estado' => $preregistro->pago_estado,
                    'puede_ser_asignado' => $preregistro->puedeSerAsignado()
                ];
            });

        return response()->json([
            'estudiantes' => $estudiantes,
            'total' => $estudiantes->count(),
            'nivel' => Preregistro::NIVELES[$nivel] ?? "Nivel $nivel"
        ]);
    }

    /**
     * Muestra el detalle de un preregistro
     */
    public function show($id)
    {
        $preregistro = Preregistro::with([
            'usuario', 
            'periodo', 
            'horarioPreferido', 
            'grupoAsignado',
            'grupoAsignado.profesor',
            'grupoAsignado.aula'
        ])->findOrFail($id);

        $gruposDisponibles = Grupo::with(['horario', 'aula', 'profesor'])
            ->where('nivel_ingles', $preregistro->nivel_solicitado)
            ->where('periodo_id', $preregistro->periodo_id)
            ->get();

        return view('coordinador.preregistros.show', compact(
            'preregistro',
            'gruposDisponibles'
        ));
    }
}