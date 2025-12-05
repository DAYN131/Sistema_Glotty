{{-- resources/views/coordinador/grupos/index.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Gestión de Grupos - Glotty')
@section('header-title', 'Gestión de Grupos')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- CAMBIO AQUÍ: Botón superior para regresar al Panel --}}
    <div class="flex justify-end mb-6">
        <a href="{{ url('/coordinador') }}" 
           class="bg-white text-gray-600 hover:bg-gray-50 border border-gray-200 px-4 py-2 rounded-lg shadow-sm transition-colors flex items-center space-x-2 text-sm font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Volver al Panel</span>
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
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
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600 mr-3">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Con Profesor</p>
                    <p class="text-xl font-bold text-blue-700">{{ $estadisticas['con_profesor'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-card">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg text-purple-600 mr-3">
                    <i class="fas fa-door-open"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Con Aula</p>
                    <p class="text-xl font-bold text-purple-700">{{ $estadisticas['con_aula'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-card">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg text-green-600 mr-3">
                    <i class="fas fa-play-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Activos</p>
                    <p class="text-xl font-bold text-green-700">{{ $estadisticas['activos'] }}</p>
                </div>
            </div>
        </div>
       
    </div>

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
        </div>
    </div>

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
                    <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
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
                <select name="periodo_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los periodos</option>
                    @foreach($periodos as $periodo)
                        <option value="{{ $periodo->id }}" {{ request('periodo_id') == $periodo->id ? 'selected' : '' }}>
                            {{ $periodo->nombre_periodo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex-1 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrar
                </button>
                <a href="{{ route('coordinador.grupos.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>

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
                           
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $grupo->periodo->nombre_periodo }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $grupo->periodo->fecha_inicio->format('d/m/Y') }} - {{ $grupo->periodo->fecha_fin->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($grupo->horario)
                            <div class="text-sm text-gray-900">{{ $grupo->horario->nombre }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $grupo->horario->hora_inicio->format('H:i') }} - {{ $grupo->horario->hora_fin->format('H:i') }}
                            </div>
                            <div class="text-xs text-gray-400">{{ $grupo->horario->dias_formateados }}</div>
                            @else
                            <span class="text-red-500 text-sm">No asignado</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($grupo->profesor)
                            <div class="text-sm text-gray-900">{{ $grupo->profesor->nombre_profesor  }}</div>
                              <div class="text-sm text-gray-900">{{ $grupo->profesor->apellidos_profesor  }}</div>
                            @else
                            <span class="text-yellow-600 text-sm">Por asignar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($grupo->aula)
                            <div class="text-sm text-gray-900">{{ $grupo->aula->nombre?? $grupo->aula->nombre }}</div>
                            <div class="text-xs text-gray-500">Cap: {{ $grupo->aula->capacidad }}</div>
                            @else
                            <span class="text-yellow-600 text-sm">Por asignar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $grupo->porcentaje_ocupacion >= 90 ? 'bg-red-600' : ($grupo->porcentaje_ocupacion >= 70 ? 'bg-yellow-600' : 'bg-green-600') }}" 
                                         style="width: {{ min($grupo->porcentaje_ocupacion, 100) }}%"></div>
                                </div>
                                <span class="text-sm text-gray-700 whitespace-nowrap">
                                    {{ $grupo->estudiantes_inscritos }}/{{ $grupo->capacidad_maxima }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ number_format($grupo->porcentaje_ocupacion, 1) }}%</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $grupo->clase_estado }}">
                                {{ $grupo->estado_legible }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                
                                <!-- NUEVO: Botón de Ver (ojito) -->
                                <a href="{{ route('coordinador.grupos.show', $grupo->id) }}" 
                                class="text-blue-600 hover:text-blue-900 transition-colors p-1 rounded" 
                                title="Ver detalles del grupo">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <a href="{{ route('coordinador.grupos.edit', $grupo->id) }}" 
                                class="text-green-600 hover:text-green-900 transition-colors p-1 rounded" 
                                title="Editar grupo">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if($grupo->estado == 'activo' && $grupo->tieneCapacidad())
                                <span class="text-green-600 p-1 rounded" title="Puede recibir estudiantes">
                                    <i class="fas fa-user-plus"></i>
                                </span>
                                @endif

                                @if($grupo->estado == 'activo' && !$grupo->tieneCapacidad())
                                <span class="text-red-600 p-1 rounded" title="Grupo lleno">
                                    <i class="fas fa-users-slash"></i>
                                </span>
                                @endif

                                @if(!$grupo->profesor)
                                <span class="text-yellow-600 p-1 rounded" title="Falta asignar profesor">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </span>
                                @endif

                                @if(!$grupo->aula)
                                <span class="text-orange-600 p-1 rounded" title="Falta asignar aula">
                                    <i class="fas fa-door-open"></i>
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
                                <p class="text-sm text-gray-400 mb-4">Comienza creando tu primer grupo</p>
                                <a href="{{ route('coordinador.grupos.create') }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Crear Primer Grupo
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($grupos->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            {{ $grupos->links() }}
        </div>
        @endif
    </div>

</div>

<style>
.shadow-card {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}
.transition-colors {
    transition: all 0.2s ease-in-out;
}
</style>

@endsection