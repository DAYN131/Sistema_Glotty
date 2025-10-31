<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Inscripciones - Glotty</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sidebar: '#2c3e50',
                        primary: {
                            light: '#3498db',
                            DEFAULT: '#2980b9',
                            dark: '#1f6ca6'
                        },
                        secondary: {
                            light: '#f8fafc',
                            DEFAULT: '#f1f5f9',
                            dark: '#e2e8f0'
                        }
                    }
                }
            }
        }
    </script>
</head>


@if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
        <p>{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
        <p>{{ session('error') }}</p>
    </div>
@endif


<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Encabezado mejorado -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Historial de Inscripciones</h1>
                <p class="text-gray-600 mt-1">Revisa el estado de tus solicitudes de inscripción</p>
            </div>
            <a href="{{ route('alumno.inscripciones.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                <i class="fas fa-plus-circle mr-2"></i>
                Nueva Inscripción
            </a>
        </div>

        <!-- Tarjetas de resumen mejoradas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Tarjeta Total -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg text-blue-600 mr-4">
                        <i class="fas fa-list-alt"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Inscripciones</p>
                        <p class="text-xl font-semibold">{{ $inscripciones->count() }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta Aprobadas -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg text-green-600 mr-4">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Aprobadas</p>
                        <p class="text-xl font-semibold">{{ $inscripciones->where('estatus_inscripcion', 'Aprobada')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta Pendientes -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg text-yellow-600 mr-4">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Pendientes</p>
                        <p class="text-xl font-semibold">{{ $inscripciones->where('estatus_inscripcion', 'Pendiente')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta Rechazadas/Expiradas -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg text-red-600 mr-4">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Rechazadas/Expiradas</p>
                        <p class="text-xl font-semibold">
                            {{ $inscripciones->whereIn('estatus_inscripcion', ['Expirada'])->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de inscripciones mejorada -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Grupo
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Periodo
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nivel
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Horario
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estatus
                            </th>
                           
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($inscripciones as $inscripcion)
                        <tr class="hover:bg-gray-50 transition-colors">
                  


                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">
                                {{ $inscripcion->grupo->nivel_ingles }}-{{ $inscripcion->grupo->letra_grupo }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-user-tie mr-1"></i>
                                Prof: {{ $inscripcion->grupo->profesor->nombre_profesor ?? 'Sin asignar' }} {{ $inscripcion->grupo->profesor->apellidos_profesor ?? 'Sin asignar' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-door-open mr-1"></i>
                                Aula: {{ $inscripcion->grupo->aula->edificio ?? 'N/A' }}-{{ $inscripcion->grupo->aula->numero_aula ?? 'N/A' }}
                            </div>
                        </td>
                            
                            <!-- Celda Periodo -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ $inscripcion->periodo_cursado }}
                                </span>
                            </td>
                            
                            <!-- Celda Nivel -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                    Nivel {{ $inscripcion->nivel_solicitado }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($inscripcion->grupo->horario)
                                <div class="flex items-start">
                                    <i class="far fa-calendar-alt mt-1 mr-2 text-gray-400"></i>
                                    <div>
                                        <!-- Días formateados -->
                                        <div class="font-medium">
                                            @if($inscripcion->grupo->horario->tipo == 'sabado')
                                                Sábados
                                            @else
                                                {{ implode(', ', array_map('ucfirst', $inscripcion->grupo->horario->dias)) }}
                                            @endif
                                        </div>
                                        
                                        <!-- Horas -->
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $inscripcion->grupo->horario->hora_inicio->format('H:i') }} - 
                                            {{ $inscripcion->grupo->horario->hora_fin->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-red-500">Horario no asignado</span>
                            @endif
                        </td>
                            
                            <!-- Celda Estatus -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'Aprobada' => ['bg-green-100 text-green-800', 'fa-check-circle'],
                                        'Pendiente' => ['bg-yellow-100 text-yellow-800', 'fa-clock'],
                                        'Rechazada' => ['bg-red-100 text-red-800', 'fa-times-circle'],
                                        'Expirada' => ['bg-gray-100 text-gray-800', 'fa-calendar-times']
                                    ];
                                    $currentStatus = $statusClasses[$inscripcion->estatus_inscripcion] ?? ['bg-gray-100 text-gray-800', 'fa-question-circle'];
                                @endphp
                                <span class="px-2 py-1 inline-flex items-center text-xs font-medium rounded-full {{ $currentStatus[0] }}">
                                    <i class="fas {{ $currentStatus[1] }} mr-1"></i>
                                    {{ $inscripcion->estatus_inscripcion }}
                                </span>
                            </td>
                            
                            
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center text-gray-400">
                                    <i class="fas fa-clipboard-list text-4xl mb-3"></i>
                                    <p>No tienes inscripciones registradas</p>
                                    <a href="{{ route('alumno.inscripciones.create') }}" class="text-blue-500 hover:text-blue-700 mt-2">
                                        Realiza tu primera inscripción
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
          
        </div>

        <!-- Panel informativo -->
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Información importante</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p class="mt-1">El coordinador esta a cargo de la aprobacion de tu inscripcion</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>