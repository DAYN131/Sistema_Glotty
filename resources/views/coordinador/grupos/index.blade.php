{{-- resources/views/coordinador/grupos/index.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Gestión de Grupos - Glotty')
@section('header-title', 'Gestión de Grupos')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Estadísticas Rápidas -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-card">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600 mr-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Grupos</p>
                    <p class="text-xl font-bold text-blue-700">{{ $estadisticas['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-card">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg text-yellow-600 mr-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Planificados</p>
                    <p class="text-xl font-bold text-yellow-700">{{ $estadisticas['planificados'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-card">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg text-green-600 mr-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Activos</p>
                    <p class="text-xl font-bold text-green-700">{{ $estadisticas['activos'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-card">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg text-purple-600 mr-3">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Ocupación</p>
                    <p class="text-xl font-bold text-purple-700">
                        {{ $estadisticas['capacidad_utilizada'] }}/{{ $estadisticas['capacidad_total'] }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Acciones -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Todos los Grupos</h2>
            <p class="text-gray-600">Gestiona los grupos del sistema</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('coordinador.grupos.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                <i class="fas fa-plus-circle mr-2"></i>
                Nuevo Grupo
            </a>
            <form action="{{ route('coordinador.grupos.asignarAutomaticamente') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-robot mr-2"></i>
                    Asignar Automático
                </button>
            </form>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-card p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los estados</option>
                    <option value="planificado" {{ request('estado') == 'planificado' ? 'selected' : '' }}>Planificado</option>
                    <option value="con_profesor" {{ request('estado') == 'con_profesor' ? 'selected' : '' }}>Con Profesor</option>
                    <option value="con_aula" {{ request('estado') == 'con_aula' ? 'selected' : '' }}>Con Aula</option>
                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nivel</label>
                <select name="nivel" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los niveles</option>
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ request('nivel') == $i ? 'selected' : '' }}>Nivel {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
                <select name="periodo" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los periodos</option>
                    @foreach($periodos as $periodo)
                        <option value="{{ $periodo->id }}" {{ request('periodo') == $periodo->id ? 'selected' : '' }}>
                            {{ $periodo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg w-full transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de Grupos -->
    <div class="bg-white rounded-xl shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periodo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profesor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aula</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ocupación</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($grupos as $grupo)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-lg text-gray-900">{{ $grupo->nombre_completo }}</div>
                            <div class="text-sm text-gray-500">Nivel {{ $grupo->nivel_ingles }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $grupo->periodo->nombre }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($grupo->horario)
                            <div class="text-sm text-gray-900">{{ $grupo->horario->nombre }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $grupo->horario->hora_inicio->format('H:i') }} - {{ $grupo->horario->hora_fin->format('H:i') }}
                            </div>
                            @else
                            <span class="text-red-500">No asignado</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($grupo->profesor)
                            <div class="text-sm text-gray-900">{{ $grupo->profesor->nombre_profesor }}</div>
                            @else
                            <span class="text-yellow-600">Por asignar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($grupo->aula)
                            <div class="text-sm text-gray-900">{{ $grupo->aula->id_aula }}</div>
                            <div class="text-xs text-gray-500">{{ $grupo->aula->edificio }}</div>
                            @else
                            <span class="text-yellow-600">Por asignar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" 
                                         style="width: {{ $grupo->porcentaje_ocupacion }}%"></div>
                                </div>
                                <span class="text-sm text-gray-700">
                                    {{ $grupo->estudiantes_inscritos }}/{{ $grupo->capacidad_maxima }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $grupo->clase_estado }}">
                                {{ $grupo->estado_legible }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @if($grupo->estado == 'planificado')
                                <a href="{{ route('coordinador.grupos.asignarProfesor', $grupo->id) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Asignar Profesor">
                                    <i class="fas fa-user-tie"></i>
                                </a>
                                @elseif($grupo->estado == 'con_profesor')
                                <a href="{{ route('coordinador.grupos.asignarAula', $grupo->id) }}" 
                                   class="text-purple-600 hover:text-purple-900" title="Asignar Aula">
                                    <i class="fas fa-door-open"></i>
                                </a>
                                @elseif($grupo->estado == 'con_aula')
                                <form action="{{ route('coordinador.grupos.activar', $grupo->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900" title="Activar Grupo">
                                        <i class="fas fa-play-circle"></i>
                                    </button>
                                </form>
                                @endif
                                
                                @if($grupo->estado == 'activo' && $grupo->tieneCapacidad())
                                <span class="text-green-600" title="Puede recibir estudiantes">
                                    <i class="fas fa-user-plus"></i>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center text-gray-400">
                                <i class="fas fa-users text-4xl mb-3"></i>
                                <p class="text-lg font-medium text-gray-500 mb-2">No hay grupos registrados</p>
                                <p class="text-sm text-gray-400">Comienza creando tu primer grupo</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection