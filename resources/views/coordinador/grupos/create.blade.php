{{-- resources/views/coordinador/grupos/create.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Crear Nuevo Grupo - Glotty')
@section('header-title', 'Crear Nuevo Grupo')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Alertas -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-400 text-xl mr-3"></i>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-400 text-xl mr-3"></i>
                <div>
                    <h3 class="text-red-800 font-medium">Errores en el formulario:</h3>
                    <ul class="mt-2 text-red-700 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Información del Periodo Activo -->
    @if($periodoActivo)
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-blue-700 font-medium">Periodo Activo: {{ $periodoActivo->nombre_periodo ?? $periodoActivo->nombre }}</p>
                <p class="text-blue-600 text-sm mt-1">
                    Fecha: {{ \Carbon\Carbon::parse($periodoActivo->fecha_inicio)->format('d/m/Y') }} - 
                    {{ \Carbon\Carbon::parse($periodoActivo->fecha_fin)->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Formulario de Creación -->
    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Información del Grupo</h2>
        </div>
        
        <div class="p-6">
            <form action="{{ route('coordinador.grupos.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Columna Izquierda: Información Básica -->
                    <div class="space-y-6">
                        <!-- Nivel -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                <i class="fas fa-layer-group text-blue-500 mr-2"></i>Nivel del Grupo *
                            </label>
                            <select name="nivel_ingles" 
                                    class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                                    required>
                                <option value="">Selecciona el nivel</option>
                                @foreach(\App\Models\Grupo::NIVELES as $nivel => $descripcion)
                                    <option value="{{ $nivel }}" {{ old('nivel_ingles') == $nivel ? 'selected' : '' }}>
                                        {{ $descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Letra del Grupo -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                <i class="fas fa-font text-blue-500 mr-2"></i>Letra del Grupo *
                            </label>
                            <select name="letra_grupo" 
                                    class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                                    required>
                                <option value="">Selecciona la letra</option>
                                @foreach(\App\Models\Grupo::LETRAS_GRUPO as $letra)
                                    <option value="{{ $letra }}" {{ old('letra_grupo') == $letra ? 'selected' : '' }}>
                                        {{ $letra }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-slate-500">Ejemplo: Nivel 1-A, Nivel 2-B, etc.</p>
                        </div>

                        <!-- Periodo -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                <i class="fas fa-calendar text-blue-500 mr-2"></i>Periodo *
                            </label>
                            <select name="periodo_id" 
                                    class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                                    required>
                                <option value="">Selecciona el periodo</option>
                                @foreach($periodos as $periodo)
                                    <option value="{{ $periodo->id }}" 
                                            {{ (old('periodo_id') == $periodo->id || $periodoActivo?->id == $periodo->id) ? 'selected' : '' }}>
                                        {{ $periodo->nombre_periodo ?? $periodo->nombre }} 
                                        ({{ \Carbon\Carbon::parse($periodo->fecha_inicio)->format('d/m/Y') }} - 
                                         {{ \Carbon\Carbon::parse($periodo->fecha_fin)->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Capacidad Máxima -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                <i class="fas fa-users text-blue-500 mr-2"></i>Capacidad Máxima *
                            </label>
                            <input type="number" 
                                   name="capacidad_maxima" 
                                   value="{{ old('capacidad_maxima', 25) }}"
                                   min="15" 
                                   max="40"
                                   class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                                   required>
                            <p class="mt-1 text-xs text-slate-500">Mínimo 15, máximo 40 estudiantes</p>
                        </div>
                    </div>

                    <!-- Columna Derecha: Asignaciones -->
                    <div class="space-y-6">
                        <!-- Horario -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                <i class="fas fa-clock text-blue-500 mr-2"></i>Horario *
                            </label>
                            <select name="horario_periodo_id" 
                                    class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                                    required>
                                <option value="">Selecciona el horario</option>
                                @foreach($horarios as $horario)
                                    @php
                                        $diasArray = [];
                                        if (is_array($horario->dias)) {
                                            $diasArray = $horario->dias;
                                        } elseif (is_string($horario->dias)) {
                                            $decoded = json_decode($horario->dias, true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                $diasArray = $decoded;
                                            } else {
                                                $diasArray = array_map('trim', explode(',', $horario->dias));
                                            }
                                        }
                                        $diasArray = array_filter($diasArray);
                                        $diasTexto = !empty($diasArray) ? implode(', ', $diasArray) : 'Días no especificados';
                                        
                                        $horaInicio = $horario->hora_inicio ? \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') : '--:--';
                                        $horaFin = $horario->hora_fin ? \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') : '--:--';
                                    @endphp
                                    <option value="{{ $horario->id }}" {{ old('horario_periodo_id') == $horario->id ? 'selected' : '' }}>
                                        {{ $horario->nombre }} - 
                                        {{ $diasTexto }} 
                                        ({{ $horaInicio }} - {{ $horaFin }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Profesor -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                <i class="fas fa-chalkboard-teacher text-blue-500 mr-2"></i>Profesor (Opcional)
                            </label>
                            <select name="profesor_id" 
                                    class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                                <option value="">Sin profesor asignado</option>
                                @foreach($profesores as $profesor)
                                    <option value="{{ $profesor->id }}" {{ old('profesor_id') == $profesor->id ? 'selected' : '' }}>
                                        {{ $profesor->nombre_profesor ?? 'Profesor ' . $profesor->id }}
                                        @if($profesor->especialidad)
                                            - {{ $profesor->especialidad }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Aula -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                <i class="fas fa-door-open text-blue-500 mr-2"></i>Aula (Opcional)
                            </label>
                            <select name="aula_id" 
                                    class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                                <option value="">Sin aula asignada</option>
                                @foreach($aulas as $aula)
                                    <option value="{{ $aula->id }}" {{ old('aula_id') == $aula->id ? 'selected' : '' }}>
                                        {{ $aula->id_aula ?? 'Aula ' . $aula->id }} 
                                        @if($aula->edificio)
                                            - {{ $aula->edificio }}
                                        @endif
                                        (Capacidad: {{ $aula->capacidad ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Vista Previa del Grupo -->
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                            <h4 class="font-medium text-slate-800 mb-2 flex items-center">
                                <i class="fas fa-eye text-blue-500 mr-2"></i>
                                Vista Previa del Grupo
                            </h4>
                            <div class="text-sm text-slate-600 space-y-1">
                                <p id="vista-previa-nombre" class="font-semibold text-slate-800">Nombre: <span class="text-blue-600">-</span></p>
                                <p id="vista-previa-estado" class="text-slate-600">Estado: <span class="font-medium">Planificado</span></p>
                                <p id="vista-previa-capacidad" class="text-slate-600">Capacidad: <span class="font-medium">25 estudiantes</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 mt-6 border-t border-slate-200">
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium py-3 px-6 rounded-xl transition-smooth flex items-center justify-center shadow-soft">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Crear Grupo
                    </button>
                    <a href="{{ route('coordinador.grupos.index') }}" 
                       class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium py-3 px-6 rounded-xl transition-smooth flex items-center justify-center text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver a Grupos
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-800">Estados del Grupo</h3>
            </div>
            <ul class="text-slate-600 text-sm space-y-2">
                <li><span class="font-medium">Planificado:</span> Grupo creado sin asignaciones</li>
                <li><span class="font-medium">Con Profesor:</span> Tiene profesor asignado</li>
                <li><span class="font-medium">Con Aula:</span> Tiene aula asignada</li>
                <li><span class="font-medium">Activo:</span> Listo para recibir estudiantes</li>
            </ul>
        </div>
        
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-lightbulb text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-800">Recomendaciones</h3>
            </div>
            <ul class="text-slate-600 text-sm space-y-2">
                <li>• Asigna horarios según la demanda de preregistros</li>
                <li>• Verifica la disponibilidad de profesores y aulas</li>
                <li>• Considera la capacidad del aula al asignarla</li>
                <li>• Puedes activar el grupo después de completar las asignaciones</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario
    const nivelSelect = document.querySelector('select[name="nivel_ingles"]');
    const letraSelect = document.querySelector('select[name="letra_grupo"]');
    const capacidadInput = document.querySelector('input[name="capacidad_maxima"]');
    const profesorSelect = document.querySelector('select[name="profesor_id"]');
    const aulaSelect = document.querySelector('select[name="aula_id"]');
    
    // Elementos de vista previa
    const vistaPreviaNombre = document.getElementById('vista-previa-nombre');
    const vistaPreviaEstado = document.getElementById('vista-previa-estado');
    const vistaPreviaCapacidad = document.getElementById('vista-previa-capacidad');

    // Función para actualizar vista previa
    function actualizarVistaPrevia() {
        const nivel = nivelSelect.value;
        const letra = letraSelect.value;
        const capacidad = capacidadInput.value;
        const tieneProfesor = profesorSelect.value !== '';
        const tieneAula = aulaSelect.value !== '';
        
        // Actualizar nombre
        if (nivel && letra) {
            vistaPreviaNombre.innerHTML = `Nombre: <span class="text-blue-600">Nivel ${nivel}-${letra}</span>`;
        } else {
            vistaPreviaNombre.innerHTML = `Nombre: <span class="text-slate-400">-</span>`;
        }
        
        // Actualizar estado
        let estado = 'Planificado';
        let estadoColor = 'text-slate-600';
        
        if (tieneProfesor && tieneAula) {
            estado = 'Activo';
            estadoColor = 'text-green-600';
        } else if (tieneProfesor) {
            estado = 'Con Profesor';
            estadoColor = 'text-blue-600';
        } else if (tieneAula) {
            estado = 'Con Aula';
            estadoColor = 'text-purple-600';
        }
        
        vistaPreviaEstado.innerHTML = `Estado: <span class="font-medium ${estadoColor}">${estado}</span>`;
        
        // Actualizar capacidad
        vistaPreviaCapacidad.innerHTML = `Capacidad: <span class="font-medium">${capacidad} estudiantes</span>`;
    }

    // Event listeners para cambios
    [nivelSelect, letraSelect, capacidadInput, profesorSelect, aulaSelect].forEach(element => {
        element.addEventListener('change', actualizarVistaPrevia);
    });
    
    capacidadInput.addEventListener('input', actualizarVistaPrevia);

    // Inicializar vista previa
    actualizarVistaPrevia();
});
</script>
@endpush
@endsection