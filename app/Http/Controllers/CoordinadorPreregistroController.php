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
     * Muestra el análisis de demanda 
     */
    public function demanda(Request $request)
    {
        // Obtener periodo activo para preregistros
        $periodoActivo = Periodo::conPreRegistrosActivos()->first();
        
        // Obtener estados de pago permitidos del request o usar valores por defecto
        $estadosPagoFiltro = $request->get('estados_pago', ['pagado', 'prorroga']);
        $filtroActivo = $request->get('filtro_activo', true);
        
        // Obtener preregistros pendientes
        $query = Preregistro::where('estado', 'pendiente');
        
        // Aplicar filtro de estado de pago si está activo
        if ($filtroActivo) {
            $query->whereIn('pago_estado', $estadosPagoFiltro);
        }
        
        $preregistrosFiltrados = $query->get();
        
        $preregistrosPendientes = $preregistrosFiltrados->count();
        $totalPreregistros = Preregistro::count();
        
        // Calcular estadísticas de estados de pago para información
        $estadisticasPago = Preregistro::where('estado', 'pendiente')
            ->selectRaw('pago_estado, COUNT(*) as cantidad')
            ->groupBy('pago_estado')
            ->pluck('cantidad', 'pago_estado')
            ->toArray();
        
        // Análisis por nivel con filtro aplicado
        $demandaPorNivel = $preregistrosFiltrados
            ->groupBy('nivel_solicitado')
            ->map(function ($group) {
                return $group->count();
            })
            ->toArray();
        
        // Análisis por horario con filtro aplicado
        $demandaPorHorario = $preregistrosFiltrados
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
        
        // Generar sugerencias de grupos con criterios de viabilidad
        $gruposSugeridos = [];
        foreach ($demandaPorNivel as $nivel => $cantidad) {
            // Criterios de viabilidad
            $minimoEstudiantes = 25; // Mínimo para considerar viable un grupo
            $maximoEstudiantes = 30; // Máximo recomendado por grupo
            $estudiantesPorGrupo = 25; // Ideal por grupo
            
            $gruposNecesarios = ceil($cantidad / $estudiantesPorGrupo);
            $esViable = $cantidad >= $minimoEstudiantes;
            
            // Determinar prioridad
            if ($cantidad >= 40) {
                $prioridad = 'alta';
                $recomendacion = 'Crear grupos urgentemente';
            } elseif ($cantidad >= $minimoEstudiantes) {
                $prioridad = 'media';
                $recomendacion = 'Grupo viable, puede crear';
            } else {
                $prioridad = 'baja';
                $recomendacion = 'Esperar más estudiantes';
            }

            // Obtener horarios populares para este nivel
            $horariosPopulares = $preregistrosFiltrados
                ->where('nivel_solicitado', $nivel)
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

            // Sugerir distribución de grupos
            $distribucionGrupos = [];
            if ($esViable) {
                $estudiantesRestantes = $cantidad;
                for ($i = 1; $i <= $gruposNecesarios; $i++) {
                    $estudiantesEnGrupo = min($estudiantesRestantes, $maximoEstudiantes);
                    $distribucionGrupos[] = [
                        'grupo' => $i,
                        'estudiantes' => $estudiantesEnGrupo,
                        'estado' => $estudiantesEnGrupo >= $minimoEstudiantes ? 'Óptimo' : 'Mínimo'
                    ];
                    $estudiantesRestantes -= $estudiantesEnGrupo;
                }
            }
            
            $gruposSugeridos[] = [
                'nivel' => $nivel,
                'descripcion_nivel' => Preregistro::NIVELES[$nivel] ?? "Nivel $nivel",
                'estudiantes' => $cantidad,
                'grupos_sugeridos' => $gruposNecesarios,
                'es_viable' => $esViable,
                'prioridad' => $prioridad,
                'recomendacion' => $recomendacion,
                'distribucion' => $distribucionGrupos,
                'horarios_populares' => $horariosPopulares,
                'minimo_requerido' => $minimoEstudiantes,
                'maximo_recomendado' => $maximoEstudiantes
            ];
        }
        
        // Ordenar por prioridad (alta primero)
        usort($gruposSugeridos, function ($a, $b) {
            $prioridades = ['alta' => 3, 'media' => 2, 'baja' => 1];
            return $prioridades[$b['prioridad']] - $prioridades[$a['prioridad']];
        });

        $horariosDisponibles = HorarioPeriodo::where('activo', true)->get();

        // Estadísticas generales
        $nivelesUnicos = count($demandaPorNivel);
        $horariosUnicos = count($demandaPorHorario);
        
        // Total de estudiantes válidos para grupos
        $totalEstudiantesValidos = $preregistrosPendientes;
        $totalGruposSugeridos = array_sum(array_column($gruposSugeridos, 'grupos_sugeridos'));
        $nivelesViables = count(array_filter($gruposSugeridos, function($grupo) {
            return $grupo['es_viable'];
        }));
        
        // Definir opciones de estados de pago para el filtro
        $opcionesEstadosPago = [
            'pagado' => 'Pagados',
            'prorroga' => 'En prórroga',
            'pendiente' => 'Pendientes de pago',
            'rechazado' => 'Rechazados',
            'cancelado' => 'Cancelados'
        ];
        
        return view('coordinador.preregistros.demanda', compact(
            'periodoActivo', 
            'totalPreregistros',
            'preregistrosPendientes',
            'demandaPorNivel',
            'demandaPorHorario',
            'gruposSugeridos',
            'nivelesUnicos',
            'horariosUnicos',
            'horariosDisponibles',
            'totalEstudiantesValidos',
            'totalGruposSugeridos',
            'nivelesViables',
            'estadisticasPago',
            'opcionesEstadosPago',
            'estadosPagoFiltro',
            'filtroActivo'
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
     * Asigna un preregistro a un grupo - MODIFICADO: permite reasignación en cualquier estado
     */
    public function asignarGrupo(Request $request, $id)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id'
        ]);

        try {
            $preregistro = Preregistro::findOrFail($id);
            
            // ✅ MODIFICACIÓN: Permitir asignación en más estados (no solo pendiente)
            // Solo verificar si puede ser asignado si está pendiente
            if ($preregistro->estado === 'pendiente' && !$preregistro->puedeSerAsignado()) {
                return back()->with('error', 
                    $preregistro->pago_estado === 'pendiente' 
                        ? 'Estudiante no ha pagado. ¿Desea dar prórroga?'
                        : 'No se puede asignar grupo en el estado actual'
                );
            }

            // ✅ MODIFICACIÓN: Verificar estados que NO permiten reasignación
            $estadosQueNoPermitenAsignacion = ['finalizado', 'cancelado'];
            if (in_array($preregistro->estado, $estadosQueNoPermitenAsignacion)) {
                return back()->with('error', 
                    "No se puede reasignar un estudiante en estado '{$preregistro->estado}'"
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

            // Guardar información del grupo anterior para logs
            $grupoAnterior = null;
            if ($preregistro->grupo_asignado_id) {
                $grupoAnterior = Grupo::find($preregistro->grupo_asignado_id);
            }

            // ✅ NUEVA LÓGICA: Manejar contadores correctamente
            // 1. Decrementar contador del grupo anterior (si existe y es diferente al nuevo)
            if ($grupoAnterior && $grupoAnterior->id != $request->grupo_id && $grupoAnterior->estudiantes_inscritos > 0) {
                $grupoAnterior->decrement('estudiantes_inscritos');
            }

            // 2. Asignar nuevo grupo
            $preregistro->update([
                'grupo_asignado_id' => $request->grupo_id,
                'estado' => $preregistro->estado === 'pendiente' ? 'asignado' : $preregistro->estado
            ]);

            // 3. Incrementar contador del nuevo grupo (solo si es diferente al anterior)
            if (!$grupoAnterior || $grupoAnterior->id != $grupo->id) {
                $grupo->increment('estudiantes_inscritos');
            }

            // Determinar mensaje según el contexto
            if ($grupoAnterior) {
                $mensaje = $grupoAnterior->id == $grupo->id 
                    ? 'Estudiante mantenido en el mismo grupo.'
                    : 'Estudiante reasignado al grupo exitosamente.';
            } else {
                $mensaje = 'Estudiante asignado al grupo exitosamente.';
            }

            // Log detallado
            \Log::info("Estudiante asignado/reasignado a grupo", [
                'preregistro_id' => $preregistro->id,
                'estudiante' => $preregistro->usuario->nombre_completo ?? 'No disponible',
                'estado_anterior_preregistro' => $preregistro->getOriginal('estado'),
                'grupo_anterior' => $grupoAnterior ? $grupoAnterior->nombre_completo : 'Ninguno',
                'grupo_nuevo' => $grupo->nombre_completo,
                'nivel' => $preregistro->nivel_solicitado,
                'periodo' => $preregistro->periodo->nombre_periodo ?? 'N/A',
                'accion_por' => auth()->user()->name ?? 'Sistema'
            ]);

            return back()->with('success', $mensaje);

        } catch (\Exception $e) {
            \Log::error("Error al asignar/reasignar grupo", [
                'preregistro_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error al asignar preregistro: ' . $e->getMessage());
        }
    }

    /**
     * Cambia el estado de pago de un preregistro - ACTUALIZADO CON PRÓRROGA
     */
    public function cambiarEstadoPago(Request $request, $id)
    {
        $request->validate([
            'pago_estado' => 'required|in:pendiente,prorroga,pagado,rechazado' 
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
     * Cambia el estado de un preregistro - MODIFICADO: más flexible
     */
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,asignado,cursando,finalizado,cancelado'
        ]);

        try {
            $preregistro = Preregistro::findOrFail($id);
            
            // ✅ MODIFICACIÓN: Validaciones más flexibles
            $estadoActual = $preregistro->estado;
            $nuevoEstado = $request->estado;

            // Solo validar transiciones importantes
            if ($nuevoEstado === 'cancelado' && !$preregistro->puedeSerCancelado()) {
                return back()->with('error', 'No se puede cancelar este preregistro en su estado actual.');
            }

            // Manejar cambios de grupo cuando sea necesario
            if (($estadoActual === 'asignado' || $estadoActual === 'cursando') && 
                ($nuevoEstado !== 'asignado' && $nuevoEstado !== 'cursando' && $nuevoEstado !== 'cancelado')) {
                // Si tenía grupo asignado, liberarlo y actualizar contador
                if ($preregistro->grupo_asignado_id) {
                    $grupo = Grupo::find($preregistro->grupo_asignado_id);
                    if ($grupo && $grupo->estudiantes_inscritos > 0) {
                        $grupo->decrement('estudiantes_inscritos');
                    }
                    $preregistro->grupo_asignado_id = null;
                }
            }

            // Si se cambia de cancelado a otro estado, podemos mantener grupo_asignado
            if ($estadoActual === 'cancelado' && ($nuevoEstado === 'asignado' || $nuevoEstado === 'cursando')) {
                // El grupo_asignado_id se mantiene si existe
                // Podríamos validar que el grupo aún existe y tiene capacidad
            }

            $preregistro->update([
                'estado' => $nuevoEstado,
                'grupo_asignado_id' => $preregistro->grupo_asignado_id
            ]);

            \Log::info("Estado de preregistro cambiado", [
                'preregistro_id' => $preregistro->id,
                'estudiante' => $preregistro->usuario->nombre_completo ?? 'No disponible',
                'estado_anterior' => $estadoActual,
                'estado_nuevo' => $nuevoEstado,
                'tiene_grupo' => $preregistro->grupo_asignado_id ? 'Sí' : 'No',
                'accion_por' => auth()->user()->name ?? 'Sistema'
            ]);

            return back()->with('success', 'Estado actualizado exitosamente.');

        } catch (\Exception $e) {
            \Log::error("Error al cambiar estado de preregistro", [
                'preregistro_id' => $id,
                'error' => $e->getMessage()
            ]);
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

            \Log::info("Preregistro cancelado", [
                'preregistro_id' => $preregistro->id,
                'estudiante' => $preregistro->usuario->nombre_completo ?? 'No disponible',
                'tenia_grupo' => $grupoAsignado ? 'Sí' : 'No',
                'grupo_id' => $grupoAsignado,
                'accion_por' => auth()->user()->name ?? 'Sistema'
            ]);

            return back()->with('success', $mensaje);

        } catch (\Exception $e) {
            \Log::error("Error al cancelar preregistro", [
                'preregistro_id' => $id,
                'error' => $e->getMessage()
            ]);
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
     * Quita el grupo de un preregistro - MODIFICADO: más flexible
     */
    public function quitarGrupo(Request $request, $id)
    {
        try {
            $preregistro = Preregistro::with('grupoAsignado')->findOrFail($id);
            
            // ✅ MODIFICACIÓN: Permitir quitar grupo en más estados
            if (!$preregistro->grupo_asignado_id) {
                return back()->with('error', 'Este preregistro no tiene grupo asignado.');
            }

            // ✅ MODIFICACIÓN: Estados que permiten quitar grupo
            $estadosQuePermitenQuitarGrupo = ['asignado', 'cursando', 'pendiente'];
            if (!in_array($preregistro->estado, $estadosQuePermitenQuitarGrupo)) {
                return back()->with('error', 
                    "No se puede quitar grupo de un preregistro en estado '{$preregistro->estado}'"
                );
            }

            // Guardar información para el log
            $grupoAnterior = $preregistro->grupoAsignado;
            $grupoId = $preregistro->grupo_asignado_id;
            $estadoAnterior = $preregistro->estado;

            // Determinar nuevo estado
            $nuevoEstado = ($estadoAnterior === 'cursando') ? 'pendiente' : 'pendiente';

            // Quitar la relación y cambiar estado
            $preregistro->update([
                'grupo_asignado_id' => null,
                'estado' => $nuevoEstado
            ]);

            // Actualizar contador del grupo (decrementar estudiantes inscritos)
            if ($grupoAnterior && $grupoAnterior->estudiantes_inscritos > 0) {
                $grupoAnterior->decrement('estudiantes_inscritos');
            }

            // Log de la acción
            \Log::info("Grupo quitado del preregistro", [
                'preregistro_id' => $preregistro->id,
                'estudiante' => $preregistro->usuario->nombre_completo ?? 'No disponible',
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $nuevoEstado,
                'grupo_anterior_id' => $grupoId,
                'grupo_anterior_nombre' => $grupoAnterior->nombre_completo ?? 'No disponible',
                'nivel' => $preregistro->nivel_solicitado,
                'periodo' => $preregistro->periodo->nombre_periodo ?? 'N/A',
                'accion_por' => auth()->user()->name ?? 'Sistema'
            ]);

            return back()->with('success', 
                "Grupo quitado correctamente. El preregistro ahora está en estado '{$nuevoEstado}'."
            );

        } catch (\Exception $e) {
            \Log::error("Error al quitar grupo del preregistro", [
                'preregistro_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error al quitar el grupo: ' . $e->getMessage());
        }
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

        // ✅ MODIFICACIÓN: Mostrar grupos disponibles incluso si ya está asignado
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