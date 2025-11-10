<?php

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
    public function index()
    {
        $aulas = Aula::orderBy('edificio')->orderBy('numero_aula')->get();
        return view('coordinador.aulas.index', compact('aulas'));
    }

    /**
     * Muestra el formulario para crear una nueva aula.
     */
    public function create()
    {
        $tiposAula = Aula::TIPO_AULA;
        return view('coordinador.aulas.create', compact('tiposAula'));
    }

    /**
     * Almacena una nueva aula en la base de datos.
     */
    public function store(Request $request)
    {
        // Reglas de validación
        $validatedData = $request->validate([
            'edificio' => 'required|string|max:10',
            'numero_aula' => 'required|string|max:10',
            'capacidad' => 'required|integer|min:1',
            'tipo_aula' => ['required', Rule::in(array_keys(Aula::TIPO_AULA))],
        ]);

        // 1. Generar el ID único a partir de los campos para la verificación
        $id_aula_generado = strtoupper(trim($validatedData['edificio'])) . '-' . trim($validatedData['numero_aula']);

        // 2. Verificar unicidad de la clave primaria generada (id_aula)
        if (Aula::where('id_aula', $id_aula_generado)->exists()) {
            return back()->withErrors([
                'edificio' => "❌ La combinación {$id_aula_generado} ya existe. Cambia el edificio o el número de aula.",
                'numero_aula' => "❌ La combinación {$id_aula_generado} ya existe. Cambia el edificio o el número de aula.",
            ])->withInput();
        }

        try {
            // El hook 'creating' del modelo se encargará de asignar id_aula.
            Aula::create($validatedData);

            return redirect()->route('coordinador.aulas.index')
                ->with('success', '✅ Aula creada exitosamente: ' . $id_aula_generado);

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al guardar el aula: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un aula específica.
     */
    public function edit(Aula $aula)
    {
        $tiposAula = Aula::TIPO_AULA;
        return view('coordinador.aulas.edit', compact('aula', 'tiposAula'));
    }

    /**
     * Actualiza un aula específica en la base de datos.
     */
    public function update(Request $request, Aula $aula)
    {
        // Reglas de validación
        $validatedData = $request->validate([
            'edificio' => 'required|string|max:10',
            'numero_aula' => 'required|string|max:10',
            'capacidad' => 'required|integer|min:1',
            'tipo_aula' => ['required', Rule::in(array_keys(Aula::TIPO_AULA))],
        ]);

        // 1. Generar el nuevo ID potencial
        $nuevo_id_aula = strtoupper(trim($validatedData['edificio'])) . '-' . trim($validatedData['numero_aula']);

        // 2. Verificar si el nuevo ID ya existe y no es el ID actual del aula
        if ($nuevo_id_aula !== $aula->id_aula && Aula::where('id_aula', $nuevo_id_aula)->exists()) {
            return back()->withErrors([
                'edificio' => "❌ La combinación {$nuevo_id_aula} ya existe. No se pudo actualizar.",
                'numero_aula' => "❌ La combinación {$nuevo_id_aula} ya existe. No se pudo actualizar.",
            ])->withInput();
        }

        try {
            // El hook 'updating' del modelo se encargará de actualizar id_aula si es necesario.
            $aula->update($validatedData);

            return redirect()->route('coordinador.aulas.index')
                ->with('success', '✅ Aula actualizada exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al actualizar el aula: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Elimina un aula de la base de datos.
     */
    public function destroy(Aula $aula)
    {
        try {
            // 1. Verificar si el aula tiene grupos asignados
            if ($aula->grupos()->exists()) {
                return back()->with('error', '❌ No se puede eliminar el aula. Primero elimina los grupos asociados a esta aula.');
            }

            // 2. Eliminar el aula
            $aula->delete();

            return redirect()->route('coordinador.aulas.index')
                ->with('success', '🗑️ Aula ' . $aula->id_aula . ' eliminada correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al eliminar el aula: ' . $e->getMessage());
        }
    }
}
