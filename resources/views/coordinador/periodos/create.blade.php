@extends('layouts.coordinador')

@section('title', 'Crear Periodo')
@section('header-title', 'Crear Nuevo Periodo Académico')

@section('content')
<div class="bg-white rounded-2xl shadow-card p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Nuevo Periodo Académico</h2>
        <p class="text-gray-600">Complete la información del nuevo periodo académico</p>
    </div>

    <form action="{{ route('coordinador.periodos.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Nombre del Periodo -->
            <div class="col-span-2">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Periodo <span class="text-red-500">*</span>
                </label>
                <select name="nombre" id="nombre" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                    <option value="">Seleccione un periodo</option>
                    <option value="AGOSTO-DIC">AGOSTO-DICIEMBRE</option>
                    <option value="ENERO-JUNIO">ENERO-JUNIO</option>
                    <option value="INVIERNO">INVIERNO</option>
                    <option value="VERANO1">VERANO 1</option>
                    <option value="VERANO2">VERANO 2</option>
                </select>
                @error('nombre')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fecha Inicio -->
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Inicio <span class="text-red-500">*</span>
                </label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" required
                    value="{{ old('fecha_inicio') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                @error('fecha_inicio')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fecha Fin -->
            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Fin <span class="text-red-500">*</span>
                </label>
                <input type="date" name="fecha_fin" id="fecha_fin" required
                    value="{{ old('fecha_fin') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                @error('fecha_fin')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Año -->
            <div>
                <label for="anio" class="block text-sm font-medium text-gray-700 mb-2">
                    Año <span class="text-red-500">*</span>
                </label>
                <select name="anio" id="anio" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                    <option value="">Seleccione el año</option>
                    @for($i = date('Y') - 1; $i <= date('Y') + 2; $i++)
                        <option value="{{ $i }}" {{ old('anio', $periodo->anio ?? '') == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
                @error('anio')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Checkbox Activo -->
            <div class="col-span-2">
                <div class="flex items-center space-x-3">
                    <input type="checkbox" name="activo" id="activo" value="1"
                        {{ old('activo') ? 'checked' : '' }}
                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="activo" class="text-sm font-medium text-gray-700">
                        Marcar como periodo activo
                    </label>
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    Al activar este periodo, se desactivarán automáticamente los demás periodos.
                </p>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="{{ route('coordinador.periodos.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Cancelar</span>
            </a>
            <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                <i class="fas fa-save"></i>
                <span>Crear Periodo</span>
            </button>
        </div>
    </form>
</div>

<script>
    // Validación básica de fechas en el cliente
    document.addEventListener('DOMContentLoaded', function() {
        const fechaInicio = document.getElementById('fecha_inicio');
        const fechaFin = document.getElementById('fecha_fin');
        
        fechaInicio.addEventListener('change', function() {
            if (fechaInicio.value && fechaFin.value) {
                if (fechaInicio.value > fechaFin.value) {
                    fechaFin.value = '';
                    alert('La fecha de fin debe ser posterior a la fecha de inicio');
                }
            }
        });
        
        fechaFin.addEventListener('change', function() {
            if (fechaInicio.value && fechaFin.value && fechaInicio.value > fechaFin.value) {
                alert('La fecha de fin debe ser posterior a la fecha de inicio');
                fechaFin.value = '';
            }
        });
    });
</script>
@endsection