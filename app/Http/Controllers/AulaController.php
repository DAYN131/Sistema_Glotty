<?php
// app/Http/Controllers/AulaController.php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Grupo;
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
            'nombre' => 'required|string|max:100|unique:aulas,nombre',
            'edificio' => 'required|string|max:50',
            'capacidad' => 'required|integer|min:1|max:200',
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
        
        // ✅ NUEVO: Obtener información de uso del aula
        $gruposAsignados = $aula->grupos()
            ->with(['periodo', 'horario'])
            ->whereNotIn('estado', ['cancelado'])
            ->get();
            
        $horariosOcupados = $aula->obtenerHorariosOcupados();
        
        return view('coordinador.aulas.edit', compact(
            'aula', 
            'tiposAula', 
            'gruposAsignados',
            'horariosOcupados'
        ));
    }

    /**
     * Actualiza un aula específica en la base de datos.
     */
    public function update(Request $request, Aula $aula)
    {
        $validatedData = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('aulas')->ignore($aula->id)
            ],
            'edificio' => 'required|string|max:50',
            'capacidad' => 'required|integer|min:1|max:200',
            'tipo' => ['required', Rule::in(array_keys(Aula::TIPOS_AULA))],
            'equipamiento' => 'nullable|string|max:500',
            'disponible' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // ✅ NUEVO: Validar que la capacidad no sea menor a los grupos actuales
            if ($validatedData['capacidad'] < $aula->capacidad) {
                $gruposConMayorCapacidad = $aula->grupos()
                    ->where('capacidad_maxima', '>', $validatedData['capacidad'])
                    ->whereNotIn('estado', ['cancelado'])
                    ->exists();
                    
                if ($gruposConMayorCapacidad) {
                    throw new \Exception('No se puede reducir la capacidad. Hay grupos asignados que requieren más capacidad.');
                }
            }

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
            // ✅ ACTUALIZADO: Solo verificar grupos (eliminamos disponibilidadHorarios)
            if ($aula->grupos()->whereNotIn('estado', ['cancelado'])->count() > 0) {
                return redirect()->route('coordinador.aulas.index')
                    ->with('error', '❌ No se puede eliminar el aula. Tiene grupos activos asignados.');
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
            // ✅ NUEVO: Validar que no tenga grupos activos si se va a desactivar
            if ($aula->disponible && $aula->grupos()->whereNotIn('estado', ['cancelado'])->count() > 0) {
                return redirect()->route('coordinador.aulas.index')
                    ->with('error', '❌ No se puede desactivar el aula. Tiene grupos activos asignados.');
            }

            $aula->update([
                'disponible' => !$aula->disponible
            ]);

            $estado = $aula->disponible ? 'disponible' : 'no disponible';
            
            return redirect()->route('coordinador.aulas.index')
                ->with('success', "✅ Aula marcada como {$estado}.");

        } catch (\Exception $e) {
            return redirect()->route('coordinador.aulas.index')
                ->with('error', '❌ Error al cambiar estado del aula: ' . $e->getMessage());
        }
    }

    /**
     * ✅ NUEVO: Muestra los horarios y grupos asignados al aula
     */
    public function show(Aula $aula)
    {
        $gruposAsignados = $aula->grupos()
            ->with(['periodo', 'horario', 'profesor'])
            ->whereNotIn('estado', ['cancelado'])
            ->orderBy('horario_periodo_id')
            ->get();
            
        $horariosOcupados = $aula->obtenerHorariosOcupados();
        $estadisticas = $this->obtenerEstadisticasAula($aula);
        
        return view('coordinador.aulas.show', compact(
            'aula',
            'gruposAsignados', 
            'horariosOcupados',
            'estadisticas'
        ));
    }

    /**
     * ✅ NUEVO: Obtiene estadísticas de uso del aula
     */
    private function obtenerEstadisticasAula(Aula $aula)
    {
        $gruposActivos = $aula->grupos()
            ->whereNotIn('estado', ['cancelado'])
            ->get();
            
        return [
            'total_grupos' => $gruposActivos->count(),
            'grupos_activos' => $gruposActivos->where('estado', 'activo')->count(),
            'ocupacion_promedio' => $gruposActivos->avg('porcentaje_ocupacion') ?? 0,
            'capacidad_utilizada' => $gruposActivos->sum('estudiantes_inscritos'),
            'horarios_ocupados' => $gruposActivos->unique('horario_periodo_id')->count()
        ];
    }

    /**
     * ✅ NUEVO: API para obtener aulas disponibles para un horario
     */
    public function disponiblesParaHorario(Request $request)
    {
        $request->validate([
            'horario_periodo_id' => 'required|exists:horarios_periodo,id',
            'capacidad_requerida' => 'nullable|integer|min:1'
        ]);

        try {
            $aulas = Aula::disponiblesParaHorario(
                $request->horario_periodo_id,
                $request->capacidad_requerida
            );

            return response()->json([
                'success' => true,
                'aulas' => $aulas->map(function ($aula) {
                    return [
                        'id' => $aula->id,
                        'nombre_completo' => $aula->nombre_completo,
                        'capacidad' => $aula->capacidad,
                        'tipo' => $aula->tipo_formateado,
                        'info_resumida' => $aula->info_resumida
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener aulas disponibles: ' . $e->getMessage()
            ], 500);
        }
    }
}