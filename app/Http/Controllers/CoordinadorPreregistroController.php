<?php

namespace App\Http\Controllers;

use App\Models\Preregistro;
use App\Models\Periodo;
use App\Models\Grupo;
use App\Models\Horario;
use Illuminate\Http\Request;

class CoordinadorPreregistroController extends Controller
{
    /**
     * Muestra el análisis de demanda (PÁGINA PRINCIPAL)
     */
    public function demanda()
    {
        // Obtener preregistros pendientes
        $preregistrosPendientes = Preregistro::where('estado', 'preregistrado')->count();
        $totalPreregistros = Preregistro::count();
        
        // Análisis por nivel
        $demandaPorNivel = Preregistro::where('estado', 'preregistrado')
            ->groupBy('nivel_solicitado')
            ->selectRaw('nivel_solicitado, count(*) as total')
            ->pluck('total', 'nivel_solicitado')
            ->toArray();
        
        // Análisis por horario
        $demandaPorHorario = Preregistro::where('estado', 'preregistrado')
            ->with('horarioSolicitado')
            ->get()
            ->groupBy('horario_solicitado_id')
            ->map(function ($group) {
                $horario = $group->first()->horarioSolicitado;
                
                // Formatear horas
                $horaInicio = $horario->hora_inicio instanceof \DateTime 
                    ? $horario->hora_inicio->format('H:i') 
                    : (\Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') ?? 'N/A');
                    
                $horaFin = $horario->hora_fin instanceof \DateTime 
                    ? $horario->hora_fin->format('H:i') 
                    : (\Carbon\Carbon::parse($horario->hora_fin)->format('H:i') ?? 'N/A');

                return [
                    'nombre' => $horario->nombre ?? 'No disponible',
                    'tipo' => $horario->tipo ?? 'Sin tipo',
                    'dias' => $horario->dias, // Esto viene como JSON
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
            $horariosPopulares = Preregistro::where('estado', 'preregistrado')
                ->where('nivel_solicitado', $nivel)
                ->with('horarioSolicitado')
                ->get()
                ->groupBy('horario_solicitado_id')
                ->map(function ($group) {
                    $horario = $group->first()->horarioSolicitado;
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
                'estudiantes' => $cantidad,
                'grupos_sugeridos' => $gruposNecesarios,
                'horarios_populares' => $horariosPopulares
            ];
        }
        
        $horariosDisponibles = Horario::where('activo', true)->get();
        $periodoActivo = Periodo::where('activo', true)->first();
        
        // Variables adicionales para compact
        $nivelesUnicos = count($demandaPorNivel);
        $horariosUnicos = count($demandaPorHorario);
        
        return view('coordinador.preregistros.demanda', compact(
            'totalPreregistros',
            'preregistrosPendientes',
            'demandaPorNivel',
            'demandaPorHorario',
            'gruposSugeridos',
            'nivelesUnicos',
            'horariosUnicos',
            'horariosDisponibles',
            'periodoActivo'
        ));
    }

    /**
     * Muestra lista detallada de preregistros (PARA GESTIÓN INDIVIDUAL)
     */
    public function index()
    {
        $preregistros = Preregistro::with([
            'usuario', 
            'periodo', 
            'horarioSolicitado', 
            'grupoAsignado'
        ])->latest()->get();

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
            'horarioSolicitado', 
            'grupoAsignado'
        ])->where('estado', $estado)->latest()->get();

        return view('coordinador.preregistros.index', compact('preregistros'));
    }

    /**
     * Asigna un preregistro a un grupo
     */
    public function asignarGrupo(Request $request, $id)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id'
        ]);

