{{-- resources/views/coordinador/dashboard.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Dashboard - Coordinador')

@section('content')
<!-- Bienvenida y estadísticas -->
<div class="mb-8">
    <!-- Tarjeta de bienvenida -->
    <div class="bg-gradient-to-r from-primary/10 to-accent/10 rounded-2xl shadow-soft p-6 mb-6 border border-gray-100">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Bienvenido, {{ session('user_fullname') ?? 'Coordinador' }}</h2>
                <p class="text-text-secondary mb-2">RFC: {{ session('user_identifier') ?? 'N/A' }}</p>
                <div class="flex items-center text-sm text-text-secondary">
                    <i class="fas fa-calendar-day text-primary mr-2"></i>
                    <span>Hoy es {{ date('d/m/Y') }}</span>
                </div>
            </div>
            <div class="text-right">
                <div class="text-lg font-semibold text-primary">Sistema Activo</div>
                <div class="text-sm text-green-600 flex items-center justify-end">
                    <i class="fas fa-circle text-xs mr-1"></i>
                    <span>Todo en orden</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas mejoradas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
        <!-- Tarjeta de Alumnos Activos -->
        <div class="bg-white rounded-xl shadow-card p-5 flex items-center border border-gray-100 hover:shadow-card-hover transition-smooth card-hover">
            <div class="p-3 bg-primary/10 rounded-lg mr-4">
                <i class="fas fa-users text-primary text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">Alumnos activos</div>
                <div class="font-bold text-2xl text-primary-dark">{{ $estadisticas['total_estudiantes'] ?? 0 }}</div>
                <div class="text-xs text-gray-400 mt-1">Inscripciones vigentes</div>
            </div>
        </div>
        
        <!-- Tarjeta de Preregistros -->
        <div class="bg-white rounded-xl shadow-card p-5 flex items-center border border-gray-100 hover:shadow-card-hover transition-smooth card-hover">
            <div class="p-3 bg-blue-50 rounded-lg mr-4">
                <i class="fas fa-file-signature text-blue-500 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">Preregistros</div>
                <div class="font-bold text-2xl text-blue-600">{{ $estadisticas['preregistrados'] ?? 0 }}</div>
                <div class="text-xs text-gray-400 mt-1">Por asignar</div>
            </div>
        </div>
        
        <!-- Tarjeta de Grupos Activos -->
        <div class="bg-white rounded-xl shadow-card p-5 flex items-center border border-gray-100 hover:shadow-card-hover transition-smooth card-hover">
            <div class="p-3 bg-green-50 rounded-lg mr-4">
                <i class="fas fa-users-between-lines text-green-500 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">Grupos activos</div>
                <div class="font-bold text-2xl text-green-600">{{ $estadisticas['grupos_activos'] ?? 0 }}</div>
                <div class="text-xs text-gray-400 mt-1">En curso</div>
            </div>
        </div>
        
        <!-- Tarjeta de Profesores -->
        <div class="bg-white rounded-xl shadow-card p-5 flex items-center border border-gray-100 hover:shadow-card-hover transition-smooth card-hover">
            <div class="p-3 bg-purple-50 rounded-lg mr-4">
                <i class="fas fa-chalkboard-user text-purple-500 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">Profesores</div>
                <div class="font-bold text-2xl text-purple-600">{{ $estadisticas['total_profesores'] ?? 0 }}</div>
                <div class="text-xs text-gray-400 mt-1">Activos</div>
            </div>
        </div>
    </div>
</div>

<!-- Main Grid mejorado -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Tarjeta Preregistros -->
    <a href="" class="bg-white p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-gray-100 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-primary/10 rounded-lg mr-4 group-hover:bg-primary/20 transition-smooth">
                <i class="fas fa-file-signature text-primary text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Preregistros</h3>
        </div>
        <p class="text-text-secondary mb-5">Analizar demanda y asignar grupos según los preregistros.</p>
        <div class="text-primary font-medium group-hover:text-primary-dark transition-smooth flex items-center">
            Gestionar
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>

    <!-- Tarjeta Profesores -->
    <a href="{{ route('coordinador.profesores.index') }}" class="bg-white p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-gray-100 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-green-100/50 rounded-lg mr-4 group-hover:bg-green-100/70 transition-smooth">
                <i class="fas fa-chalkboard-user text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Profesores</h3>
        </div>
        <p class="text-text-secondary mb-5">Administra la plantilla docente y las asignaciones de cursos.</p>
        <div class="text-green-600 font-medium group-hover:text-green-700 transition-smooth flex items-center">
            Gestionar
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>

    <!-- Tarjeta Grupos -->
    <a href="" class="bg-white p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-gray-100 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-secondary/10 rounded-lg mr-4 group-hover:bg-secondary/20 transition-smooth">
                <i class="fas fa-users-between-lines text-secondary text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Grupos</h3>
        </div>
        <p class="text-text-secondary mb-5">Gestiona los grupos académicos y la asignación de alumnos.</p>
        <div class="text-secondary font-medium group-hover:text-secondary-dark transition-smooth flex items-center">
            Gestionar
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>

    <!-- Tarjeta Alumnos -->
    <a href="" class="bg-white p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-gray-100 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-blue-100/50 rounded-lg mr-4 group-hover:bg-blue-100/70 transition-smooth">
                <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Alumnos</h3>
        </div>
        <p class="text-text-secondary mb-5">Administra la información de los alumnos registrados.</p>
        <div class="text-blue-600 font-medium group-hover:text-blue-700 transition-smooth flex items-center">
            Gestionar
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>
    
    <!-- Tarjeta Horarios -->
    <a href="" class="bg-white p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-gray-100 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-purple-100/50 rounded-lg mr-4 group-hover:bg-purple-100/70 transition-smooth">
                <i class="fas fa-calendar-days text-purple-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Horarios</h3>
        </div>
        <p class="text-text-secondary mb-5">Administra horarios de clases y disponibilidad de aulas.</p>
        <div class="text-purple-600 font-medium group-hover:text-purple-700 transition-smooth flex items-center">
            Gestionar
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>
    
    <!-- Tarjeta Constancias -->
    <a href="" class="bg-white p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-gray-100 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-amber-100/50 rounded-lg mr-4 group-hover:bg-amber-100/70 transition-smooth">
                <i class="fas fa-folder-open text-amber-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Constancias</h3>
        </div>
        <p class="text-text-secondary mb-5">Gestiona documentos académicos y administrativos.</p>
        <div class="text-amber-600 font-medium group-hover:text-amber-700 transition-smooth flex items-center">
            Gestionar
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>
</div>
@endsection