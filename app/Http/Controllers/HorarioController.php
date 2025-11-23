<?php
// app/Http/Controllers/HorarioController.php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\HorarioPeriodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HorarioController extends Controller
{
    public function index()
    {
        $horarios = Horario::withCount(['horariosPeriodo'])
                          ->orderBy('tipo')
                          ->orderBy('hora_inicio')
                          ->get();

        $estadisticas = [
            'total' => $horarios->count(),
            'activos' => $horarios->where('activo', true)->count(),
            'semanales' => $horarios->where('tipo', 'semanal')->count(),
            'sabatinos' => $horarios->where('tipo', 'sabatino')->count(),
        ];

        return view('coordinador.horarios.index', compact('horarios', 'estadisticas'));
    }

    public function create()
    {
        $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $diasSabado = ['Sábado'];
        
        return view('coordinador.horarios.create', compact('diasSemana', 'diasSabado'));
    }

    public function store(Request $request)
    {

         \Log::info('Datos del formulario:', $request->all());
        \Log::info('Horarios existentes:', Horario::pluck('nombre')->toArray());


        $request->validate([
            'nombre' => 'required|string|max:255|unique:horarios,nombre',
            'tipo' => 'required|in:semanal,sabatino',
            'dias' => 'required|array|min:1',
            'dias.*' => 'string|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        // Validación adicional para días según tipo
        if ($request->tipo === 'sabatino') {
            if (!in_array('Sábado', $request->dias)) {
                return redirect()->back()
                    ->with('error', 'Los horarios sabatinos deben incluir el día Sábado.')
                    ->withInput();
            }
            if (count($request->dias) > 1) {
                return redirect()->back()
                    ->with('error', 'Los horarios sabatinos solo pueden incluir el día Sábado.')
                    ->withInput();
            }
        }

        if ($request->tipo === 'semanal' && in_array('Sábado', $request->dias)) {
            return redirect()->back()
                ->with('error', 'Los horarios semanales no pueden incluir Sábado.')
                ->withInput();
        }

        try {
            $horario = Horario::create([
                'nombre' => $request->nombre,
                'tipo' => $request->tipo,
                'dias' => $request->dias,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'activo' => $request->activo ?? true
            ]);

            Log::info('Horario base creado', [
                'horario_id' => $horario->id,
                'nombre' => $horario->nombre,
                'tipo' => $horario->tipo
            ]);

            return redirect()->route('coordinador.horarios.index')
                ->with('success', 'Horario base creado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error creando horario base: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear el horario base.')
                ->withInput();
        }
    }

    public function show(Horario $horario)
    {
        $periodosAsignados = $horario->periodosActivos()
                                   ->withCount(['grupos'])
                                   ->get();

        return view('coordinador.horarios.show', compact('horario', 'periodosAsignados'));
    }

    public function edit(Horario $horario)
    {
        if ($horario->enUsoEnPeriodosActivos()) {
            return redirect()->route('coordinador.horarios.index')
                ->with('warning', 'No se puede editar un horario que está en uso en periodos activos.');
        }

        $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $diasSabado = ['Sábado'];
        
        return view('coordinador.horarios.edit', compact('horario', 'diasSemana', 'diasSabado'));
    }

    public function update(Request $request, Horario $horario)
    {
        if ($horario->enUsoEnPeriodosActivos()) {
            return redirect()->route('coordinador.horarios.index')
                ->with('warning', 'No se puede editar un horario que está en uso en periodos activos.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255|unique:horarios,nombre,' . $horario->id,
            'tipo' => 'required|in:semanal,sabatino',
            'dias' => 'required|array|min:1',
            'dias.*' => 'string|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        // Validación adicional para días según tipo
        if ($request->tipo === 'sabatino') {
            if (!in_array('Sábado', $request->dias)) {
                return redirect()->back()
                    ->with('error', 'Los horarios sabatinos deben incluir el día Sábado.')
                    ->withInput();
            }
            if (count($request->dias) > 1) {
                return redirect()->back()
                    ->with('error', 'Los horarios sabatinos solo pueden incluir el día Sábado.')
                    ->withInput();
            }
        }

        if ($request->tipo === 'semanal' && in_array('Sábado', $request->dias)) {
            return redirect()->back()
                ->with('error', 'Los horarios semanales no pueden incluir Sábado.')
                ->withInput();
        }

        try {
            $horario->update([
                'nombre' => $request->nombre,
                'tipo' => $request->tipo,
                'dias' => $request->dias,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'activo' => $request->activo ?? $horario->activo
            ]);

            Log::info('Horario base actualizado', [
                'horario_id' => $horario->id,
                'nombre' => $horario->nombre
            ]);

            return redirect()->route('coordinador.horarios.index')
                ->with('success', 'Horario base actualizado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error actualizando horario base: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar el horario base.')
                ->withInput();
        }
    }

    public function destroy(Horario $horario)
    {
        if (!$horario->sePuedeEliminar()) {
            return redirect()->route('coordinador.horarios.index')
                ->with('warning', 'No se puede eliminar el horario porque está siendo usado en periodos.');
        }

        try {
            $nombreHorario = $horario->nombre;
            $horario->delete();

            Log::info('Horario base eliminado', ['horario_nombre' => $nombreHorario]);

            return redirect()->route('coordinador.horarios.index')
                ->with('success', "Horario \"{$nombreHorario}\" eliminado exitosamente.");

        } catch (\Exception $e) {
            Log::error('Error eliminando horario base: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar el horario base.');
        }
    }

    public function toggleActivo(Horario $horario)
    {
        try {
            $nuevoEstado = !$horario->activo;
            $horario->update(['activo' => $nuevoEstado]);

            $estadoTexto = $nuevoEstado ? 'activado' : 'desactivado';

            Log::info('Estado de horario cambiado', [
                'horario_id' => $horario->id,
                'nuevo_estado' => $nuevoEstado
            ]);

            return redirect()->route('coordinador.horarios.index')
                ->with('success', "Horario {$estadoTexto} exitosamente.");

        } catch (\Exception $e) {
            Log::error('Error cambiando estado del horario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cambiar el estado del horario.');
        }
    }
}