        try {
            $preregistro = Preregistro::findOrFail($id);
            
            if ($preregistro->estado !== 'preregistrado') {
                return back()->with('error', 'Solo se pueden asignar preregistros en estado "Preregistrado"');
            }

            $preregistro->update([
                'grupo_asignado_id' => $request->grupo_id,
                'estado' => 'asignado'
            ]);

            return back()->with('success', 'Preregistro asignado exitosamente al grupo.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al asignar preregistro: ' . $e->getMessage());
        }
    }

    /**
     * Cambia el estado de un preregistro
     */
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:preregistrado,asignado,cursando,finalizado,cancelado'
        ]);

        try {
            $preregistro = Preregistro::findOrFail($id);
            $preregistro->update(['estado' => $request->estado]);

            return back()->with('success', 'Estado actualizado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }
    }


    /**
     * Obtiene los estudiantes por nivel (para AJAX)
     */
    public function obtenerEstudiantesPorNivel($nivel)
    {
        $estudiantes = Preregistro::with(['usuario', 'horarioSolicitado'])
            ->where('estado', 'preregistrado')
            ->where('nivel_solicitado', $nivel)
            ->get()
            ->map(function ($preregistro) {
                $horario = $preregistro->horarioSolicitado;
                
                // Procesar días del horario
                $diasArray = is_string($horario->dias ?? '') ? json_decode($horario->dias, true) : ($horario->dias ?? []);
                $diasArray = $diasArray ?? [];
                $diasTexto = !empty($diasArray) ? implode(', ', $diasArray) : 'No especificado';

                return [
                    'nombre' => $preregistro->usuario->nombre_completo,
                    'numero_control' => $preregistro->usuario->numero_control,
                    'correo' => $preregistro->usuario->correo_institucional,
                    'especialidad' => $preregistro->usuario->especialidad,
                    'horario_solicitado' => $horario->nombre ?? 'No especificado',
                    'tipo_horario' => $horario->tipo ?? 'N/A',
                    'dias_horario' => $diasTexto,
                    'semestre_carrera' => $preregistro->semestre_carrera
                ];
            });

        return response()->json([
            'estudiantes' => $estudiantes,
            'total' => $estudiantes->count()
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
            'horarioSolicitado', 
            'grupoAsignado',
            'grupoAsignado.profesor',
            'grupoAsignado.aula'
        ])->findOrFail($id);

        $gruposDisponibles = Grupo::with(['horario', 'aula', 'profesor'])
            ->where('nivel_ingles', $preregistro->nivel_solicitado)
            ->get();

        return view('coordinador.preregistros.show', compact(
            'preregistro',
            'gruposDisponibles'
        ));
    }

     /**
     * Crear grupo rápido desde análisis de demanda
     */
    public function crearGrupoRapido(Request $request)
    {
        $request->validate([
            'nivel_ingles' => 'required|integer|between:1,5',
            'letra_grupo' => 'required|string|size:1',
            'horario_id' => 'required|exists:horarios,id',
            'periodo_id' => 'required|exists:periodos,id',
            'capacidad_maxima' => 'required|integer|min:20|max:50'
        ]);

        try {
            // Usar periodo activo si no se especifica
            $periodoId = $request->periodo_id;
            if (!$periodoId) {
                $periodoActivo = Periodo::where('activo', true)->first();
                if (!$periodoActivo) {
                    return back()->with('error', 'No hay un periodo activo.');
                }
                $periodoId = $periodoActivo->id;
            }

            // Verificar que no exista grupo duplicado
            $grupoExistente = Grupo::where('nivel_ingles', $request->nivel_ingles)
                ->where('letra_grupo', $request->letra_grupo)
                ->where('periodo_id', $periodoId)
                ->first();

            if ($grupoExistente) {
                return back()->with('error', 'Ya existe un grupo ' . $request->nivel_ingles . '-' . $request->letra_grupo . ' en este periodo.');
            }

            // Crear el grupo
            $grupo = Grupo::create([
                'nivel_ingles' => $request->nivel_ingles,
                'letra_grupo' => $request->letra_grupo,
                'periodo_id' => $periodoId,
                'horario_id' => $request->horario_id,
                'capacidad_maxima' => $request->capacidad_maxima ?? 25,
                'estado' => 'planificado',
                'estudiantes_inscritos' => 0
            ]);

            // Opcional: Asignar automáticamente preregistros compatibles
            $asignados = $this->asignarPreregistrosAlGrupo($grupo);

            $mensaje = 'Grupo ' . $grupo->nombre_completo . ' creado exitosamente en estado "Planificado".';
            
            if ($asignados > 0) {
                $mensaje .= " Se asignaron {$asignados} estudiantes automáticamente.";
            }

            return redirect()->route('coordinador.grupos.index')
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear grupo: ' . $e->getMessage());
        }
    }

    /**
     * Asignar preregistros compatibles al grupo
     */
    private function asignarPreregistrosAlGrupo(Grupo $grupo): int
    {
        $preregistrosCompatibles = Preregistro::where('estado', 'preregistrado')
            ->where('nivel_solicitado', $grupo->nivel_ingles)
            ->where('horario_solicitado_id', $grupo->horario_id)
            ->where('periodo_id', $grupo->periodo_id)
            ->limit($grupo->capacidad_maxima)
            ->get();

        $asignados = 0;

        foreach ($preregistrosCompatibles as $preregistro) {
            if ($grupo->tieneCapacidad()) {
                $preregistro->update([
                    'grupo_asignado_id' => $grupo->id,
                    'estado' => 'asignado'
                ]);
                
                $grupo->increment('estudiantes_inscritos');
                $asignados++;
            }
        }

        return $asignados;
    }


}