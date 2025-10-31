<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // Añade esta línea

class HorarioController extends Controller
{
    
    // Index de Horarios
    public function index()
    {
    // Obtenemos los horarios
    $horarios = Horario::orderBy('tipo')
                ->get();
    return view('coordinador.horarios.index',compact('horarios'));
    }

    // Redirigir hacia la vista de registrar aulas
    public function create()
    {
        return view('coordinador.horarios.create');
    }

    public function eliminados()
    {
        $horarios = Horario::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();
        
        return view('coordinador.horarios.eliminados', compact('horarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'tipo' => 'required|in:semanal,sabado',
            'dias' => 'required_if:tipo,semanal|array',
            'dias.*' => 'string|in:lunes,martes,miercoles,jueves,viernes',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'inicio_vigencia' => 'required|date',
            'fin_vigencia' => 'required|date|after_or_equal:inicio_vigencia',
            'activo' => 'required|boolean'
        ]);

        try {
            // Si es sabatino, forzar días a null
            if ($validated['tipo'] === 'sabado') {
                $validated['dias'] = "sabado";
            }

            // Si se marca como activo, desactivar otros horarios del mismo tipo
            if ($validated['activo']) {
                Horario::where('tipo', $validated['tipo'])
                    ->update(['activo' => false]);
            }

            Horario::create($validated);
            
            return redirect()->route('coordinador.horarios.index')
                ->with('success', 'Horario creado correctamente');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])
                        ->withInput();
        }
    }
    
    public function update(Request $request, $id)
    {
        $horario = Horario::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'tipo' => 'required|in:semanal,sabado',
            'dias' => 'required_if:tipo,semanal|array',
            'dias.*' => 'string|in:lunes,martes,miercoles,jueves,viernes',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'inicio_vigencia' => 'required|date',
            'fin_vigencia' => 'required|date|after_or_equal:inicio_vigencia',
            'activo' => 'required|boolean'
        ]);

        DB::transaction(function () use ($horario, $validated) {
            // Si cambia el estado a activo, desactivar otros
            if ($validated['activo']) {
                Horario::where('tipo', $validated['tipo'])
                    ->where('id', '!=', $horario->id)
                    ->update(['activo' => false]);
            }

            $horario->update($validated);
        });

        return redirect()->route('coordinador.horarios.index')
            ->with('success', 'Horario actualizado');
    }

    // Método para mostrar formulario de edición
    public function edit($id)
    {
        $horario = Horario::findOrFail($id);
        return view('coordinador.horarios.edit', compact('horario'));
    }


    public function destroy($id)
    {
        $horario = Horario::findOrFail($id);
        $horario->delete(); // Esto hace el soft delete
        
        // Verifica en la base de datos
        dd(Horario::withTrashed()->find($id));
    }

    public function restore($id)
    {
        $horario = Horario::onlyTrashed()->findOrFail($id);
        $horario->restore();
        
        return redirect()->route('coordinador.horarios.index')
            ->with('success', 'Horario restaurado correctamente');
    }



}



    
