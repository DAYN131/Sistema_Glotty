<?php

namespace App\Http\Controllers;

use App\Models\Preregistro;
use App\Models\Periodo;
use App\Models\HorarioPeriodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreregistroController extends Controller
{
    /**
     * Muestra el formulario de preregistro
     */
    public function create()
    {
        // Obtener periodo activo para preregistros
        $periodoActivo = Periodo::conPreRegistrosActivos()->first();
        
        if (!$periodoActivo) {
            return redirect()->route('alumno.dashboard')
                ->with('error', ' No hay un periodo activo para preregistro en este momento.');
        }

        // Obtener horarios del periodo activo 
        $horarios = HorarioPeriodo::where('periodo_id', $periodoActivo->id)
            ->where('activo', true)
            ->with('horarioBase')
            ->get();

        // Verificar si ya tiene preregistro activo
        $preregistroExistente = Preregistro::where('usuario_id', Auth::id())
            ->where('periodo_id', $periodoActivo->id)
            ->whereIn('estado', ['pendiente', 'asignado', 'cursando'])
            ->first();

        if ($preregistroExistente) {
            return redirect()->route('alumno.dashboard')
                ->with('info', 'ℹ Ya tienes un preregistro activo para este periodo.');
        }

      
        return view('alumno.preregistro.create', compact('periodoActivo', 'horarios'));
    }

    /**
     * Almacena un nuevo preregistro
     */
    public function store(Request $request)
    {
        // Obtener periodo activo para preregistros - CORREGIDO: usando el scope correcto
        $periodoActivo = Periodo::conPreRegistrosActivos()->first();
        
        if (!$periodoActivo) {
            return back()->with('error', ' No hay un periodo activo para preregistro.');
        }

        // Validar que no tenga preregistro activo
        $preregistroExistente = Preregistro::where('usuario_id', Auth::id())
            ->where('periodo_id', $periodoActivo->id)
            ->whereIn('estado', ['pendiente', 'asignado', 'cursando'])
            ->first();

        if ($preregistroExistente) {
            return redirect()->route('alumno.dashboard')
                ->with('error', ' Ya tienes un preregistro activo para este periodo.');
        }

        $validatedData = $request->validate([
            'nivel_solicitado' => 'required|integer|between:1,5',
            'horario_preferido_id' => 'required|exists:horarios_periodo,id',
             'semestre_actual' => 'required|integer',
        ]);

        try {
            Preregistro::create([
                'usuario_id' => Auth::id(),
                'periodo_id' => $periodoActivo->id,
                'nivel_solicitado' => $validatedData['nivel_solicitado'],
                'horario_preferido_id' => $validatedData['horario_preferido_id'],
                'semestre_actual' => $validatedData['semestre_actual'],
                'estado' => 'pendiente',
                'pago_estado' => 'pendiente'
            ]);

            return redirect()->route('alumno.preregistro.index')
                ->with('success', ' Preregistro realizado exitosamente. Serás asignado a un grupo próximamente.');

        } catch (\Exception $e) {
            return back()->with('error', ' Error al realizar el preregistro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Muestra los preregistros del alumno
     */
    public function index()
    {
        $preregistros = Preregistro::with([
                'periodo', 
                'horarioPreferido.horarioBase',
                'grupoAsignado'
            ])
            ->where('usuario_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('alumno.preregistro.index', compact('preregistros'));
    }

    /**
     * Muestra un preregistro específico
     */
    public function show(Preregistro $preregistro)
    {
        // Verificar que el preregistro pertenezca al usuario
        if ($preregistro->usuario_id !== Auth::id()) {
            abort(403);
        }

        $preregistro->load([
            'periodo',
            'horarioPreferido.horarioBase', 
            'grupoAsignado.profesor',
            'grupoAsignado.aula'
        ]);

        return view('alumno.preregistro.show', compact('preregistro'));
    }

    public function reactivar(Preregistro $preregistro)
    {
        try {
            // Solo si está cancelado
            if (!$preregistro->estaCancelado()) {
                return redirect()->back()
                    ->with('error', 'Solo se pueden reactivar preregistros cancelados');
            }
            
            // Verificar que el periodo aún esté activo
            if ($preregistro->periodo->estaFinalizado()) {
                return redirect()->back()
                    ->with('error', 'No se puede reactivar porque el periodo ya finalizó');
            }
            
            // Determinar el nuevo estado
            $nuevoEstado = $preregistro->grupoAsignado ? 'asignado' : 'pendiente';
            
            // Reactivar
            $preregistro->update(['estado' => $nuevoEstado]);
            
      
            
            return redirect()->back()
                ->with('success', "Preregistro reactivado exitosamente. Estado: {$nuevoEstado}");
                
        } catch (\Exception $e) {
            Log::error('Error al reactivar preregistro: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al reactivar el preregistro');
        }
    }
}