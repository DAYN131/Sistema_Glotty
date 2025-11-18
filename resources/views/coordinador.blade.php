{{-- resources/views/coordinador/dashboard.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Dashboard - Coordinador')

@section('content')
<!-- Bienvenida y estadísticas -->
<div class="mb-8">
    <!-- Tarjeta de bienvenida -->
    <div class="bg-slate-50 rounded-2xl shadow-soft p-6 mb-6 border border-slate-200">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Bienvenido, {{ session('user_fullname') ?? 'Coordinador' }}</h2>
                <p class="text-text-secondary mb-2">RFC: {{ session('user_identifier') ?? 'N/A' }}</p>
                <div class="flex items-center text-sm text-text-secondary">
                    <i class="fas fa-calendar-day text-indigo-500 mr-2"></i>
                    <span>Hoy es {{ date('d/m/Y') }}</span>
                </div>
            </div>
            <div class="text-right">
                <div class="text-lg font-semibold text-indigo-600">Sistema Activo</div>
                <div class="text-sm text-emerald-600 flex items-center justify-end">
                    <i class="fas fa-circle text-xs mr-1"></i>
                    <span>Todo en orden</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas mejoradas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
        <!-- Tarjeta de Alumnos Activos -->
        <div class="bg-slate-50 rounded-xl shadow-card p-5 flex items-center border border-slate-200 hover:shadow-card-hover transition-smooth card-hover">
            <div class="p-3 bg-indigo-100 rounded-lg mr-4">
                <i class="fas fa-users text-indigo-600 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">Alumnos activos</div>
                <div class="font-bold text-2xl text-indigo-700">{{ $estadisticas['total_estudiantes'] ?? 0 }}</div>
                <div class="text-xs text-gray-400 mt-1">Inscripciones vigentes</div>
            </div>
        </div>
        
        <!-- Tarjeta de Preregistros -->
        <div class="bg-slate-50 rounded-xl shadow-card p-5 flex items-center border border-slate-200 hover:shadow-card-hover transition-smooth card-hover">
            <div class="p-3 bg-sky-100 rounded-lg mr-4">
                <i class="fas fa-file-signature text-sky-600 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">Preregistros</div>
                <div class="font-bold text-2xl text-sky-700">{{ $estadisticas['preregistrados'] ?? 0 }}</div>
                <div class="text-xs text-gray-400 mt-1">Por asignar</div>
            </div>
        </div>
        
        <!-- Tarjeta de Grupos Activos -->
        <div class="bg-slate-50 rounded-xl shadow-card p-5 flex items-center border border-slate-200 hover:shadow-card-hover transition-smooth card-hover">
            <div class="p-3 bg-emerald-100 rounded-lg mr-4">
                <i class="fas fa-users-between-lines text-emerald-600 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">Grupos activos</div>
                <div class="font-bold text-2xl text-emerald-700">{{ $estadisticas['grupos_activos'] ?? 0 }}</div>
                <div class="text-xs text-gray-400 mt-1">En curso</div>
            </div>
        </div>
        
        <!-- Tarjeta de Profesores -->
        <div class="bg-slate-50 rounded-xl shadow-card p-5 flex items-center border border-slate-200 hover:shadow-card-hover transition-smooth card-hover">
            <div class="p-3 bg-violet-100 rounded-lg mr-4">
                <i class="fas fa-chalkboard-user text-violet-600 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">Profesores</div>
                <div class="font-bold text-2xl text-violet-700">{{ $estadisticas['total_profesores'] ?? 0 }}</div>
                <div class="text-xs text-gray-400 mt-1">Activos</div>
            </div>
        </div>
    </div>
</div>

<!-- Main Grid mejorado -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Tarjeta de Periodos -->
    <a href="{{ route('coordinador.periodos.index') }}" class="bg-slate-50 p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-slate-200 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-indigo-100 rounded-xl mr-4 group-hover:bg-indigo-200 transition-smooth">
                <i class="fas fa-calendar-check text-indigo-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Periodos</h3>
        </div>
        <p class="text-text-secondary mb-5">Administra los periodos escolares y ciclos académicos</p>
        <div class="text-indigo-600 font-medium group-hover:text-indigo-700 transition-smooth flex items-center">
            Gestionar
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>


    <!-- Tarjeta Horarios -->
    <a href="{{ route('coordinador.horarios.index') }}" class="bg-slate-50 p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-slate-200 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-violet-100 rounded-lg mr-4 group-hover:bg-violet-200 transition-smooth">
                <i class="fas fa-calendar-days text-violet-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Horarios</h3>
        </div>
        <p class="text-text-secondary mb-5">Administra horarios de clases y disponibilidad de aulas.</p>
        <div class="text-violet-600 font-medium group-hover:text-violet-700 transition-smooth flex items-center">
            Gestionar
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>


    <!-- Tarjeta Profesores -->
    <a href="{{ route('coordinador.profesores.index') }}" class="bg-slate-50 p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-slate-200 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-emerald-100 rounded-lg mr-4 group-hover:bg-emerald-200 transition-smooth">
                <i class="fas fa-chalkboard-user text-emerald-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Profesores</h3>
        </div>
        <p class="text-text-secondary mb-5">Administra la plantilla docente y las asignaciones de cursos.</p>
        <div class="text-emerald-600 font-medium group-hover:text-emerald-700 transition-smooth flex items-center">
            Gestionar
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>

    <!-- Tarjeta Alumnos -->
    <a href="" class="bg-slate-50 p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-slate-200 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-sky-100 rounded-lg mr-4 group-hover:bg-sky-200 transition-smooth">
                <i class="fas fa-user-graduate text-sky-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Alumnos</h3>
        </div>
        <p class="text-text-secondary mb-5">Administra la información de los alumnos registrados.</p>
        <div class="text-sky-600 font-medium group-hover:text-sky-700 transition-smooth flex items-center">
            Gestionar
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>


    <!-- Tarjeta Preregistros -->
    <a href="{{ route('coordinador.preregistros.index') }}" class="bg-slate-50 p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-slate-200 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-indigo-100 rounded-lg mr-4 group-hover:bg-indigo-200 transition-smooth">
                <i class="fas fa-file-signature text-indigo-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Preregistros</h3>
        </div>
        <p class="text-text-secondary mb-5">Analizar demanda y asignar grupos según los preregistros.</p>
        <div class="text-indigo-600 font-medium group-hover:text-indigo-700 transition-smooth flex items-center">
            Gestionar
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>



    <!-- Tarjeta Grupos -->
    <a href="{{ route('coordinador.grupos.index') }}" class="bg-slate-50 p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-slate-200 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-teal-100 rounded-lg mr-4 group-hover:bg-teal-200 transition-smooth">
                <i class="fas fa-users-between-lines text-teal-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Grupos</h3>
        </div>
        <p class="text-text-secondary mb-5">Gestiona los grupos académicos y la asignación de alumnos.</p>
        <div class="text-teal-600 font-medium group-hover:text-teal-700 transition-smooth flex items-center">
            Gestionar
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>


    

    
    <!-- Tarjeta Constancias -->
    <a href="" class="bg-slate-50 p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-slate-200 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-amber-100 rounded-lg mr-4 group-hover:bg-amber-200 transition-smooth">
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


     
    <!-- Tarjeta de Aulas -->
    <a href="{{ route('coordinador.aulas.index') }}" class="bg-slate-50 p-6 rounded-2xl shadow-card hover:shadow-card-hover transition-smooth border border-slate-200 card-hover group">
        <div class="flex items-center mb-5">
            <div class="p-3 bg-cyan-100 rounded-xl mr-4 group-hover:bg-cyan-200 transition-smooth">
                <i class="fas fa-door-open text-cyan-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Aulas</h3>
        </div>
        <p class="text-text-secondary mb-5">Gestiona las aulas y espacios disponibles del plantel</p>
        <div class="text-cyan-600 font-medium group-hover:text-cyan-700 transition-smooth flex items-center">
            Gestionar
            <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
        </div>
    </a>
</div>
@endsection