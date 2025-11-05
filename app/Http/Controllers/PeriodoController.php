<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Para usar transacciones y consultas directas
use Illuminate\Validation\Rule; // Para una validación más limpia de ENUMs

class PeriodoController extends Controller
{
    public function index()
    {
        $periodos = Periodo::orderBy('fecha_inicio', 'desc')->get();
        return view('coordinador.periodos.index', compact('periodos'));
    }

    public function create()
    {
        return view('coordinador.periodos.create');
    }

    public function store(Request $request)
    {
        // 1. Validaciones (Se elimina 'anio')
        $request->validate([
            'nombre' => ['required', Rule::in(['AGOSTO-DIC', 'ENERO-JUNIO', 'INVIERNO', 'VERANO1', 'VERANO2'])],
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            // El campo activo es opcional (checkbox), lo manejamos en la lógica
        ]);

        // 2. VERIFICAR QUE NO EXISTA EL MISMO PERIODO+AÑO (Usando el año de la fecha de inicio)
        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $anio = $fechaInicio->year;

        $existe = Periodo::where('nombre', $request->nombre)
            // Buscamos si ya existe el mismo nombre EN EL MISMO AÑO
            ->whereYear('fecha_inicio', $anio)
            ->exists();

        if ($existe) {
            return back()->withErrors([
                'nombre' => 'Ya existe un periodo ' . $request->nombre . ' que inicia en el año ' . $anio
            ])->withInput();
        }

        // 3. Lógica del campo 'activo' y Transacción (Si el negocio es "solo 1 activo")
        $activo = $request->has('activo') && $request->activo;

        try {
            DB::beginTransaction();

            // Si este periodo se marca como activo, desactivamos todos los demás.
            if ($activo) {
                Periodo::where('activo', true)->update(['activo' => false]);
            }

            // 4. Creación del Periodo
            Periodo::create([
                'nombre' => $request->nombre,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'activo' => $activo // Usamos la variable booleana ya procesada
            ]);

            DB::commit();

            return redirect()->route('coordinador.periodos.index')
                ->with('success', '✅ Periodo creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el periodo: ' . $e->getMessage()])->withInput();
        }
    }


    public function edit($id)
    {
        $periodo = Periodo::findOrFail($id);
        return view('coordinador.periodos.edit', compact('periodo'));
    }

    public function update(Request $request, $id)
    {
        $periodo = Periodo::findOrFail($id);

        $request->validate([
            'nombre' => ['required', Rule::in(['AGOSTO-DIC', 'ENERO-JUNIO', 'INVIERNO', 'VERANO1', 'VERANO2'])],
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio'
        ]);

        // **Verificación de unicidad al actualizar**
        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $anio = $fechaInicio->year;

        $existe = Periodo::where('nombre', $request->nombre)
            ->whereYear('fecha_inicio', $anio)
            ->where('id', '!=', $id) // Excluir el periodo actual
            ->exists();

        if ($existe) {
            return back()->withErrors([
                'nombre' => 'Ya existe otro periodo ' . $request->nombre . ' que inicia en el año ' . $anio
            ])->withInput();
        }

        // Lógica del campo 'activo'
        $activo = $request->has('activo') && $request->activo;

        try {
            DB::beginTransaction();

            // Si este periodo se marca como activo, desactivamos todos los demás.
            if ($activo) {
                Periodo::where('activo', true)
                    ->where('id', '!=', $id) // Desactivar solo a los *otros*
                    ->update(['activo' => false]);
            }

            $periodo->update([
                'nombre' => $request->nombre,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'activo' => $activo
            ]);

            DB::commit();

            return redirect()->route('coordinador.periodos.index')
                ->with('success', '✅ Periodo actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el periodo: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $periodo = Periodo::findOrFail($id);

        try {
            // VERIFICACIÓN DE LLAVES FORÁNEAS USANDO EL MODELO
            // Asumo que el modelo Periodo tiene una relación 'grupos' (hasMany)
            if ($periodo->grupos()->exists()) {
                return back()
                    ->with('error', 'No se puede eliminar el periodo porque tiene grupos asociados. Elimina los grupos primero.');
            }

            $periodo->delete();

            return redirect()->route('coordinador.periodos.index')
                ->with('success', '🗑️ Periodo eliminado exitosamente');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al eliminar el periodo. ' . $e->getMessage()]);
        }
    }
}
