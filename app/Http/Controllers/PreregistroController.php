<?php

namespace App\Http\Controllers;

use App\Models\Preregistro;
use App\Models\Grupo;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

// Asume que este controlador usa un middleware de autenticación y autorización
class PreregistroController extends Controller
{
    /**
     * Muestra todos los pre-registros con filtros.
     */
    public function index(Request $request)
    {
        $query = Preregistro::with(['usuario', 'periodo', 'horarioSolicitado', 'grupoAsignado'])
            ->orderBy('created_at', 'desc');

        // Filtro por estado
        if ($request->has('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        } else {
            // Por defecto, mostrar solo los pendientes y asignados
            $query->whereIn('estado', [Preregistro::ESTADO['preregistrado'], Preregistro::ESTADO['asignado']]);
        }

        $preregistros = $query->paginate(25);
        $estados = Preregistro::ESTADO;
        $periodos = Periodo::all();

        return view('coordinador.preregistros.index', compact('preregistros', 'estados', 'periodos'));
    }

    /**
     * Muestra los detalles de un pre-registro para su gestión.
     */
    public function show(Preregistro $preregistro)
    {
        // Grupos disponibles para el nivel solicitado y el período del pre-registro
        $gruposDisponibles = Grupo::where('periodo_id', $preregistro->periodo_id)
            ->where('nivel_ingles', $preregistro->nivel_solicitado)
            // Filtra grupos que aún tienen cupo (inscritos < capacidad)
            ->whereRaw('estudiantes_inscritos < capacidad_maxima')
            ->get();

        return view('coordinador.preregistros.show', compact('preregistro', 'gruposDisponibles'));
    }


    /**
     * Asigna un grupo a un preregistro existente y actualiza el estado.
     */
    public function asignarGrupo(Request $request, Preregistro $preregistro)
    {
        // 1. Validar el grupo
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
        ]);

        $grupo = Grupo::findOrFail($request->grupo_id);

        // 2. Verificaciones de Lógica de Negocio
        if ($preregistro->estado !== Preregistro::ESTADO['preregistrado']) {
            return back()->with('error', '❌ El pre-registro no está pendiente de asignación.');
        }

        if ($preregistro->nivel_solicitado !== $grupo->nivel_ingles) {
            return back()->with('error', '❌ El nivel solicitado no coincide con el nivel del grupo.');
        }

        if ($grupo->estudiantes_inscritos >= $grupo->capacidad_maxima) {
            return back()->with('error', '❌ El grupo ha alcanzado su cupo máximo. Selecciona otro.');
        }

        // 3. Procesar la asignación dentro de una transacción
        try {
            DB::beginTransaction();

            // Actualizar el pre-registro
            $preregistro->grupo_asignado_id = $grupo->id;
            $preregistro->estado = Preregistro::ESTADO['asignado']; // Cambiar el estado a 'asignado'
            $preregistro->save();

            // Aumentar el contador de inscritos en la tabla 'grupos'
            $grupo->increment('estudiantes_inscritos');

            DB::commit();

            return redirect()->route('coordinador.preregistros.show', $preregistro->id)
                ->with('success', '✅ Grupo ' . $grupo->letra_grupo . ' asignado con éxito. Estado: ASIGNADO.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Error al intentar asignar el grupo: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza el estado de pago de un pre-registro.
     */
    public function actualizarPago(Request $request, Preregistro $preregistro)
    {
        $request->validate([
            'pagado' => ['required', Rule::in(array_keys(Preregistro::PAGO))],
        ]);

        $nuevoEstadoPago = $request->pagado;
        $estadoActual = $preregistro->estado;

        try {
            DB::beginTransaction();

            $preregistro->pagado = $nuevoEstadoPago;

            // Lógica para cambiar el estado de INSCRIPCIÓN al estar pagado
            // Si el alumno paga y ya tiene grupo asignado, su estado final es 'cursando'.
            if ($nuevoEstadoPago === Preregistro::PAGO['pagado'] && $preregistro->grupo_asignado_id !== null) {
                $preregistro->estado = Preregistro::ESTADO['cursando'];
            }

            // Si el coordinador lo pone como "pendiente", revierte a asignado si estaba cursando
            if ($nuevoEstadoPago === Preregistro::PAGO['pendiente'] && $estadoActual === Preregistro::ESTADO['cursando']) {
                $preregistro->estado = Preregistro::ESTADO['asignado'];
            }

            $preregistro->save();

            DB::commit();

            return back()->with('success', '✅ Estado de pago y de inscripción actualizados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Error al actualizar el estado de pago: ' . $e->getMessage());
        }
    }
}
