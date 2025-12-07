<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Preregistro;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Mostrar lista de usuarios/alumnos
     */
    public function index(Request $request)
    {

    // Obtener parámetros de búsqueda
    $search = $request->input('search');
    $tipo = $request->input('tipo', 'todos');
    $status = $request->input('status', 'todos');
    
    // Construir consulta base - CAMBIOS AQUÍ
    $query = Usuario::query()
        ->withCount([
            'preregistros', // Esto creará 'preregistros_count'
            'preregistros as preregistros_activos_count' => function($q) {
                $q->whereIn('estado', ['asignado', 'cursando']);
            },
            'preregistros as preregistros_finalizados_count' => function($q) {
                $q->where('estado', 'finalizado');
            }
        ])
        ->with(['preregistros' => function($q) {
            $q->whereIn('estado', ['asignado', 'cursando'])
              ->with(['periodo', 'grupoAsignado'])
              ->latest();
        }]);
        
        // Aplicar filtro de búsqueda
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre_completo', 'LIKE', "%{$search}%")
                  ->orWhere('correo_personal', 'LIKE', "%{$search}%")
                  ->orWhere('correo_institucional', 'LIKE', "%{$search}%")
                  ->orWhere('numero_control', 'LIKE', "%{$search}%");
            });
        }
        
        // Filtrar por tipo de usuario
        if ($tipo === 'interno') {
            $query->whereNotNull('numero_control');
        } elseif ($tipo === 'externo') {
            $query->whereNull('numero_control');
        }
        
        // Filtrar por status (activos vs inactivos)
        if ($status === 'activos') {
            $query->whereHas('preregistros', function($q) {
                $q->whereIn('estado', ['asignado', 'cursando']);
            });
        } elseif ($status === 'inactivos') {
            $query->whereDoesntHave('preregistros', function($q) {
                $q->whereIn('estado', ['asignado', 'cursando']);
            });
        }
        
        // Ordenar
        $query->orderBy('nombre_completo');
        
        // Paginar
        $usuarios = $query->paginate(20);
        
        // Estadísticas generales
        $estadisticas = [
            'total' => Usuario::count(),
            'internos' => Usuario::whereNotNull('numero_control')->count(),
            'externos' => Usuario::whereNull('numero_control')->count(),
            'activos' => Usuario::whereHas('preregistros', function($q) {
                $q->whereIn('estado', ['asignado', 'cursando']);
            })->count(),
            'con_preregistro' => Usuario::has('preregistros')->count(),
        ];
        
        return view('coordinador.usuarios.index', compact('usuarios', 'estadisticas'));
    }
    
    /**
     * Mostrar detalles de un usuario específico
     */
    public function show($id)
    {
        $usuario = Usuario::with([
            'preregistros' => function($query) {
                $query->with(['periodo', 'grupoAsignado', 'grupoAsignado.horario'])
                      ->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);
        
        // Calcular estadísticas del usuario
        $preregistros = $usuario->preregistros;
        
        $estadisticas = [
            'total_preregistros' => $preregistros->count(),
            'preregistros_activos' => $preregistros->whereIn('estado', ['asignado', 'cursando'])->count(),
            'preregistros_finalizados' => $preregistros->where('estado', 'finalizado')->count(),
            'preregistros_cancelados' => $preregistros->where('estado', 'cancelado')->count(),
            'preregistros_pendientes' => $preregistros->where('estado', 'pendiente')->count(),
            'niveles_cursados' => $preregistros->where('estado', 'finalizado')
                                               ->pluck('nivel_solicitado')
                                               ->unique()
                                               ->sort()
                                               ->values(),
            'ultimo_preregistro' => $preregistros->first(),
            'periodos_inscritos' => $preregistros->pluck('periodo.nombre')->unique()->filter(),
        ];
        
        // Obtener el periodo actual si está activo
        $periodoActual = Periodo::where('estado', 'en_curso')->first();
        $preregistroActual = null;
        
        if ($periodoActual) {
            $preregistroActual = $usuario->preregistros()
                ->where('periodo_id', $periodoActual->id)
                ->whereIn('estado', ['asignado', 'cursando'])
                ->first();
        }
        
        return view('coordinador.usuarios.show', compact('usuario', 'estadisticas', 'preregistroActual'));
    }
    
    /**
     * Mostrar formulario para editar usuario (solo información básica)
     */
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        return view('coordinador.usuarios.edit', compact('usuario'));
    }
    
    /**
     * Actualizar información básica del usuario
     * USANDO LOS MISMOS CAMPOS QUE EL REGISTER
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'correo_personal' => 'required|email|max:255|unique:usuarios,correo_personal,' . $id,
            'numero_telefonico' => 'nullable|string|max:20',
            'genero' => 'nullable|in:M,F,Otro',
            'fecha_nacimiento' => 'nullable|date',
            'tipo_usuario' => 'required|in:interno,externo',
            'correo_institucional' => 'nullable|email|max:255|unique:usuarios,correo_institucional,' . $id,
            'numero_control' => 'nullable|string|max:50|unique:usuarios,numero_control,' . $id,
            'especialidad' => 'nullable|string|max:100',
        ]);
        
        try {
            // Si cambia a externo, limpiar campos de interno
            if ($validated['tipo_usuario'] === 'externo') {
                $validated['numero_control'] = null;
                $validated['correo_institucional'] = null;
                $validated['especialidad'] = null;
            }
            
            // Convertir fecha si está presente
            if (isset($validated['fecha_nacimiento'])) {
                $validated['fecha_nacimiento'] = \Carbon\Carbon::parse($validated['fecha_nacimiento'])->format('Y-m-d');
            }
            
            $usuario->update($validated);
            
            Log::info('Usuario actualizado por coordinador', [
                'usuario_id' => $usuario->id,
                'nombre' => $usuario->nombre_completo,
                'coordinador_id' => auth()->guard('coordinador')->user()->id
            ]);
            
            return redirect()->route('coordinador.usuarios.show', $usuario->id)
                ->with('success', 'Información del usuario actualizada correctamente');
                
        } catch (\Exception $e) {
            Log::error('Error al actualizar usuario: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }
    
    /**
     * Método para actualizar contraseña (opcional)
     */
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $usuario = Usuario::findOrFail($id);
        $usuario->update([
            'contraseña' => Hash::make($request->password)
        ]);
        
        return back()->with('success', 'Contraseña actualizada correctamente');
    }
    
    /**
     * Activar/Desactivar usuario (soft delete)
     */
    public function toggleStatus($id)
    {
        $usuario = Usuario::findOrFail($id);
        
        try {
            if ($usuario->deleted_at) {
                // Reactivar
                $usuario->restore();
                $message = 'Usuario reactivado correctamente';
                $logAction = 'Usuario reactivado';
            } else {
                // Desactivar
                $usuario->delete();
                $message = 'Usuario desactivado correctamente';
                $logAction = 'Usuario desactivado';
            }
            
            Log::info($logAction, [
                'usuario_id' => $usuario->id,
                'nombre' => $usuario->nombre_completo,
                'coordinador_id' => auth()->guard('coordinador')->user()->id
            ]);
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del usuario: ' . $e->getMessage());
            return back()->with('error', 'Error al cambiar el estado del usuario');
        }
    }
    
    /**
     * Exportar usuarios a CSV
     */
    public function export(Request $request)
{
    try {
        // Obtener los mismos filtros que en index
        $search = $request->input('search');
        $tipo = $request->input('tipo', 'todos');
        
        $query = Usuario::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre_completo', 'LIKE', "%{$search}%")
                  ->orWhere('correo_personal', 'LIKE', "%{$search}%")
                  ->orWhere('correo_institucional', 'LIKE', "%{$search}%")
                  ->orWhere('numero_control', 'LIKE', "%{$search}%");
            });
        }
        
        if ($tipo === 'interno') {
            $query->whereNotNull('numero_control');
        } elseif ($tipo === 'externo') {
            $query->whereNull('numero_control');
        }
        
        $usuarios = $query->withCount('preregistros')
            ->orderBy('nombre_completo')
            ->get();
        
        $filename = "usuarios_" . date('Y-m-d_H-i-s') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($usuarios) {
            // Abrir output stream
            $output = fopen('php://output', 'w');
            
            // Agregar BOM para UTF-8 en Excel
            fwrite($output, "\xEF\xBB\xBF");
            
            // Encabezados
            $headers = [
                'ID', 
                'Nombre Completo', 
                'Correo Personal', 
                'Correo Institucional',
                'Número de Control', 
                'Tipo de Usuario', 
                'Género',
                'Fecha de Nacimiento', 
                'Especialidad/Carrera', 
                'Total Preregistros', 
                'Fecha de Registro', 
            ];
            
            fputcsv($output, $headers);
            
            // Datos
            foreach ($usuarios as $usuario) {
                // Convertir fecha_nacimiento si existe
                $fechaNacimiento = '';
                if ($usuario->fecha_nacimiento) {
                    try {
                        $fechaNacimiento = \Carbon\Carbon::parse($usuario->fecha_nacimiento)->format('d/m/Y');
                    } catch (\Exception $e) {
                        $fechaNacimiento = $usuario->fecha_nacimiento;
                    }
                }
                
                $row = [
                    $usuario->id,
                    $usuario->nombre_completo,
                    $usuario->correo_personal,
                    $usuario->correo_institucional ?? '',
                    $usuario->numero_control ?? '',
                    $usuario->tipo_usuario,
                    $usuario->genero ?? '',
                    $fechaNacimiento,
                    $usuario->especialidad ?? '',
                    $usuario->preregistros_count ?? 0,
                    $usuario->created_at->format('d/m/Y H:i')
                ];
                
                fputcsv($output, $row);
            }
            
            fclose($output);
        };
        
        return response()->stream($callback, 200, $headers);
        
    } catch (\Exception $e) {
        Log::error('Error al exportar usuarios: ' . $e->getMessage());
        
        // Si estás en desarrollo, muestra el error
        if (app()->environment('local')) {
            return response("Error al generar CSV: " . $e->getMessage(), 500);
        }
        
        // En producción, redirige con error
        return redirect()->route('coordinador.usuarios.index')
            ->with('error', 'Error al exportar el archivo CSV');
    }
}
    
    /**
     * Obtener historial de preregistros para modal (AJAX)
     */
    public function historial($id)
    {
        $usuario = Usuario::findOrFail($id);
        
        $preregistros = $usuario->preregistros()
            ->with(['periodo', 'grupoAsignado'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $html = view('coordinador.usuarios.partials.historial', compact('preregistros'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'total' => $preregistros->count()
        ]);
    }
    
    /**
     * Buscar usuarios rápido (para autocompletado)
     */
    public function search(Request $request)
    {
        $search = $request->input('q');
        
        $usuarios = Usuario::where('nombre_completo', 'LIKE', "%{$search}%")
            ->orWhere('numero_control', 'LIKE', "%{$search}%")
            ->orWhere('correo_personal', 'LIKE', "%{$search}%")
            ->limit(10)
            ->get(['id', 'nombre_completo', 'numero_control', 'correo_personal']);
        
        return response()->json($usuarios);
    }
}