<?php
// app/Http\Controllers/PeriodoController.php

namespace App\Http\Controllers;

use App\Models\Periodo;
use App\Models\Horario;
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
            }
        ])->orderBy('fecha_inicio', 'desc');

        if (request('estado')) {
            $query->where('estado', request('estado'));
        }

        $periodos = $query->paginate(10);

        return view('coordinador.periodos.index', compact('periodos'));
    }

    public function create()
    {
        return view('coordinador.periodos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_periodo' => 'required|string|max:50|unique:periodos,nombre_periodo',
            'fecha_inicio' => 'required|date|after:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $periodo = Periodo::create([
                    'nombre_periodo' => $request->nombre_periodo,
                    'fecha_inicio' => $request->fecha_inicio,
                    'fecha_fin' => $request->fecha_fin,
                    'estado' => 'configuracion'
                ]);

                $this->asignarHorariosBase($periodo);
            });

            return redirect()->route('coordinador.periodos.index')
                ->with('success', 'Período creado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error creando período: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear el período.')->withInput();
        }
    }

    public function show(Periodo $periodo)
    {
        $estadisticas = $this->obtenerEstadisticasDetalladas($periodo->id);
        return view('coordinador.periodos.show', compact('periodo', 'estadisticas'));
    }

    public function edit(Periodo $periodo)
    {
        if (!$periodo->estaEnConfiguracion()) {
            return redirect()->route('coordinador.periodos.index')
                ->with('warning', 'Solo se pueden editar periodos en estado "Configuración"');
        }

        return view('coordinador.periodos.edit', compact('periodo'));
    }

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

            return redirect()->route('coordinador.periodos.index')
                ->with('success', 'Fechas del periodo actualizadas exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error actualizando periodo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar el periodo.');
        }
    }

    public function destroy(Periodo $periodo)
    {
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

    /**
     * 🎯 MÉTODOS DE CAMBIO DE ESTADO
     */
    public function activarPreregistros(Periodo $periodo)
    {
        if ($periodo->estado !== 'configuracion') {
            return redirect()->back()->with('warning', 'Solo se pueden activar pre-registros desde configuración');
        }

        try {
            $periodo->update(['estado' => 'preregistros_activos']);
            
            Log::info('Pre-registros activados', ['periodo_id' => $periodo->id]);
            
            return redirect()->route('coordinador.periodos.index')
                ->with('success', 'Pre-registros activados. Los estudiantes ya pueden registrarse.');

        } catch (\Exception $e) {
            Log::error('Error activando pre-registros: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al activar pre-registros.');
        }
    }

    public function cerrarPreregistros(Periodo $periodo)
    {
        if ($periodo->estado !== 'preregistros_activos') {
            return redirect()->back()->with('warning', 'Los pre-registros no están activos');
        }

        try {
            $periodo->update(['estado' => 'configuracion']);
            
            Log::info('Pre-registros cerrados manualmente', ['periodo_id' => $periodo->id]);
            
            return redirect()->route('coordinador.periodos.index')
                ->with('success', 'Pre-registros cerrados. Los estudiantes ya no pueden registrarse.');

        } catch (\Exception $e) {
            Log::error('Error cerrando pre-registros: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cerrar pre-registros.');
        }
    }

    public function iniciarPeriodo(Periodo $periodo)
    {
        if ($periodo->estado !== 'preregistros_activos') {
            return redirect()->back()->with('warning', 'Solo se puede iniciar periodo desde pre-registros activos');
        }

        try {
            $periodo->update(['estado' => 'en_curso']);
            return redirect()->route('coordinador.periodos.index')
                ->with('success', 'Periodo iniciado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al iniciar periodo.');
        }
    }

    public function finalizarPeriodo(Periodo $periodo)
    {
        if ($periodo->estado !== 'en_curso') {
            return redirect()->back()->with('warning', 'Solo se puede finalizar periodo desde estado en curso');
        }

        try {
            $periodo->update(['estado' => 'finalizado']);
            return redirect()->route('coordinador.periodos.index')
                ->with('success', 'Periodo finalizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al finalizar periodo.');
        }
    }

    public function cambiarEstado(Request $request, Periodo $periodo)
    {
        $request->validate([
            'nuevo_estado' => 'required|in:configuracion,preregistros_activos,en_curso,finalizado'
        ]);

        $nuevoEstado = $request->nuevo_estado;
        $estadoActual = $periodo->estado;

        try {
            DB::transaction(function () use ($periodo, $nuevoEstado) {
                $periodo->update(['estado' => $nuevoEstado]);

                Log::info("Estado cambiado manualmente", [
                    'periodo_id' => $periodo->id,
                    'nuevo_estado' => $nuevoEstado
                ]);
            });

            return redirect()->route('coordinador.periodos.index')
                ->with('success', "Estado actualizado: " . $this->obtenerNombreEstado($nuevoEstado));

        } catch (\Exception $e) {
            Log::error('Error cambiando estado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cambiar estado.');
        }
    }

    /**
     * 📊 MÉTODOS PRIVADOS DE APOYO
     */
    private function asignarHorariosBase(Periodo $periodo)
    {
        $horariosBase = Horario::where('activo', true)->get();
        
        if ($horariosBase->isEmpty()) {
            Log::warning('No hay horarios base activos para asignar al período');
            return;
        }

        $horariosData = [];
        foreach ($horariosBase as $horario) {
            $horariosData[] = [
                'periodo_id' => $periodo->id,
                'horario_base_id' => $horario->id,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('horarios_periodo')->insert($horariosData);
        Log::info('Horarios base asignados al período', [
            'periodo_id' => $periodo->id,
            'horarios_count' => count($horariosData)
        ]);
    }

    private function obtenerEstadisticasDetalladas($periodoId)
    {
        return [
            'total_grupos' => DB::table('grupos')->where('periodo_id', $periodoId)->count(),
            'grupos_activos' => DB::table('grupos')->where('periodo_id', $periodoId)->where('estado', 'activo')->count(),
            'total_preregistros' => DB::table('preregistros')->where('periodo_id', $periodoId)->count(),
            'preregistros_pagados' => DB::table('preregistros')->where('periodo_id', $periodoId)->where('pago_estado', 'pagado')->count(),
            'estudiantes_activos' => DB::table('preregistros')->where('periodo_id', $periodoId)->where('estado', 'cursando')->count(),
        ];
    }

    private function obtenerNombreEstado($estado)
    {
        $nombres = [
            'configuracion' => 'Configuración',
            'preregistros_activos' => 'Pre-registros Activos', 
            'en_curso' => 'En Curso',
            'finalizado' => 'Finalizado'
        ];
        return $nombres[$estado] ?? $estado;
    }
}