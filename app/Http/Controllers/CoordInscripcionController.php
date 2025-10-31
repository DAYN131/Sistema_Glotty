<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use Illuminate\Http\Request;

class CoordInscripcionController extends Controller
{
    public function index()
    {
        $pendientes = Inscripcion::with(['alumno', 'grupo.horario', 'grupo.profesor'])
            ->where('estatus_inscripcion', 'Pendiente')
            ->orderBy('fecha_inscripcion', 'asc')
            ->paginate(10);
        
        // Calcular cupo disponible para cada grupo
        $pendientes->each(function ($inscripcion) {
            $inscripcion->grupo->cupo_disponible = $inscripcion->grupo->cupo_maximo - $inscripcion->grupo->inscripciones()
                ->where('estatus_inscripcion', 'Aprobada')
                ->count();
        });
    
        return view('coordinador.inscripciones.index', compact('pendientes'));
    }

    public function aprobar(Inscripcion $inscripcion)
    {
        // Validar que esté pendiente
        if ($inscripcion->estatus_inscripcion !== 'Pendiente') {
            return back()->with('error', 'Solo puedes aprobar inscripciones pendientes');
        }

        // Cupo real (solo aprobados) para decisiones de coordinación
        $cupoDisponible = $inscripcion->grupo->cupoDisponibleReal();
        
        if ($cupoDisponible <= 0) {
            return back()->with('error', 'No hay cupo disponible en este grupo');
        }

        // Aprobar la inscripción
        $inscripcion->update([
            'estatus_inscripcion' => 'Aprobada',
            'fecha_aprobacion' => now()
        ]);

        return back()->with('success', 'Inscripción aprobada correctamente');
    }

    public function inscripcionesAprobadas()
    {
        $inscripciones = Inscripcion::with([
                'alumno', 
                'grupo',
            ])
            ->where('estatus_inscripcion', 'Aprobada')
            ->latest()
            ->get();

        return view('coordinador.inscripciones.aprobadas', compact('inscripciones'));
    }


}