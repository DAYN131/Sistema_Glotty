<?php

namespace App\Http\Controllers;

use App\Models\Preregistro;
use App\Models\Periodo;
use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreregistroController extends Controller
{
    /**
     * Muestra el formulario de preregistro
     */
    public function create()
    {
        // Obtener periodo activo
        $periodoActivo = Periodo::where('activo', true)->first();
        
        if (!$periodoActivo) {
            return redirect()->route('alumno.dashboard')
                ->with('error', '❌ No hay un periodo activo para preregistro.');
        }

        // Obtener horarios activos
        $horarios = Horario::where('activo', true)->get();
        
        // Verificar si ya tiene preregistro activo
        $preregistroExistente = Preregistro::where('usuario_id', Auth::id())
            ->where('periodo_id', $periodoActivo->id)
            ->whereIn('estado', ['preregistrado', 'asignado', 'cursando'])
            ->first();

        if ($preregistroExistente) {
            return redirect()->route('alumno.dashboard')
                ->with('info', 'ℹ️ Ya tienes un preregistro activo para este periodo.');
        }

        return view('alumno.preregistro.create', compact('periodoActivo', 'horarios'));
    }

    /**
     * Almacena un nuevo preregistro
     */
    public function store(Request $request)
    {
        // Obtener periodo activo
        $periodoActivo = Periodo::where('activo', true)->first();
        
        if (!$periodoActivo) {
            return back()->with('error', '❌ No hay un periodo activo para preregistro.');
        }

        // Validar que no tenga preregistro activo
        $preregistroExistente = Preregistro::where('usuario_id', Auth::id())
            ->where('periodo_id', $periodoActivo->id)
            ->whereIn('estado', ['preregistrado', 'asignado', 'cursando'])
            ->first();

        if ($preregistroExistente) {
            return redirect()->route('alumno.dashboard')
                ->with('error', '❌ Ya tienes un preregistro activo para este periodo.');
        }

        $validatedData = $request->validate([
            'nivel_solicitado' => 'required|integer|between:1,5',
            'horario_solicitado_id' => 'required|exists:horarios,id',
            'semestre_carrera' => 'nullable|string|max:50',
        ]);

        try {
            Preregistro::create([
                'usuario_id' => Auth::id(),
                'periodo_id' => $periodoActivo->id,
                'nivel_solicitado' => $validatedData['nivel_solicitado'],
                'horario_solicitado_id' => $validatedData['horario_solicitado_id'],
                'semestre_carrera' => $validatedData['semestre_carrera'],
                'estado' => Preregistro::ESTADO['preregistrado'],
                'pagado' => Preregistro::PAGO['pendiente'],
            ]);

            return redirect()->route('alumno.preregistro.index')
                ->with('success', '✅ Preregistro realizado exitosamente. Serás asignado a un grupo próximamente.');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al realizar el preregistro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Muestra los preregistros del alumno
     */
    public function index()
    {
        $preregistros = Preregistro::with(['periodo', 'horarioSolicitado', 'grupoAsignado'])
            ->where('usuario_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('alumno.preregistro.index', compact('preregistros'));
    }

    /**
     * Muestra un preregistro específico
     */
    public function show($id)
    {
        $preregistro = Preregistro::with(['periodo', 'horarioSolicitado', 'grupoAsignado', 'grupoAsignado.profesor', 'grupoAsignado.aula'])
            ->where('usuario_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        return view('alumno.preregistro.show', compact('preregistro'));
    }

    /**
     * Cancela un preregistro
     */
    public function cancel($id)
    {
        try {
            $preregistro = Preregistro::where('usuario_id', Auth::id())
                ->where('id', $id)
                ->where('estado', Preregistro::ESTADO['preregistrado'])
                ->firstOrFail();

            $preregistro->update([
                'estado' => Preregistro::ESTADO['cancelado']
            ]);

            return redirect()->route('alumno.preregistro.index')
                ->with('success', '✅ Preregistro cancelado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', '❌ No se pudo cancelar el preregistro: ' . $e->getMessage());
        }
    }
}