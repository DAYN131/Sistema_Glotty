@extends('layouts.alumno')

@section('title', 'Mi Perfil - Alumno')
@section('header-title', 'Mi Perfil')

@section('content')
<div class="max-w-6xl mx-auto px-4">
    
    {{-- Navegación --}}
    <div class="flex justify-end mb-8">
        <a href="{{ route('alumno.dashboard') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm">
            <i class="fas fa-arrow-left"></i>
            <span>Volver al Dashboard</span>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- HEADER CON INFORMACIÓN PERSONAL --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-lg p-8 mb-8 text-white">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
            <div>
                <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center text-4xl mb-4">
                    <i class="fas fa-user"></i>
                </div>
                <h1 class="text-3xl font-bold mb-2">{{ $usuario->nombre_completo }}</h1>
                <p class="text-blue-100 text-sm">
                    @if($usuario->tipo_usuario == 'interno')
                        <i class="fas fa-user-graduate mr-1"></i>Alumno Interno
                    @else
                        <i class="fas fa-user-tie mr-1"></i>Alumno Externo
                    @endif
                </p>
            </div>
            
            <div class="md:col-span-2 grid grid-cols-2 gap-4">
                <div class="bg-blue-500 bg-opacity-30 rounded-lg p-4">
                    <p class="text-blue-100 text-sm font-medium">Carrera</p>
                    <p class="text-white font-semibold mt-1">{{ $usuario->especialidad ?? 'N/A' }}</p>
                </div>
                @if($usuario->numero_control)
                <div class="bg-blue-500 bg-opacity-30 rounded-lg p-4">
                    <p class="text-blue-100 text-sm font-medium">Número de Control</p>
                    <p class="text-white font-semibold mt-1">{{ $usuario->numero_control }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ESTADÍSTICAS PRINCIPALES --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow p-6 border-t-4 border-blue-600">
            <p class="text-gray-600 text-sm font-medium">Total Preregistros</p>
            <p class="text-4xl font-bold text-blue-600 mt-2">{{ $estadisticas['total_preregistros'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 border-t-4 border-green-600">
            <p class="text-gray-600 text-sm font-medium">Activos</p>
            <p class="text-4xl font-bold text-green-600 mt-2">{{ $estadisticas['preregistros_activos'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 border-t-4 border-gray-400">
            <p class="text-gray-600 text-sm font-medium">Finalizados</p>
            <p class="text-4xl font-bold text-gray-600 mt-2">{{ $estadisticas['preregistros_finalizados'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 border-t-4 border-red-600">
            <p class="text-gray-600 text-sm font-medium">Cancelados</p>
            <p class="text-4xl font-bold text-red-600 mt-2">{{ $estadisticas['preregistros_cancelados'] ?? 0 }}</p>
        </div>
    </div>

    {{-- INFORMACIÓN PERSONAL DETALLADA --}}
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold text-slate-800 mb-6 pb-4 border-b-2 border-blue-200">
            <i class="fas fa-user-circle text-blue-600 mr-2"></i>Información de Contacto
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">Correo Personal</label>
                <p class="text-gray-800 font-medium">{{ $usuario->correo_personal }}</p>
            </div>
            
            @if($usuario->correo_institucional)
            <div>
                <label class="block text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">Correo Institucional</label>
                <p class="text-gray-800 font-medium">{{ $usuario->correo_institucional }}</p>
            </div>
            @endif
            
            @if($usuario->numero_telefonico)
            <div>
                <label class="block text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">Teléfono</label>
                <p class="text-gray-800 font-medium">{{ $usuario->numero_telefonico }}</p>
            </div>
            @endif
            
            @if($usuario->genero)
            <div>
                <label class="block text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">Género</label>
                <p class="text-gray-800 font-medium">
                    @switch($usuario->genero)
                        @case('M')
                            Masculino
                            @break
                        @case('F')
                            Femenino
                            @break
                        @case('Otro')
                            Otro
                            @break
                        @default
                            {{ $usuario->genero }}
                    @endswitch
                </p>
            </div>
            @endif
            
            @if($usuario->fecha_nacimiento)
            <div>
                <label class="block text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">Fecha de Nacimiento</label>
                <p class="text-gray-800 font-medium">
                    @php
                        try {
                            echo \Carbon\Carbon::parse($usuario->fecha_nacimiento)->format('d/m/Y');
                        } catch (Exception $e) {
                            echo $usuario->fecha_nacimiento;
                        }
                    @endphp
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- INSCRIPCIÓN ACTUAL --}}
    @if($preregistroActual)
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border-l-4 border-blue-600">
        <h2 class="text-2xl font-bold text-slate-800 mb-6 pb-4 border-b-2 border-blue-200">
            <i class="fas fa-book-open text-blue-600 mr-2"></i>Inscripción Actual
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-50 p-5 rounded-xl">
                <label class="block text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">Periodo</label>
                <p class="text-gray-900 font-bold text-lg">{{ $preregistroActual->periodo->nombre ?? 'N/A' }}</p>
            </div>
            <div class="bg-blue-50 p-5 rounded-xl">
                <label class="block text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">Nivel</label>
                <p class="text-gray-900 font-bold text-lg">{{ $preregistroActual->nivel_formateado }}</p>
            </div>
            <div class="bg-blue-50 p-5 rounded-xl">
                <label class="block text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">Estado</label>
                @php
                    $estadoColors = [
                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                        'asignado' => 'bg-blue-100 text-blue-800',
                        'cursando' => 'bg-green-100 text-green-800',
                        'finalizado' => 'bg-gray-100 text-gray-800',
                        'cancelado' => 'bg-red-100 text-red-800'
                    ];
                    $color = $estadoColors[$preregistroActual->estado] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <span class="inline-block px-3 py-2 text-xs font-bold rounded-lg {{ $color }}">
                    {{ $preregistroActual->estado_formateado }}
                </span>
            </div>
            @if($preregistroActual->grupoAsignado)
            <div class="md:col-span-3 bg-blue-50 p-5 rounded-xl">
                <label class="block text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">Grupo Asignado</label>
                <p class="text-gray-900 font-bold">{{ $preregistroActual->grupoAsignado->nombre_completo }}</p>
                <p class="text-sm text-gray-600 mt-2">
                    <i class="fas fa-clock text-blue-600 mr-1"></i>{{ $preregistroActual->grupoAsignado->horario->nombre ?? 'Sin horario definido' }}
                </p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- NIVELES COMPLETADOS --}}
    @if($estadisticas['niveles_cursados']->count() > 0)
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-slate-800 mb-6 pb-4 border-b-2 border-blue-200">
            <i class="fas fa-trophy text-blue-600 mr-2"></i>Niveles Completados
        </h2>
        <div class="flex flex-wrap gap-6">
            @foreach($estadisticas['niveles_cursados'] as $nivel)
            <div class="flex flex-col items-center">
                <div class="relative w-24 h-24">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-3xl font-bold text-white">{{ $nivel }}</span>
                    </div>
                    <div class="absolute -top-1 -right-1 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center shadow-md border-2 border-white">
                        <i class="fas fa-check text-white text-sm"></i>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-3 font-medium">Nivel {{ $nivel }}</p>
            </div>
            @endforeach
        </div>
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-600">
            <p class="text-sm text-gray-700">
                <i class="fas fa-star text-blue-600 mr-2"></i>
                <span class="font-semibold">¡Excelente!</span> Has completado {{ $estadisticas['niveles_cursados']->count() }} nivel(es) del curso
            </p>
        </div>
    </div>
    @endif

</div>
@endsection
