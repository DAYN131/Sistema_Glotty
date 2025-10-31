<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AulaController extends Controller
{
    
    // Index de Aulas
    public function index()
    {
    // Obtenemos las aulas
    $aulas = Aula::orderBy('edificio')
                ->orderBy('numero_aula')
                ->get();
    return view('coordinador.aulas.index', compact('aulas'));
    }

    // Redirigir hacia la vista de registrar aulas
    public function create()
    {
        return view('coordinador.aulas.create');
    }

    // Registrar Aulas Funcion
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'edificio' => 'required|string|max:1',
            'numero_aula' => 'required|integer|min:1',
            'capacidad' => 'required|integer|min:1',
            'tipo_aula' => 'required|in:regular,laboratorio,multimedia,conferencia' 
        ]);

        try {
            // Crear el aula (se generará automáticamente el id_aula)
            Aula::create($validatedData);
            
            return redirect()
                   ->route('coordinador.aulas.index')
                   ->with('success', 'Aula creada exitosamente');
                   
        } catch (\Exception $e) {
            return back()
                   ->withErrors(['error' => $e->getMessage()])
                   ->withInput();
        }
    }

    // Método para mostrar formulario de edición
    public function edit($id_aula)
    {
        $aula = Aula::findOrFail($id_aula);
        return view('coordinador.aulas.edit', compact('aula'));
    }

    public function update(Request $request, $id_aula)
    {
        $aula = Aula::findOrFail($id_aula);
        
        $validatedData = $request->validate([
            'edificio' => 'required|string|max:1',
            'numero_aula' => 'required|integer|min:1',
            'capacidad' => 'required|integer|min:1',
            'tipo_aula' => 'required|in:regular,laboratorio,multimedia,conferencia'
        ]);
    
        try {
            // Generar nuevo ID
            $nuevo_id = strtoupper(trim($validatedData['edificio'])) . $validatedData['numero_aula'];
            
            // Verificar si el nuevo ID ya existe (y no es el mismo que estamos editando)
            if ($nuevo_id !== $id_aula && Aula::where('id_aula', $nuevo_id)->exists()) {
                throw new \Exception("Ya existe un aula con este edificio y número");
            }
    
            // Si el ID cambió, necesitamos un enfoque especial para actualizar la PK
            if ($nuevo_id !== $id_aula) {
                // 1. Crear un nuevo registro con el nuevo ID
                $nueva_aula = Aula::create([
                    'id_aula' => $nuevo_id,
                    'edificio' => $validatedData['edificio'],
                    'numero_aula' => $validatedData['numero_aula'],
                    'capacidad' => $validatedData['capacidad'],
                    'tipo_aula' => $validatedData['tipo_aula']
                ]);
                
                // 2. Eliminar el registro antiguo
                $aula->delete();
                
                // 3. Actualizar relaciones (si las hay)
                // Ejemplo si tienes grupos relacionados:
                // Grupo::where('id_aula', $id_aula)->update(['id_aula' => $nuevo_id]);
            } else {
                // Si el ID no cambió, actualizar normalmente
                $aula->update($validatedData);
            }
            
            return redirect()
                ->route('coordinador.aulas.index')
                ->with('success', 'Aula actualizada exitosamente');
                
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    // Método para eliminar el aula
    public function destroy($id_aula)
    {
        try {
            $aula = Aula::findOrFail($id_aula);
            $aula->delete();
            
            return redirect()
                ->route('coordinador.aulas.index')
                ->with('success', 'Aula eliminada exitosamente');
                
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'No se pudo eliminar el aula: ' . $e->getMessage()]);
        }
    }
}