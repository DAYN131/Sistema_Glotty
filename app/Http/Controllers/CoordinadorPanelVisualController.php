<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use App\Models\Grupo;
use App\Models\Aula;
use App\Models\HorarioPeriodo;
use Illuminate\Http\Request;

class CoordinadorPanelVisualController extends Controller
{
    public function index()
    {
        $periodoActivo = Periodo::activo()->first();
        
        if (!$periodoActivo) {
            return view('coordinador.panel-visual', [
                'periodoActivo' => null,
                'horarios' => [],
                'edificios' => []
            ]);
        }

        // Obtener todos los horarios del periodo activo
        $horariosPeriodo = HorarioPeriodo::where('periodo_id', $periodoActivo->id)
            ->where('activo', true)
            ->with(['horarioBase', 'grupos.aula', 'grupos.profesor'])
            ->get();

        // Estructurar datos para la vista
        $horarios = [];
        foreach ($horariosPeriodo as $hp) {
            $horarioBase = $hp->horarioBase;
            
            if (!$horarioBase) {
                continue; // Saltar si no tiene horario base
            }

            // Procesar días del horario
            $diasArray = $this->procesarDiasHorario($horarioBase->dias);

            // Procesar grupos de este horario
            $gruposData = [];
            foreach ($hp->grupos as $grupo) {
                $ocupacion = $grupo->capacidad_maxima > 0 ? 
                    round(($grupo->estudiantes_inscritos / $grupo->capacidad_maxima) * 100) : 0;
                
                $gruposData[] = [
                    'id' => $grupo->id,
                    'nombre_grupo' => 'Grupo ' . $grupo->nivel_ingles . $grupo->letra_grupo,
                    'aula' => $grupo->aula ? $grupo->aula->edificio . ' - ' . $grupo->aula->numero_aula : 'Sin aula',
                    'edificio' => $grupo->aula ? $grupo->aula->edificio : 'N/A',
                    'profesor' => $grupo->profesor ? 
                        $grupo->profesor->nombre_profesor . ' ' . $grupo->profesor->apellidos_profesor : 
                        'Sin asignar',
                    'estudiantes_inscritos' => $grupo->estudiantes_inscritos,
                    'capacidad' => $grupo->capacidad_maxima,
                    'ocupacion' => $ocupacion,
                    'estado' => $grupo->estado,
                    'dias' => $diasArray
                ];
            }

            $horarios[] = [
                'id' => $horarioBase->id,
                'rango' => \Carbon\Carbon::parse($horarioBase->hora_inicio)->format('H:i') . ' - ' . 
                          \Carbon\Carbon::parse($horarioBase->hora_fin)->format('H:i'),
                'tipo' => $horarioBase->tipo,
                'dias' => $diasArray,
                'grupos' => $gruposData
            ];
        }

        // Ordenar por hora de inicio
        usort($horarios, function($a, $b) {
            $horaA = \Carbon\Carbon::parse(explode(' - ', $a['rango'])[0]);
            $horaB = \Carbon\Carbon::parse(explode(' - ', $b['rango'])[0]);
            return $horaA->gt($horaB) ? 1 : -1;
        });

        // Obtener lista de edificios para filtro
        $edificios = Aula::distinct()->pluck('edificio')->toArray();

        return view('coordinador.panel-visual', compact(
            'periodoActivo', 'horarios', 'edificios'
        ));
    }

    /**
     * Procesa los días del horario desde diferentes formatos
     */
    private function procesarDiasHorario($dias)
    {
        if (is_array($dias)) {
            return $dias;
        }

        if (is_string($dias)) {
            // Intentar decodificar JSON
            $decoded = json_decode($dias, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            
            // Si no es JSON, separar por comas
            return array_map('trim', explode(',', $dias));
        }

        return [];
    }

    /**
     * API para obtener datos del panel visual (por si quieres AJAX después)
     */
    public function apiPanelVisual()
    {
        $periodoActivo = Periodo::activo()->first();
        
        if (!$periodoActivo) {
            return response()->json([
                'success' => false,
                'message' => 'No hay periodo activo'
            ]);
        }

        $horariosPeriodo = HorarioPeriodo::where('periodo_id', $periodoActivo->id)
            ->where('activo', true)
            ->with(['horarioBase', 'grupos.aula', 'grupos.profesor'])
            ->get();

        $data = [
            'periodo' => $periodoActivo->nombre_periodo, // ✅ CORREGIDO: nombre_periodo
            'horarios' => []
        ];

        foreach ($horariosPeriodo as $hp) {
            $horarioBase = $hp->horarioBase;
            
            if (!$horarioBase) {
                continue;
            }
            
            $diasArray = $this->procesarDiasHorario($horarioBase->dias);

            $data['horarios'][] = [
                'id' => $horarioBase->id,
                'rango' => \Carbon\Carbon::parse($horarioBase->hora_inicio)->format('H:i') . ' - ' . 
                          \Carbon\Carbon::parse($horarioBase->hora_fin)->format('H:i'),
                'tipo' => $horarioBase->tipo,
                'dias' => $diasArray,
                'grupos' => $hp->grupos->map(function($grupo) use ($diasArray) {
                    $ocupacion = $grupo->capacidad_maxima > 0 ? 
                        round(($grupo->estudiantes_inscritos / $grupo->capacidad_maxima) * 100) : 0;
                    
                    return [
                        'id' => $grupo->id,
                        'nombre' => 'Grupo ' . $grupo->nivel_ingles . $grupo->letra_grupo,
                        'aula' => $grupo->aula ? $grupo->aula->edificio . ' - ' . $grupo->aula->numero_aula : 'Sin aula',
                        'edificio' => $grupo->aula ? $grupo->aula->edificio : 'N/A',
                        'profesor' => $grupo->profesor ? 
                            $grupo->profesor->nombre_profesor . ' ' . $grupo->profesor->apellidos_profesor : 
                            'Sin asignar',
                        'estudiantes_inscritos' => $grupo->estudiantes_inscritos,
                        'capacidad' => $grupo->capacidad_maxima,
                        'ocupacion' => $ocupacion,
                        'estado' => $grupo->estado,
                        'dias' => $diasArray
                    ];
                })->toArray()
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}