{{-- resources/views/coordinador/grupos/create.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Crear Grupo - Glotty')
@section('header-title', 'Crear Nuevo Grupo')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Crear Grupo - Estado: Planificado</h2>
        </div>
        
        <div class="p-6">
            <form action="{{ route('coordinador.grupos.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nivel -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nivel de Inglés *</label>
                        <select name="nivel_ingles" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Selecciona un nivel</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ old('nivel_ingles', $nivelSugerido) == $i ? 'selected' : '' }}>
                                    Nivel {{ $i }}
                                </option>
                            @endfor
                        </select>
                        @error('nivel_ingles')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Letra del Grupo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Letra del Grupo *</label>
                        <select name="letra_grupo" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Selecciona una letra</option>
                            <option value="A" {{ old('letra_grupo') == 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('letra_grupo') == 'B' ? 'selected' : '' }}>B</option>
                            <option value="C" {{ old('letra_grupo') == 'C' ? 'selected' : '' }}>C</option>
                            <option value="D" {{ old('letra_grupo') == 'D' ? 'selected' : '' }}>D</option>
                        </select>
                        @error('letra_grupo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Periodo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Periodo *</label>
                        <select name="periodo_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Selecciona un periodo</option>
                            @foreach($periodos as $periodo)
                                <option value="{{ $periodo->id }}" {{ old('periodo_id') == $periodo->id ? 'selected' : '' }}>
                                    {{ $periodo->nombre }} ({{ $periodo->fecha_inicio->format('d/m/Y') }} - {{ $periodo->fecha_fin->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('periodo_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Horario -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Horario *</label>
                        <select name="horario_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Selecciona un horario</option>
                            @foreach($horarios as $horario)
                                @php
                                    $diasArray = is_string($horario->dias) ? json_decode($horario->dias, true) : $horario->dias;
                                    $diasArray = $diasArray ?? [];
                                    $diasTexto = !empty($diasArray) ? implode(', ', $diasArray) : 'Días no especificados';
                                @endphp
                                <option value="{{ $horario->id }}" {{ old('horario_id') == $horario->id ? 'selected' : '' }}>
                                    {{ $horario->nombre }} ({{ $horario->tipo }}) - {{ $diasTexto }} {{ $horario->hora_inicio->format('H:i') }}-{{ $horario->hora_fin->format('H:i') }}
                                </option>
                            @endforeach
                        </select>
                        @error('horario_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Capacidad Máxima -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Capacidad Máxima *</label>
                        <input type="number" name="capacidad_maxima" 
                               value="{{ old('capacidad_maxima', 25) }}"
                               min="1" max="50"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                               placeholder="Ej: 25" required>
                        @error('capacidad_maxima')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Número máximo de estudiantes para este grupo</p>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-medium transition-colors flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i>
                        Crear Grupo
                    </button>
                    <a href="{{ route('coordinador.grupos.index') }}" 
                       class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-3 px-6 rounded-lg font-medium transition-colors flex items-center justify-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Información -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-xl mt-1"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Información importante</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p class="mt-1">• El grupo se creará en estado "Planificado"</p>
                    <p class="mt-1">• Posteriormente podrás asignar profesor y aula</p>
                    <p class="mt-1">• El grupo estará listo cuando tenga profesor y aula asignados</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection