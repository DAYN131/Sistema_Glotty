<?php
// app/Http/Controllers/DisponibilidadAulaController.php

namespace App\Http\Controllers;

use App\Models\DisponibilidadAula;
use App\Models\Aula;
use App\Models\HorarioPeriodo;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisponibilidadAulaController extends Controller
{
    /**
     * Muestra la disponibilidad de aulas para un período
     */
    public function index(Periodo $periodo)
    {
        $horariosPeriodo = $periodo->horariosPeriodo()
                                ->with(['disponibilidadAulas.aula', 'disponibilidadAulas.grupo'])
                                ->where('activo', true)
                                ->get();

        $aulas = Aula::where('disponible', true)
                    ->orderBy('edificio')
                    ->orderBy('nombre')
                    ->get();

        return view('coordinador.disponibilidad.index', compact('periodo', 'horariosPeriodo', 'aulas'));
    }

    /**
     * Actualiza la disponibilidad de un aula en un horario específico
     */
    public function update(Request $request, Aula $aula, HorarioPeriodo $horarioPeriodo)
    {
        $request->validate([
            'disponible' => 'required|boolean',
            'grupo_id' => 'nullable|exists:grupos,id'
        ]);

        try {
            DB::beginTransaction();

            $disponibilidad = DisponibilidadAula::updateOrCreate(
                [
                    'aula_id' => $aula->id,
                    'horario_periodo_id' => $horarioPeriodo->id
                ],
                [
                    'disponible' => $request->disponible,
                    'grupo_id' => $request->disponible ? null : $request->grupo_id
                ]
            );

            DB::commit();

            return redirect()->back()
                ->with('success', 'Disponibilidad actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar la disponibilidad: ' . $e->getMessage());
        }
    }

    /**
     * Genera disponibilidad inicial para todas las aulas de un período
     */
    public function generarDisponibilidadInicial(Periodo $periodo)
    {
        try {
            DB::beginTransaction();

            $aulas = Aula::where('disponible', true)->get();
            $horariosPeriodo = $periodo->horariosPeriodo()->where('activo', true)->get();

            foreach ($aulas as $aula) {
                foreach ($horariosPeriodo as $horario) {
                    DisponibilidadAula::firstOrCreate(
                        [
                            'aula_id' => $aula->id,
                            'horario_periodo_id' => $horario->id
                        ],
                        [
                            'disponible' => true,
                            'grupo_id' => null
                        ]
                    );
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Disponibilidad inicial generada para ' . $aulas->count() . ' aulas.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al generar disponibilidad: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene aulas disponibles para un horario específico
     */
    public function getAulasDisponibles(HorarioPeriodo $horarioPeriodo)
    {
        $aulasDisponibles = Aula::where('disponible', true)
            ->whereDoesntHave('disponibilidadHorarios', function($query) use ($horarioPeriodo) {
                $query->where('horario_periodo_id', $horarioPeriodo->id)
                      ->where('disponible', false);
            })
            ->orderBy('edificio')
            ->orderBy('nombre')
            ->get();

        return response()->json($aulasDisponibles);
    }
}