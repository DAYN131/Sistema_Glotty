{{-- resources/views/coordinador/periodos/show.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Gestión de Horarios - ' . $periodo->nombre_periodo)
@section('header-title', 'Gestión de Horarios: ' . $periodo->nombre_periodo)

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Header con información del período --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">{{ $periodo->nombre_periodo }}</h2>
                <div class="flex items-center gap-4 text-blue-100">
                    <span class="flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        {{ \Carbon\Carbon::parse($periodo->fecha_inicio)->format('d/M/Y') }} - {{ \Carbon\Carbon::parse($periodo->fecha_fin)->format('d/M/Y') }}
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        {{ \Carbon\Carbon::parse($periodo->fecha_inicio)->diffInDays($periodo->fecha_fin) }} días
                    </span>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold
                    {{ $periodo->estado == 'configuracion' ? 'bg-yellow-400 text-yellow-900' : '' }}
                    {{ $periodo->estado == 'preregistro' ? 'bg-green-400 text-green-900' : '' }}
                    {{ $periodo->estado == 'activo' ? 'bg-blue-400 text-blue-900' : '' }}
                    {{ $periodo->estado == 'finalizado' ? 'bg-gray-400 text-gray-900' : '' }}">
                    <i class="fas fa-circle text-xs mr-1"></i>
                    {{ ucfirst($periodo->estado) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Barra de progreso --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Progreso de Configuración</h3>
        
        {{-- Barra visual --}}
        <div class="relative mb-6">
            <div class="overflow-hidden h-2 text-xs flex rounded-full bg-gray-200">
                @php
                    $progreso = 20; // Período creado
                    if($periodo->horariosPeriodo()->count() > 0) $progreso += 20;
                    if(\App\Models\Aula::where('disponible', true)->exists()) $progreso += 20;
                    if(\App\Models\Profesor::exists()) $progreso += 20;
                    if($periodo->estado != 'configuracion') $progreso += 20;
                @endphp
                <div style="width:{{ $progreso }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-blue-500 to-blue-600 transition-all duration-500"></div>
            </div>
            <div class="text-right mt-1">
                <span class="text-sm font-semibold text-gray-600">{{ $progreso }}% Completado</span>
            </div>
        </div>

        {{-- Pasos --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center mb-2 shadow-md">
                    <i class="fas fa-check text-lg"></i>
                </div>
                <span class="text-xs font-medium text-gray-700">Período Creado</span>
            </div>

            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 {{ $periodo->horariosPeriodo()->count() > 0 ? 'bg-green-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center mb-2 shadow-md">
                    @if($periodo->horariosPeriodo()->count() > 0)
                        <i class="fas fa-check text-lg"></i>
                    @else
                        <span class="font-bold">2</span>
                    @endif
                </div>
                <span class="text-xs font-medium {{ $periodo->horariosPeriodo()->count() > 0 ? 'text-gray-700' : 'text-gray-400' }}">Horarios Config.</span>
            </div>

            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 {{ \App\Models\Aula::where('disponible', true)->exists() ? 'bg-green-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center mb-2 shadow-md">
                    @if(\App\Models\Aula::where('disponible', true)->exists())
                        <i class="fas fa-check text-lg"></i>
                    @else
                        <span class="font-bold">3</span>
                    @endif
                </div>
                <span class="text-xs font-medium {{ \App\Models\Aula::where('disponible', true)->exists() ? 'text-gray-700' : 'text-gray-400' }}">Aulas Disponibles</span>
            </div>

            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 {{ \App\Models\Profesor::exists() ? 'bg-green-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center mb-2 shadow-md">
                    @if(\App\Models\Profesor::exists())
                        <i class="fas fa-check text-lg"></i>
                    @else
                        <span class="font-bold">4</span>
                    @endif
                </div>
                <span class="text-xs font-medium {{ \App\Models\Profesor::exists() ? 'text-gray-700' : 'text-gray-400' }}">Profesores Reg.</span>
            </div>

            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 {{ $periodo->estado != 'configuracion' ? 'bg-green-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center mb-2 shadow-md">
                    @if($periodo->estado != 'configuracion')
                        <i class="fas fa-check text-lg"></i>
                    @else
                        <span class="font-bold">5</span>
                    @endif
                </div>
                <span class="text-xs font-medium {{ $periodo->estado != 'configuracion' ? 'text-gray-700' : 'text-gray-400' }}">Preregistros</span>
            </div>
        </div>
    </div>

    {{-- Alertas de requisitos --}}
    @if($periodo->estado == 'configuracion')
        <div class="space-y-3 mb-6">
            @if($periodo->horariosPeriodo()->where('activo', true)->count() === 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4 shadow-sm">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <h4 class="font-semibold text-yellow-800 mb-1">Horarios Requeridos</h4>
                            <p class="text-yellow-700 text-sm">Debe agregar y activar al menos un horario antes de poder activar los preregistros.</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(!\App\Models\Aula::where('disponible', true)->exists())
                <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4 shadow-sm">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <h4 class="font-semibold text-yellow-800 mb-1">Aulas Requeridas</h4>
                            <p class="text-yellow-700 text-sm mb-2">Debe crear aulas disponibles en el sistema antes de activar preregistros.</p>
                            <a href="{{ route('coordinador.aulas.index') }}" 
                               class="inline-flex items-center text-sm font-medium text-yellow-800 hover:text-yellow-900 underline">
                                <i class="fas fa-arrow-right mr-1"></i>
                                Gestionar Aulas
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if(!\App\Models\Profesor::exists())
                <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4 shadow-sm">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <h4 class="font-semibold text-yellow-800 mb-1">Profesores Requeridos</h4>
                            <p class="text-yellow-700 text-sm mb-2">Debe tener profesores registrados en el sistema antes de activar preregistros.</p>
                            <a href="{{ route('coordinador.profesores.index') }}" 
                               class="inline-flex items-center text-sm font-medium text-yellow-800 hover:text-yellow-900 underline">
                                <i class="fas fa-arrow-right mr-1"></i>
                                Gestionar Profesores
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Botón para activar preregistros --}}
            @if($periodo->horariosPeriodo()->where('activo', true)->count() > 0 && 
                \App\Models\Aula::where('disponible', true)->exists() && 
                \App\Models\Profesor::exists())
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mt-1 mr-3 text-xl"></i>
                            <div>
                                <h4 class="font-bold text-green-800 mb-1">¡Todo está listo!</h4>
                                <p class="text-green-700 text-sm">Todos los requisitos están completos. Puede activar los preregistros para este período.</p>
                            </div>
                        </div>
                        <form action="{{ route('coordinador.periodos.activar-preregistros', $periodo) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center whitespace-nowrap"
                                    onclick="return confirm('¿Está seguro de activar los preregistros para este período?\n\nUna vez activados, los estudiantes podrán comenzar a realizar sus preregistros.')">
                                <i class="fas fa-play-circle mr-2"></i>
                                Activar Preregistros
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Sección de agregar horarios --}}
    @if($periodo->estado == 'configuracion')
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
            Agregar Horarios desde Plantillas
        </h3>
        
        <form action="{{ route('coordinador.periodos.agregar-horarios', $periodo) }}" method="POST" id="agregarHorariosForm">
            @csrf
            <div class="grid md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-3">
                    <label for="horarios_base_ids" class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccionar Plantillas de Horarios
                    </label>
                    <select name="horarios_base_ids[]" 
                            id="horarios_base_ids" 
                            multiple
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            size="4">
                        @foreach($horariosBaseDisponibles as $horarioBase)
                            <option value="{{ $horarioBase->id }}" class="py-2">
                                <span class="font-semibold">{{ $horarioBase->nombre }}</span> - 
                                <span class="text-gray-600">{{ $horarioBase->tipo }}</span>
                                <span class="text-gray-500">({{ $horarioBase->hora_inicio->format('H:i') }} - {{ $horarioBase->hora_fin->format('H:i') }})</span>
                            </option>
                        @endforeach
                    </select>
                    @if($horariosBaseDisponibles->count() === 0)
                        <p class="text-sm text-amber-600 mt-2 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            No hay plantillas de horarios disponibles
                        </p>
                    @else
                        <p class="text-sm text-gray-500 mt-2 flex items-center">
                            <i class="fas fa-hand-pointer mr-1"></i>
                            Mantén <kbd class="px-1 py-0.5 bg-gray-100 border border-gray-300 rounded text-xs">Ctrl</kbd> para seleccionar múltiples
                        </p>
                    @endif
                </div>
                <div>
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-md"
                            {{ $horariosBaseDisponibles->count() === 0 ? 'disabled' : '' }}>
                        <i class="fas fa-plus-circle mr-2"></i>
                        Agregar
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endif

    {{-- Lista de horarios configurados --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-clock text-blue-600 mr-2"></i>
                Horarios Configurados
                <span class="ml-2 text-sm font-normal text-gray-500">({{ $horariosPeriodo->count() }} total)</span>
            </h3>
            @if($periodo->estado == 'configuracion' && $horariosPeriodo->count() > 0)
                <span class="text-sm text-gray-600">
                    <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-1"></span> Activos: {{ $horariosPeriodo->where('activo', true)->count() }}
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($horariosPeriodo as $horarioPeriodo)
            <div class="border-2 {{ $horarioPeriodo->activo ? 'border-green-200 bg-white' : 'border-gray-200 bg-gray-50' }} rounded-xl p-5 hover:shadow-md transition-all duration-200">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800 text-lg mb-1">{{ $horarioPeriodo->nombre }}</h4>
                        <p class="text-xs text-gray-500 flex items-center">
                            <i class="fas fa-copy mr-1"></i>
                            Plantilla: {{ $horarioPeriodo->horarioBase->nombre ?? 'N/A' }}
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $horarioPeriodo->activo ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-gray-200 text-gray-600 border border-gray-300' }}">
                        {{ $horarioPeriodo->activo ? '✓ Activo' : '○ Inactivo' }}
                    </span>
                </div>
                
                <div class="space-y-2 mb-4 bg-gray-50 rounded-lg p-3">
                    <div class="flex items-center text-sm text-gray-700">
                        <i class="fas fa-clock text-blue-500 mr-2 w-4"></i>
                        <span class="font-medium">{{ $horarioPeriodo->horario_completo }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-700">
                        <i class="fas fa-hourglass-half text-purple-500 mr-2 w-4"></i>
                        <span>Duración: <span class="font-medium">{{ $horarioPeriodo->duracion }} horas</span></span>
                    </div>
                </div>

                @if($periodo->estado == 'configuracion')
                <div class="flex gap-2 pt-3 border-t border-gray-200">
                    <form action="{{ route('coordinador.periodos.toggle-horario', [$periodo, $horarioPeriodo]) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" 
                                class="w-full text-sm font-medium px-4 py-2 rounded-lg transition-all duration-200 flex items-center justify-center
                                {{ $horarioPeriodo->activo 
                                    ? 'bg-yellow-100 hover:bg-yellow-200 text-yellow-700 border border-yellow-300' 
                                    : 'bg-green-100 hover:bg-green-200 text-green-700 border border-green-300' }}">
                            <i class="fas {{ $horarioPeriodo->activo ? 'fa-pause-circle' : 'fa-play-circle' }} mr-2"></i>
                            {{ $horarioPeriodo->activo ? 'Desactivar' : 'Activar' }}
                        </button>
                    </form>

                    <form action="{{ route('coordinador.periodos.eliminar-horario', [$periodo, $horarioPeriodo]) }}" method="POST">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" 
                                class="text-sm font-medium bg-red-100 hover:bg-red-200 text-red-700 border border-red-300 px-4 py-2 rounded-lg transition-all duration-200"
                                onclick="return confirm('¿Está seguro de eliminar este horario del período?\n\nEsta acción no se puede deshacer.')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @empty
            <div class="col-span-full text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                <i class="fas fa-clock text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-600 font-medium mb-2">No hay horarios configurados</p>
                @if($periodo->estado == 'configuracion')
                <p class="text-sm text-gray-500">Use el formulario superior para agregar horarios desde plantillas.</p>
                @endif
            </div>
            @endforelse
        </div>
    </div>
</div>

@if($periodo->estado == 'configuracion')
<script>
    document.getElementById('agregarHorariosForm').addEventListener('submit', function(e) {
        const select = document.getElementById('horarios_base_ids');
        const selectedOptions = select.selectedOptions;
        
        if (selectedOptions.length === 0) {
            e.preventDefault();
            alert('⚠️ Por favor selecciona al menos un horario para agregar.');
            select.focus();
            return false;
        }
        
        // Confirmación con lista de horarios seleccionados
        const nombres = Array.from(selectedOptions).map(opt => opt.text).join('\n• ');
        if (!confirm(`¿Agregar ${selectedOptions.length} horario(s) al período?\n\n• ${nombres}`)) {
            e.preventDefault();
            return false;
        }
    });

    // Mejorar experiencia de selección múltiple
    const selectElement = document.getElementById('horarios_base_ids');
    if (selectElement) {
        selectElement.addEventListener('change', function() {
            const count = this.selectedOptions.length;
            const button = this.form.querySelector('button[type="submit"]');
            if (count > 0) {
                button.innerHTML = `<i class="fas fa-plus-circle mr-2"></i>Agregar (${count})`;
            } else {
                button.innerHTML = '<i class="fas fa-plus-circle mr-2"></i>Agregar';
            }
        });
    }
</script>
@endif
@endsection