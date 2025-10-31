<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprobar Inscripciones - Coordinador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Encabezado -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Aprobación de Inscripciones</h1>
                <p class="text-gray-600">Solicitudes pendientes de aprobación</p>
            </div>
            
            <!-- Enlaces a vistas relacionadas -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('coordinador.grupos.index') }}" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition flex items-center">
                    <i class="fas fa-users mr-2 text-blue-500"></i>
                    Ver Grupos
                </a>
                <a href="{{ route('coordinador.horarios.index') }}" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition flex items-center">
                    <i class="fas fa-clock mr-2 text-blue-500"></i>
                    Ver Horarios
                </a>
                <a href="{{ route('coordinador.profesores') }}" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition flex items-center">
                    <i class="fas fa-chalkboard-teacher mr-2 text-blue-500"></i>
                    Ver Profesores
                </a>
                <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Pendientes: {{ $pendientes->total() }}
                </div>
            </div>
        </div>

        <!-- Tarjeta de resumen -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 text-blue-600 rounded-lg mr-4">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-700">
                        Revisa cuidadosamente cada solicitud antes de aprobarla. Verifica que el alumno cumpla con los requisitos y que haya cupo disponible.
                    </p>
                </div>
            </div>
        </div>

  

        <!-- Listado de inscripciones -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alumno</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profesor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periodo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Solicitud</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pendientes as $inscripcion)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">
                                    {{ $inscripcion->alumno->nombre_alumno }} {{ $inscripcion->alumno->apellidos_alumno }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $inscripcion->alumno->no_control }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium">
                                    {{ $inscripcion->grupo->nivel_ingles }}-{{ $inscripcion->grupo->letra_grupo }}
                                </div>
                                <div class="mt-1">
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        {{ $inscripcion->grupo->cupo_disponible > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <i class="fas fa-users mr-1"></i>
                                        {{ $inscripcion->grupo->cupo_disponible }}/{{ $inscripcion->grupo->cupo_maximo }} cupos
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">
                                    {{ $inscripcion->grupo->profesor->nombre_profesor }}
                                </div>
                                <div class="font-medium text-gray-900">
                                    {{ $inscripcion->grupo->profesor->apellidos_profesor }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
    @if($inscripcion->grupo->horario)
        <div class="horario-info">
            @if($inscripcion->grupo->horario->tipo == 'sabado')
                <span>Sábados</span>
            @else
                <span>
                    @if(!empty($inscripcion->grupo->horario->dias))
                        {{ implode(', ', array_map('ucfirst', (array)$inscripcion->grupo->horario->dias)) }}
                    @else
                        Días no definidos
                    @endif
                </span>
            @endif
            <div class="horas">
                {{ $inscripcion->grupo->horario->hora_inicio->format('H:i') }} - 
                {{ $inscripcion->grupo->horario->hora_fin->format('H:i') }}
            </div>
        </div>
    @else
        <span class="text-warning">Sin horario asignado</span>
    @endif
</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    {{ $inscripcion->periodo_cursado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $inscripcion->fecha_inscripcion->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <button type="button" 
                                            onclick="openDetailsModal({{ $inscripcion->id }})"
                                            class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-lg flex items-center transition-colors">
                                        <i class="fas fa-eye mr-2"></i>
                                        Detalles
                                    </button>
                                    
                                    <form action="{{ route('coordinador.inscripciones.aprobar', $inscripcion) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg flex items-center transition-colors">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Aprobar
                                        </button>
                                    </form>
                                </div>
                            </td>

                            
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-check-circle text-4xl mb-3 text-gray-300"></i>
                                <p>No hay inscripciones pendientes de aprobación</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            @if($pendientes->hasPages())
            <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
                {{ $pendientes->links() }}
            </div>
            @endif
        </div>
    </div>





</body>
</html>