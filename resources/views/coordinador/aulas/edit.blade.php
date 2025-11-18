@extends('layouts.coordinador')

@section('title', 'Editar Aula')
@section('header-title', 'Editar Aula: ' . $aula->nombre_completo)

@section('content')
    <div class="bg-white rounded-2xl shadow-card p-6 max-w-2xl mx-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Editar Aula</h2>
            <p class="text-gray-600">Modifique la información del aula {{ $aula->nombre_completo }}</p>
        </div>

        <form action="{{ route('coordinador.aulas.update', $aula) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Nombre Corto del Aula --}}
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre/Número <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre" id="nombre" required
                           value="{{ old('nombre', $aula->nombre) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                           placeholder="Ej: 101, LBD, M2, 05">
                    <p class="text-sm text-gray-500 mt-1">Código o número del aula</p>
                    @error('nombre')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Edificio --}}
                <div>
                    <label for="edificio" class="block text-sm font-medium text-gray-700 mb-2">
                        Edificio <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="edificio" id="edificio" required
                           value="{{ old('edificio', $aula->edificio) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                           placeholder="Ej: A, B, L, M">
                    @error('edificio')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Capacidad --}}
                <div>
                    <label for="capacidad" class="block text-sm font-medium text-gray-700 mb-2">
                        Capacidad <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="capacidad" id="capacidad" required min="1"
                           value="{{ old('capacidad', $aula->capacidad) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                           placeholder="Ej: 30">
                    @error('capacidad')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tipo de Aula --}}
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Aula <span class="text-red-500">*</span>
                    </label>
                    <select name="tipo" id="tipo" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                        <option value="">Seleccione el tipo de aula</option>
                        <option value="regular" {{ old('tipo', $aula->tipo) == 'regular' ? 'selected' : '' }}>Aula Regular</option>
                        <option value="laboratorio" {{ old('tipo', $aula->tipo) == 'laboratorio' ? 'selected' : '' }}>Laboratorio</option>
                        <option value="computo" {{ old('tipo', $aula->tipo) == 'computo' ? 'selected' : '' }}>Sala de Cómputo</option>
                        <option value="audiovisual" {{ old('tipo', $aula->tipo) == 'audiovisual' ? 'selected' : '' }}>Aula Audiovisual</option>
                    </select>
                    @error('tipo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Equipamiento --}}
                <div class="md:col-span-2">
                    <label for="equipamiento" class="block text-sm font-medium text-gray-700 mb-2">
                        Equipamiento
                    </label>
                    <textarea name="equipamiento" id="equipamiento" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                              placeholder="Ej: Proyector, 25 computadoras, pizarra interactiva...">{{ old('equipamiento', $aula->equipamiento) }}</textarea>
                    <p class="text-sm text-gray-500 mt-1">Lista de equipamiento disponible (opcional)</p>
                    @error('equipamiento')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Estado Disponible --}}
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                        <input type="checkbox" name="disponible" id="disponible" value="1" 
                               {{ old('disponible', $aula->disponible) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                        <label for="disponible" class="text-sm font-medium text-gray-700">
                            Aula disponible para asignación
                        </label>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        Si se desactiva, el aula no podrá ser asignada a grupos hasta que se reactive.
                    </p>
                </div>
            </div>

            {{-- Vista previa del identificador --}}
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h4 class="font-medium text-blue-800 mb-2">Se mostrará como:</h4>
                <div id="vista-previa" class="text-blue-700 font-semibold">
                    {{ $aula->edificio }}-{{ $aula->nombre }}
                </div>
                <p class="text-sm text-blue-600 mt-1">Este es el identificador único del aula en el sistema</p>
            </div>

            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('coordinador.aulas.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Cancelar</span>
                </a>
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Actualizar Aula</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        // Actualizar vista previa en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const edificioInput = document.getElementById('edificio');
            const nombreInput = document.getElementById('nombre');
            const vistaPrevia = document.getElementById('vista-previa');

            function actualizarVistaPrevia() {
                const edificio = edificioInput.value.trim().toUpperCase();
                const nombre = nombreInput.value.trim();
                
                if (edificio && nombre) {
                    vistaPrevia.textContent = edificio + '-' + nombre;
                } else if (edificio) {
                    vistaPrevia.textContent = edificio + '-[Nombre]';
                } else if (nombre) {
                    vistaPrevia.textContent = '[Edificio]-' + nombre;
                } else {
                    vistaPrevia.textContent = '[Edificio]-[Nombre]';
                }
            }

            edificioInput.addEventListener('input', actualizarVistaPrevia);
            nombreInput.addEventListener('input', actualizarVistaPrevia);

            // Inicializar vista previa
            actualizarVistaPrevia();
        });
    </script>
@endsection