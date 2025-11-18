<?php
// app/Http/Controllers/CoordinadorGrupoController.php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Periodo;
use App\Models\HorarioPeriodo;
use App\Models\Aula;
use App\Models\Profesor;
use App\Models\Preregistro;
use Illuminate\Http\Request;

class CoordinadorGrupoController extends Controller
{
    /**
     * Muestra la lista de grupos
     */
   public function index(Request $request)
    {
        $query = Grupo::with(['periodo', 'horario', 'aula', 'profesor', 'estudiantesActivos'])
            ->latest();

        // Filtros
        if ($request->filled('nivel')) {
            $query->where('nivel_ingles', $request->nivel);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('periodo_id')) {
            $query->where('periodo_id', $request->periodo_id);
        }

        $grupos = $query->paginate(20);
        $periodos = Periodo::all();
        $periodoActivo = Periodo::conPreRegistrosActivos()->first();

        // ✅ CALCULAR ESTADÍSTICAS
        $estadisticas = $this->calcularEstadisticas($request);

        return view('coordinador.grupos.index', compact(
            'grupos', 
            'periodos', 
            'periodoActivo',
            'estadisticas' // ✅ AGREGADO
        ));
    }

    /**
     * Calcula las estadísticas para la vista
     */
    private function calcularEstadisticas(Request $request)
    {
        $query = Grupo::query();

        // Aplicar mismos filtros que en el index
        if ($request->filled('nivel')) {
            $query->where('nivel_ingles', $request->nivel);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('periodo_id')) {
            $query->where('periodo_id', $request->periodo_id);
        }

        $grupos = $query->get();

        return [
            'total' => $grupos->count(),
            'planificados' => $grupos->where('estado', 'planificado')->count(),
            'con_profesor' => $grupos->where('estado', 'con_profesor')->count(),
            'con_aula' => $grupos->where('estado', 'con_aula')->count(),
            'activos' => $grupos->where('estado', 'activo')->count(),
            'cancelados' => $grupos->where('estado', 'cancelado')->count(),
            'capacidad_total' => $grupos->sum('capacidad_maxima'),
            'capacidad_utilizada' => $grupos->sum('estudiantes_inscritos'),
            'ocupacion_promedio' => $grupos->avg('porcentaje_ocupacion') ?? 0
        ];
    }

    /**
     * Muestra el formulario para crear grupo
     */
    public function create()
    {
            $periodos = Periodo::whereIn('estado', ['configuracion', 'preregistros_activos'])->get();
            $horarios = HorarioPeriodo::where('activo', true)->get();
            
            $aulas = Aula::all();
            $profesores = Profesor::all();
            
            $periodoActivo = Periodo::conPreRegistrosActivos()->first();

            return view('coordinador.grupos.create', compact(
                'periodos',
                'horarios',
                'aulas',
                'profesores',
                'periodoActivo'
            ));
    }

    /**
     * Almacena un nuevo grupo
     */
    public function store(Request $request)
    {
        $request->validate([
            'nivel_ingles' => 'required|integer|between:1,5',
            'letra_grupo' => 'required|string|size:1',
            'periodo_id' => 'required|exists:periodos,id',
            'horario_periodo_id' => 'required|exists:horarios_periodo,id',
            'aula_id' => 'nullable|exists:aulas,id',
            'profesor_id' => 'nullable|exists:profesores,id',
            'capacidad_maxima' => 'required|integer|min:15|max:40'
        ]);

        try {
            // Verificar unicidad del grupo
            $grupoExistente = Grupo::where('periodo_id', $request->periodo_id)
                ->where('nivel_ingles', $request->nivel_ingles)
                ->where('letra_grupo', $request->letra_grupo)
                ->first();

            if ($grupoExistente) {
                return back()->with('error', 
                    "Ya existe el grupo {$request->nivel_ingles}-{$request->letra_grupo} en este periodo."
                )->withInput();
            }

            // Crear grupo
            $grupo = Grupo::create([
                'nivel_ingles' => $request->nivel_ingles,
                'letra_grupo' => $request->letra_grupo,
                'periodo_id' => $request->periodo_id,
                'horario_periodo_id' => $request->horario_periodo_id,
                'aula_id' => $request->aula_id,
                'profesor_id' => $request->profesor_id,
                'capacidad_maxima' => $request->capacidad_maxima,
                'estado' => $this->determinarEstadoInicial($request)
            ]);

            return redirect()->route('coordinador.grupos.show', $grupo->id)
                ->with('success', "Grupo {$grupo->nombre_completo} creado exitosamente.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear grupo: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra los detalles de un grupo
     */
    public function show($id)
    {
        $grupo = Grupo::with([
            'periodo',
            'horario',
            'aula',
            'profesor',
            'preregistros.usuario',
            'estudiantesActivos.usuario'
        ])->findOrFail($id);

        // Estudiantes disponibles para asignar (mismo nivel, periodo, y pueden asignarse)
        $estudiantesDisponibles = Preregistro::with(['usuario', 'horarioPreferido'])
            ->where('periodo_id', $grupo->periodo_id)
            ->where('nivel_solicitado', $grupo->nivel_ingles)
            ->where('estado', 'pendiente')
            ->whereIn('pago_estado', ['pagado', 'prorroga'])
            ->get();

        return view('coordinador.grupos.show', compact(
            'grupo',
            'estudiantesDisponibles'
        ));
    }

    /**
     * Muestra el formulario para editar grupo
     */
    public function edit($id)
    {
        $grupo = Grupo::findOrFail($id);
        $periodos = Periodo::whereIn('estado', ['configuracion', 'preregistros_activos'])->get();
        $horarios = HorarioPeriodo::where('activo', true)->get();
        $aulas = Aula::where('disponible', true)->get();
        $profesores = Profesor::where('activo', true)->get();

        return view('coordinador.grupos.edit', compact(
            'grupo',
            'periodos',
            'horarios',
            'aulas',
            'profesores'
        ));
    }

    /**
     * Actualiza un grupo
     */
    public function update(Request $request, $id)
    {
        $grupo = Grupo::findOrFail($id);

        $request->validate([
            'horario_periodo_id' => 'required|exists:horarios_periodo,id',
            'aula_id' => 'nullable|exists:aulas,id',
            'profesor_id' => 'nullable|exists:profesores,id',
            'capacidad_maxima' => 'required|integer|min:15|max:40',
            'estado' => 'required|in:planificado,con_profesor,con_aula,activo,cancelado'
        ]);

        try {
            // Verificar que la capacidad no sea menor a los estudiantes inscritos
            if ($request->capacidad_maxima < $grupo->estudiantes_inscritos) {
                return back()->with('error', 
                    "La capacidad no puede ser menor a los estudiantes inscritos ({$grupo->estudiantes_inscritos})."
                )->withInput();
            }

            $grupo->update([
                'horario_periodo_id' => $request->horario_periodo_id,
                'aula_id' => $request->aula_id,
                'profesor_id' => $request->profesor_id,
                'capacidad_maxima' => $request->capacidad_maxima,
                'estado' => $request->estado
            ]);

            return back()->with('success', 'Grupo actualizado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar grupo: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Asigna un estudiante al grupo
     */
    public function asignarEstudiante(Request $request, $id)
    {
        $grupo = Grupo::findOrFail($id);
        $preregistro = Preregistro::findOrFail($request->preregistro_id);

        if (!$preregistro->puedeSerAsignado()) {
            return back()->with('error', 'Este estudiante no puede ser asignado a un grupo.');
        }

        if (!$grupo->tieneCapacidad()) {
            return back()->with('error', 'El grupo no tiene capacidad disponible.');
        }

        if ($grupo->asignarEstudiante($preregistro->id)) {
            return back()->with('success', 'Estudiante asignado al grupo exitosamente.');
        }

        return back()->with('error', 'Error al asignar estudiante al grupo.');
    }

    /**
     * Remueve un estudiante del grupo
     */
    public function removerEstudiante(Request $request, $id)
    {
        $grupo = Grupo::findOrFail($id);

        if ($grupo->removerEstudiante($request->preregistro_id)) {
            return back()->with('success', 'Estudiante removido del grupo exitosamente.');
        }

        return back()->with('error', 'Error al remover estudiante del grupo.');
    }

    /**
     * Cambia el estado de un grupo
     */
    public function cambiarEstado(Request $request, $id)
    {
        $grupo = Grupo::findOrFail($id);

        $request->validate([
            'estado' => 'required|in:planificado,con_profesor,con_aula,activo,cancelado'
        ]);

        try {
            // Validaciones específicas por estado
            if ($request->estado === 'activo' && !$grupo->puedeSerActivo()) {
                return back()->with('error', 
                    'El grupo necesita profesor, aula y capacidad disponible para activarse.'
                );
            }

            if ($request->estado === 'cancelado' && !$grupo->puedeSerCancelado()) {
                return back()->with('error', 
                    'No se puede cancelar un grupo con estudiantes inscritos.'
                );
            }

            $grupo->update(['estado' => $request->estado]);

            return back()->with('success', 'Estado del grupo actualizado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }

    /**
     * Determina el estado inicial del grupo basado en los datos
     */
    private function determinarEstadoInicial(Request $request)
    {
        if ($request->profesor_id && $request->aula_id) {
            return 'activo';
        } elseif ($request->profesor_id) {
            return 'con_profesor';
        } elseif ($request->aula_id) {
            return 'con_aula';
        } else {
            return 'planificado';
        }
    }
}