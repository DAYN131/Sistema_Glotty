<?php
// app/Http\Controllers/PeriodoController.php

namespace App\Http\Controllers;

use App\Models\Periodo;
use App\Models\Horario;
use App\Models\HorarioPeriodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PeriodoController extends Controller
{
    public function index()
    {
        $query = Periodo::withCount([
            'grupos',
            'preregistros',
            'preregistros as preregistros_pagados_count' => function($query) {
                $query->where('pago_estado', 'pagado');
            },
            'horariosPeriodo as horarios_activos_count' => function($query) {
                $query->where('activo', true);
            }
        ])->orderBy('fecha_inicio', 'desc');

        if (request('estado')) {
            $query->where('estado', request('estado'));
        }

        $periodos = $query->paginate(10);

        return view('coordinador.periodos.index', compact('periodos'));
    }

    // Vista de Create
    public function create()
    {
        return view('coordinador.periodos.create');
    }
    
    // Funcion de Guardar
    public function store(Request $request)
    {
        $request->validate([
            'nombre_periodo' => 'required|string|max:50|unique:periodos,nombre_periodo',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        try {
            DB::beginTransaction();

            $periodo = Periodo::create([
                'nombre_periodo' => $request->nombre_periodo,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'estado' => 'configuracion'
            ]);


            DB::commit();

            return redirect()->route('coordinador.periodos.show', $periodo)
                ->with('success', 'Período creado exitosamente. Ahora puedes agregar horarios desde plantillas.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creando período: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error_trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error al crear el período: ' . $e->getMessage())
                ->withInput();
        }
    }
    // Esta es clave para poder poner horarios
    public function show(Periodo $periodo)
    {
        $horariosPeriodo = $periodo->horariosPeriodo()
                                ->with('horarioBase')
                                ->orderBy('tipo')
                                ->orderBy('hora_inicio')
                                ->get();
        
        $estadisticas = $this->obtenerEstadisticasDetalladas($periodo->id);
        
        // Horarios base disponibles para agregar
        $horariosBaseDisponibles = Horario::where('activo', true)
            ->whereNotIn('id', function($query) use ($periodo) {
                $query->select('horario_base_id')
                      ->from('horarios_periodo')
                      ->where('periodo_id', $periodo->id);
            })
            ->get();

        return view('coordinador.periodos.show', compact(
            'periodo', 
            'horariosPeriodo', 
            'estadisticas',
            'horariosBaseDisponibles'
        ));
    }

    // Funcion para mandarlo a la vista de Editar
    public function edit(Periodo $periodo)
    {
        if (!$periodo->estaEnConfiguracion()) {
            return redirect()->route('coordinador.periodos.index')
                ->with('warning', 'Solo se pueden editar periodos en estado "Configuración"');
        }

        return view('coordinador.periodos.edit', compact('periodo'));
    }

    // Actualizar 
    public function update(Request $request, Periodo $periodo)
    {
        if (!$periodo->estaEnConfiguracion()) {
            return redirect()->route('coordinador.periodos.index')
                ->with('warning', 'Solo se pueden editar periodos en estado "Configuración"');
        }

        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        try {
            $periodo->update([
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
            ]);

            Log::info('Periodo actualizado (solo fechas)', [
                'periodo_id' => $periodo->id,
                'fecha_inicio' => $periodo->fecha_inicio,
                'fecha_fin' => $periodo->fecha_fin
            ]);

            return redirect()->route('coordinador.periodos.show', $periodo)
                ->with('success', 'Fechas del periodo actualizadas exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error actualizando periodo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar el periodo.');
        }
    }

    // Eliminar Periodo
    public function destroy(Periodo $periodo)
    {
        // Solo si esta en configuracion puede eliminarse
        if (!$periodo->estaEnConfiguracion()) {
            return redirect()->route('coordinador.periodos.index')
                ->with('warning', 'Solo se pueden eliminar periodos en estado "Configuración"');
        }

        if (!$periodo->puedeEliminarse()) {
            return redirect()->route('coordinador.periodos.index')
                ->with('warning', 'No se puede eliminar el periodo porque tiene grupos o pre-registros asociados.');
        }

        try {
            $nombrePeriodo = $periodo->nombre_periodo;
            $periodo->delete();

            Log::info('Periodo eliminado', ['periodo_nombre' => $nombrePeriodo]);

            return redirect()->route('coordinador.periodos.index')
                ->with('success', "Periodo \"{$nombrePeriodo}\" eliminado exitosamente.");

        } catch (\Exception $e) {
            Log::error('Error eliminando periodo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar el periodo.');
        }
    }

    public function agregarHorarios(Request $request, Periodo $periodo)
    {
        if ($periodo->estaFinalizado() || $periodo->estaCancelado()) {
            return redirect()->back()
                ->with('error', 'No se pueden agregar horarios en periodos finalizados o cancelados');
        }

        $request->validate([
            'horarios_base_ids' => 'required|array|min:1',
            'horarios_base_ids.*' => 'exists:horarios,id'
        ]);

        try {
            DB::transaction(function () use ($periodo, $request) {
                foreach ($request->horarios_base_ids as $horarioBaseId) {
                    // Evitar duplicados
                    if (!$periodo->horariosPeriodo()->where('horario_base_id', $horarioBaseId)->exists()) {
                        $horarioBase = Horario::find($horarioBaseId);
                        
                        HorarioPeriodo::create([
                            'periodo_id' => $periodo->id,
                            'horario_base_id' => $horarioBaseId,
                            'nombre' => $this->generarNombreHorarioPeriodo($horarioBase, $periodo),
                            'tipo' => $horarioBase->tipo,
                            'dias' => $horarioBase->dias,
                            'hora_inicio' => $horarioBase->hora_inicio,
                            'hora_fin' => $horarioBase->hora_fin,
                            'activo' => true
                        ]);
                    }
                }
            });

            Log::info('Horarios agregados a periodo', [
                'periodo_id' => $periodo->id,
                'estado_actual' => $periodo->estado,
                'horarios_count' => count($request->horarios_base_ids)
            ]);

            return redirect()->route('coordinador.periodos.show', $periodo)
                ->with('success', 'Horarios agregados exitosamente al periodo.');

        } catch (\Exception $e) {
            Log::error('Error agregando horarios: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al agregar horarios: ' . $e->getMessage());
        }
    }

    public function eliminarHorarioPeriodo(Periodo $periodo, HorarioPeriodo $horarioPeriodo)
    {
        if ($periodo->estaFinalizado() || $periodo->estaCancelado()) {
            return redirect()->back()
                ->with('error', 'No se pueden eliminar horarios en periodos finalizados o cancelados');
        }

        // Verificar que el horario pertenece al periodo
        if ($horarioPeriodo->periodo_id !== $periodo->id) {
            return redirect()->back()
                ->with('error', 'El horario no pertenece a este periodo.');
        }

        // No eliminar si hay grupos usando este horario
        if ($horarioPeriodo->grupos()->exists()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el horario porque tiene grupos asignados.');
        }

        try {
            $nombreHorario = $horarioPeriodo->nombre;
            $horarioPeriodo->delete();

            Log::info('Horario periodo eliminado', [
                'periodo_id' => $periodo->id,
                'estado_actual' => $periodo->estado,
                'horario_periodo_id' => $horarioPeriodo->id,
                'nombre' => $nombreHorario
            ]);

            return redirect()->route('coordinador.periodos.show', $periodo)
                ->with('success', "Horario \"{$nombreHorario}\" eliminado del periodo.");

        } catch (\Exception $e) {
            Log::error('Error eliminando horario periodo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar el horario.');
        }
    }

    public function toggleHorarioPeriodo(Periodo $periodo, HorarioPeriodo $horarioPeriodo)
    {
        // Permitir en cualquier estado EXCEPTO finalizado/cancelado
        if ($periodo->estaFinalizado() || $periodo->estaCancelado()) {
            return redirect()->back()
                ->with('error', 'No se pueden modificar horarios en periodos finalizados o cancelados');
        }

        // Verificar que el horario pertenece al periodo
        if ($horarioPeriodo->periodo_id !== $periodo->id) {
            return redirect()->back()
                ->with('error', 'El horario no pertenece a este periodo.');
        }

        try {
            $nuevoEstado = !$horarioPeriodo->activo;
            $horarioPeriodo->update(['activo' => $nuevoEstado]);

            $estadoTexto = $nuevoEstado ? 'activado' : 'desactivado';
            
            Log::info('Horario periodo toggled', [
                'periodo_id' => $periodo->id,
                'estado_actual' => $periodo->estado,
                'horario_periodo_id' => $horarioPeriodo->id,
                'nuevo_estado' => $estadoTexto
            ]);

            return redirect()->route('coordinador.periodos.show', $periodo)
                ->with('success', "Horario {$estadoTexto} correctamente.");

        } catch (\Exception $e) {
            Log::error('Error cambiando estado horario periodo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cambiar estado del horario.');
        }
    }

    public function activarPreregistros(Periodo $periodo)
    {
        // Validar transición de estado
        if (!$periodo->puedeCambiarA('preregistros_activos')) {
            return redirect()->back()->with('error', 
                "No se puede activar preregistros desde '{$periodo->estado_legible}'"
            );
        }

        //  Verificar horarios activos en el período
        if ($periodo->horariosPeriodo()->where('activo', true)->count() === 0) {
            return redirect()->back()
                ->with('error', 'No se pueden activar preregistros: Debe tener al menos un horario activo en el período.');
        }

        //   Verificar que existan aulas (con campo 'disponible')
        if (!\App\Models\Aula::where('disponible', true)->exists()) {
            return redirect()->back()
                ->with('error', 'No se pueden activar preregistros: Debe crear aulas disponibles en el sistema.');
        }

        // Verificar que existan profesores (aunque sea 1)
        if (!\App\Models\Profesor::exists()) {
            return redirect()->back()
                ->with('error', 'No se pueden activar preregistros: Debe tener profesores registrados en el sistema.');
        }

        try {
            $periodo->update(['estado' => 'preregistros_activos']);
            
            Log::info('Pre-registros activados', [
                'periodo_id' => $periodo->id,
                'estado_anterior' => $periodo->getOriginal('estado'),
                'horarios_activos' => $periodo->horariosPeriodo()->where('activo', true)->count(),
                'aulas_disponibles' => \App\Models\Aula::where('disponible', true)->count(),
                'total_profesores' => \App\Models\Profesor::count()
            ]);
            
            return redirect()->route('coordinador.periodos.index')
                ->with('success', 'Pre-registros activados. Los estudiantes ya pueden registrarse.');

        } catch (\Exception $e) {
            Log::error('Error activando pre-registros: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al activar pre-registros.');
        }
    }

    public function cerrarPreregistros(Periodo $periodo)
    {
        // 1. Validar transición de estado
        if (!$periodo->puedeCambiarA('preregistros_cerrados')) {
            return redirect()->back()->with('error', 
                "No se puede cerrar preregistros desde '{$periodo->estado_legible}'"
            );
        }

        //  2. OPCIONAL: Verificar que haya preregistros (para evitar períodos vacíos)
        if ($periodo->preregistros()->count() === 0) {
            return redirect()->back()
                ->with('warning', '¿Está seguro? El período no tiene preregistros. ¿Desea continuar?')
                ->with('confirmar_cierre', true); // Para mostrar confirmación en la vista
        }

        try {
            $periodo->update(['estado' => 'preregistros_cerrados']);
            
            Log::info('Pre-registros cerrados', [
                'periodo_id' => $periodo->id,
                'estado_anterior' => $periodo->getOriginal('estado'),
                'total_preregistros' => $periodo->preregistros()->count()
            ]);
            
            return redirect()->route('coordinador.periodos.index')
                ->with('success', 'Pre-registros cerrados. Se procederá con la asignación de grupos.');

        } catch (\Exception $e) {
            Log::error('Error cerrando pre-registros: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cerrar pre-registros.');
        }
    }

    public function iniciarPeriodo(Periodo $periodo)
    {
        if (!$periodo->puedeCambiarA('en_curso')) {
            return redirect()->back()->with('error', 
                "No se puede iniciar periodo desde '{$periodo->estado_legible}'"
            );
        }

        try {
            DB::transaction(function () use ($periodo) {
                $estadoAnterior = $periodo->estado;
                $periodo->update(['estado' => 'en_curso']);
                
                // AUTOMATIZACIÓN: Cambiar preregistros "asignados" a "cursando"
                $preregistrosActualizados = $periodo->preregistros()
                    ->where('estado', 'asignado')
                    ->whereNotNull('grupo_asignado_id')
                    ->update(['estado' => 'cursando']);
                

                Log::info('Periodo iniciado con automatización', [
                    'periodo_id' => $periodo->id,
                    'estado_anterior' => $estadoAnterior,
                    'preregistros_actualizados' => $preregistrosActualizados
                ]);
            });

            return redirect()->route('coordinador.periodos.index')
                ->with('success', 'Periodo iniciado exitosamente. Los estudiantes asignados ahora están en curso.');

        } catch (\Exception $e) {
            Log::error('Error iniciando periodo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al iniciar periodo.');
        }
    }


   public function finalizarPeriodo(Periodo $periodo)
    {
        if (!$periodo->puedeCambiarA('finalizado')) {
            return redirect()->back()->with('error', 
                "No se puede finalizar periodo desde '{$periodo->estado_legible}'"
            );
        }

        try {
            DB::transaction(function () use ($periodo) {
                $estadoAnterior = $periodo->estado;
                $periodo->update(['estado' => 'finalizado']);
                
                // Si se finaliza desde "En Curso", finalizar grupos y estudiantes
                if ($estadoAnterior === 'en_curso') {
                    $periodo->grupos()->where('estado', 'activo')->update(['estado' => 'finalizado']);
                    $periodo->preregistros()->where('estado', 'cursando')->update(['estado' => 'finalizado']);
                }
            });

            Log::info('Periodo finalizado', [
                'periodo_id' => $periodo->id,
                'estado_anterior' => $periodo->getOriginal('estado')
            ]);
            
            return redirect()->route('coordinador.periodos.index')
                ->with('success', 'Periodo finalizado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error finalizando periodo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al finalizar periodo.');
        }
    }

    public function cancelarPeriodo(Periodo $periodo)
    {
        //  USAR EL MÉTODO DEL MODELO
        if (!$periodo->puedeCambiarA('cancelado')) {
            return redirect()->back()->with('error', 
                "No se puede cancelar periodo desde '{$periodo->estado_legible}'"
            );
        }

        try {
            DB::transaction(function () use ($periodo) {
                $estadoAnterior = $periodo->estado;
                $periodo->update(['estado' => 'cancelado']);
                
                // Solo revertir si había grupos o estudiantes activos
                if (in_array($estadoAnterior, ['en_curso', 'preregistros_abiertos', 'preregistros_cerrados'])) {
                    $periodo->grupos()->update(['estado' => 'cancelado']);
                    $periodo->preregistros()
                        ->whereIn('estado', ['asignado', 'cursando'])
                        ->update([
                            'estado' => 'pendiente',
                            'grupo_asignado_id' => null
                        ]);
                }
            });

            Log::info('Periodo cancelado', [
                'periodo_id' => $periodo->id,
                'estado_anterior' => $periodo->getOriginal('estado')
            ]);
            
            return redirect()->route('coordinador.periodos.index')
                ->with('success', 'Periodo cancelado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error cancelando periodo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cancelar periodo.');
        }
    }

    public function cambiarEstado(Request $request, Periodo $periodo)
    {
        $request->validate([
            'nuevo_estado' => 'required|in:configuracion,preregistros_activos,preregistros_cerrados,en_curso,finalizado,cancelado'
        ]);

        $nuevoEstado = $request->nuevo_estado;

        if (!$periodo->puedeCambiarA($nuevoEstado)) {
            return redirect()->back()->with('error', 
                "No se puede cambiar de '{$periodo->estado_legible}' a '" . 
                ($periodo::ESTADOS[$nuevoEstado] ?? $nuevoEstado) . "'"
            );
        }

        // VALIDACIONES ESPECÍFICAS PARA ACTIVAR PRE-REGISTROS
        if ($nuevoEstado === 'preregistros_activos') {
            // Verificar horarios activos
            if ($periodo->horariosPeriodo()->where('activo', true)->count() === 0) {
                return redirect()->back()
                    ->with('error', 'No se pueden activar preregistros: Debe tener al menos un horario activo en el período.');
            }

            // Verificar aulas disponibles
            if (!\App\Models\Aula::where('disponible', true)->exists()) {
                return redirect()->back()
                    ->with('error', 'No se pueden activar preregistros: Debe crear aulas disponibles en el sistema.');
            }

            // Verificar profesores
            if (!\App\Models\Profesor::exists()) {
                return redirect()->back()
                    ->with('error', 'No se pueden activar preregistros: Debe tener profesores registrados en el sistema.');
            }
        }

        try {
            DB::transaction(function () use ($periodo, $nuevoEstado) {
                $estadoActual = $periodo->estado;
                
                // Lógica específica para transiciones importantes
                switch ($nuevoEstado) {
                    case 'en_curso':
                        $periodo->preregistros()
                            ->where('estado', 'asignado')
                            ->whereNotNull('grupo_asignado_id')
                            ->update(['estado' => 'cursando']);
                        break; 
                        
                    case 'finalizado':
                        if ($estadoActual === 'en_curso') {
                            $periodo->grupos()->where('estado', 'activo')->update(['estado' => 'cancelado']);
                            $periodo->preregistros()->where('estado', 'cursando')->update(['estado' => 'finalizado']);
                        }
                        break;
                        
                    case 'cancelado':
                        $periodo->grupos()->update(['estado' => 'cancelado']);
                        $periodo->preregistros()
                            ->whereIn('estado', ['asignado', 'cursando'])
                            ->update([
                                'estado' => 'pendiente',
                                'grupo_asignado_id' => null
                            ]);
                        break;
                }

                $periodo->update(['estado' => $nuevoEstado]);
            });

            Log::info("Estado cambiado", [
                'periodo_id' => $periodo->id,
                'estado_anterior' => $periodo->getOriginal('estado'),
                'nuevo_estado' => $nuevoEstado
            ]);

            return redirect()->route('coordinador.periodos.index')
                ->with('success', "Estado actualizado: " . $periodo->estado_legible);

        } catch (\Exception $e) {
            Log::error('Error cambiando estado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }

    /**
     * MÉTODOS PRIVADOS
     */
    
    private function generarNombreHorarioPeriodo(Horario $horario, Periodo $periodo)
    {
        $base = str_replace('Plantilla', '', $horario->nombre);
        $base = trim($base);
        
        return "{$base} - {$periodo->nombre_periodo}";
    }

    private function obtenerEstadisticasDetalladas($periodoId)
    {
        return [
            'total_grupos' => DB::table('grupos')->where('periodo_id', $periodoId)->count(),
            'grupos_activos' => DB::table('grupos')->where('periodo_id', $periodoId)->where('estado', 'activo')->count(),
            'total_preregistros' => DB::table('preregistros')->where('periodo_id', $periodoId)->count(),
            'preregistros_pagados' => DB::table('preregistros')->where('periodo_id', $periodoId)->where('pago_estado', 'pagado')->count(),
            'preregistros_con_prorroga' => DB::table('preregistros')->where('periodo_id', $periodoId)->where('pago_estado', 'prorroga')->count(),
            'estudiantes_activos' => DB::table('preregistros')->where('periodo_id', $periodoId)->where('estado', 'cursando')->count(),
            'horarios_activos' => DB::table('horarios_periodo')->where('periodo_id', $periodoId)->where('activo', true)->count(),
        ];
    }
}