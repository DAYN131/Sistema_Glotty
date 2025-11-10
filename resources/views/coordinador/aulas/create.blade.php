@extends('layouts.coordinador')

@section('title', 'Crear Aula')
@section('header-title', 'Crear Nueva Aula')

@section('content')
    <div class="bg-white rounded-2xl shadow-card p-6 max-w-2xl mx-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Nueva Aula</h2>
            <p class="text-gray-600">Complete la información de la nueva aula</p>
        </div>

        <form action="{{ route('coordinador.aulas.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="col-span-2">
                    <label for="id_aula" class="block text-sm font-medium text-gray-700 mb-2">
                        Identificador del Aula (Generado automáticamente)
                    </label>
                    <input type="text" name="id_aula" id="id_aula" readonly
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                           placeholder="Se generará automáticamente">
                    <p class="text-sm text-gray-500 mt-1">Se genera como: Edificio-Nombre (Ej: A-101, L-LBD)</p>
                </div>

                <div>
                    <label for="edificio" class="block text-sm font-medium text-gray-700 mb-2">
                        Edificio <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="edificio" id="edificio" required
                           value="{{ old('edificio') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                           placeholder="Ej: A, B, L, M">
                    @error('edificio')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nombre_aula" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre/Número de Aula <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre_aula" id="nombre_aula" required
                           value="{{ old('nombre_aula') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                           placeholder="Ej: 101, LBD, M2, 05">
                    <p class="text-sm text-gray-500 mt-1">Puede ser número (101) o nombre (LBD)</p>
                    @error('nombre_aula')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="capacidad" class="block text-sm font-medium text-gray-700 mb-2">
                        Capacidad <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="capacidad" id="capacidad" required min="1"
                           value="{{ old('capacidad') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                           placeholder="Ej: 30">
                    @error('capacidad')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipo_aula" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Aula <span class="text-red-500">*</span>
                    </label>
                    <select name="tipo_aula" id="tipo_aula" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                        <option value="">Seleccione el tipo de aula</option>
                        <option value="regular" {{ old('tipo_aula') == 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="laboratorio" {{ old('tipo_aula') == 'laboratorio' ? 'selected' : '' }}>Laboratorio</option>
                    </select>
                    @error('tipo_aula')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
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
                    <span>Crear Aula</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        // Generar el ID del aula en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const edificioInput = document.getElementById('edificio');
            const nombreInput = document.getElementById('nombre_aula');
            const idInput = document.getElementById('id_aula');

            function actualizarIdAula() {
                const edificio = edificioInput.value.trim().toUpperCase();
                const nombre = nombreInput.value.trim();
                
                if (edificio && nombre) {
                    idInput.value = edificio + '-' + nombre;
                } else {
                    idInput.value = '';
                }
            }

            edificioInput.addEventListener('input', actualizarIdAula);
            nombreInput.addEventListener('input', actualizarIdAula);
        });
    </script>
@endsection