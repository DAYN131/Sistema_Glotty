<?php
namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumnoInscripcionController extends Controller{

    
    public function index()
    {
        $alumno = Auth::guard('alumno')->user();
        
        if (!$alumno) {
            return redirect()->route('login')->withErrors(['error' => 'Debes iniciar sesión como alumno']);
        }

        $inscripciones = $alumno->inscripciones()
            ->with(['grupo.horario', 'grupo.profesor', 'grupo.aula'])
            ->latest('fecha_inscripcion')
            ->get();
            
        return view('alumno.inscripciones.index', compact('inscripciones'));
    }

    public function create()
    {
        $alumno = Auth::guard('alumno')->user();
        if (!$alumno) return redirect()->route('login');

        // Verificar inscripción activa (pendiente o aprobada en cualquier grupo)
        $inscripcionActiva = Inscripcion::where('no_control', $alumno->no_control)
        ->whereIn('estatus_inscripcion', ['Pendiente', 'Aprobada'])
        ->exists();

        if ($inscripcionActiva) {
        return redirect()->route('alumno.inscripciones.index')
            ->with('error', 'Ya tienes una inscripción activa (pendiente o aprobada). No puedes inscribirte a otro grupo hasta que se resuelva.');
        }

        $nivelRecomendado = $alumno->nivelRecomendado();
            
        // Obtener grupos del nivel recomendado inicialmente
        $gruposDisponibles = $this->obtenerGruposPorNivel($nivelRecomendado);

        return view('alumno.inscripciones.create', [
            'gruposDisponibles' => $gruposDisponibles,
            'nivelRecomendado' => $nivelRecomendado,
        ]);
    }
    
    public function gruposPorNivel(Request $request)
    {
        try {
            $nivel = $request->query('nivel', 1);
            
            $grupos = $this->obtenerGruposPorNivel($nivel);
            
            return response()->json([
                'html' => view('alumno.inscripciones.partials.grupos', [
                    'grupos' => $grupos,
                    'nivel' => $nivel
                ])->render(),
                'grupos' => $grupos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar grupos: ' . $e->getMessage()
            ], 500);
        }
    }

    private function obtenerGruposPorNivel($nivel)
    {
        return Grupo::where('nivel_ingles', $nivel)
            ->with([
                'horario:id,nombre,hora_inicio,hora_fin,tipo,nombre',
                'profesor:rfc_profesor,nombre_profesor,apellidos_profesor',
                'aula:id_aula,edificio,numero_aula'
            ])
            ->get()
            ->map(function ($grupo) {
                return [
                    'id' => $grupo->id,
                    'nombre_grupo' => "Nivel {$grupo->nivel_ingles}-{$grupo->letra_grupo}",
                    'periodo' => $grupo->periodo, // Asegúrate que este campo existe en la tabla grupos
                    'horario' => $grupo->horario ? 
                        "{$grupo->horario->nombre} ({$grupo->horario->tipo}) {$grupo->horario->hora_inicio} - {$grupo->horario->hora_fin}" : 
                        'Horario no asignado',
                    'profesor' => $grupo->profesor ? 
                        "{$grupo->profesor->nombre_profesor} {$grupo->profesor->apellidos_profesor}" : 
                        'Profesor por asignar',
                    'aula' => $grupo->aula ? 
                        "{$grupo->aula->edificio} - {$grupo->aula->numero_aula}" : 
                        'Aula por asignar',
                    // Cupo teórico (incluye pendientes) para vista alumno
                   'cupo_disponible' => $grupo->cupoDisponibleParaAlumnos()
                ];
            })
            ->toArray();
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_grupo' => 'required|exists:grupos,id',
            'nivel_seleccionado' => 'required|numeric'
        ]);
        
        $alumno = Auth::guard('alumno')->user();
        $grupo = Grupo::findOrFail($validated['id_grupo']);

        // Pasa el objeto grupo completo al método
        $periodoCursado = $this->generarPeriodoCursado($grupo);

        Inscripcion::create([
            'no_control' => $alumno->no_control,
            'id_grupo' => $grupo->id,
            'periodo_cursado' => $periodoCursado,
            'fecha_inscripcion' => now(),
            'estatus_inscripcion' => 'Pendiente',
            'nivel_solicitado' => $validated['nivel_seleccionado']
        ]);

        return redirect()->route('alumno.inscripciones.index')
            ->with('success', 'Inscripción realizada');
    }
    
    protected function generarPeriodoCursado($grupo)
    {
        // Obtener el periodo y año directamente del objeto grupo
        $periodo = $grupo->periodo; // Ej: "Febrero-Junio", "Verano1", etc.
        $anio = $grupo->anio;       // El año está en un campo separado

        // Mapeo de periodos a números
        $periodosNumericos = [
            'Febrero-Junio' => 1,
            'Septiembre-Noviembre' => 2,
            'Verano1' => 3,
            'Verano2' => 4,
            'Invierno' => 5
        ];

        // Obtener el número de periodo (usamos 0 como valor por defecto si no coincide)
        $numPeriodo = $periodosNumericos[$periodo] ?? 0;

        return "{$anio}-{$numPeriodo}";
    }

    public function verCalificaciones()
    {
        $alumno = Auth::guard('alumno')->user();
        
        if (!$alumno) {
            return redirect()->route('login')->withErrors(['error' => 'Debes iniciar sesión como alumno']);
        }
    
        $inscripciones = $alumno->inscripciones()
            ->with([
                'grupo.horario',
                'grupo.profesor',
                'grupo.aula'
            ])
            ->select([
                'id', // Asegúrate de incluir la PK
                'calificacion_parcial_1',
                'calificacion_parcial_2',
                'calificacion_final',
                'id_grupo' // Clave foránea necesaria para la relación
            ])
            ->latest('fecha_inscripcion')
            ->get();
                
        return view('alumno.calificaciones.index', compact('inscripciones'));
    }
    
}