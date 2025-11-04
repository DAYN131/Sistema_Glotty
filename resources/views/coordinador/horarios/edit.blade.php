@extends('layouts.coordinador')

@section('title', 'Editar Horario')
@section('header-title', 'Editar Horario')

@section('content')
    <div class="bg-white rounded-2xl shadow-card p-6 max-w-2xl mx-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Editar Horario</h2>
            <p class="text-gray-600">Modifique la información del horario</p>
        </div>

        @php
            // Asegurarnos de que 'dias' sea un array, incluso si es null o hay un old()
            $diasSeleccionados = old('dias', $horario->dias ?? []);
            if (is_string($diasSeleccionados)) {
                 $diasSeleccionados = json_decode($diasSeleccionados, true) ?? [];
            }
        @endphp

        <form action="{{ route('coordinador.horarios.update', $horario->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del Horario <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                        <i class="fas fa-tag"></i>
                    </span>
                        <input type="text" name="nombre" id="nombre" required
                               value="{{ old('nombre', $horario->nombre) }}"
                               class="w-full pl-10 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                               placeholder="Ej: Horario 2025-1 (L-Mi-V)">
                    </div>
                    @error('nombre')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Horario <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                        <i class="fas fa-calendar-week"></i>
                    </span>
                        <select name="tipo" id="tipo" required
                                class="w-full pl-10 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth appearance-none">
                            <option value="">Seleccione un tipo</option>
                            <option value="semanal" {{ old('tipo', $horario->tipo) == 'semanal' ? 'selected' : '' }}>Semanal</option>
                            <option value="sabado" {{ old('tipo', $horario->tipo) == 'sabado' ? 'selected' : '' }}>Sábado</option>
                        </select>
                    </div>
                    @error('tipo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="dias_container" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Días de clase</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-2">
                        @foreach(['lunes', 'martes', 'miercoles', 'jueves', 'viernes'] as $dia)
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" id="{{ $dia }}" name="dias[]" value="{{ $dia }}"
                                       {{ in_array($dia, $diasSeleccionados) ? 'checked' : '' }}
                                       class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="{{ $dia }}" class="text-sm font-medium text-gray-700">{{ ucfirst($dia) }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('dias')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                            Hora de Inicio <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                            <i class="fas fa-clock"></i>
                        </span>
                            <input type="time" name="hora_inicio" id="hora_inicio" required
                                   value="{{ old('hora_inicio', $horario->hora_inicio->format('H:i')) }}"
                                   class="w-full pl-10 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                        </div>
                        @error('hora_inicio')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-2">
                            Hora de Fin <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                            <i class="fas fa-clock"></i>
                        </span>
                            <input type="time" name="hora_fin" id="hora_fin" required
                                   value="{{ old('hora_fin', $horario->hora_fin->format('H:i')) }}"
                                   class="w-full pl-10 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                        </div>
                        @error('hora_fin')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <div class="flex space-x-6 mt-2">
                        <div class="flex items-center space-x-3">
                            <input type="radio" id="activo_si" name="activo" value="1"
                                   {{ old('activo', $horario->activo) == '1' ? 'checked' : '' }}
                                   class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <label for="activo_si" class="text-sm font-medium text-gray-700">Activo</label>
                        </div>
                        <div class="flex items-center space-x-3">
                            <input type="radio" id="activo_no" name="activo" value="0"
                                   {{ old('activo', $horario->activo) == '0' ? 'checked' : '' }}
                                   class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <label for="activo_no" class="text-sm font-medium text-gray-700">Inactivo</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 mt-8">
                <a href="{{ route('coordinador.horarios.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Cancelar</span>
                </a>
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Actualizar Horario</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Lógica adaptada de horario.js
        document.addEventListener("DOMContentLoaded", function () {
            const tipoSelect = document.getElementById("tipo");
            const diasContainer = document.getElementById("dias_container");

            function toggleDiasContainer() {
                if (tipoSelect.value === "semanal") {
                    diasContainer.classList.remove("hidden");
                } else {
                    diasContainer.classList.add("hidden");
                }
            }

            // Ejecutar al cargar la página
            toggleDiasContainer();

            // Añadir el "oyente" para cuando cambie el select
            tipoSelect.addEventListener("change", toggleDiasContainer);
        });
    </script>
@endpush
