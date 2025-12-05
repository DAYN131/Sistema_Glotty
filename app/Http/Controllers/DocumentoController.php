<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Grupo;
use App\Models\Preregistro;
use App\Models\Periodo;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    /**
     * Muestra el panel de documentos
     */
    public function panel()
    {
        // Obtener grupos con conteo de estudiantes
        $grupos = Grupo::withCount('preregistros')
            ->with(['horario'])
            ->orderBy('nivel_ingles', 'asc')
            ->orderBy('letra_grupo', 'asc')
            ->get();

        // Obtener estudiantes para constancias
        $estudiantes = Preregistro::with('usuario')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($preregistro) {
                // Crear un campo para mostrar en el select
                $preregistro->display_text = 
                    ($preregistro->usuario->nombre_completo ?? 'Sin nombre') . ' - ' . 
                    ($preregistro->usuario->numero_control ?? 'EXTERNO');
                
                return $preregistro;
            });

        return view('coordinador.documentos.panel', [
            'grupos' => $grupos,
            'estudiantes' => $estudiantes,
            'totalGrupos' => $grupos->count(),
            'totalEstudiantes' => $estudiantes->count()
        ]);
    }

    /**
     * Genera una lista de estudiantes de un grupo específico
     */
    public function listaGrupo(Request $request)
    {
        // Validar que se haya seleccionado un grupo
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id'
        ]);

        $grupo = $this->obtenerGrupoCompleto($request->grupo_id);
        
        $data = $this->prepararDatosListaGrupo($grupo);
        
        $pdf = Pdf::loadView('coordinador.documentos.lista-grupo', $data)
                  ->setPaper('a4', 'portrait')
                  ->setOption('defaultFont', 'Arial');

        $nombreArchivo = 'lista-grupo-' . Str::slug($grupo->nombre) . '-' . now()->format('Ymd') . '.pdf';
        return $pdf->download($nombreArchivo);
    }

    /**
     * Versión para previsualizar la lista de grupo
     */
    public function listaGrupoPreview($grupo)  // Cambiado de $grupoId a $grupo
    {
        $grupo = $this->obtenerGrupoCompleto($grupo);  // Cambiado aquí también
        
        $data = $this->prepararDatosListaGrupo($grupo);
        
        $pdf = Pdf::loadView('coordinador.documentos.lista-grupo', $data)
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('lista-grupo-preview.pdf');
    }

    /**
     * Genera constancia individual para un estudiante
     */
    public function constanciaIndividual($preregistroId)
    {
        // Cargar el preregistro con las relaciones CORRECTAS
        $preregistro = Preregistro::with([
            'usuario',
            'grupoAsignado.horario', // Cambiado de 'grupo' a 'grupoAsignado'
            'periodo'
        ])->findOrFail($preregistroId);

        // Meses en español para la fecha
        $meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];

        $data = [
            'estudiante' => $preregistro,
            'usuario' => $preregistro->usuario,
            'grupo' => $preregistro->grupoAsignado, // Cambiado aquí
            'periodo' => $preregistro->periodo,
            'fechaGeneracion' => now()->format('d/m/Y'),
            'codigoConstancia' => 'CONST-' . strtoupper(Str::random(8)),
            'meses' => $meses,
            'nivel_actual' => $preregistro->nivel_solicitado
        ];

        $pdf = Pdf::loadView('coordinador.documentos.constancia-individual', $data)
                ->setPaper('letter', 'portrait')
                ->setOption('defaultFont', 'Times New Roman');

        $nombreArchivo = 'constancia-' . 
                        Str::slug($preregistro->usuario->name ?? 'estudiante') . '-' . 
                        now()->format('Ymd') . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }

    /**
     * Genera reporte de estadísticas
     */
    public function reporteEstadisticas()
    {
        // CORRECCIÓN AQUÍ: Usar 'en_curso' en lugar de 'activo'
        $periodoActivo = Periodo::where('estado', 'en_curso')->first();
        
        $data = $this->generarEstadisticas($periodoActivo);
        
        $pdf = Pdf::loadView('coordinador.documentos.reporte-estadisticas', $data)
                  ->setPaper('a4', 'landscape')
                  ->setOption('defaultFont', 'Arial');

        $nombreArchivo = 'reporte-estadisticas-' . now()->format('Ymd') . '.pdf';
        return $pdf->download($nombreArchivo);
    }

    /**
     * Vista previa del reporte de estadísticas
     */
   public function reporteEstadisticasPreview()
    {
        $periodoActivo = Periodo::where('estado', 'en_curso')->first();
        $data = $this->generarEstadisticas($periodoActivo);
        
        $pdf = Pdf::loadView('coordinador.documentos.reporte-estadisticas', $data)
                ->setPaper('a4', 'portrait'); // Cambiado a vertical

        return $pdf->stream('reporte-estadisticas-preview.pdf');
    
    }

    /**
     * Métodos auxiliares privados
     */
    private function obtenerGrupoCompleto($grupoId)
    {
        return Grupo::with(['preregistros.usuario', 'profesor', 'horario', 'aula'])
            ->findOrFail($grupoId);
    }

    private function prepararDatosListaGrupo($grupo)
    {
        return [
            'grupo' => $grupo,
            'estudiantes' => $grupo->preregistros ?? collect(),
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            'totalEstudiantes' => $grupo->preregistros->count() ?? 0,
            'profesor' => $grupo->profesor,
            'horario' => $grupo->horario,
            'aula' => $grupo->aula,
            'periodo' => $grupo->periodo
        ];
    }

    private function generarEstadisticas($periodo)
    {
        $preregistros = Preregistro::query();
        
        if ($periodo) {
            $preregistros->where('periodo_id', $periodo->id);
        }

        $total = $preregistros->count();
        $porEstado = $preregistros->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $porNivel = $preregistros->selectRaw('nivel_solicitado, count(*) as total')
            ->groupBy('nivel_solicitado')
            ->orderBy('nivel_solicitado')
            ->get();

        $porHorario = $preregistros->selectRaw('horario_preferido_id, count(*) as total')
            ->groupBy('horario_preferido_id')
            ->with('horarioPreferido')
            ->get()
            ->map(function ($item) {
                return [
                    'horario' => $item->horarioPreferido->nombre ?? 'Sin horario',
                    'total' => $item->total
                ];
            });

        return [
            'periodo' => $periodo,
            'totalPreregistros' => $total,
            'porEstado' => $porEstado,
            'porNivel' => $porNivel,
            'porHorario' => $porHorario,
            'fechaGeneracion' => now()->format('d/m/Y H:i')
        ];
    }
}