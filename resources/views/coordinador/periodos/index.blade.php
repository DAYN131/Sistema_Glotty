{{-- resources/views/coordinador/periodos/index.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Gestión de Periodos Académicos')
@section('header-title', 'Gestión de Periodos Académicos')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        {{-- Tarjeta: Total Periodos --}}
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-calendar-alt text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Periodos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $periodos->total() }}</p>
                </div>
            </div>
        </div>

        {{-- Tarjeta: En Configuración --}}
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-gray-100 p-3 rounded-xl">
                    <i class="fas fa-cogs text-gray-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">En Configuración</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ $periodos->where('estado', 'configuracion')->count() }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Tarjeta: Pre-registros Activos --}}
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-yellow-100 p-3 rounded-xl">
                    <i class="fas fa-user-clock text-yellow-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Pre-registros</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ $periodos->where('estado', 'preregistros_activos')->count() }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Tarjeta: En Curso --}}
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-green-100 p-3 rounded-xl">
                    <i class="fas fa-play-circle text-green-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">En Curso</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ $periodos->where('estado', 'en_curso')->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card p-6">
        {{-- Header con Filtros --}}
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Listado de Periodos</h2>
                <p class="text-gray-600 mt-1">Gestiona los periodos académicos del sistema</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                {{-- Filtro por Estado --}}
                <div class="relative">
                    <select id="filtro-estado" 
                            class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth cursor-pointer">
                        <option value="">Todos los estados</option>
                        <option value="configuracion" {{ request('estado') == 'configuracion' ? 'selected' : '' }}>En Configuración</option>
                        <option value="preregistros_activos" {{ request('estado') == 'preregistros_activos' ? 'selected' : '' }}>Pre-registros Activos</option>
                        <option value="en_curso" {{ request('estado') == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                        <option value="finalizado" {{ request('estado') == 'finalizado' ? 'selected' : '' }}>Finalizados</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>

                {{-- Botón Nuevo Periodo --}}
                <a href="{{ route('coordinador.periodos.create') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-smooth flex items-center space-x-2 w-fit">
                    <i class="fas fa-plus"></i>
                    <span>Nuevo Periodo</span>
                </a>
            </div>
        </div>

        {{-- Contador de resultados filtrados --}}
        @if(request('estado'))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-filter text-blue-600 mr-2"></i>
                        <span class="text-blue-800">
                            Mostrando periodos en estado: 
                            <strong class="capitalize">
                                {{ str_replace('_', ' ', request('estado')) }}
                            </strong>
                            ({{ $periodos->total() }} resultados)
                        </span>
                    </div>
                    <a href="{{ route('coordinador.periodos.index') }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm flex items-center space-x-1">
                        <i class="fas fa-times"></i>
                        <span>Limpiar filtro</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- Notificaciones --}}
        @include('partials.notifications')

        @if($periodos->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-calendar-plus text-gray-400 text-6xl mb-4"></i>
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
                    <div class="border border-gray-200 rounded-xl p-5 hover:shadow-card-hover transition-smooth">
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4">
                            {{-- Información del Periodo --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <h3 class="font-semibold text-lg text-gray-800">{{ $periodo->nombre_periodo }}</h3>
                                    <span class="text-xs px-3 py-1 rounded-full font-medium 
                                        @if($periodo->estado == 'configuracion') bg-gray-100 text-gray-800
                                        @elseif($periodo->estado == 'preregistros_activos') bg-yellow-100 text-yellow-800
                                        @elseif($periodo->estado == 'en_curso') bg-green-100 text-green-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $periodo->estado)) }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar-day text-gray-500"></i>
                                        <span>Inicio: <span class="font-medium">{{ $periodo->fecha_inicio->format('d/m/Y') }}</span></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar-check text-gray-500"></i>
                                        <span>Fin: <span class="font-medium">{{ $periodo->fecha_fin->format('d/m/Y') }}</span></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-clock text-gray-500"></i>
                                        <span>Duración: <span class="font-medium">{{ $periodo->fecha_inicio->diffInDays($periodo->fecha_fin) }} días</span></span>
                                    </div>
                                </div>

                                {{-- Estadísticas rápidas --}}
                                <div class="flex flex-wrap gap-4 mt-3 text-sm">
                                    <span class="text-gray-600">
                                        <i class="fas fa-users text-gray-400 mr-1"></i>
                                        Grupos: <strong>{{ $periodo->grupos_count ?? 0 }}</strong>
                                    </span>
                                    <span class="text-gray-600">
                                        <i class="fas fa-user-plus text-gray-400 mr-1"></i>
                                        Pre-registros: <strong>{{ $periodo->preregistros_count ?? 0 }}</strong>
                                    </span>
                                    <span class="text-gray-600">
                                        <i class="fas fa-check-circle text-gray-400 mr-1"></i>
                                        Pagados: <strong>{{ $periodo->preregistros_pagados_count ?? 0 }}</strong>
                                    </span>
                                </div>

                                {{-- Progreso del Flujo --}}
                                <div class="mt-4">
                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                                        <span>Progreso del periodo</span>
                                        <span>
                                            @if($periodo->estado == 'configuracion') 25%
                                            @elseif($periodo->estado == 'preregistros_activos') 50%
                                            @elseif($periodo->estado == 'en_curso') 75%
                                            @else 100% @endif
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full 
                                            @if($periodo->estado == 'configuracion') bg-gray-400 w-1/4
                                            @elseif($periodo->estado == 'preregistros_activos') bg-yellow-400 w-1/2
                                            @elseif($periodo->estado == 'en_curso') bg-green-400 w-3/4
                                            @else bg-blue-400 w-full @endif">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SECCIÓN DE ACCIONES MEJORADA --}}
                            <div class="flex flex-col gap-3 lg:flex-shrink-0 min-w-[200px]">
                                {{-- Botón Ver Detalles --}}
                                <a href="{{ route('coordinador.periodos.show', $periodo) }}"
                                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg transition-smooth flex items-center justify-center gap-2 text-sm w-full">
                                    <i class="fas fa-eye"></i>
                                    <span>Ver Detalles</span>
                                </a>

                                {{-- Acciones de Administración --}}
                                <div class="border-t pt-3">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 text-center">Administración</h4>
                                    
                                    <div class="flex flex-col gap-2">
                                        {{-- Editar --}}
                                        @if($periodo->estaEnConfiguracion())
                                            <a href="{{ route('coordinador.periodos.edit', $periodo) }}"
                                                class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-4 py-2 rounded-lg transition-smooth flex items-center justify-center gap-2 text-sm w-full">
                                                <i class="fas fa-edit"></i>
                                                <span>Editar Periodo</span>
                                            </a>

                                            {{-- Eliminar --}}
                                            @if($periodo->puedeEliminarse())
                                                <form action="{{ route('coordinador.periodos.destroy', $periodo) }}" method="POST" class="w-full">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg transition-smooth flex items-center justify-center gap-2 text-sm w-full"
                                                            onclick="return confirm('¿Estás seguro de eliminar el periodo {{ $periodo->nombre_periodo }}? Esta acción no se puede deshacer.')">
                                                        <i class="fas fa-trash"></i>
                                                        <span>Eliminar Periodo</span>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif

                                        {{-- Dropdown Cambiar Estado --}}
                                        <div class="relative">
                                            <select name="nuevo_estado" 
                                                    class="cambiar-estado-selector appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-smooth cursor-pointer w-full text-sm"
                                                    data-periodo-id="{{ $periodo->id }}"
                                                    data-periodo-nombre="{{ $periodo->nombre_periodo }}"
                                                    data-current-state="{{ $periodo->estado }}">
                                                <option value="" disabled selected>↻ Cambiar Estado</option>
                                                <option value="configuracion" {{ $periodo->estado == 'configuracion' ? 'disabled' : '' }}>
                                                    ⚙ Configuración
                                                </option>
                                                <option value="preregistros_activos" {{ $periodo->estado == 'preregistros_activos' ? 'disabled' : '' }}>
                                                    ✎ Pre-registros Activos
                                                </option>
                                                <option value="en_curso" {{ $periodo->estado == 'en_curso' ? 'disabled' : '' }}>
                                                    ⦿ En Curso
                                                </option>
                                                <option value="finalizado" {{ $periodo->estado == 'finalizado' ? 'disabled' : '' }}>
                                                    ✓ Finalizado
                                                </option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                                <i class="fas fa-chevron-down text-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            @if($periodos->hasPages())
                <div class="mt-6">
                    {{ $periodos->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection

@push('scripts')
<script>
    // Auto-ocultar notificaciones después de 5 segundos
    setTimeout(function() {
        const notifications = [
            'success-notification',
            'warning-notification', 
            'error-notification'
        ];
        
        notifications.forEach(id => {
            const notification = document.getElementById(id);
            if (notification) {
                notification.style.display = 'none';
            }
        });
    }, 5000);

    // Filtro por estado
    document.getElementById('filtro-estado').addEventListener('change', function() {
        const estado = this.value;
        const url = new URL(window.location.href);
        
        if (estado) {
            url.searchParams.set('estado', estado);
        } else {
            url.searchParams.delete('estado');
        }
        
        window.location.href = url.toString();
    });

    // Cambio de estado con dropdown
    document.addEventListener('DOMContentLoaded', function() {
        // Preservar filtro
        const urlParams = new URLSearchParams(window.location.search);
        const estadoFiltrado = urlParams.get('estado');
        if (estadoFiltrado) {
            document.getElementById('filtro-estado').value = estadoFiltrado;
        }

        // Inicializar selectores de estado
        document.querySelectorAll('.cambiar-estado-selector').forEach(select => {
            select.addEventListener('change', function() {
                const nuevoEstado = this.value;
                const periodoId = this.getAttribute('data-periodo-id');
                const periodoNombre = this.getAttribute('data-periodo-nombre');
                const estadoActual = this.getAttribute('data-current-state');
                
                if (!nuevoEstado) return;
                
                // Obtener nombre del estado seleccionado
                const estadoNombre = this.options[this.selectedIndex].text.replace(/[🔧📝🎯✅⚙️]/g, '').trim();
                
                // Confirmar cambio
                if (confirm(`¿Estás seguro de cambiar el estado del periodo "${periodoNombre}" a "${estadoNombre}"?`)) {
                    // Crear formulario dinámico
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/coordinador/periodos/${periodoId}/cambiar-estado`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const estadoInput = document.createElement('input');
                    estadoInput.type = 'hidden';
                    estadoInput.name = 'nuevo_estado';
                    estadoInput.value = nuevoEstado;
                    
                    form.appendChild(csrfToken);
                    form.appendChild(estadoInput);
                    document.body.appendChild(form);
                    form.submit();
                } else {
                    // Resetear selector si cancela
                    this.value = '';
                }
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    .transition-smooth {
        transition: all 0.3s ease;
    }
    .shadow-card {
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    .shadow-card-hover:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    /* Estilos para el dropdown de cambiar estado */
    .cambiar-estado-selector option:disabled {
        color: #9CA3AF;
        background-color: #F3F4F6;
    }
    
    .cambiar-estado-selector option:first-child {
        font-weight: 600;
        color: #6B7280;
    }
</style>
@endpush