<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Alumno;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InscripcionController extends Controller
{
    public function store(Request $request)
    {
        $alumno = Auth::user()->alumno;
        
        $request->validate([
            'id_grupo' => 'required|exists:grupos,id',
            'periodo' => 'required|string|max:20'
        ]);

        $grupo = Grupo::findOrFail($request->id_grupo);

        // Verificar que el alumno no esté ya inscrito en este periodo
        $yaInscrito = Inscripcion::where('no_control', $alumno->no_control)
            ->where('periodo', $request->periodo)
            ->exists();

        if ($yaInscrito) {
            return back()->with('error', 'Ya tienes una inscripción para este periodo');
        }

        // Verificar cupo
        $inscritos = Inscripcion::where('id_grupo', $request->id_grupo)
            ->where('periodo', $request->periodo)
            ->where('estatus_inscripcion', 'Aprobado')
            ->count();

        if ($inscritos >= $grupo->cupo_maximo) {
            return back()->with('error', 'El grupo ha alcanzado su cupo máximo');
        }

        // Crear la inscripción
        Inscripcion::create([
            'no_control' => $alumno->no_control,
            'id_grupo' => $request->id_grupo,
            'periodo' => $request->periodo,
            'fecha_inscripcion' => now(),
            'estatus_pago' => 'Pendiente',
            'estatus_inscripcion' => 'Pendiente',
            'nivel_solicitado' => $alumno->nivelRecomendado()
        ]);

        return redirect()->route('alumno.inscripciones')
            ->with('success', 'Solicitud de inscripción enviada. Espera aprobación del coordinador');
    }
}