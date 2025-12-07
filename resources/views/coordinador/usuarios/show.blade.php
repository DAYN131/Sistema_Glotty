{{-- resources/views/coordinador/usuarios/show.blade.php --}}
@extends('layouts.coordinador')

@section('title', "Usuario {$usuario->nombre_completo} - Glotty")
@section('header-title', "Usuario: {$usuario->nombre_completo}")

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- Botones de navegación --}}
    <div class="flex justify-between mb-6">
        <a href="{{ route('coordinador.usuarios.index') }}" 
           class="bg-white text-gray-600 hover:bg-gray-50 border border-gray-200 px-4 py-2 rounded-lg shadow-sm transition-colors flex items-center space-x-2 text-sm font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Volver a Usuarios</span>
        </a>
        
        <div class="flex space-x-2">
            <a href="{{ url('/coordinador') }}" 
               class="bg-slate-100 text-slate-600 hover:bg-slate-200 px-4 py-2 rounded-lg flex items-center transition-colors text-sm">
                <i class="fas fa-home mr-2"></i>
                Panel
            </a>
        </div>
    </div>

    {{-- Información del usuario --}}
    <div class="bg-white rounded-2xl shadow-card p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Información personal --}}
            <div class="md:col-span-2">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Información Personal</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-500">Nombre Completo</label>
                        <p class="mt-1 text-slate-900">{{ $usuario->nombre_completo }}</p>
                    </div>
                    
                    @if($usuario->numero_control)
                    <div>
                        <label class="block text-sm font-medium text-slate-500">Número de Control</label>
                        <p class="mt-1 text-slate-900">{{ $usuario->numero_control }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-500">Email</label>
                        <p class="mt-1 text-slate-900">{{ $usuario->correo_institucional  }}</p>
                    </div>
                    
                    
                    @if($usuario->semestre_carrera)
                    <div>
                        <label class="block text-sm font-medium text-slate-500">Semestre/Carrera</label>
                        <p class="mt-1 text-slate-900">{{ $usuario->semestre_carrera }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-500">Tipo de Usuario</label>
                        <p class="mt-1">
                            @if($usuario->numero_control)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-user-graduate mr-1"></i> Alumno Interno
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                    <i class="fas fa-user-tie mr-1"></i> Alumno Externo
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            {{-- Estadísticas --}}
            <div>
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Estadísticas</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm text-slate-600">Total Preregistros</span>
                        <span class="text-lg font-bold text-blue-600">{{ $estadisticas['total_preregistros'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-sm text-slate-600">Activos</span>
                        <span class="text-lg font-bold text-green-600">{{ $estadisticas['preregistros_activos'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-slate-600">Finalizados</span>
                        <span class="text-lg font-bold text-gray-600">{{ $estadisticas['preregistros_finalizados'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <span class="text-sm text-slate-600">Cancelados</span>
                        <span class="text-lg font-bold text-red-600">{{ $estadisticas['preregistros_cancelados'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial de preregistros --}}
    <div class="bg-white rounded-2xl shadow-card overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Historial de Preregistros</h2>
            <p class="text-slate-200 text-sm mt-1">{{ $usuario->preregistros->count() }} registros encontrados</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Periodo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nivel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Pago</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Grupo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Fecha</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($usuario->preregistros as $preregistro)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-900">{{ $preregistro->periodo->nombre_periodo ?? 'N/A' }}</div>
                            <div class="text-xs text-slate-500">{{ $preregistro->created_at->format('d/m/Y') }}</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Nivel {{ $preregistro->nivel_solicitado }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $estadoColors = [
                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                    'asignado' => 'bg-blue-100 text-blue-800',
                                    'cursando' => 'bg-green-100 text-green-800',
                                    'finalizado' => 'bg-gray-100 text-gray-800',
                                    'cancelado' => 'bg-red-100 text-red-800'
                                ];
                                $color = $estadoColors[$preregistro->estado] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                                {{ $preregistro->estado_formateado }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $pagoColors = [
                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                    'pagado' => 'bg-green-100 text-green-800',
                                    'rechazado' => 'bg-red-100 text-red-800',
                                    'prorroga' => 'bg-orange-200 text-orange-900'
                                ];
                                $colorPago = $pagoColors[$preregistro->pago_estado] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $colorPago }}">
                                {{ $preregistro->pago_estado_formateado }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($preregistro->grupoAsignado)
                                <div class="text-sm text-slate-900">{{ $preregistro->grupoAsignado->nombre_completo }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ $preregistro->grupoAsignado->horario->nombre ?? 'Sin horario' }}
                                </div>
                            @else
                                <span class="text-sm text-slate-400">Sin asignar</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            {{ $preregistro->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            <i class="fas fa-history text-4xl mb-3"></i>
                            <p class="text-lg">No hay historial de preregistros</p>
                            <p class="text-sm mt-2">Este usuario no ha realizado preregistros</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Niveles cursados --}}
    @if($estadisticas['niveles_cursados']->count() > 0)
    <div class="bg-white rounded-2xl shadow-card p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Progreso Académico</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($estadisticas['niveles_cursados'] as $nivel)
            <div class="relative">
                <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                    {{ $nivel }}
                </div>
                <div class="absolute -top-1 -right-1 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-white text-xs"></i>
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-sm text-slate-500 mt-4">
            <i class="fas fa-info-circle mr-1"></i>
            Este usuario ha completado {{ $estadisticas['niveles_cursados']->count() }} niveles del curso
        </p>
    </div>
    @endif
</div>
@endsection