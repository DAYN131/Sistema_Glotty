{{-- resources/views/alumno/preregistro/create.blade.php --}}
@extends('layouts.alumno')

@section('title', 'Nuevo Preregistro - Glotty')
@section('header-title', 'Nuevo Preregistro')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Encabezado -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-2">Nuevo Preregistro</h1>
        <p class="text-slate-600">Completa la información para solicitar tu preregistro en el periodo actual.</p>
    </div>

    <!-- Alertas -->
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                </div>
                <div class="ml-3">
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

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Información del periodo -->
    @if($periodoActivo)
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-blue-700 font-medium">Periodo Activo: {{ $periodoActivo->nombre }}</p>
                <p class="text-blue-600 text-sm mt-1">
                    Fecha: {{ \Carbon\Carbon::parse($periodoActivo->fecha_inicio)->format('d/m/Y') }} - 
                    {{ \Carbon\Carbon::parse($periodoActivo->fecha_fin)->format('d/m/Y') }}
                </p>
                @if($periodoActivo->fecha_limite_preregistro)
                <p class="text-blue-600 text-sm mt-1">
                    Fecha límite para preregistro: {{ \Carbon\Carbon::parse($periodoActivo->fecha_limite_preregistro)->format('d/m/Y') }}
                </p>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-yellow-700">No es periodo de preregistros</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Formulario de preregistro -->
    @if($periodoActivo)
    <div class="bg-white rounded-2xl shadow-card overflow-hidden transition-smooth card-hover">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Formulario de Preregistro</h2>
        </div>
        
        <div class="p-6">
            <form action="{{ route('alumno.preregistro.store') }}" method="POST">
                @csrf
                
                <!-- Campo oculto para periodo_id -->
                <input type="hidden" name="periodo_id" value="{{ $periodoActivo->id }}">

                <!-- Nivel solicitado -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-slate-700 mb-3">
                        <i class="fas fa-layer-group text-blue-500 mr-2"></i>Nivel solicitado *
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        @foreach(App\Models\Preregistro::NIVELES as $nivel => $descripcion)
                            <div class="relative">
                                <input type="radio" id="nivel_{{ $nivel }}" name="nivel_solicitado" value="{{ $nivel }}" 
                                       class="hidden peer" {{ old('nivel_solicitado') == $nivel ? 'checked' : '' }} required>
                                <label for="nivel_{{ $nivel }}" 
                                       class="flex flex-col items-center justify-center p-4 border-2 border-slate-200 rounded-xl cursor-pointer transition-smooth
                                              peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700
                                              hover:border-blue-300 hover:bg-blue-50">
                                    <span class="text-2xl font-bold text-slate-700 peer-checked:text-blue-600">{{ $nivel }}</span>
                                    <span class="text-xs text-slate-500 mt-1 text-center">{{ Str::after($descripcion, ' - ') }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('nivel_solicitado')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Horario preferido -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-slate-700 mb-3">
                        <i class="fas fa-clock text-blue-500 mr-2"></i>Horario preferido *
                    </label>
                    <div class="space-y-4">
                        @forelse($horarios as $horario)
                            @php
                                // Manejar diferentes formatos del campo dias
                                $diasArray = [];
                                
                                if (is_array($horario->dias)) {
                                    $diasArray = $horario->dias;
                                } elseif (is_string($horario->dias)) {
                                    // Intentar decodificar JSON
                                    $decoded = json_decode($horario->dias, true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $diasArray = $decoded;
                                    } else {
                                        // Si no es JSON, intentar separar por comas
                                        $diasArray = array_map('trim', explode(',', $horario->dias));
                                    }
                                }
                                
                                // Filtrar elementos vacíos
                                $diasArray = array_filter($diasArray);
                                $diasTexto = !empty($diasArray) ? implode(', ', $diasArray) : 'No especificado';
                                
                                // Formatear horas
                                $horaInicio = $horario->hora_inicio instanceof \DateTime 
                                    ? $horario->hora_inicio->format('H:i') 
                                    : (\Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') ?? 'N/A');
                                    
                                $horaFin = $horario->hora_fin instanceof \DateTime 
                                    ? $horario->hora_fin->format('H:i') 
                                    : (\Carbon\Carbon::parse($horario->hora_fin)->format('H:i') ?? 'N/A');
                            @endphp

                            <div class="relative">
                                <input type="radio" id="horario_{{ $horario->id }}" name="horario_preferido_id" 
                                       value="{{ $horario->id }}" class="hidden peer" 
                                       {{ old('horario_preferido_id') == $horario->id ? 'checked' : '' }} required>
                                <label for="horario_{{ $horario->id }}" 
                                       class="block p-5 border-2 border-slate-200 rounded-xl cursor-pointer transition-smooth
                                              peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700
                                              hover:border-blue-300 hover:bg-blue-50">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-4">
                                            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                                <i class="fas fa-calendar-alt text-blue-600 text-lg"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-3 mb-2">
                                                    <h3 class="font-semibold text-slate-800 peer-checked:text-blue-800 text-lg">
                                                        {{ $horario->nombre }}
                                                    </h3>
                                                    <span class="bg-blue-100 text-blue-700 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                        {{ $horario->tipo ?? 'Sin tipo' }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Información de días y horario -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                                    <div class="space-y-2">
                                                        <div class="flex items-start text-slate-600">
                                                            <i class="fas fa-calendar-day text-slate-400 mr-2 w-4 mt-0.5"></i>
                                                            <div>
                                                                <span class="font-medium">Días:</span>
                                                                <span class="ml-2 text-slate-700">{{ $diasTexto }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center text-slate-600">
                                                            <i class="fas fa-clock text-slate-400 mr-2 w-4"></i>
                                                            <span class="font-medium">Horario:</span>
                                                            <span class="ml-2 text-slate-700">{{ $horaInicio }} - {{ $horaFin }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="space-y-2">
                                                        <div class="flex items-center text-slate-600">
                                                            <i class="fas fa-tag text-slate-400 mr-2 w-4"></i>
                                                            <span class="font-medium">Tipo:</span>
                                                            <span class="ml-2 text-slate-700 capitalize">{{ $horario->tipo ?? 'No especificado' }}</span>
                                                        </div>
                                                        <div class="flex items-center text-slate-600">
                                                            <i class="fas fa-users text-slate-400 mr-2 w-4"></i>
                                                            <span class="font-medium">Cupo:</span>
                                                            <span class="ml-2 text-slate-700">
                                                                {{ $horario->cupo_actual ?? 0 }}/{{ $horario->cupo_maximo ?? 0 }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex-shrink-0 ml-4">
                                            <i class="fas fa-check-circle text-transparent peer-checked:text-blue-500 text-2xl transition-smooth"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @empty
                            <div class="text-center py-10 border-2 border-dashed border-slate-300 rounded-xl bg-slate-50">
                                <i class="fas fa-calendar-times text-slate-400 text-4xl mb-4"></i>
                                <p class="text-slate-500 text-lg mb-2">No hay horarios disponibles</p>
                                <p class="text-slate-400 text-sm">Por favor, contacta con la administración para más información.</p>
                            </div>
                        @endforelse
                    </div>
                    @error('horario_preferido_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

               
                <div class="mb-6">
                    <label for="semestre_actual" class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="fas fa-graduation-cap text-blue-500 mr-2"></i>Semestre actual de tu carrera
                    </label>
                    <input type="text" id="semestre_actual" name="semestre_actual" 
                           value="{{ old('semestre_actual') }}"
                           class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-smooth"
                           placeholder="Solo escribe el numero del semestre de tu carrera">
                    @error('semestre_actual')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Información sobre el proceso -->
                <div class="bg-slate-50 rounded-xl p-4 mb-6">
                    <h4 class="font-medium text-slate-800 mb-2 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Información importante
                    </h4>
                    <ul class="text-sm text-slate-600 space-y-1">
                        <li>• Tu preregistro iniciará con estado <span class="font-medium">"Pendiente"</span></li>
                        <li>• El pago será marcado como <span class="font-medium">"Pendiente de Pago"</span> inicialmente</li>
                        <li>• Serás asignado a un grupo una vez que tu pago sea confirmado</li>
                        <li>• Puedes cancelar tu preregistro antes de ser asignado a un grupo</li>
                    </ul>
                </div>

                <!-- Botones de acción -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-slate-200">
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium py-3 px-6 rounded-xl transition-smooth flex items-center justify-center shadow-soft disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ count($horarios) == 0 ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane mr-2"></i>
                        Enviar Preregistro
                    </button>
                    <a href="{{ route('alumno.dashboard') }}" 
                       class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium py-3 px-6 rounded-xl transition-smooth flex items-center justify-center text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-800">Proceso de Asignación</h3>
            </div>
            <p class="text-slate-600 text-sm">
                Una vez enviado tu preregistro, serás asignado a un grupo en función de la disponibilidad. 
                Recibirás una notificación cuando se complete tu asignación.
            </p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-credit-card text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-800">Proceso de Pago</h3>
            </div>
            <p class="text-slate-600 text-sm">
                Después del preregistro, deberás completar el proceso de pago. Tu asignación a grupo 
                dependerá de la confirmación del pago.
            </p>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Efecto visual mejorado para selección de horarios
        const horarioRadios = document.querySelectorAll('input[name="horario_preferido_id"]');
        
        horarioRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Remover cualquier selección previa visual
                document.querySelectorAll('input[name="horario_preferido_id"] + label').forEach(label => {
                    label.classList.remove('ring-2', 'ring-blue-400', 'shadow-md');
                });
                
                // Agregar efecto visual a la selección actual
                if (this.checked) {
                    const label = this.nextElementSibling;
                    label.classList.add('ring-2', 'ring-blue-400', 'shadow-md');
                }
            });
            
            // Inicializar selecciones si existen valores anteriores
            if (radio.checked) {
                radio.nextElementSibling.classList.add('ring-2', 'ring-blue-400', 'shadow-md');
            }
        });

        // Efecto para niveles
        const nivelRadios = document.querySelectorAll('input[name="nivel_solicitado"]');
        
        nivelRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('input[name="nivel_solicitado"] + label').forEach(label => {
                    label.classList.remove('ring-2', 'ring-blue-400', 'scale-105');
                });
                
                if (this.checked) {
                    this.nextElementSibling.classList.add('ring-2', 'ring-blue-400', 'scale-105');
                }
            });
            
            if (radio.checked) {
                radio.nextElementSibling.classList.add('ring-2', 'ring-blue-400', 'scale-105');
            }
        });

        // Validación antes de enviar
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const nivelSeleccionado = document.querySelector('input[name="nivel_solicitado"]:checked');
            const horarioSeleccionado = document.querySelector('input[name="horario_preferido_id"]:checked');
            
            if (!nivelSeleccionado) {
                e.preventDefault();
                alert('Por favor, selecciona el nivel solicitado.');
                return;
            }
            
            if (!horarioSeleccionado) {
                e.preventDefault();
                alert('Por favor, selecciona un horario preferido.');
                return;
            }
        });
    });
</script>
@endpush
@endsection