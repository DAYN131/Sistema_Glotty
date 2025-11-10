<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aulas = Aula::orderBy('edificio')
                    ->orderBy('numero_aula')
                    ->paginate(10);

        return view('coordinador.aulas.index', compact('aulas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener edificios únicos para el dropdown
        $edificios = Aula::distinct()->pluck('edificio')->filter();
        
        return view('coordinador.aulas.create', compact('edificios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_aula' => 'required|string|max:255|unique:aulas,id_aula',
            'edificio' => 'required|string|max:255',
            'numero_aula' => 'required|integer|min:1',
            'capacidad' => 'required|integer|min:1',
            'tipo_aula' => 'required|string|in:regular,laboratorio,taller,audiovisual,sala_computo',
        ]);

        try {
            // Verificar si ya existe un aula con el mismo edificio y número
            $aulaExistente = Aula::where('edificio', $request->edificio)
                                ->where('numero_aula', $request->numero_aula)
                                ->first();

            if ($aulaExistente) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['numero_aula' => 'Ya existe un aula con este número en el mismo edificio.']);
            }

            Aula::create([
                'id_aula' => $request->id_aula,
                'edificio' => $request->edificio,
                'numero_aula' => $request->numero_aula,
                'capacidad' => $request->capacidad,
                'tipo_aula' => $request->tipo_aula,
            ]);

            return redirect()->route('coordinador.aulas.index')
                ->with('success', 'Aula creada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el aula: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $aula = Aula::where('id_aula', $id)->firstOrFail();
        $edificios = Aula::distinct()->pluck('edificio')->filter();

        return view('coordinador.aulas.edit', compact('aula', 'edificios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'edificio' => 'required|string|max:255',
            'numero_aula' => 'required|integer|min:1',
            'capacidad' => 'required|integer|min:1',
            'tipo_aula' => 'required|string|in:regular,laboratorio,taller,audiovisual,sala_computo',
        ]);

        try {
            $aula = Aula::where('id_aula', $id)->firstOrFail();

            // Verificar si ya existe otro aula con el mismo edificio y número
            $aulaExistente = Aula::where('edificio', $request->edificio)
                                ->where('numero_aula', $request->numero_aula)
                                ->where('id_aula', '!=', $id)
                                ->first();

            if ($aulaExistente) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['numero_aula' => 'Ya existe otro aula con este número en el mismo edificio.']);
            }

            $aula->update([
                'edificio' => $request->edificio,
                'numero_aula' => $request->numero_aula,
                'capacidad' => $request->capacidad,
                'tipo_aula' => $request->tipo_aula,
            ]);

            return redirect()->route('coordinador.aulas.index')
                ->with('success', 'Aula actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el aula: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $aula = Aula::where('id_aula', $id)->firstOrFail();

            // Verificar si el aula está siendo usada en horarios
            $usoEnHorarios = DB::table('horarios')
                ->where('aula_id', $id)
                ->exists();

            if ($usoEnHorarios) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el aula porque está asignada en horarios existentes.');
            }

            $aula->delete();

            return redirect()->route('coordinador.aulas.index')
                ->with('success', 'Aula eliminada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el aula: ' . $e->getMessage());
        }
    }

    /**
     * Obtener aulas por edificio (para AJAX)
     */
    public function getAulasByEdificio(Request $request)
    {
        $aulas = Aula::where('edificio', $request->edificio)
                    ->orderBy('numero_aula')
                    ->get();

        return response()->json($aulas);
    }

    /**
     * Obtener información del aula (para AJAX)
     */
    public function getAulaInfo(Request $request)
    {
        $aula = Aula::where('id_aula', $request->aula_id)->first();
        
        if (!$aula) {
            return response()->json(['error' => 'Aula no encontrada'], 404);
        }

        return response()->json([
            'capacidad' => $aula->capacidad,
            'tipo_aula' => $aula->tipo_aula,
            'numero_aula' => $aula->numero_aula,
            'edificio' => $aula->edificio
        ]);
    }
}
