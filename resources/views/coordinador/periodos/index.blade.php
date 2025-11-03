@extends('layouts.coordinador')

@section('title', 'Gestión de Periodos')
@section('header-title', 'Gestión de Periodos Académicos')

@section('content')
<!-- Estadísticas al inicio -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-card p-6">
        <div class="flex items-center space-x-4">
            <div class="bg-blue-100 p-3 rounded-xl">
                <i class="fas fa-calendar text-blue-600 text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Total Periodos</p>
                <p class="text-2xl font-bold text-gray-800">{{ $periodos->count() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-card p-6">
        <div class="flex items-center space-x-4">
            <div class="bg-green-100 p-3 rounded-xl">
                <i class="fas fa-play-circle text-green-600 text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Periodos Activos</p>
                <p class="text-2xl font-bold text-gray-800">{{ $periodos->where('activo', true)->count() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-card p-6">
        <div class="flex items-center space-x-4">
            <div class="bg-purple-100 p-3 rounded-xl">
                <i class="fas fa-history text-purple-600 text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Periodos Pasados</p>
                <p class="text-2xl font-bold text-gray-800">{{ $periodos->where('activo', false)->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Lista de periodos -->
<div class="bg-white rounded-2xl shadow-card p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Periodos Académicos</h2>
        <a href="{{ route('coordinador.periodos.create') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Nuevo Periodo</span>
        </a>
    </div>

    <!-- Mensajes -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($periodos->isEmpty())
        <div class="text-center py-12">
            <i class="fas fa-calendar-times text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No hay periodos registrados</h3>
            <p class="text-gray-500 mb-6">Comienza creando el primer periodo académico</p>
            <a href="{{ route('coordinador.periodos.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth inline-flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Crear Primer Periodo</span>
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($periodos as $periodo)
            <div class="border border-gray-200 rounded-xl p-5 hover:shadow-card-hover transition-smooth {{ $periodo->activo ? 'bg-blue-50 border-blue-200' : '' }}">
                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                    <!-- Información del periodo -->
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="font-semibold text-lg text-gray-800">{{ $periodo->nombre }}</h3>
                            @if($periodo->activo)
                            <span class="bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-medium flex items-center gap-1.5">
                                <i class="fas fa-circle text-[6px]"></i>
                                <span>Activo</span>
                            </span>
                            @else
                            <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full font-medium">
                                Inactivo
                            </span>
                            @endif
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar-day text-blue-500"></i>
                                <span>Inicio: <span class="font-medium">{{ $periodo->fecha_inicio->format('d/m/Y') }}</span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar-check text-green-500"></i>
                                <span>Fin: <span class="font-medium">{{ $periodo->fecha_fin->format('d/m/Y') }}</span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clock text-purple-500"></i>
                                <span>Duración: <span class="font-medium">{{ $periodo->fecha_inicio->diffInDays($periodo->fecha_fin) }} días</span></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Acciones -->
                    <div class="flex items-center gap-3 lg:flex-shrink-0">
                        <a href="{{ route('coordinador.periodos.edit', $periodo->id) }}" 
                           class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg transition-smooth flex items-center gap-2">
                            <i class="fas fa-edit"></i>
                            <span>Editar</span>
                        </a>
                        
                        @if(!$periodo->activo)
                        <form action="{{ route('coordinador.periodos.destroy', $periodo->id) }}" method="POST" class="inline">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg transition-smooth flex items-center gap-2"
                                    onclick="return confirm('¿Estás seguro de eliminar el periodo {{ $periodo->nombre }}?')">
                                <i class="fas fa-trash"></i>
                                <span>Eliminar</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection