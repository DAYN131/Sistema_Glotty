{{-- resources/views/alumno/preregistro/index.blade.php --}}
@extends('layouts.alumno')

@section('title', 'Mis Preregistros - Glotty')
@section('header-title', 'Mis Preregistros')

@section('content')
<!-- Notificaciones -->
@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
            <span class="text-green-800 font-medium">{{ session('success') }}</span>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-600 text-xl mr-3"></i>
            <span class="text-red-800 font-medium">{{ session('error') }}</span>
        </div>
    </div>
@endif

<!-- Encabezado mejorado -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Historial de Preregistros</h1>
        <p class="text-gray-600 mt-1">Revisa el estado de tus solicitudes de preregistro</p>
    </div>
    <a href="{{ route('alumno.preregistro.create') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center transition-colors">
        <i class="fas fa-plus-circle mr-2"></i>
        Nuevo Preregistro
    </a>
</div>

<!-- Tarjetas de resumen mejoradas -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Tarjeta Total -->
    <div class="bg-white p-6 rounded-2xl shadow-card border border-slate-200">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg text-blue-600 mr-4">
                <i class="fas fa-list-alt text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Preregistros</p>
                <p class="text-2xl font-bold text-blue-700">{{ $preregistros->count() }}</p>
            </div>
        </div>
    </div>
    
    <!-- Tarjeta Activos -->
    <div class="bg-white p-6 rounded-2xl shadow-card border border-slate-200">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg text-green-600 mr-4">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Activos</p>
                <p class="text-2xl font-bold text-green-700">
                    {{ $preregistros->whereIn('estado', ['preregistrado', 'asignado', 'cursando'])->count() }}
                </p>
            </div>
        </div>
    </div>
    
    <!-- Tarjeta Finalizados -->
    <div class="bg-white p-6 rounded-2xl shadow-card border border-slate-200">
        <div class="flex items-center">
            <div class="p-3 bg-emerald-100 rounded-lg text-emerald-600 mr-4">
                <i class="fas fa-graduation-cap text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Finalizados</p>
                <p class="text-2xl font-bold text-emerald-700">
                    {{ $preregistros->where('estado', 'finalizado')->count() }}
                </p>
            </div>
        </div>
    </div>
    
    <!-- Tarjeta Cancelados -->
    <div class="bg-white p-6 rounded-2xl shadow-card border border-slate-200">
        <div class="flex items-center">
            <div class="p-3 bg-slate-100 rounded-lg text-slate-600 mr-4">
                <i class="fas fa-times-circle text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Cancelados</p>
                <p class="text-2xl font-bold text-slate-700">
                    {{ $preregistros->where('estado', 'cancelado')->count() }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de preregistros mejorada -->
<div class="bg-white rounded-2xl shadow-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Periodo
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nivel Solicitado
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Horario Preferido
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Grupo Asignado
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                   
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($preregistros as $preregistro)
                <tr class="hover:bg-slate-50 transition-colors">
                    <!-- Celda Periodo -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">
                            {{ $preregistro->periodo->nombre ?? 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ $preregistro->created_at->format('d/m/Y') }}
                        </div>
                    </td>
                    
                    <!-- Celda Nivel Solicitado -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                            Nivel {{ $preregistro->nivel_solicitado }}
                        </span>
                    </td>
                    
                    <!-- Celda Horario Preferido - MEJORADA -->
                    <td class="px-6 py-4">
                        @if($preregistro->horarioSolicitado)
                            @php
                                // Procesar los días del horario (igual que en la vista create)
                                $diasArray = [];
                                
                                if (is_array($preregistro->horarioSolicitado->dias)) {
                                    $diasArray = $preregistro->horarioSolicitado->dias;
                                } elseif (is_string($preregistro->horarioSolicitado->dias)) {
                                    $decoded = json_decode($preregistro->horarioSolicitado->dias, true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $diasArray = $decoded;
                                    } else {
                                        $diasArray = array_map('trim', explode(',', $preregistro->horarioSolicitado->dias));
                                    }
                                }
                                
                                $diasArray = array_filter($diasArray);
                                $diasTexto = !empty($diasArray) ? implode(', ', $diasArray) : 'No especificado';
                                
                                // Formatear horas
                                $horaInicio = $preregistro->horarioSolicitado->hora_inicio instanceof \DateTime 
                                    ? $preregistro->horarioSolicitado->hora_inicio->format('H:i') 
                                    : (\Carbon\Carbon::parse($preregistro->horarioSolicitado->hora_inicio)->format('H:i') ?? 'N/A');
                                    
                                $horaFin = $preregistro->horarioSolicitado->hora_fin instanceof \DateTime 
                                    ? $preregistro->horarioSolicitado->hora_fin->format('H:i') 
                                    : (\Carbon\Carbon::parse($preregistro->horarioSolicitado->hora_fin)->format('H:i') ?? 'N/A');
                            @endphp
                            
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-blue-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <!-- Nombre y tipo del horario -->
                                    <div class="flex items-center space-x-2 mb-1">
                                  
                                        <span class="bg-blue-100 text-blue-700 text-xs font-medium px-2 py-0.5 rounded-full">
                                            {{ $preregistro->horarioSolicitado->tipo ?? 'Sin tipo' }}
                                        </span>
                                    </div>
                                    
                                    <!-- Información detallada del horario -->
                                    <div class="space-y-1 text-xs text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-day text-gray-400 mr-1 w-3"></i>
                                            <span class="font-medium mr-1">Días:</span>
                                            <span class="text-gray-700">{{ $diasTexto }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock text-gray-400 mr-1 w-3"></i>
                                            <span class="font-medium mr-1">Horas:</span>
                                            <span class="text-gray-700">{{ $horaInicio }} - {{ $horaFin }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-red-500 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Horario no disponible
                            </div>
                        @endif
                    </td>
                    
                    <!-- Celda Grupo Asignado -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($preregistro->grupoAsignado)
                            <div class="font-medium text-gray-900">
                                {{ $preregistro->grupoAsignado->nivel_ingles }}-{{ $preregistro->grupoAsignado->letra_grupo }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-users mr-1"></i>
                                {{ $preregistro->grupoAsignado->estudiantes_inscritos ?? 0 }}/{{ $preregistro->grupoAsignado->capacidad_maxima ?? 0 }}
                            </div>
                        @else
                            <span class="text-gray-400">Por asignar</span>
                        @endif
                    </td>
                    
                    <!-- Celda Estado -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusClasses = [
                                'preregistrado' => ['bg-yellow-100 text-yellow-800', 'fa-clock', 'Preregistrado'],
                                'asignado' => ['bg-blue-100 text-blue-800', 'fa-user-check', 'Asignado'],
                                'cursando' => ['bg-green-100 text-green-800', 'fa-play-circle', 'Cursando'],
                                'finalizado' => ['bg-emerald-100 text-emerald-800', 'fa-graduation-cap', 'Finalizado'],
                                'cancelado' => ['bg-slate-100 text-slate-800', 'fa-times-circle', 'Cancelado']
                            ];
                            $currentStatus = $statusClasses[$preregistro->estado] ?? ['bg-gray-100 text-gray-800', 'fa-question-circle', 'Desconocido'];
                        @endphp
                        <span class="px-3 py-1 inline-flex items-center text-sm font-medium rounded-full {{ $currentStatus[0] }}">
                            <i class="fas {{ $currentStatus[1] }} mr-2"></i>
                            {{ $currentStatus[2] }}
                        </span>
                    </td>
                    
                    
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center text-gray-400">
                            <i class="fas fa-clipboard-list text-4xl mb-3"></i>
                            <p class="text-lg font-medium text-gray-500 mb-2">No tienes preregistros registrados</p>
                            <p class="text-sm text-gray-400 mb-4">Comienza solicitando tu primer preregistro</p>
                            <a href="{{ route('alumno.preregistro.create') }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Nuevo Preregistro
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
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-500 text-xl mt-1"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">Información importante</h3>
            <div class="mt-2 text-sm text-blue-700">
                <p class="mt-1">• El coordinador está a cargo de la asignación de grupos</p>
                <p class="mt-1">• Una vez asignado a un grupo, recibirás una notificación</p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Estilos para mejorar la visualización de la información de horarios */
    .horario-info {
        transition: all 0.2s ease;
    }
    
    .horario-info:hover {
        transform: translateY(-1px);
    }
</style>
@endpush

@endsection