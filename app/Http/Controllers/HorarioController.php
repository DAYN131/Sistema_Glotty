<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $horarios = Horario::orderBy('activo', 'desc')
                          ->orderBy('tipo')
                          ->orderBy('hora_inicio')
                          ->paginate(10);

        return view('coordinador.horarios.index', compact('horarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $diasSabatinos = ['Sábado'];
        
        return view('coordinador.horarios.create', compact('diasSemana', 'diasSabatinos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|in:semanal,sabatino',
            'dias' => 'required|array|min:1',
            'dias.*' => 'string|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Validar que los días correspondan al tipo
            $diasValidos = $this->validarDiasPorTipo($request->tipo, $request->dias);
            if (!$diasValidos) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Los días seleccionados no son válidos para el tipo de horario elegido.');
            }

            // Validar que no exista un horario con el mismo nombre
            $horarioExistente = Horario::where('nombre', $request->nombre)->first();
            if ($horarioExistente) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['nombre' => 'Ya existe un horario con este nombre.']);
            }

            Horario::create([
                'nombre' => $request->nombre,
                'tipo' => $request->tipo,
                'dias' => json_encode($request->dias),
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'activo' => $request->activo ?? true,
            ]);

            return redirect()->route('coordinador.horarios.index')
                ->with('success', 'Horario creado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el horario: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $horario = Horario::findOrFail($id);
        
        // Usar el accesor dias_array en lugar de procesar manualmente
        $horario->dias_array = $horario->dias_array;
        
        $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $diasSabatinos = ['Sábado'];
        
        return view('coordinador.horarios.edit', compact('horario', 'diasSemana', 'diasSabatinos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|in:semanal,sabatino',
            'dias' => 'required|array|min:1',
            'dias.*' => 'string|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $horario = Horario::findOrFail($id);

            // Validar que los días correspondan al tipo
            $diasValidos = $this->validarDiasPorTipo($request->tipo, $request->dias);
            if (!$diasValidos) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Los días seleccionados no son válidos para el tipo de horario elegido.');
            }

            // Validar que no exista otro horario con el mismo nombre
            $horarioExistente = Horario::where('nombre', $request->nombre)
                                     ->where('id', '!=', $id)
                                     ->first();
            if ($horarioExistente) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['nombre' => 'Ya existe otro horario con este nombre.']);
            }

            $horario->update([
                'nombre' => $request->nombre,
                'tipo' => $request->tipo,
                'dias' => json_encode($request->dias),
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'activo' => $request->activo ?? $horario->activo,
            ]);

            return redirect()->route('coordinador.horarios.index')
                ->with('success', 'Horario actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el horario: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $horario = Horario::findOrFail($id);

            // Verificar si el horario está siendo usado en grupos
            $usoEnGrupos = DB::table('grupos')
                ->where('horario_id', $id)
                ->exists();

            if ($usoEnGrupos) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el horario porque está asignado a grupos existentes.');
            }

            $horario->delete();

            return redirect()->route('coordinador.horarios.index')
                ->with('success', 'Horario eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el horario: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of soft deleted resources.
     */
    public function eliminados()
    {
        $horarios = Horario::onlyTrashed()
                          ->orderBy('deleted_at', 'desc')
                          ->paginate(10);

        return view('coordinador.horarios.eliminados', compact('horarios'));
    }

    /**
     * Restore the specified resource from soft delete.
     */
    public function restore(string $id)
    {
        try {
            $horario = Horario::onlyTrashed()->findOrFail($id);
            $horario->restore();

            return redirect()->route('coordinador.horarios.eliminados')
                ->with('success', 'Horario restaurado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al restaurar el horario: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete(string $id)
    {
        try {
            $horario = Horario::onlyTrashed()->findOrFail($id);
            $horario->forceDelete();

            return redirect()->route('coordinador.horarios.eliminados')
                ->with('success', 'Horario eliminado permanentemente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar permanentemente el horario: ' . $e->getMessage());
        }
    }

    /**
     * Validar que los días correspondan al tipo de horario
     */
    private function validarDiasPorTipo(string $tipo, array $dias): bool
    {
        $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $diasSabatinos = ['Sábado'];

        if ($tipo === 'semanal') {
            // Para horario semanal, solo permitir días de semana
            return empty(array_diff($dias, $diasSemana));
        } elseif ($tipo === 'sabatino') {
            // Para horario sabatino, solo permitir sábado
            return count($dias) === 1 && in_array('Sábado', $dias);
        }

        return false;
    }

    /**
     * Obtener horarios por tipo (para AJAX)
     */
    public function getHorariosPorTipo(Request $request)
    {
        $horarios = Horario::where('tipo', $request->tipo)
                          ->where('activo', true)
                          ->orderBy('hora_inicio')
                          ->get()
                          ->map(function ($horario) {
                              return [
                                  'id' => $horario->id,
                                  'nombre' => $horario->nombre,
                                  'dias' => json_decode($horario->dias),
                                  'hora_inicio' => $horario->hora_inicio,
                                  'hora_fin' => $horario->hora_fin,
                                  'descripcion' => $horario->nombre . ' (' . $horario->hora_inicio . ' - ' . $horario->hora_fin . ')'
                              ];
                          });

        return response()->json($horarios);
    }

    /**
     * Cambiar estado activo/inactivo del horario
     */
    public function toggleActivo(string $id)
    {
        try {
            $horario = Horario::findOrFail($id);
            $horario->update(['activo' => !$horario->activo]);

            $estado = $horario->activo ? 'activado' : 'desactivado';

            return redirect()->back()
                ->with('success', "Horario {$estado} exitosamente.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al cambiar el estado del horario: ' . $e->getMessage());
        }
    }
}