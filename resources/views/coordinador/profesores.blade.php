{{-- resources/views/coordinador/profesores/index.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Gestión de Profesores')
@section('header-title', 'Gestión de Profesores')

@section('content')
<div class="mb-8">
    <!-- Header espectacular -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-700 rounded-2xl shadow-soft p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Gestión de Profesores</h1>
                <p class="text-blue-100 text-lg">Administra la plantilla docente del sistema académico</p>
                <div class="flex items-center mt-4 space-x-4">
                    <div class="flex items-center">
                        <i class="fas fa-users text-blue-200 mr-2"></i>
                        <span class="text-blue-100">{{ $profesores->count() }} profesores registrados</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-chalkboard text-blue-200 mr-2"></i>
                        <span class="text-blue-100">{{ $totalGrupos ?? 0 }} grupos asignados</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="bg-white/20 p-4 rounded-xl">
                    <i class="fas fa-chalkboard-teacher text-4xl text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Card principal -->
<div class="bg-white rounded-2xl shadow-card overflow-hidden border border-gray-100">
    <!-- Card Header con acciones -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-5 border-b border-gray-200">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Lista de Profesores</h2>
                <p class="text-gray-600 mt-1">Gestiona toda la información de la plantilla docente</p>
            </div>
            <a href="{{ route('coordinador.profesores.create') }}" 
               class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i>
                Nuevo Profesor
            </a>
        </div>
    </div>

    <!-- Table mejorada -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Profesor</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Información</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contacto</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($profesores as $profesor)
                <tr class="hover:bg-blue-50 transition-all duration-200 group">
                    <!-- Columna Profesor -->
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                                <i class="fas fa-chalkboard-teacher text-white text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition-colors">
                                    {{ $profesor->nombre_profesor }} {{ $profesor->apellidos_profesor }}
                                </div>
                                <div class="text-sm text-gray-500 flex items-center mt-1">
                                    <i class="fas fa-envelope text-gray-400 mr-1 text-xs"></i>
                                    {{ $profesor->correo_profesor }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Columna Información -->
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="space-y-1">
                            <div class="text-sm">
                                <span class="font-mono bg-gray-100 px-2 py-1 rounded text-gray-700 text-xs">
                                    {{ $profesor->rfc_profesor }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500">
                                ID: {{ $profesor->id_profesor }}
                            </div>
                        </div>
                    </td>

                    <!-- Columna Contacto -->
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="space-y-1">
                            <div class="text-sm text-gray-900 flex items-center">
                                <i class="fas fa-phone text-gray-400 mr-2 text-xs"></i>
                                {{ $profesor->num_telefono }}
                            </div>
                            <div class="text-xs text-gray-500 flex items-center">
                                <i class="fas fa-clock text-gray-400 mr-2 text-xs"></i>
                                Registrado: {{ $profesor->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </td>

                    <!-- Columna Estado -->
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="flex flex-col space-y-2">
                           
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $profesor->grupos_count ?? 0 }} grupos
                            </span>
                        </div>
                    </td>

                    <!-- Columna Acciones -->
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="flex space-x-2">
                            <a href="{{ route('coordinador.profesores.edit', $profesor->id_profesor) }}" 
                               class="bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 px-3 py-2 rounded-lg font-medium transition-all duration-200 flex items-center text-sm group/btn">
                                <i class="fas fa-edit mr-1 group-hover/btn:scale-110 transition-transform"></i>
                                Editar
                            </a>
                            <form action="{{ route('coordinador.profesores.destroy', $profesor->id_profesor) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('¿Estás seguro de eliminar al profesor {{ $profesor->nombre_profesor }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 px-3 py-2 rounded-lg font-medium transition-all duration-200 flex items-center text-sm group/btn">
                                    <i class="fas fa-trash mr-1 group-hover/btn:scale-110 transition-transform"></i>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-100 p-6 rounded-full mb-4">
                                <i class="fas fa-users text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">No hay profesores registrados</h3>
                            <p class="text-gray-500 mb-6">Comienza agregando el primer profesor a la plantilla docente</p>
                            <a href="{{ route('coordinador.profesores.create') }}" 
                               class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-md hover:shadow-lg">
                                <i class="fas fa-plus mr-2"></i>
                                Agregar Primer Profesor
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer de la tabla -->
    @if($profesores->count() > 0)
    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
        <div class="flex justify-between items-center text-sm text-gray-600">
            <div>
                Mostrando <span class="font-semibold">{{ $profesores->count() }}</span> profesores
            </div>
            <div class="flex items-center space-x-1">
                <i class="fas fa-info-circle text-blue-500"></i>
                <span>Total: {{ $profesores->count() }} profesores en el sistema</span>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Script para mejoras interactivas -->
<script>
    // Efectos hover suaves para las filas
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(4px)';
            });
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });
    });
</script>

<style>
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    tbody tr {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endsection