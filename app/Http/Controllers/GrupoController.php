<?php

namespace App\Http\Controllers;
use App\Models\Grupo;
use App\Models\Profesor;
use App\Models\Aula;
use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Coordinador;
use Illuminate\Support\Facades\Auth; // Esta es la línea clave que falta
use Illuminate\Validation\Rule;

class GrupoController extends Controller
{
    public function index()
    {
        $grupos = Grupo::with(['profesor', 'aula', 'horario'])
                    ->withCount(['inscripciones as alumnos_count' => function($query) {
                        $query->where('estatus_inscripcion', 'Aprobada');
                    }])
                    ->orderBy('nivel_ingles')
                    ->orderBy('letra_grupo')
                    ->get();
        
        return view('coordinador.grupos.index', compact('grupos'));
    }

    public function create()
    {
        $profesores = Profesor::orderBy('nombre_profesor')->get();
        $aulas = Aula::orderBy('edificio')->orderBy('numero_aula')->get();
        $horarios = Horario::where('activo', true)->orderBy('nombre')->get();
        
        return view('coordinador.grupos.create', compact('profesores', 'aulas', 'horarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nivel_ingles' => 'required|integer|between:1,5',
            'letra_grupo' => 'required|string|size:1|alpha',
            'anio' => 'required|integer|digits:4',
            'periodo' => 'required|string|max:20',
            'id_horario' => [
                'required',
                'exists:horarios,id',
                // Validar que el aula no esté ocupada en ese horario
                Rule::unique('grupos')->where(function ($query) use ($request) {
                    return $query->where('id_horario', $request->id_horario)
                                 ->where('id_aula', $request->id_aula);
                }),
                // Validar que el profesor no tenga otro grupo en ese horario
                Rule::unique('grupos')->where(function ($query) use ($request) {
                    return $query->where('id_horario', $request->id_horario)
                                 ->where('rfc_profesor', $request->rfc_profesor);
                })
            ],
            'id_aula' => [
                'required',
                'exists:aulas,id_aula',
                // Validar que el aula no esté ocupada en ese horario
                Rule::unique('grupos')->where(function ($query) use ($request) {
                    return $query->where('id_horario', $request->id_horario)
                                 ->where('id_aula', $request->id_aula);
                })
            ],
            'rfc_profesor' => [
                'required',
                'exists:profesores,rfc_profesor',
                // Validar que el profesor no tenga otro grupo en ese horario
                Rule::unique('grupos')->where(function ($query) use ($request) {
                    return $query->where('id_horario', $request->id_horario)
                                 ->where('rfc_profesor', $request->rfc_profesor);
                })
            ],
            'cupo_minimo' => 'required|integer|min:1',
            'cupo_maximo' => 'required|integer|min:1|gte:cupo_minimo'
        ], $this->validationMessages());

        $coordinador = auth()->guard('coordinador')->user();
        
        if (!$coordinador) {
            return back()->withErrors(['error' => 'No hay coordinador autenticado']);
        }

        try {
            $grupo = Grupo::create([
                'nivel_ingles' => $validated['nivel_ingles'],
                'letra_grupo' => strtoupper($validated['letra_grupo']),
                'anio' => $validated['anio'],
                'periodo' => $validated['periodo'],
                'id_horario' => $validated['id_horario'],
                'id_aula' => $validated['id_aula'],
                'rfc_profesor' => $validated['rfc_profesor'],
                'rfc_coordinador' => $coordinador->rfc_coordinador,
                'cupo_minimo' => $validated['cupo_minimo'],
                'cupo_maximo' => $validated['cupo_maximo'],
            ]);

            return redirect()->route('coordinador.grupos.index')
                ->with('success', 'Grupo creado exitosamente');
                
        } catch (\Exception $e) {
            \Log::error('Error al crear grupo: '.$e->getMessage());
            return back()->withErrors(['error' => 'Error al crear grupo: '.$e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $grupo = Grupo::findOrFail($id);
        $profesores = Profesor::orderBy('nombre_profesor')->get();
        $aulas = Aula::orderBy('edificio')->orderBy('numero_aula')->get();
        $horarios = Horario::where('activo', true)->orderBy('nombre')->get();
        
        return view('coordinador.grupos.edit', compact('grupo', 'profesores', 'aulas', 'horarios'));
    }

      
    public function update(Request $request, $id)
    {
        $grupo = Grupo::findOrFail($id);

        $validated = $request->validate([
            'nivel_ingles' => 'required|integer|between:1,5',
            'letra_grupo' => 'required|string|size:1|alpha',
            'anio' => 'required|integer|digits:4',
            'periodo' => 'required|string|max:20',
            'id_horario' => [
                'required',
                'exists:horarios,id',
                // Validar que el aula no esté ocupada en ese horario (excepto este grupo)
                Rule::unique('grupos')->where(function ($query) use ($request) {
                    return $query->where('id_horario', $request->id_horario)
                                 ->where('id_aula', $request->id_aula);
                })->ignore($grupo->id),
                // Validar que el profesor no tenga otro grupo en ese horario (excepto este)
                Rule::unique('grupos')->where(function ($query) use ($request) {
                    return $query->where('id_horario', $request->id_horario)
                                 ->where('rfc_profesor', $request->rfc_profesor);
                })->ignore($grupo->id)
            ],
            'id_aula' => [
                'required',
                'exists:aulas,id_aula',
                // Validar que el aula no esté ocupada en ese horario (excepto este grupo)
                Rule::unique('grupos')->where(function ($query) use ($request) {
                    return $query->where('id_horario', $request->id_horario)
                                 ->where('id_aula', $request->id_aula);
                })->ignore($grupo->id)
            ],
            'rfc_profesor' => [
                'required',
                'exists:profesores,rfc_profesor',
                // Validar que el profesor no tenga otro grupo en ese horario (excepto este)
                Rule::unique('grupos')->where(function ($query) use ($request) {
                    return $query->where('id_horario', $request->id_horario)
                                 ->where('rfc_profesor', $request->rfc_profesor);
                })->ignore($grupo->id)
            ],
            'cupo_minimo' => 'required|integer|min:1',
            'cupo_maximo' => 'required|integer|min:1|gte:cupo_minimo'
        ], $this->validationMessages());

        $grupo->update([
            'nivel_ingles' => $validated['nivel_ingles'],
            'letra_grupo' => strtoupper($validated['letra_grupo']),
            'anio' => $validated['anio'],
            'periodo' => $validated['periodo'],
            'id_horario' => $validated['id_horario'],
            'id_aula' => $validated['id_aula'],
            'rfc_profesor' => $validated['rfc_profesor'],
            'cupo_minimo' => $validated['cupo_minimo'],
            'cupo_maximo' => $validated['cupo_maximo'],
        ]);

        return redirect()->route('coordinador.grupos.index')
            ->with('success', 'Grupo actualizado exitosamente');
    }

    // Mensajes de validación personalizados
    protected function validationMessages()
    {
        return [
            'id_horario.unique' => 'El aula ya está ocupada en este horario o el profesor ya tiene una clase asignada en este horario',
            'id_aula.unique' => 'El aula ya está ocupada en este horario',
            'rfc_profesor.unique' => 'El profesor ya tiene una clase asignada en este horario',
        ];
    }

   
    public function destroy($id)
    {
        $grupo = Grupo::findOrFail($id);
    
        // Verificar si el grupo tiene alumnos inscritos
        if ($grupo->tieneAlumnosInscritos()) {
            return redirect()->route('coordinador.grupos.index')
                ->with('error', 'No se puede eliminar el grupo porque ya tiene alumnos inscritos');
        }
    
        $grupo->delete();
    
        return redirect()->route('coordinador.grupos.index')
            ->with('success', 'Grupo eliminado exitosamente');
    }
  
      // Mostrar grupos eliminados
      public function trashed()
      {
          $grupos = Grupo::onlyTrashed()
                      ->with(['profesor', 'aula', 'horario'])
                      ->orderBy('deleted_at', 'desc')
                      ->get();
          
          return view('coordinador.grupos.eliminados', compact('grupos'));
      }
  
      // Restaurar grupo eliminado
      public function restore($id)
      {
          $grupo = Grupo::onlyTrashed()->findOrFail($id);
          $grupo->restore();
  
          return redirect()->route('coordinador.grupos.index')
              ->with('success', 'Grupo restaurado exitosamente');
      }
  
      public function forceDelete($id)
      {
          $grupo = Grupo::onlyTrashed()->findOrFail($id);
      
          // Verificar si el grupo tiene alumnos inscritos
          if ($grupo->tieneAlumnosInscritos()) {
              return redirect()->route('coordinador.grupos.eliminados')
                  ->with('error', 'No se puede eliminar permanentemente el grupo porque tiene alumnos inscritos');
          }
      
          $grupo->forceDelete();
      
          return redirect()->route('coordinador.grupos.eliminados')
              ->with('success', 'Grupo eliminado permanentemente');
      }
  
  }