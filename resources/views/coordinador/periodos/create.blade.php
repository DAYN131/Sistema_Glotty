{{-- resources/views/coordinador/periodos/create.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Crear Nuevo Periodo')
@section('header-title', 'Crear Nuevo Periodo')

@section('content')
    <div class="max-w-2xl mx-auto">
        {{-- Formulario --}}
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Nuevo Periodo Académico</h2>
                <p class="text-gray-600">Completa la información para crear un nuevo periodo</p>
            </div>

            {{-- Notificaciones --}}
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-600 text-xl mr-3"></i>
                        <div>
                            <h4 class="text-red-800 font-medium mb-1">Errores en el formulario</h4>
                            <ul class="text-red-700 text-sm list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('coordinador.periodos.store') }}" method="POST">
                @csrf

                {{-- Campo: Nombre del Periodo --}}
                <div class="mb-6">
                    <label for="nombre_periodo" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del Periodo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="nombre_periodo"
                           name="nombre_periodo" 
                           value="{{ old('nombre_periodo') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                           placeholder="Ej: AGOSTO-DICIEMBRE 2024, INVIERNO 2024-2025"
                           required>
                    <p class="text-gray-500 text-xs mt-2">
                        Usa un nombre descriptivo que identifique claramente el periodo
                    </p>
                </div>

                {{-- Fechas --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Fecha Inicio --}}
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Inicio <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="fecha_inicio"
                               name="fecha_inicio" 
                               value="{{ old('fecha_inicio') }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-smooth"
                               required>
                    </div>

                    {{-- Fecha Fin --}}
                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Fin <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="fecha_fin"
                               name="fecha_fin" 
                               value="{{ old('fecha_fin') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-smooth"
                               required>
                    </div>
                </div>

                {{-- Vista Previa --}}
                <div id="vista-previa" class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-6">
                    <h4 class="font-semibold text-gray-800 mb-3">Resumen del Periodo</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Nombre:</span>
                            <span id="preview-nombre" class="font-medium text-gray-800 ml-2">
                                {{ old('nombre_periodo', '-') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600">Inicio:</span>
                            <span id="preview-inicio" class="font-medium text-gray-800 ml-2">
                                {{ old('fecha_inicio') ? \Carbon\Carbon::parse(old('fecha_inicio'))->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600">Fin:</span>
                            <span id="preview-fin" class="font-medium text-gray-800 ml-2">
                                {{ old('fecha_fin') ? \Carbon\Carbon::parse(old('fecha_fin'))->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <span class="text-gray-600">Duración:</span>
                        <span id="preview-duracion" class="font-medium text-gray-800 ml-2">-</span>
                    </div>
                </div>

                {{-- Información de Configuración --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-600 text-xl mr-3"></i>
                        <div>
                            <p class="text-blue-700 text-sm">
                                El periodo se creará en estado <strong>"Configuración"</strong> y podrás activar los pre-registros cuando esté listo.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center justify-center gap-2 font-medium">
                        <i class="fas fa-plus"></i>
                        <span>Crear Periodo</span>
                    </button>
                    
                    <a href="{{ route('coordinador.periodos.index') }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-smooth flex items-center justify-center gap-2 font-medium">
                        <i class="fas fa-arrow-left"></i>
                        <span>Cancelar</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Script para Vista Previa --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nombreInput = document.getElementById('nombre_periodo');
            const fechaInicioInput = document.getElementById('fecha_inicio');
            const fechaFinInput = document.getElementById('fecha_fin');
            
            function actualizarVistaPrevia() {
                const nombre = nombreInput.value;
                const fechaInicio = fechaInicioInput.value;
                const fechaFin = fechaFinInput.value;
                
                // Actualizar datos
                document.getElementById('preview-nombre').textContent = nombre || '-';
                document.getElementById('preview-inicio').textContent = fechaInicio ? 
                    formatDate(fechaInicio) : '-';
                document.getElementById('preview-fin').textContent = fechaFin ? 
                    formatDate(fechaFin) : '-';
                
                // Calcular duración
                if (fechaInicio && fechaFin) {
                    const inicio = new Date(fechaInicio);
                    const fin = new Date(fechaFin);
                    const diffTime = Math.abs(fin - inicio);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    document.getElementById('preview-duracion').textContent = `${diffDays} días`;
                } else {
                    document.getElementById('preview-duracion').textContent = '-';
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
            
            // Event listeners
            nombreInput.addEventListener('input', actualizarVistaPrevia);
            fechaInicioInput.addEventListener('change', function() {
                if (this.value) {
                    fechaFinInput.min = this.value;
                }
                actualizarVistaPrevia();
            });
            fechaFinInput.addEventListener('change', actualizarVistaPrevia);
            
            // Inicializar vista previa
            actualizarVistaPrevia();
        });
    </script>

    <style>
        .transition-smooth {
            transition: all 0.3s ease;
        }
        .shadow-card {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
    </style>
@endsection