<?php

namespace App\Http\Controllers;


use App\Models\Preregistro;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PreinscripcionController extends Controller
{
    public function store(Request $request, Preregistro $preregistro)
    {
        $request->validate([
            'grupo_id' => [
                'required',
                'exists:grupos,id',
            ],
        ]);

        $grupo = Grupo::findOrFail($request->id_grupo);

        // a. El pre-registro debe estar en estado 'preregistrado' (pendiente de asignación)
        if ($preregistro->estado !== 'preregistrado') {
            return back()->with('error', 'El pre-registro ya fue asignado o tiene un estado diferente.');
        }

        // b. El nivel solicitado debe coincidir con el nivel del grupo
        if ($preregistro->nivel_solicitado !== $grupo->nivel_ingles) {
            return back()->with('error', 'El nivel solicitado por el alumno no coincide con el nivel del grupo.');
        }

        // c. Verificar cupo actual del grupo
        // Nota: Asumo que 'estudiantes_inscritos' se mantiene actualizado por otras operaciones
        if ($grupo->estudiantes_inscritos >= $grupo->capacidad_maxima) {
            return back()->with('error', 'El grupo seleccionado ha alcanzado su capacidad máxima.');
        }

        // 3. Procesar la asignación dentro de una transacción
        try {
            DB::beginTransaction();

            // Actualizar el pre-registro
            $preregistro->grupo_asignado_id = $grupo->id;
            $preregistro->estado = 'asignado'; // Cambiar el estado a 'asignado'
            $preregistro->save();

            // Aumentar el contador de inscritos en la tabla 'grupos'
            // Usa el incremento de la DB para evitar problemas de concurrencia
            $grupo->increment('estudiantes_inscritos');

            DB::commit();

            return redirect()->route('coordinador.preregistros.show', $preregistro->id)
                ->with('success', 'Grupo ' . $grupo->letra_grupo . ' asignado con éxito. El estudiante ha sido notificado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al intentar asignar el grupo: ' . $e->getMessage());
        }
    }
}
