{{-- resources/views/coordinador/grupos/edit.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Editar Grupo - ' . $grupo->nombre_completo)
@section('header-title', 'Editar Grupo')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Tarjeta Principal -->
    <div class="bg-white rounded-xl shadow-card overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white">Editando: {{ $grupo->nombre_completo }}</h2>
                    <p class="text-blue-100 text-sm mt-1">Periodo: {{ $grupo->periodo->nombre }}</p>
                </div>
                <a href="{{ route('coordinador.grupos.show', $grupo->id) }}" 
                   class="mt-3 md:mt-0 bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver al grupo
                </a>
            </div>
        </div>

        <!-- Formulario -->
        <form action="{{ route('coordinador.grupos.update', $grupo->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                <!-- Información Básica -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Grupo</label>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-lg font-semibold text-gray-900">{{ $grupo->nombre_completo }}</p>
                            <p class="text-sm text-gray-500 mt-1">El nombre del grupo no se puede modificar</p>
                        </div>
                    </div>
                    
                    
                </div>

                <!-- Configuración Principal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Horario -->
                    <div>
                        <label for="horario_periodo_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Horario <span class="text-red-500">*</span>
                        </label>
                        <select name="horario_periodo_id" id="horario_periodo_id" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('horario_periodo_id') border-red-500 @enderror" required>
                            <option value="">Seleccione un horario</option>
                            @foreach($horarios as $horario)
                                <option value="{{ $horario->id }}" {{ old('horario_periodo_id', $grupo->horario_periodo_id) == $horario->id ? 'selected' : '' }}>
                                    {{ $horario->nombre }} 
                                  
                                </option>
                            @endforeach
                        </select>
                        @error('horario_periodo_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Asignación de Recursos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Profesor -->
                    <div>
                        <label for="profesor_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Profesor
                        </label>
                        <select name="profesor_id" id="profesor_id" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('profesor_id') border-red-500 @enderror">
                            <option value="">Sin profesor asignado</option>
                            @foreach($profesores as $profesor)
                                <option value="{{ $profesor->id }}" {{ old('profesor_id', $grupo->profesor_id) == $profesor->id ? 'selected' : '' }}>
                                    {{ $profesor->nombre_profesor }} {{ $profesor->apellidos_profesor }}
                                </option>
                            @endforeach
                        </select>
                        @error('profesor_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Aula -->
                    <div>
                        <label for="aula_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Aula
                        </label>
                        <select name="aula_id" id="aula_id" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('aula_id') border-red-500 @enderror">
                            <option value="">Sin aula asignada</option>
                            @foreach($aulas as $aula)
                                <option value="{{ $aula->id }}" {{ old('aula_id', $grupo->aula_id) == $aula->id ? 'selected' : '' }}>
                                    {{ $aula->nombre }} (Capacidad: {{ $aula->capacidad }})
                                </option>
                            @endforeach
                        </select>
                        @error('aula_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Capacidad -->
                <div class="max-w-xs">
                    <label for="capacidad_maxima" class="block text-sm font-medium text-gray-700 mb-2">
                        Capacidad Máxima <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="capacidad_maxima" 
                           id="capacidad_maxima" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('capacidad_maxima') border-red-500 @enderror" 
                           value="{{ old('capacidad_maxima', $grupo->capacidad_maxima) }}" 
                           min="15" 
                           max="40" 
                           required>
                    @error('capacidad_maxima')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-2 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-700">
                            <span class="font-semibold">{{ $grupo->estudiantes_inscritos }}</span> estudiantes actualmente inscritos
                        </p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-blue-600 h-2 rounded-full" 
                                 style="width: {{ $grupo->porcentaje_ocupacion }}%">
                            </div>
                        </div>
                        <p class="text-xs text-blue-600 mt-1 text-right">
                            {{ number_format($grupo->porcentaje_ocupacion, 1) }}% de ocupación
                        </p>
                    </div>
                </div>

                <!-- Información de Estado Actual -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Estado Actual del Grupo</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div class="text-center">
                            <div class="p-2 bg-blue-100 rounded-lg text-blue-600 mb-1">
                                <i class="fas fa-users"></i>
                            </div>
                            <p class="text-gray-600">Estudiantes</p>
                            <p class="font-semibold text-gray-900">{{ $grupo->estudiantes_inscritos }}</p>
                        </div>
                        <div class="text-center">
                            <div class="p-2 {{ $grupo->profesor ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }} rounded-lg mb-1">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <p class="text-gray-600">Profesor</p>
                            <p class="font-semibold text-gray-900">
                                {{ $grupo->profesor ? 'Asignado' : 'Por asignar' }}
                            </p>
                        </div>
                        <div class="text-center">
                            <div class="p-2 {{ $grupo->aula ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }} rounded-lg mb-1">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <p class="text-gray-600">Aula</p>
                            <p class="font-semibold text-gray-900">
                                {{ $grupo->aula ? 'Asignada' : 'Por asignar' }}
                            </p>
                        </div>
                        <div class="text-center">
                            <div class="p-2 {{ $grupo->estado == 'activo' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }} rounded-lg mb-1">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <p class="text-gray-600">Estado</p>
                            <p class="font-semibold text-gray-900">{{ $grupo->estado_texto }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer del Formulario -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                    <p class="text-sm text-gray-600">
                        Última actualización: {{ $grupo->updated_at->format('d/m/Y H:i') }}
                    </p>
                    <div class="flex space-x-3">
                        <a href="{{ route('coordinador.grupos.show', $grupo->id) }}" 
                           class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Actualizar Grupo
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar estado automáticamente cuando se asignen profesor y aula
    const profesorSelect = document.getElementById('profesor_id');
    const aulaSelect = document.getElementById('aula_id');
    const estadoSelect = document.getElementById('estado');

    function actualizarEstadoSugerido() {
        const tieneProfesor = profesorSelect.value !== '';
        const tieneAula = aulaSelect.value !== '';
        
        if (tieneProfesor && tieneAula) {
            // Si ambos están asignados, sugerir estado "activo"
            if (estadoSelect.value === 'planificado' || estadoSelect.value === 'con_profesor' || estadoSelect.value === 'con_aula') {
                if (confirm('¿Desea cambiar el estado del grupo a "Activo"?')) {
                    estadoSelect.value = 'activo';
                }
            }
        } else if (tieneProfesor) {
            // Si solo tiene profesor, sugerir "con_profesor"
            if (estadoSelect.value === 'planificado') {
                estadoSelect.value = 'con_profesor';
            }
        } else if (tieneAula) {
            // Si solo tiene aula, sugerir "con_aula"
            if (estadoSelect.value === 'planificado') {
                estadoSelect.value = 'con_aula';
            }
        }
    }

    profesorSelect.addEventListener('change', actualizarEstadoSugerido);
    aulaSelect.addEventListener('change', actualizarEstadoSugerido);

    // Validación de capacidad
    const capacidadInput = document.getElementById('capacidad_maxima');
    capacidadInput.addEventListener('change', function() {
        const capacidad = parseInt(this.value);
        const estudiantesInscritos = {{ $grupo->estudiantes_inscritos }};
        
        if (capacidad < estudiantesInscritos) {
            alert(`⚠️ Advertencia: La capacidad (${capacidad}) no puede ser menor a los estudiantes inscritos (${estudiantesInscritos}).`);
            this.value = estudiantesInscritos;
        }
    });
});
</script>
@endsection