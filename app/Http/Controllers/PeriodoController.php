<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        $request->validate([
            'nombre' => 'required|in:AGOSTO-DIC,ENERO-JUNIO,INVIERNO,VERANO1,VERANO2',
            'anio' => 'required|integer|min:2020|max:2030',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio'
        ]);

        // 🆕 VERIFICAR QUE LAS FECHAS COINCIDAN CON EL AÑO
        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaFin = Carbon::parse($request->fecha_fin);
        
        if ($fechaInicio->year != $request->anio || $fechaFin->year != $request->anio) {
            return back()->withErrors([
                'anio' => 'Las fechas deben corresponder al año seleccionado'
            ])->withInput();
        }

        // VERIFICAR QUE NO EXISTA EL MISMO PERIODO+AÑO
        $existe = Periodo::where('nombre', $request->nombre)
                        ->where('anio', $request->anio)
                        ->exists();
                        
        if ($existe) {
            return back()->withErrors([
                'nombre' => 'Ya existe un periodo ' . $request->nombre . ' para el año ' . $request->anio
            ])->withInput();
        }

    Periodo::create([
        'nombre' => $request->nombre,
        'anio' => $request->anio, // 🆕
        'fecha_inicio' => $request->fecha_inicio,
        'fecha_fin' => $request->fecha_fin,
        'activo' => $request->has('activo') ? $request->activo : false
    ]);

    return redirect()->route('coordinador.periodos.index')
        ->with('success', 'Periodo creado exitosamente');
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
            'nombre' => 'required|in:AGOSTO-DIC,ENERO-JUNIO,INVIERNO,VERANO1,VERANO2',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio'
        ]);

        // Desactivar otros periodos si este se marca como activo
        if ($request->has('activo') && $request->activo) {
            Periodo::where('activo', true)->where('id', '!=', $id)->update(['activo' => false]);
        }

        $periodo->update([
            'nombre' => $request->nombre,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'activo' => $request->has('activo') ? $request->activo : false
        ]);

        return redirect()->route('coordinador.periodos.index')
            ->with('success', 'Periodo actualizado exitosamente');
    }

    public function destroy($id)
    {
        $periodo = Periodo::findOrFail($id);
        
        // 🆕 VALIDACIÓN SIMPLIFICADA - sin relación
        $tieneGrupos = \DB::table('grupos')->where('periodo_id', $id)->exists();
        
        if ($tieneGrupos) {
            return redirect()->route('coordinador.periodos.index')
                ->with('error', 'No se puede eliminar el periodo porque tiene grupos asociados.');
        }

        $periodo->delete();

        return redirect()->route('coordinador.periodos.index')
            ->with('success', 'Periodo eliminado exitosamente');
    }
}