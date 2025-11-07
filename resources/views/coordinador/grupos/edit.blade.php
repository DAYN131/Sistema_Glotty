@extends('layouts.coordinador')

@section('title', 'Editar Grupo')
@section('header-title', 'Editar Grupo Académico')

@section('content')
    <div class="bg-white rounded-2xl shadow-card p-6 max-w-3xl mx-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                Editando Grupo: Nivel {{ $grupo->nivel_ingles }} - "{{ $grupo->letra_grupo }}"
            </h2>
            <p class="text-gray-600">Modifique la información del grupo académico</p>
        </div>

        <form action="{{ route('coordinador.grupos.update', $grupo->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                <div>
                    <label for="periodo_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Periodo <span class="text-red-500">*</span>
                    </label>
                    <select name="periodo_id" id="periodo_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                        <option value="">Seleccione un periodo</option>
                        @foreach($periodos as $periodo)
                            <option value="{{ $periodo->id }}" {{ old('periodo_id', $grupo->periodo_id) == $periodo->id ? 'selected' : '' }}>
                                {{ $periodo->nombre }} - {{ $periodo->anio }} {{ $periodo->activo ? '(Activo)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('periodo_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="horario_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Horario <span class="text-red-500">*</span>
                    </label>
                    <select name="horario_id" id="horario_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                        <option value="">Seleccione un horario</option>
                        @foreach($horarios as $horario)
                            <option value="{{ $horario->id }}" {{ old('horario_id', $grupo->horario_id) == $horario->id ? 'selected' : '' }}>
                                {{ $horario->nombre }} ({{ $horario->tipo }})
                            </option>
                        @endforeach
                    </select>
                    @error('horario_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nivel_ingles" class="block text-sm font-medium text-gray-700 mb-2">
                        Nivel de Inglés <span class="text-red-500">*</span>
                    </label>
                    <select name="nivel_ingles" id="nivel_ingles" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                        <option value="">Seleccione un nivel</option>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('nivel_ingles', $grupo->nivel_ingles) == $i ? 'selected' : '' }}>Nivel {{ $i }}</option>
                        @endfor
                    </select>
                    @error('nivel_ingles')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="letra_grupo" class="block text-sm font-medium text-gray-700 mb-2">
                        Letra del Grupo <span class="text-red-500">*</span> (Ej: A, B, C)
                    </label>
                    <input type="text" name="letra_grupo" id="letra_grupo" required maxlength="1"
                           value="{{ old('letra_grupo', $grupo->letra_grupo) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth uppercase"
                           onkeyup="this.value = this.value.toUpperCase();">
                    @error('letra_grupo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="profesor_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Profesor (Opcional)
                    </label>
                    <select name="profesor_id" id="profesor_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                        <option value="">Sin asignar</option>
                        @foreach($profesores as $profesor)
                            <option value="{{ $profesor->id_profesor }}" {{ old('profesor_id', $grupo->profesor_id) == $profesor->id_profesor ? 'selected' : '' }}>
                                {{ $profesor->nombre_profesor }} {{ $profesor->apellidos_profesor }}
                            </option>
                        @endforeach
                    </select>
                    @error('profesor_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="aula_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Aula (Opcional)
                    </label>
                    <select name="aula_id" id="aula_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                        <option value="">Sin asignar</option>
                        @foreach($aulas as $aula)
                            <option value="{{ $aula->id_aula }}" {{ old('aula_id', $grupo->aula_id) == $aula->id_aula ? 'selected' : '' }}>
                                {{ $aula->id_aula }} (Edif: {{ $aula->edificio }}, Cap: {{ $aula->capacidad }})
                            </option>
                        @endforeach
                    </select>
                    @error('aula_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="capacidad_maxima" class="block text-sm font-medium text-gray-700 mb-2">
                        Capacidad Máxima <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="capacidad_maxima" id="capacidad_maxima" required min="1" max="50"
                           value="{{ old('capacidad_maxima', $grupo->capacidad_maxima) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                    @error('capacidad_maxima')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select name="estado" id="estado" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                        <option value="planificado" {{ old('estado', $grupo->estado) == 'planificado' ? 'selected' : '' }}>Planificado</option>
                        <option value="con_profesor" {{ old('estado', $grupo->estado) == 'con_profesor' ? 'selected' : '' }}>Con Profesor</option>
                        <option value="con_aula" {{ old('estado', $grupo->estado) == 'con_aula' ? 'selected' : '' }}>Con Aula</option>
                        <option value="activo" {{ old('estado', $grupo->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="cancelado" {{ old('estado', $grupo->estado) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    @error('estado')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <h4 class="font-semibold text-gray-800 mb-2">Información del Grupo</h4>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                    <div>
                        <span class="font-medium">Inscritos:</span>
                        {{ $grupo->estudiantes_inscritos }}
                    </div>
                    <div>
                        <span class="font-medium">Creado:</span>
                        {{ $grupo->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div>
                        <span class="font-medium">Actualizado:</span>
                        {{ $grupo->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('coordinador.grupos.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Cancelar</span>
                </a>
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Actualizar Grupo</span>
                </button>
            </div>
        </form>
    </div>
@endsection
