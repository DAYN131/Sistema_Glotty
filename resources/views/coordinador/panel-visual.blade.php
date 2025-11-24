@extends('layouts.coordinador')

@section('title', 'Panel Visual - Glotty')
@section('header-title', 'Panel Visual de Grupos')

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

    <div class="bg-white rounded-2xl shadow-card p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">Filtrar por día</label>
                <select id="filtroDia" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                    <option value="">Todos los días</option>
                    <option value="Lunes">Lunes</option>
                    <option value="Martes">Martes</option>
                    <option value="Miércoles">Miércoles</option>
                    <option value="Jueves">Jueves</option>
                    <option value="Viernes">Viernes</option>
                    <option value="Sábado">Sábado</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">Filtrar por edificio</label>
                <select id="filtroEdificio" class="w-full border border-slate-300 rounded-lg px-3 py-2">
                    <option value="">Todos los edificios</option>
                    @foreach($edificios as $edificio)
                        <option value="{{ $edificio }}">{{ $edificio }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Horario General - {{ $periodoActivo->nombre_periodo ?? 'Sin Periodo' }}</h2>
        </div>

        <div class="grid grid-cols-8 bg-slate-50 border-b border-slate-200">
            <div class="p-4 font-medium text-slate-500">Horario</div>
            @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dia)
            <div class="p-4 text-center font-medium text-slate-500">{{ $dia }}</div>
            @endforeach
        </div>

        @foreach($horarios as $hora)
        <div class="grid grid-cols-8 border-b border-slate-200 hover:bg-slate-50">
            <div class="p-4 border-r border-slate-200">
                <div class="font-medium text-slate-900">{{ $hora['rango'] }}</div>
                <div class="text-sm text-slate-500">{{ $hora['tipo'] }}</div>
            </div>

            @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dia)
            <div class="p-2 border-r border-slate-200 min-h-24" data-dia="{{ $dia }}" data-horario="{{ $hora['id'] }}">
                @php
                    $gruposDia = collect($hora['grupos'])->filter(function($grupo) use ($dia) {
                        return in_array($dia, $grupo['dias']);
                    });
                @endphp
                
                @foreach($gruposDia as $grupo)
                <div class="grupo-card mb-2 p-3 rounded-lg border-l-4 
                    {{ $grupo['estado'] == 'activo' ? 'bg-green-50 border-green-400' : 'bg-gray-50 border-gray-400' }}"
                    data-edificio="{{ $grupo['edificio'] }}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="font-semibold text-sm text-slate-800">
                                {{ $grupo['nombre_grupo'] }}
                            </div>
                            <div class="text-xs text-slate-600 mt-1">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $grupo['aula'] }}
                            </div>
                            <div class="text-xs text-slate-600">
                                <i class="fas fa-user mr-1"></i>
                                {{ $grupo['profesor'] }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-medium 
                                {{ $grupo['ocupacion'] >= 90 ? 'text-red-600' : 
                                   ($grupo['ocupacion'] >= 70 ? 'text-orange-600' : 'text-green-600') }}">
                                {{ $grupo['estudiantes_inscritos'] }}/{{ $grupo['capacidad'] }}
                            </div>
                            <div class="text-xs text-slate-500 mt-1">
                                {{ $grupo['ocupacion'] }}%
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-2 w-full bg-slate-200 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full 
                            {{ $grupo['ocupacion'] >= 90 ? 'bg-red-500' : 
                               ($grupo['ocupacion'] >= 70 ? 'bg-orange-500' : 'bg-green-500') }}"
                            style="width: {{ min($grupo['ocupacion'], 100) }}%">
                        </div>
                    </div>
                </div>
                @endforeach

                @if($gruposDia->isEmpty())
                <div class="text-center text-slate-400 text-sm py-4">
                    <i class="fas fa-door-open text-lg mb-1"></i>
                    <div>Disponible</div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <div class="mt-6 bg-white rounded-2xl shadow-card p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Leyenda</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-400 rounded mr-2"></div>
                <span class="text-sm text-slate-600">Grupo Activo</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                <span class="text-sm text-slate-600">Capacidad >90%</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-orange-500 rounded mr-2"></div>
                <span class="text-sm text-slate-600">Capacidad 70-89%</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                <span class="text-sm text-slate-600">Capacidad <70%</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Filtros en tiempo real
document.getElementById('filtroDia').addEventListener('change', function() {
    aplicarFiltros();
});

document.getElementById('filtroEdificio').addEventListener('change', function() {
    aplicarFiltros();
});

function aplicarFiltros() {
    const diaSeleccionado = document.getElementById('filtroDia').value;
    const edificioSeleccionado = document.getElementById('filtroEdificio').value;
    
    document.querySelectorAll('.grupo-card').forEach(card => {
        const cardDia = card.closest('[data-dia]').getAttribute('data-dia');
        const cardEdificio = card.getAttribute('data-edificio');
        
        const mostrarDia = !diaSeleccionado || cardDia === diaSeleccionado;
        const mostrarEdificio = !edificioSeleccionado || cardEdificio === edificioSeleccionado;
        
        card.style.display = (mostrarDia && mostrarEdificio) ? 'block' : 'none';
    });
    
    // Mostrar/ocultar celdas vacías
    document.querySelectorAll('[data-dia]').forEach(celda => {
        const tieneGruposVisibles = Array.from(celda.querySelectorAll('.grupo-card'))
            .some(card => card.style.display !== 'none');
        
        const contenidoVacio = celda.querySelector('.text-slate-400');
        if (contenidoVacio) {
            contenidoVacio.style.display = tieneGruposVisibles ? 'none' : 'block';
        }
    });
}
</script>
@endpush
@endsection