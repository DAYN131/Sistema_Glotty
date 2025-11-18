<?php
// app/Http/Controllers/AulaController.php

namespace App\Http\Controllers;

use App\Models\Aula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AulaController extends Controller
{
    /**
     * Muestra una lista de todas las aulas.
     */
    public function index(Request $request)
    {
        $query = Aula::query();
        
        // Filtro por edificio
        if ($request->has('edificio') && $request->edificio) {
            $query->where('edificio', $request->edificio);
        }
        
        // Filtro por tipo
        if ($request->has('tipo') && $request->tipo) {
            $query->where('tipo', $request->tipo);
        }
        
        // Filtro por disponibilidad
        if ($request->has('disponible') && $request->disponible !== '') {
            $query->where('disponible', $request->disponible);
        }
        
        $aulas = $query->orderBy('edificio')
                       ->orderBy('nombre')
                       ->get();
        
        // Datos para los filtros
        $edificios = Aula::distinct()->pluck('edificio')->sort();
        $tiposAula = Aula::TIPOS_AULA;
                       
        return view('coordinador.aulas.index', compact('aulas', 'edificios', 'tiposAula'));
    }

    /**
     * Muestra el formulario para crear una nueva aula.
     */
    public function create()
    {
        $tiposAula = Aula::TIPOS_AULA;
        return view('coordinador.aulas.create', compact('tiposAula'));
    }

    /**
     * Almacena una nueva aula en la base de datos.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
            'edificio' => 'required|string|max:50',
            'capacidad' => 'required|integer|min:1',
            'tipo' => ['required', Rule::in(array_keys(Aula::TIPOS_AULA))],
            'equipamiento' => 'nullable|string|max:500',
            'disponible' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            Aula::create($validatedData);

            DB::commit();

            return redirect()->route('coordinador.aulas.index')
                ->with('success', '✅ Aula creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', '❌ Error al crear el aula: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un aula específica.
     */
    public function edit(Aula $aula)
    {
        $tiposAula = Aula::TIPOS_AULA;
        return view('coordinador.aulas.edit', compact('aula', 'tiposAula'));
    }

    /**
     * Actualiza un aula específica en la base de datos.
     */
    public function update(Request $request, Aula $aula)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
            'edificio' => 'required|string|max:50',
            'capacidad' => 'required|integer|min:1',
            'tipo' => ['required', Rule::in(array_keys(Aula::TIPOS_AULA))],
            'equipamiento' => 'nullable|string|max:500',
            'disponible' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $aula->update($validatedData);

            DB::commit();

            return redirect()->route('coordinador.aulas.index')
                ->with('success', '✅ Aula actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', '❌ Error al actualizar el aula: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Elimina un aula de la base de datos.
     */
    public function destroy(Aula $aula)
    {
        try {
            // Verificar si el aula tiene grupos asignados
            if ($aula->grupos()->count() > 0) {
                return redirect()->route('coordinador.aulas.index')
                    ->with('error', '❌ No se puede eliminar el aula. Tiene grupos asignados.');
            }

            // Verificar si tiene disponibilidad asociada
            if ($aula->disponibilidadHorarios()->count() > 0) {
                return redirect()->route('coordinador.aulas.index')
                    ->with('error', '❌ No se puede eliminar el aula. Tiene horarios asignados.');
            }

            $aula->delete();

            return redirect()->route('coordinador.aulas.index')
                ->with('success', '🗑️ Aula eliminada correctamente.');

        } catch (\Exception $e) {
            return redirect()->route('coordinador.aulas.index')
                ->with('error', '❌ Error al eliminar el aula: ' . $e->getMessage());
        }
    }

    /**
     * Cambia el estado disponible/inactivo del aula.
     */
    public function toggleDisponible(Aula $aula)
    {
        try {
            $aula->update([
                'disponible' => !$aula->disponible
            ]);

            $estado = $aula->disponible ? 'disponible' : 'no disponible';
            
            return redirect()->route('coordinador.aulas.index')
                ->with('success', "✅ Aula marcada como {$estado}.");

        } catch (\Exception $e) {
            return redirect()->route('coordinador.aulas.index')
                ->with('error', '❌ Error al cambiar estado del aula.');
        }
    }
}