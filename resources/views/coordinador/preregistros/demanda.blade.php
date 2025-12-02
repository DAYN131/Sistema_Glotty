{{-- resources/views/coordinador/preregistros/demanda.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Análisis de Demanda - Glotty')
@section('header-title', 'Análisis de Demanda de Preregistros')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Alertas -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-400 text-xl mr-3"></i>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-400 text-xl mr-3"></i>
                <span class="text-red-800 font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Filtro de estados de pago -->
    <div class="bg-white rounded-2xl shadow-card overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Filtros de Demanda</h2>
            <p class="text-slate-200 text-sm mt-1">Selecciona qué estados de pago incluir en el análisis</p>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('coordinador.preregistros.demanda') }}" class="space-y-4">
                <!-- Estados de pago -->
                <div>
                    <label class="block font-medium text-slate-700 mb-3">Estados de Pago a Incluir:</label>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        @foreach($opcionesEstadosPago as $valor => $etiqueta)
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <div class="relative">
                                <input class="sr-only peer" type="checkbox" 
                                       name="estados_pago[]" 
                                       value="{{ $valor }}"
                                       id="estado_{{ $valor }}"
                                       {{ in_array($valor, $estadosPagoFiltro) ? 'checked' : '' }}>
                                <div class="w-5 h-5 border-2 border-slate-300 rounded peer-checked:border-blue-500 peer-checked:bg-blue-500 transition-colors">
                                    <svg class="w-full h-full text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <span class="text-slate-700 text-sm">{{ $etiqueta }}</span>
                                @isset($estadisticasPago[$valor])
                                <span class="ml-2 bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-xs">
                                    {{ $estadisticasPago[$valor] }}
                                </span>
                                @endisset
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Activar/Desactivar filtro -->
                <div class="pt-4 border-t border-slate-200">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <div class="relative">
                                <input class="sr-only peer" type="checkbox" 
                                       name="filtro_activo" 
                                       id="filtro_activo"
                                       value="1"
                                       {{ $filtroActivo ? 'checked' : '' }}>
                                <div class="w-10 h-6 bg-slate-300 rounded-full peer peer-checked:bg-blue-500 transition-colors">
                                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-4"></div>
                                </div>
                            </div>
                            <div>
                                <span class="font-medium text-slate-700">Aplicar filtro de estados de pago</span>
                                <p class="text-sm text-slate-500 mt-1">
                                    Si se desactiva, se incluirán todos los preregistros pendientes
                                </p>
                            </div>
                        </label>
                        
                        <div class="flex space-x-2">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors flex items-center">
                                <i class="fas fa-filter mr-2"></i>
                                Aplicar Filtros
                            </button>
                            <a href="{{ route('coordinador.preregistros.demanda') }}" 
                               class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-5 py-2.5 rounded-lg font-medium transition-colors flex items-center">
                                <i class="fas fa-redo mr-2"></i>
                                Restablecer
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            
            <!-- Resumen del filtro aplicado -->
            <div class="mt-6 pt-6 border-t border-slate-200">
                <div class="{{ $filtroActivo ? 'bg-blue-50 border border-blue-200' : 'bg-slate-50 border border-slate-200' }} rounded-lg p-4">
                    <div class="flex items-start">
                        @if($filtroActivo)
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        @else
                        <i class="fas fa-info-circle text-slate-500 mt-1 mr-3"></i>
                        @endif
                        <div>
                            <p class="font-medium {{ $filtroActivo ? 'text-blue-700' : 'text-slate-700' }}">
                                <strong>Estudiantes incluidos en el análisis:</strong> {{ $preregistrosPendientes }}
                            </p>
                            <p class="text-sm {{ $filtroActivo ? 'text-blue-600' : 'text-slate-600' }} mt-1">
                                @if($filtroActivo)
                                    Filtro activo: 
                                    @foreach($estadosPagoFiltro as $index => $estado)
                                        <span class="inline-block bg-white border border-blue-200 text-blue-700 px-2 py-0.5 rounded text-xs mx-1">
                                            {{ $opcionesEstadosPago[$estado] ?? $estado }}
                                        </span>
                                        @if(!$loop->last) + @endif
                                    @endforeach
                                @else
                                    <span class="inline-block bg-white border border-slate-300 text-slate-600 px-2 py-0.5 rounded text-xs">
                                        Sin filtro de pago - todos los preregistros pendientes
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Demanda -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg text-blue-600 mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Preregistros</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $totalPreregistros }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-3 {{ $filtroActivo ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-600' }} rounded-lg mr-4">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Válidos para Asignar</p>
                    <p class="text-2xl font-bold {{ $filtroActivo ? 'text-green-700' : 'text-slate-700' }}">{{ $preregistrosPendientes }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        @if($filtroActivo)
                            Con filtro activo
                        @else
                            Todos los pendientes
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg text-purple-600 mr-4">
                    <i class="fas fa-chart-bar text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Niveles Solicitados</p>
                    <p class="text-2xl font-bold text-purple-700">{{ $nivelesUnicos }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg text-orange-600 mr-4">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Horarios Solicitados</p>
                    <p class="text-2xl font-bold text-orange-700">{{ $horariosUnicos }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-3 bg-emerald-100 rounded-lg text-emerald-600 mr-4">
                    <i class="fas fa-layer-group text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Niveles Viables</p>
                    <p class="text-2xl font-bold text-emerald-700">{{ $nivelesViables }} / {{ $nivelesUnicos }}</p>
                    <p class="text-xs text-gray-400 mt-1">(≥25 estudiantes)</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Columna Izquierda: Análisis de Demanda -->
        <div>
            <div class="bg-white rounded-2xl shadow-card overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Demanda por Nivel</h2>
                    @if($filtroActivo)
                    <p class="text-slate-200 text-sm mt-1">Filtro aplicado: 
                        {{ implode(', ', array_map(function($estado) use ($opcionesEstadosPago) {
                            return $opcionesEstadosPago[$estado] ?? $estado;
                        }, $estadosPagoFiltro)) }}
                    </p>
                    @endif
                </div>
                <div class="p-6">
                    @forelse($demandaPorNivel as $nivel => $cantidad)
                    @php
                        $esViable = $cantidad >= 25;
                        $prioridad = $cantidad >= 40 ? 'alta' : ($cantidad >= 25 ? 'media' : 'baja');
                        $colorClase = $prioridad === 'alta' ? 'bg-red-100 text-red-800 border-red-200' : 
                                    ($prioridad === 'media' ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : 
                                    'bg-slate-100 text-slate-600 border-slate-200');
                    @endphp
                    <div class="flex items-center justify-between py-3 border-b border-slate-200 last:border-b-0">
                        <div class="flex items-center">
                            <span class="w-8 h-8 {{ $colorClase }} rounded-lg flex items-center justify-center font-bold mr-3">
                                {{ $nivel }}
                            </span>
                            <div>
                                <span class="font-medium text-slate-700">{{ \App\Models\Preregistro::NIVELES[$nivel] ?? "Nivel $nivel" }}</span>
                                <p class="text-xs text-slate-500 mt-1">{{ $cantidad }} estudiantes válidos</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="{{ $colorClase }} px-3 py-1 rounded-full text-sm font-medium">
                                @if($prioridad === 'alta')
                                    🔥 Alta Prioridad
                                @elseif($prioridad === 'media')
                                    ⚡ Viable
                                @else
                                    ⏳ En espera
                                @endif
                            </span>
                            <!-- Botón para ver estudiantes -->
                            <button onclick="mostrarEstudiantesNivel({{ $nivel }}, '{{ implode(",", $estadosPagoFiltro) }}', {{ $filtroActivo ? 'true' : 'false' }})" 
                                    class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1 rounded text-sm flex items-center transition-colors">
                                <i class="fas fa-eye mr-1"></i>
                                Ver
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500">
                        <i class="fas fa-users text-4xl mb-3"></i>
                        <p>No hay preregistros con los filtros seleccionados</p>
                        <p class="text-sm mt-2">Intenta ajustar los filtros de estado de pago</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Demanda por Horario -->
            <div class="bg-white rounded-2xl shadow-card overflow-hidden">
                <div class="bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Demanda por Horario</h2>
                    @if($filtroActivo)
                    <p class="text-slate-200 text-sm mt-1">Mostrando solo estudiantes con los estados de pago seleccionados</p>
                    @endif
                </div>
                <div class="p-6">
                    @forelse($demandaPorHorario as $horarioId => $data)
                    <div class="border border-slate-200 rounded-lg p-4 mb-4 last:mb-0 bg-slate-50 hover:bg-slate-100 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Header con nombre y tipo -->
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-clock text-orange-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-1">
                                            <h3 class="font-semibold text-slate-800 text-lg">{{ $data['nombre'] ?? 'Horario no disponible' }}</h3>
                                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-medium">
                                                {{ $data['tipo'] ?? 'Sin tipo' }}
                                            </span>
                                        </div>
                                        
                                        <!-- Información de días y horario -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                            <div class="flex items-center text-slate-600">
                                                <i class="fas fa-calendar-day text-slate-400 mr-2 w-4"></i>
                                                <div>
                                                    <span class="font-medium">Días:</span>
                                                    <span class="ml-2 text-slate-700">
                                                        @php
                                                            $diasArray = $data['dias'] ?? [];
                                                            $diasTexto = !empty($diasArray) ? implode(', ', $diasArray) : 'No especificado';
                                                        @endphp
                                                        {{ $diasTexto }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center text-slate-600">
                                                <i class="fas fa-clock text-slate-400 mr-2 w-4"></i>
                                                <div>
                                                    <span class="font-medium">Horario:</span>
                                                    <span class="ml-2 text-slate-700">
                                                        {{ $data['hora_inicio'] ?? '--:--' }} - {{ $data['hora_fin'] ?? '--:--' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contador de estudiantes -->
                            <div class="flex flex-col items-center justify-center bg-white border border-slate-200 rounded-lg px-4 py-3 min-w-[100px] ml-4">
                                <span class="text-2xl font-bold text-slate-800">{{ $data['cantidad'] ?? 0 }}</span>
                                <span class="text-xs text-slate-500 font-medium">estudiantes</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500 border-2 border-dashed border-slate-300 rounded-xl bg-slate-50">
                        <i class="fas fa-calendar-times text-slate-400 text-4xl mb-3"></i>
                        <p class="text-slate-500 text-lg mb-2">No hay horarios con demanda</p>
                        <p class="text-slate-400 text-sm">Con los filtros actuales no hay estudiantes que hayan seleccionado horarios</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Grupos Sugeridos y Creación Rápida -->
        <div>
            <div class="bg-white rounded-2xl shadow-card overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Grupos Sugeridos</h2>
                    <p class="text-blue-100 text-sm mt-1">Criterio: Mínimo 25 estudiantes por grupo</p>
                    @if($filtroActivo)
                    <p class="text-blue-100 text-sm mt-1">Filtro: 
                        {{ implode(', ', array_map(function($estado) use ($opcionesEstadosPago) {
                            return $opcionesEstadosPago[$estado] ?? $estado;
                        }, $estadosPagoFiltro)) }}
                    </p>
                    @endif
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-slate-600 text-sm mb-3">
                            @if($filtroActivo)
                                Basado en estudiantes con estado de pago:
                                @foreach($estadosPagoFiltro as $estado)
                                    <span class="inline-block {{ 
                                        $estado === 'pagado' ? 'bg-green-100 text-green-700' : 
                                        ($estado === 'prorroga' ? 'bg-yellow-100 text-yellow-700' : 
                                        'bg-slate-100 text-slate-700') 
                                    }} px-2 py-0.5 rounded text-xs mx-1">
                                        {{ $opcionesEstadosPago[$estado] ?? $estado }}
                                    </span>
                                @endforeach
                            @else
                                <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs">
                                    Todos los preregistros pendientes (sin filtro de pago)
                                </span>
                            @endif
                        </p>
                    </div>

                    @forelse($gruposSugeridos as $sugerencia)
                    @php
                        $prioridadColor = [
                            'alta' => 'bg-red-50 border-red-200',
                            'media' => 'bg-yellow-50 border-yellow-200', 
                            'baja' => 'bg-slate-50 border-slate-200'
                        ][$sugerencia['prioridad']];
                        
                        $prioridadIcon = [
                            'alta' => '🔥',
                            'media' => '⚡',
                            'baja' => '⏳'
                        ][$sugerencia['prioridad']];
                    @endphp
                    
                    <div class="border rounded-xl p-4 mb-4 {{ $prioridadColor }}">
                        <!-- Header del nivel -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span class="text-lg mr-2">{{ $prioridadIcon }}</span>
                                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-bold mr-3">
                                    {{ $sugerencia['nivel'] }}
                                </span>
                                <div>
                                    <h4 class="font-semibold text-slate-800">{{ $sugerencia['descripcion_nivel'] }}</h4>
                                    <p class="text-sm text-slate-500">
                                        {{ $sugerencia['estudiantes'] }} / {{ $sugerencia['minimo_requerido'] }} estudiantes
                                    </p>
                                </div>
                            </div>
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $sugerencia['grupos_sugeridos'] }} grupos
                            </span>
                        </div>
                        
                        <!-- Estado de viabilidad -->
                        <div class="mb-3">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-sm {{ $sugerencia['es_viable'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $sugerencia['recomendacion'] }}
                                </span>
                                @if($sugerencia['es_viable'])
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-medium">
                                    ✅ Grupo viable
                                </span>
                                @else
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-medium">
                                    ❌ Insuficiente
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Distribución sugerida -->
                        @if($sugerencia['es_viable'] && !empty($sugerencia['distribucion']))
                        <div class="mb-3">
                            <p class="text-sm text-slate-600 mb-2 font-medium">Distribución sugerida:</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach($sugerencia['distribucion'] as $grupo)
                                <div class="bg-white border border-slate-200 rounded-lg p-2 text-center">
                                    <div class="text-xs text-slate-500">Grupo {{ $grupo['grupo'] }}</div>
                                    <div class="font-bold text-slate-800">{{ $grupo['estudiantes'] }} est.</div>
                                    <div class="text-xs {{ $grupo['estado'] === 'Óptimo' ? 'text-green-600' : 'text-yellow-600' }}">
                                        {{ $grupo['estado'] }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Horarios más demandados para este nivel -->
                        @if(isset($sugerencia['horarios_populares']) && count($sugerencia['horarios_populares']) > 0)
                        <div class="mb-3">
                            <p class="text-sm text-slate-600 mb-2 font-medium">Horarios más solicitados:</p>
                            <div class="space-y-2">
                                @foreach($sugerencia['horarios_populares'] as $horario)
                                <div class="flex items-center justify-between text-xs bg-white px-3 py-2 rounded border">
                                    <span class="font-medium">{{ $horario['nombre'] ?? 'Horario no disponible' }}</span>
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded">
                                        {{ $horario['cantidad'] ?? 0 }} est.
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Botón de acción -->
                        <div class="mt-3 pt-3 border-t border-slate-200">
                            @if($sugerencia['es_viable'])
                            <button onclick="sugerirCreacionGrupo({{ $sugerencia['nivel'] }})" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors flex items-center justify-center">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Crear Grupo para Nivel {{ $sugerencia['nivel'] }}
                            </button>
                            @else
                            <button disabled
                                    class="w-full bg-slate-300 text-slate-500 py-2 px-4 rounded-lg text-sm font-medium cursor-not-allowed flex items-center justify-center">
                                <i class="fas fa-clock mr-2"></i>
                                Esperar más estudiantes ({{ $sugerencia['minimo_requerido'] - $sugerencia['estudiantes'] }} faltan)
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500">
                        <i class="fas fa-chart-pie text-4xl mb-3"></i>
                        <p>No hay suficiente demanda para sugerir grupos</p>
                        <p class="text-sm mt-2">Con los filtros actuales no se alcanza el mínimo de 25 estudiantes por nivel</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Resumen de Viabilidad -->
            <div class="bg-white rounded-2xl shadow-card overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Resumen de Viabilidad</h2>
                    @if($filtroActivo)
                    <p class="text-emerald-100 text-sm mt-1">Con filtro de estados de pago aplicado</p>
                    @endif
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600">Total estudiantes incluidos:</span>
                            <span class="font-bold text-slate-800">{{ $totalEstudiantesValidos }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600">Grupos totales sugeridos:</span>
                            <span class="font-bold text-slate-800">{{ $totalGruposSugeridos }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600">Niveles viables:</span>
                            <span class="font-bold text-emerald-600">{{ $nivelesViables }} / {{ $nivelesUnicos }}</span>
                        </div>
                        <div class="pt-4 border-t border-slate-200">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                                    <div class="text-sm text-blue-700">
                                        <p class="font-medium">Criterios de viabilidad:</p>
                                        <ul class="mt-1 space-y-1">
                                            <li>• Mínimo 25 estudiantes por grupo</li>
                                            <li>• Máximo 30 estudiantes recomendado</li>
                                            @if($filtroActivo)
                                            <li>• Solo estudiantes con estado de pago: 
                                                @foreach($estadosPagoFiltro as $estado)
                                                    <span class="font-semibold">{{ $opcionesEstadosPago[$estado] ?? $estado }}</span>
                                                    @if(!$loop->last) o @endif
                                                @endforeach
                                            </li>
                                            @else
                                            <li>• Todos los preregistros pendientes incluidos</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver estudiantes por nivel -->
<div id="modalEstudiantes" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-4xl mx-4 max-h-[80vh] overflow-hidden flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-xl font-bold text-slate-800" id="modalTitulo">Estudiantes - Nivel </h3>
                <p class="text-sm text-slate-500 mt-1" id="modalSubtitulo"></p>
            </div>
            <button onclick="cerrarModalEstudiantes()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            <div class="space-y-3" id="listaEstudiantes">
                <!-- Los estudiantes se cargarán aquí dinámicamente -->
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-t border-slate-200">
            <div class="flex justify-between items-center text-sm text-slate-600">
                <span id="totalEstudiantes">Total: 0 estudiantes</span>
                <button onclick="cerrarModalEstudiantes()" 
                        class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded-lg transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Variables globales para los filtros
let filtroActual = {
    estadosPago: @json($estadosPagoFiltro),
    filtroActivo: {{ $filtroActivo ? 'true' : 'false' }}
};

// Funciones para el modal de estudiantes
function mostrarEstudiantesNivel(nivel, estadosPagoParam = null, filtroActivoParam = null) {
    // Usar parámetros si se proporcionan, de lo contrario usar los globales
    const estadosPago = estadosPagoParam ? estadosPagoParam.split(',') : filtroActual.estadosPago;
    const filtroActivo = filtroActivoParam !== null ? filtroActivoParam : filtroActual.filtroActivo;
    
    // Mostrar loading
    document.getElementById('listaEstudiantes').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-blue-500 text-2xl mb-2"></i>
            <p class="text-slate-500">Cargando estudiantes...</p>
        </div>
    `;
    
    // Actualizar título
    document.getElementById('modalTitulo').textContent = `Estudiantes - Nivel ${nivel}`;
    
    // Actualizar subtítulo con filtros
    let subtitulo = 'Mostrando estudiantes ';
    if (filtroActivo) {
        subtitulo += 'con estado de pago: ';
        @foreach($opcionesEstadosPago as $valor => $etiqueta)
        if (estadosPago.includes('{{ $valor }}')) {
            subtitulo += '<span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs mx-1">{{ $etiqueta }}</span>';
        }
        @endforeach
    } else {
        subtitulo += '<span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs">Todos los preregistros pendientes</span>';
    }
    document.getElementById('modalSubtitulo').innerHTML = subtitulo;
    
    // Mostrar modal
    document.getElementById('modalEstudiantes').classList.remove('hidden');
    
    // Construir URL con parámetros
    let url = `/coordinador/preregistros/estudiantes-por-nivel/${nivel}?`;
    if (filtroActivo) {
        url += `estados_pago=${estadosPago.join(',')}&filtro_activo=1`;
    }
    
    // Hacer petición AJAX
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la petición');
            }
            return response.json();
        })
        .then(data => {
            mostrarListaEstudiantes(data.estudiantes, data.total, data.nivel);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('listaEstudiantes').innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                    <p>Error al cargar los estudiantes</p>
                    <p class="text-sm mt-2">Por favor, intenta nuevamente</p>
                </div>
            `;
        });
}

function mostrarListaEstudiantes(estudiantes, total, nivel) {
    const lista = document.getElementById('listaEstudiantes');
    const totalElement = document.getElementById('totalEstudiantes');
    
    totalElement.textContent = `Total: ${total} estudiantes`;
    
    if (estudiantes.length === 0) {
        lista.innerHTML = `
            <div class="text-center py-8 text-slate-500">
                <i class="fas fa-users text-2xl mb-2"></i>
                <p>No hay estudiantes con los filtros seleccionados</p>
                <p class="text-sm mt-2">Intenta ajustar los filtros de estado de pago</p>
            </div>
        `;
        return;
    }
    
    lista.innerHTML = estudiantes.map(estudiante => {
        // Determinar color según estado de pago
        let pagoColor = 'slate';
        let pagoTexto = estudiante.pago_estado || 'pendiente';
        
        if (estudiante.pago_estado === 'pagado') {
            pagoColor = 'green';
            pagoTexto = 'Pagado';
        } else if (estudiante.pago_estado === 'prorroga') {
            pagoColor = 'yellow';
            pagoTexto = 'Prórroga';
        } else if (estudiante.pago_estado === 'pendiente') {
            pagoColor = 'orange';
            pagoTexto = 'Pendiente';
        } else if (estudiante.pago_estado === 'rechazado') {
            pagoColor = 'red';
            pagoTexto = 'Rechazado';
        }
        
        return `
        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <!-- Header con nombre y número de control -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <h4 class="font-semibold text-slate-800">${estudiante.nombre || 'Nombre no disponible'}</h4>
                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded">${estudiante.numero_control || 'N/C'}</span>
                        <span class="bg-${pagoColor}-100 text-${pagoColor}-700 text-xs px-2 py-1 rounded">
                           Pago: ${pagoTexto}
                        </span>
                        ${estudiante.puede_ser_asignado ? `
                        <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded">Listo para asignar</span>
                        ` : ''}
                    </div>
                    
                    <!-- Información académica -->
                    <div class="flex flex-wrap items-center gap-4 text-sm text-slate-600 mb-3">
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-slate-400 mr-2"></i>
                            <span>${estudiante.correo || 'Sin correo'}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-graduation-cap text-slate-400 mr-2"></i>
                            <span>${estudiante.especialidad || 'Sin especialidad'}</span>
                            ${estudiante.semestre_actual ? `
                            <span class="ml-2 bg-slate-200 text-slate-700 px-2 py-0.5 rounded text-xs">
                                Semestre: ${estudiante.semestre_actual}
                            </span>
                            ` : ''}
                        </div>
                    </div>
                    
                    <!-- Información del horario -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <!-- Horario preferido -->
                        <div class="flex items-center">
                            <i class="fas fa-clock text-slate-400 mr-2 w-4"></i>
                            <span class="font-medium">${estudiante.horario_preferido || 'No especificado'}</span>
                        </div>
                        <!-- Fecha de registro -->
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-slate-400 mr-2 w-4"></i>
                            <span>Registrado: ${estudiante.fecha_registro || 'N/A'}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
    }).join('');
}

function sugerirCreacionGrupo(nivel) {
    // Aquí puedes implementar la lógica para redirigir a la creación de grupo
    // o mostrar un modal con opciones específicas para ese nivel
    alert(`Redirigiendo a creación de grupo para nivel ${nivel}. Esta funcionalidad se puede implementar según tus necesidades.`);
    // Ejemplo: window.location.href = `/coordinador/grupos/crear?nivel=${nivel}`;
}

function cerrarModalEstudiantes() {
    document.getElementById('modalEstudiantes').classList.add('hidden');
}

// Cerrar modales al hacer click fuera
document.addEventListener('click', function(e) {
    const modalEstudiantes = document.getElementById('modalEstudiantes');
    
    if (modalEstudiantes && e.target === modalEstudiantes) {
        cerrarModalEstudiantes();
    }
});

// Cerrar modales con ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalEstudiantes();
    }
});
</script>
@endpush
@endsection