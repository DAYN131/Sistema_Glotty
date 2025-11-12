<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Periodo;
use App\Models\Horario;
use App\Models\Aula;
use App\Models\Profesor;
use App\Models\Preregistro;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    /**
     * Muestra lista de grupos con filtros por estado
     */
    public function index(Request $request)
    {
        $query = Grupo::with(['periodo', 'horario', 'aula', 'profesor']);
        
        // Filtros
        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->has('nivel') && $request->nivel) {
            $query->where('nivel_ingles', $request->nivel);
        }
        
        if ($request->has('periodo') && $request->periodo) {
            $query->where('periodo_id', $request->periodo);
        }

        $grupos = $query->latest()->get();
        $periodos = Periodo::all();
        $estadisticas = $this->obtenerEstadisticas();

        return view('coordinador.grupos.index', compact(
            'grupos', 
            'periodos',
            'estadisticas'
        ));
    }

    /**
     * Muestra formulario para crear grupo (solo datos básicos)
     */
    public function create()
    {
        $periodos = Periodo::where('activo', true)->get();
        $horarios = Horario::where('activo', true)->get();
        $nivelSugerido = request('nivel'); // Para crear desde análisis de demanda

        return view('coordinador.grupos.create', compact(
            'periodos', 
            'horarios',
            'nivelSugerido'
        ));
    }

    /**
     * Almacena grupo en estado "planificado"
     */
    public function store(Request $request)
    {
        $request->validate([
            'nivel_ingles' => 'required|integer|between:1,5',
            'letra_grupo' => 'required|string|size:1',
            'periodo_id' => 'required|exists:periodos,id',
            'horario_id' => 'required|exists:horarios,id',
            'capacidad_maxima' => 'required|integer|min:1|max:50'
        ]);

        try {
            // Verificar que no exista grupo duplicado
            $grupoExistente = Grupo::where('nivel_ingles', $request->nivel_ingles)
                ->where('letra_grupo', $request->letra_grupo)
                ->where('periodo_id', $request->periodo_id)
                ->first();

            if ($grupoExistente) {
                return back()->with('error', 'Ya existe un grupo con ese nivel y letra en el periodo seleccionado.');
            }

            Grupo::create([
                'nivel_ingles' => $request->nivel_ingles,
                'letra_grupo' => $request->letra_grupo,
                'periodo_id' => $request->periodo_id,
                'horario_id' => $request->horario_id,
                'capacidad_maxima' => $request->capacidad_maxima,
                'estado' => 'planificado', // Estado inicial
                'estudiantes_inscritos' => 0
            ]);

            return redirect()->route('coordinador.grupos.index')
                ->with('success', 'Grupo creado exitosamente en estado "Planificado". Ahora puedes asignar profesor y aula.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el grupo: ' . $e->getMessage());
        }
    }

    /**
     * Muestra formulario para asignar profesor a grupo
     */
    public function asignarProfesor($id)
    {
        $grupo = Grupo::with(['horario', 'periodo'])->findOrFail($id);
        
        if ($grupo->estado !== 'planificado' && $grupo->estado !== 'con_profesor') {
            return back()->with('error', 'No se puede asignar profesor a un grupo en estado: ' . $grupo->estado);
        }

        // Profesores disponibles en ese horario
        $profesoresDisponibles = Profesor::whereDoesntHave('grupos', function($query) use ($grupo) {
            $query->where('horario_id', $grupo->horario_id)
                  ->where('periodo_id', $grupo->periodo_id);
        })->get();

        return view('coordinador.grupos.asignar-profesor', compact(
            'grupo',
            'profesoresDisponibles'
        ));
    }

    /**
     * Asigna profesor al grupo
     */
    public function guardarProfesor(Request $request, $id)
    {
        $request->validate([
            'profesor_id' => 'required|exists:profesores,id_profesor'
        ]);

        try {
            $grupo = Grupo::findOrFail($id);
            
            $grupo->update([
                'profesor_id' => $request->profesor_id,
                'estado' => 'con_profesor'
            ]);

            return redirect()->route('coordinador.grupos.index')
                ->with('success', 'Profesor asignado exitosamente. Ahora puedes asignar un aula.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al asignar profesor: ' . $e->getMessage());
        }
    }

    /**
     * Muestra formulario para asignar aula a grupo
     */
    public function asignarAula($id)
    {
        $grupo = Grupo::with(['horario', 'periodo'])->findOrFail($id);
        
        if ($grupo->estado !== 'con_profesor') {
            return back()->with('error', 'Primero debe asignar un profesor al grupo.');
        }

        // Aulas disponibles en ese horario
        $aulasDisponibles = Aula::where('capacidad', '>=', $grupo->capacidad_maxima)
            ->whereDoesntHave('grupos', function($query) use ($grupo) {
                $query->where('horario_id', $grupo->horario_id)
                      ->where('periodo_id', $grupo->periodo_id);
            })->get();

        return view('coordinador.grupos.asignar-aula', compact(
            'grupo',
            'aulasDisponibles'
        ));
    }

    /**
     * Asigna aula al grupo
     */
    public function guardarAula(Request $request, $id)
    {
        $request->validate([
            'aula_id' => 'required|exists:aulas,id_aula'
        ]);

        try {
            $grupo = Grupo::findOrFail($id);
            
            $grupo->update([
                'aula_id' => $request->aula_id,
                'estado' => 'activo' // Grupo completamente configurado
            ]);

            return redirect()->route('coordinador.grupos.index')
                ->with('success', 'Aula asignada exitosamente. El grupo ahora está activo.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al asignar aula: ' . $e->getMessage());
        }
    }

    /**
     * Activa grupo (cambia estado a "activo")
     */
    public function activar($id)
    {
        try {
            $grupo = Grupo::findOrFail($id);
            
            if ($grupo->estado !== 'con_aula') {
                return back()->with('error', 'El grupo debe tener aula asignada para activarse.');
            }

            $grupo->update(['estado' => 'activo']);

            return back()->with('success', 'Grupo activado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al activar grupo: ' . $e->getMessage());
        }
    }

    /**
     * Asigna preregistros automáticamente a grupos activos
     */
    public function asignarAutomaticamente()
    {
        try {
            $preregistrosPendientes = Preregistro::where('estado', 'preregistrado')
                ->with(['horarioSolicitado'])
                ->get();

            $gruposActivos = Grupo::where('estado', 'activo')
                ->withCount('preregistros')
                ->get();

            $asignados = 0;

            foreach ($preregistrosPendientes as $preregistro) {
                // Buscar grupo compatible
                $grupoCompatible = $gruposActivos->first(function($grupo) use ($preregistro) {
                    return $grupo->nivel_ingles == $preregistro->nivel_solicitado &&
                           $grupo->horario_id == $preregistro->horario_solicitado_id &&
                           $grupo->preregistros_count < $grupo->capacidad_maxima;
                });

                if ($grupoCompatible) {
                    $preregistro->update([
                        'grupo_asignado_id' => $grupoCompatible->id,
                        'estado' => 'asignado'
                    ]);
                    
                    // Actualizar contador de estudiantes
                    $grupoCompatible->increment('estudiantes_inscritos');
                    $grupoCompatible->preregistros_count++;
                    
                    $asignados++;
                }
            }

            return back()->with('success', "Se asignaron {$asignados} estudiantes a grupos automáticamente.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error en asignación automática: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene estadísticas de grupos
     */
    private function obtenerEstadisticas()
    {
        return [
            'total' => Grupo::count(),
            'planificados' => Grupo::where('estado', 'planificado')->count(),
            'con_profesor' => Grupo::where('estado', 'con_profesor')->count(),
            'con_aula' => Grupo::where('estado', 'con_aula')->count(),
            'activos' => Grupo::where('estado', 'activo')->count(),
            'capacidad_utilizada' => Grupo::sum('estudiantes_inscritos'),
            'capacidad_total' => Grupo::sum('capacidad_maxima')
        ];
    }
}