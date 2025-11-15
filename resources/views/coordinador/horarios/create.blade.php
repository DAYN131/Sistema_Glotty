@extends('layouts.coordinador')

@section('title', 'Crear Horario Base')
@section('header-title', 'Crear Nuevo Horario Base')

@section('content')
    <div class="max-w-2xl mx-auto">
        {{-- Información --}}
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mb-6">
            <div class="flex items-start space-x-4">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-blue-800 mb-2">Horarios Base</h3>
                    <p class="text-blue-700 text-sm">
                        Los horarios base son plantillas que estarán disponibles para todos los periodos académicos.
                        Cada periodo activará automáticamente estos horarios para su uso.
                    </p>
                </div>
            </div>
        </div>

        {{-- Formulario --}}
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Nuevo Horario Base</h2>
                <p class="text-gray-600">Complete la información del nuevo horario base</p>
            </div>

            <form action="{{ route('coordinador.horarios.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    {{-- Nombre --}}
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Horario <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                                <i class="fas fa-tag"></i>
                            </span>
                            <input type="text" name="nombre" id="nombre" required
                                   value="{{ old('nombre') }}"
                                   class="w-full pl-10 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                                   placeholder="Ej: Lunes y Miércoles 7:00-9:00">
                        </div>
                        <p class="text-gray-500 text-xs mt-2">
                            Use un nombre descriptivo que incluya días y horario
                        </p>
                        @error('nombre')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tipo --}}
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
                                <option value="semanal" {{ old('tipo') == 'semanal' ? 'selected' : '' }}>Semanal (Lunes a Viernes)</option>
                                <option value="sabatino" {{ old('tipo') == 'sabatino' ? 'selected' : '' }}>Sabatino (Sábados)</option>
                            </select>
                        </div>
                        @error('tipo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Días Semanal --}}
                    <div id="dias_semanal_container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Días de clase <span class="text-red-500">*</span>
                            <span class="text-gray-500 font-normal">(Seleccione al menos uno)</span>
                        </label>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dia)
                                    <div class="flex items-center space-x-3">
                                        <input type="checkbox" id="dia_{{ strtolower($dia) }}" name="dias[]" value="{{ $dia }}"
                                               {{ (is_array(old('dias')) && in_array($dia, old('dias'))) ? 'checked' : '' }}
                                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 transition-smooth dias-checkbox">
                                        <label for="dia_{{ strtolower($dia) }}" class="text-sm font-medium text-gray-700 cursor-pointer hover:text-blue-600 transition-smooth">
                                            {{ $dia }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @error('dias')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Días Sabatino --}}
                    <div id="dias_sabatino_container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Día de clase <span class="text-red-500">*</span>
                        </label>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" id="dia_sabado" name="dias[]" value="Sábado"
                                       {{ (is_array(old('dias')) && in_array('Sábado', old('dias'))) ? 'checked' : '' }}
                                       class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 transition-smooth"
                                       checked>
                                <label for="dia_sabado" class="text-sm font-medium text-gray-700 cursor-pointer hover:text-blue-600 transition-smooth">
                                    Sábado
                                </label>
                            </div>
                        </div>
                        <p class="text-gray-500 text-xs mt-2">
                            Los horarios sabatinos solo incluyen el día sábado
                        </p>
                        @error('dias')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Horas --}}
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
                                       value="{{ old('hora_inicio', '07:00') }}"
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
                                       value="{{ old('hora_fin', '09:00') }}"
                                       class="w-full pl-10 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth">
                            </div>
                            @error('hora_fin')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Vista Previa --}}
                    <div id="vista_previa" class="bg-blue-50 border border-blue-200 rounded-xl p-4 hidden">
                        <h4 class="font-semibold text-blue-800 mb-3 flex items-center">
                            <i class="fas fa-eye text-blue-600 mr-2"></i>
                            Vista Previa del Horario
                        </h4>
                        <div class="text-sm text-blue-700 space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt text-blue-500 mr-2 w-4"></i>
                                <span id="preview_horario" class="font-medium">-</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-hourglass-half text-blue-500 mr-2 w-4"></i>
                                <span id="preview_duracion" class="font-medium">-</span>
                            </div>
                        </div>
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Estado inicial del horario base
                        </label>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex space-x-6">
                                <div class="flex items-center space-x-3">
                                    <input type="radio" id="activo_si" name="activo" value="1"
                                           {{ old('activo', '1') == '1' ? 'checked' : '' }}
                                           class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500 transition-smooth">
                                    <label for="activo_si" class="text-sm font-medium text-gray-700 cursor-pointer flex items-center">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        Activar este horario
                                    </label>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="radio" id="activo_no" name="activo" value="0"
                                           {{ old('activo') == '0' ? 'checked' : '' }}
                                           class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500 transition-smooth">
                                    <label for="activo_no" class="text-sm font-medium text-gray-700 cursor-pointer flex items-center">
                                        <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                        Mantener inactivo
                                    </label>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-500 text-xs mt-2">
                            <strong>Horarios activos:</strong> Disponibles para nuevos periodos académicos<br>
                            <strong>Horarios inactivos:</strong> No se incluirán en nuevos periodos (útil para horarios temporales)
                        </p>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200 mt-8">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center justify-center gap-2 font-medium shadow-lg shadow-blue-500/25">
                        <i class="fas fa-plus"></i>
                        <span>Crear Horario Base</span>
                    </button>
                    
                    <a href="{{ route('coordinador.horarios.index') }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-smooth flex items-center justify-center gap-2 font-medium">
                        <i class="fas fa-arrow-left"></i>
                        <span>Cancelar</span>
                    </a>
                </div>
            </form>
        </div>

        {{-- Ejemplos de horarios --}}
        <div class="bg-white rounded-2xl shadow-card p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                Ejemplos de Horarios Base
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                    <h4 class="font-medium text-gray-700 mb-2">Horarios Semanales</h4>
                    <ul class="text-gray-600 space-y-1">
                        <li>• Lunes y Miércoles 7:00-9:00</li>
                        <li>• Martes y Jueves 16:00-18:00</li>
                        <li>• Lunes, Miércoles, Viernes 10:00-11:30</li>
                    </ul>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <h4 class="font-medium text-gray-700 mb-2">Horarios Sabatinos</h4>
                    <ul class="text-gray-600 space-y-1">
                        <li>• Sábados 8:00-14:00</li>
                        <li>• Sábados 9:00-13:00</li>
                        <li>• Sábados 7:00-12:00</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tipoSelect = document.getElementById("tipo");
        const diasSemanalContainer = document.getElementById("dias_semanal_container");
        const diasSabatinoContainer = document.getElementById("dias_sabatino_container");
        const vistaPrevia = document.getElementById("vista_previa");
        const horaInicio = document.getElementById("hora_inicio");
        const horaFin = document.getElementById("hora_fin");

        function toggleDiasContainers() {
            diasSemanalContainer.classList.add("hidden");
            diasSabatinoContainer.classList.add("hidden");

            if (tipoSelect.value === "semanal") {
                diasSemanalContainer.classList.remove("hidden");
                // Auto-seleccionar primer día si no hay selección
                if (!document.querySelector('#dias_semanal_container input:checked')) {
                    document.querySelector('#dias_semanal_container input').checked = true;
                }
            } else if (tipoSelect.value === "sabatino") {
                diasSabatinoContainer.classList.remove("hidden");
                // Auto-seleccionar sábado
                document.getElementById('dia_sabado').checked = true;
            }
            
            actualizarVistaPrevia();
        }

        function actualizarVistaPrevia() {
            const tipo = tipoSelect.value;
            const inicio = horaInicio.value;
            const fin = horaFin.value;
            
            if (tipo && inicio && fin) {
                vistaPrevia.classList.remove("hidden");
                
                // Formatear horas
                const inicioFormateado = formatearHora(inicio);
                const finFormateado = formatearHora(fin);
                
                // Calcular duración
                const duracion = calcularDuracion(inicio, fin);
                
                // Obtener días seleccionados
                let diasTexto = '';
                if (tipo === 'semanal') {
                    const diasSeleccionados = Array.from(document.querySelectorAll('#dias_semanal_container input:checked'))
                        .map(input => {
                            const diaCompleto = input.value;
                            const diasCortos = {
                                'Lunes': 'Lun', 'Martes': 'Mar', 'Miércoles': 'Mié', 
                                'Jueves': 'Jue', 'Viernes': 'Vie'
                            };
                            return diasCortos[diaCompleto] || diaCompleto.substring(0, 3);
                        });
                    diasTexto = diasSeleccionados.join(', ');
                } else {
                    diasTexto = 'Sábados';
                }
                
                document.getElementById('preview_horario').textContent = 
                    `${diasTexto} ${inicioFormateado} - ${finFormateado}`;
                document.getElementById('preview_duracion').textContent = 
                    `${duracion} horas de clase`;
            } else {
                vistaPrevia.classList.add("hidden");
            }
        }

        function formatearHora(hora) {
            const [horas, minutos] = hora.split(':');
            const horaNum = parseInt(horas);
            const ampm = horaNum >= 12 ? 'PM' : 'AM';
            const hora12 = horaNum % 12 || 12;
            return `${hora12}:${minutos} ${ampm}`;
        }

        function calcularDuracion(inicio, fin) {
            const inicioDate = new Date(`2000-01-01T${inicio}`);
            const finDate = new Date(`2000-01-01T${fin}`);
            const diffMs = finDate - inicioDate;
            const diffHoras = diffMs / (1000 * 60 * 60);
            return diffHoras.toFixed(1);
        }

        // Event listeners
        tipoSelect.addEventListener("change", toggleDiasContainers);
        horaInicio.addEventListener("change", actualizarVistaPrevia);
        horaFin.addEventListener("change", actualizarVistaPrevia);
        
        // Actualizar vista previa cuando cambien los días
        document.querySelectorAll('input[name="dias[]"]').forEach(input => {
            input.addEventListener("change", actualizarVistaPrevia);
        });

        // Inicializar
        toggleDiasContainers();
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
    
    /* Mejorar la experiencia de los checkboxes */
    .dias-checkbox:checked {
        background-color: #3B82F6;
        border-color: #3B82F6;
    }
</style>
@endpush