{{-- resources/views/alumno/dashboard.blade.php --}}
@extends('layouts.alumno')

@section('title', 'Dashboard - Alumno')
@section('header-title', 'Panel del Alumno')

@section('content')
<!-- Bienvenida y estadísticas -->
<div class="mb-8">
    <!-- Tarjeta de bienvenida -->
    <div class="bg-slate-50 rounded-2xl shadow-soft p-6 mb-6 border border-slate-200">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Bienvenido, {{ $nombre_completo }}</h2>
                <p class="text-text-secondary mb-2">Número de control: {{ $identificador }}</p>
                <div class="flex items-center text-sm text-text-secondary">
                    <i class="fas fa-calendar-day text-indigo-500 mr-2"></i>
                    <span>Hoy es {{ date('d/m/Y') }}</span>
                </div>
            </div>
            <div class="text-right">
                <div class="text-lg font-semibold text-indigo-600">Estudiante Activo</div>
                
            </div>
        </div>
    </div>
    
    <!-- Estadísticas mejoradas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
        <!-- Tarjeta de Preregistros Activos -->
        <div class="bg-slate-50 rounded-xl shadow-card p-5 flex items-center border border-slate-200 hover:shadow-card-hover transition-smooth card-hover">
            <div class="p-3 bg-indigo-100 rounded-lg mr-4">
                <i class="fas fa-clipboard-check text-indigo-600 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">Preregistros activos</div>
                <div class="font-bold text-2xl text-indigo-700">
                    {{ Auth::user()->preregistros()->whereIn('estado', ['preregistrado', 'asignado', 'cursando'])->count() }}
                </div>
                <div class="text-xs text-gray-400 mt-1">En proceso</div>
            </div>
        </div>
        
        <!-- Tarjeta de Cursos Finalizados -->
        <div class="bg-slate-50 rounded-xl shadow-card p-5 flex items-center border border-slate-200 hover:shadow-card-hover transition-smooth card-hover">
            <div class="p-3 bg-emerald-100 rounded-lg mr-4">
                <i class="fas fa-graduation-cap text-emerald-600 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">Cursos finalizados</div>
                <div class="font-bold text-2xl text-emerald-700">
                    {{ Auth::user()->preregistros()->where('estado', 'finalizado')->count() }}
                </div>
                <div class="text-xs text-gray-400 mt-1">Completados</div>
            </div>
        </div>
        
        <!-- Tarjeta de Cursos en Progreso -->
        <div class="bg-slate-50 rounded-xl shadow-card p-5 flex items-center border border-slate-200 hover:shadow-card-hover transition-smooth card-hover">
            <div class="p-3 bg-sky-100 rounded-lg mr-4">
                <i class="fas fa-chart-line text-sky-600 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">En progreso</div>
                <div class="font-bold text-2xl text-sky-700">
                    {{ Auth::user()->preregistros()->where('estado', 'cursando')->count() }}
                </div>
                <div class="text-xs text-gray-400 mt-1">Actualmente</div>
            </div>
        </div>
        
        <!-- Tarjeta de Total Solicitudes -->
        <div class="bg-slate-50 rounded-xl shadow-card p-5 flex items-center border border-slate-200 hover:shadow-card-hover transition-smooth card-hover">
            <div class="p-3 bg-violet-100 rounded-lg mr-4">
                <i class="fas fa-history text-violet-600 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">Total solicitudes</div>
                <div class="font-bold text-2xl text-violet-700">
                    {{ Auth::user()->preregistros()->count() }}
                </div>
                <div class="text-xs text-gray-400 mt-1">Historial</div>
            </div>
        </div>
    </div>
</div>

<!-- Main Grid mejorado -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Tarjeta Preregistro -->
    <a href="{{ route('alumno.preregistro.create') }}" class="bg-slate-50 p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-slate-200 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-indigo-100 rounded-lg mr-4 group-hover:bg-indigo-200 transition-smooth">
                <i class="fas fa-file-signature text-indigo-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Nuevo Preregistro</h3>
        </div>
        <p class="text-text-secondary mb-5">Realiza tu solicitud de inscripción para el próximo periodo de cursos.</p>
        <div class="text-indigo-600 font-medium group-hover:text-indigo-700 transition-smooth flex items-center">
            Solicitar
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>

    <!-- Tarjeta Mis Preregistros -->
    <a href="{{ route('alumno.preregistro.index') }}" class="bg-slate-50 p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-slate-200 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-emerald-100 rounded-lg mr-4 group-hover:bg-emerald-200 transition-smooth">
                <i class="fas fa-history text-emerald-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Mis Preregistros</h3>
        </div>
        <p class="text-text-secondary mb-5">Consulta el estado y historial de tus solicitudes de inscripción.</p>
        <div class="text-emerald-600 font-medium group-hover:text-emerald-700 transition-smooth flex items-center">
            Ver historial
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>

    <!-- Tarjeta Mi Información (CORREGIDA) -->
    <a href="#" class="bg-slate-50 p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-slate-200 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-amber-100 rounded-lg mr-4 group-hover:bg-amber-200 transition-smooth">
                <i class="fas fa-user-circle text-amber-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Mi Información</h3>
        </div>
        <p class="text-text-secondary mb-5">Consulta y actualiza tus datos personales y de contacto.</p>
        <div class="text-amber-600 font-medium group-hover:text-amber-700 transition-smooth flex items-center">
            Ver perfil
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>
</div>

<!-- Notificaciones -->
@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mt-6">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
            <span class="text-green-800 font-medium">{{ session('success') }}</span>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mt-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-600 text-xl mr-3"></i>
            <span class="text-red-800 font-medium">{{ session('error') }}</span>
        </div>
    </div>
@endif

@if(session('info'))
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mt-6">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3"></i>
            <span class="text-blue-800 font-medium">{{ session('info') }}</span>
        </div>
    </div>
@endif
@endsection