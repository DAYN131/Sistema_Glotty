{{-- resources/views/coordinador/periodos/edit.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Editar Periodo - ' . $periodo->nombre_periodo)
@section('header-title', 'Editar Periodo')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-card p-6">
        {{-- Header --}}
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Editar Periodo</h2>
            <p class="text-gray-600 mt-1">Actualiza las fechas del periodo académico</p>
        </div>

        {{-- Información Actual --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-800 mb-2">Información Actual</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-blue-600 font-medium">Nombre:</span>
                    <span class="text-blue-800">{{ $periodo->nombre_periodo }}</span>
                </div>
                <div>
                    <span class="text-blue-600 font-medium">Estado:</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                        {{ ucfirst(str_replace('_', ' ', $periodo->estado)) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Formulario --}}
        <form action="{{ route('coordinador.periodos.update', $periodo) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Fecha Inicio --}}
            <div class="mb-6">
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Inicio <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       name="fecha_inicio" 
                       id="fecha_inicio"
                       value="{{ old('fecha_inicio', $periodo->fecha_inicio->format('Y-m-d')) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                       required>
                @error('fecha_inicio')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fecha Fin --}}
            <div class="mb-6">
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Finalización <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       name="fecha_fin" 
                       id="fecha_fin"
                       value="{{ old('fecha_fin', $periodo->fecha_fin->format('Y-m-d')) }}"
                       min="{{ $periodo->fecha_inicio->format('Y-m-d') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                       required>
                @error('fecha_fin')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Información de Duración --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <h4 class="font-medium text-gray-700 mb-2">Resumen del Periodo</h4>
                <div class="text-sm text-gray-600 space-y-1">
                    <p>• Duración total: <span id="duracion-dias" class="font-medium">{{ $periodo->fecha_inicio->diffInDays($periodo->fecha_fin) }} días</span></p>
                    <p>• Fecha de inicio: <span id="fecha-inicio-texto" class="font-medium">{{ $periodo->fecha_inicio->format('d/m/Y') }}</span></p>
                    <p>• Fecha de fin: <span id="fecha-fin-texto" class="font-medium">{{ $periodo->fecha_fin->format('d/m/Y') }}</span></p>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center justify-center gap-2 font-medium">
                    <i class="fas fa-save"></i>
                    <span>Guardar Cambios</span>
                </button>
                
                <a href="{{ route('coordinador.periodos.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-smooth flex items-center justify-center gap-2 font-medium">
                    <i class="fas fa-arrow-left"></i>
                    <span>Cancelar</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Sección de Eliminación --}}
    @if($periodo->puedeEliminarse())
    <div class="bg-white rounded-2xl shadow-card p-6 mt-6 border border-red-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="font-semibold text-red-700">Eliminar Periodo</h3>
                <p class="text-red-600 text-sm mt-1">
                    Esta acción no se puede deshacer. Se eliminará permanentemente el periodo.
                </p>
            </div>
            
            <form action="{{ route('coordinador.periodos.destroy', $periodo) }}" method="POST" class="sm:text-right">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center justify-center gap-2 font-medium"
                        onclick="return confirm('¿ESTÁS SEGURO?\\n\\nEsta acción eliminará permanentemente el periodo \"{{ $periodo->nombre_periodo }}\".\\n\\nEsta acción NO se puede deshacer.')">
                    <i class="fas fa-trash"></i>
                    <span>Eliminar Periodo</span>
                </button>
            </form>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Actualizar información en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        const fechaInicio = document.getElementById('fecha_inicio');
        const fechaFin = document.getElementById('fecha_fin');
        const duracionDias = document.getElementById('duracion-dias');
        const fechaInicioTexto = document.getElementById('fecha-inicio-texto');
        const fechaFinTexto = document.getElementById('fecha-fin-texto');

        function actualizarResumen() {
            const inicio = new Date(fechaInicio.value);
            const fin = new Date(fechaFin.value);
            
            if (inicio && fin && fin > inicio) {
                const diffTime = Math.abs(fin - inicio);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                duracionDias.textContent = diffDays + ' días';
            }
            
            // Actualizar fechas formateadas
            if (fechaInicio.value) {
                fechaInicioTexto.textContent = formatDate(fechaInicio.value);
            }
            if (fechaFin.value) {
                fechaFinTexto.textContent = formatDate(fechaFin.value);
            }
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        fechaInicio.addEventListener('change', function() {
            fechaFin.min = this.value;
            actualizarResumen();
        });

        fechaFin.addEventListener('change', actualizarResumen);
        
        // Inicializar
        actualizarResumen();
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
</style>
@endpush
@endsection