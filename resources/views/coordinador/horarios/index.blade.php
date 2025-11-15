@extends('layouts.coordinador')

@section('title', 'Gestión de Horarios Base')
@section('header-title', 'Gestión de Horarios Base')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-clock text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Horarios</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $estadisticas['total'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-green-100 p-3 rounded-xl">
                    <i class="fas fa-play-circle text-green-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Horarios Activos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $estadisticas['activos'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-purple-100 p-3 rounded-xl">
                    <i class="fas fa-calendar-week text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Horarios Semanales</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $estadisticas['semanales'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-orange-100 p-3 rounded-xl">
                    <i class="fas fa-calendar-day text-orange-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Horarios Sabatinos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $estadisticas['sabatinos'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Horarios Base del Sistema</h2>
                <p class="text-gray-600 mt-1">Plantillas de horarios disponibles para todos los periodos</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('coordinador.horarios.create') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Nuevo Horario</span>
                </a>
            </div>
        </div>

        {{-- Notificaciones --}}
        @include('partials.notifications')

        @if($horarios->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-clock text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No hay horarios base registrados</h3>
                <p class="text-gray-500 mb-6">Comienza creando el primer horario base</p>
                <a href="{{ route('coordinador.horarios.create') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth inline-flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Crear Primer Horario</span>
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($horarios as $horario)
                    <div class="border border-gray-200 rounded-xl p-5 hover:shadow-card-hover transition-smooth {{ !$horario->activo ? 'bg-gray-50 opacity-75' : '' }}">
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4">
                            {{-- Información del Horario --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <h3 class="font-semibold text-lg text-gray-800">{{ $horario->nombre }}</h3>
                                    @if($horario->activo)
                                        <span class="bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-medium">
                                            Activo
                                        </span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full font-medium">
                                            Inactivo
                                        </span>
                                    @endif
                                    <span class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-medium">
                                        {{ ucfirst($horario->tipo) }}
                                    </span>
                                    <span class="bg-purple-100 text-purple-800 text-xs px-3 py-1 rounded-full font-medium">
                                        {{ $horario->horarios_periodo_count }} periodos
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600 mb-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-clock text-gray-500"></i>
                                        <span>
                                            {{ $horario->hora_inicio->format('h:i A') }} - {{ $horario->hora_fin->format('h:i A') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar-alt text-gray-500"></i>
                                        <span>{{ $horario->dias_formateados }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-hourglass text-gray-500"></i>
                                        <span>{{ $horario->duracion }} horas</span>
                                    </div>
                                </div>

                                {{-- Periodos donde se usa --}}
                                @if($horario->horarios_periodo_count > 0)
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                        <p class="text-blue-700 text-sm">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Usado en <strong>{{ $horario->horarios_periodo_count }}</strong> 
                                            periodo(s) activo(s)
                                        </p>
                                    </div>
                                @else
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        <p class="text-gray-600 text-sm">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            No usado en periodos activos
                                        </p>
                                    </div>
                                @endif
                            </div>

                            {{-- Acciones --}}
                            <div class="flex flex-col gap-2 lg:flex-shrink-0 min-w-[200px]">
                                {{-- Toggle Activo --}}
                                <form action="{{ route('coordinador.horarios.toggle-activo', $horario) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit"
                                            class="w-full {{ $horario->activo ? 'bg-yellow-100 hover:bg-yellow-200 text-yellow-700' : 'bg-green-100 hover:bg-green-200 text-green-700' }} px-4 py-2 rounded-lg transition-smooth flex items-center justify-center gap-2 text-sm">
                                        <i class="fas fa-{{ $horario->activo ? 'pause' : 'play' }}"></i>
                                        <span>{{ $horario->activo ? 'Desactivar' : 'Activar' }}</span>
                                    </button>
                                </form>

                                {{-- Editar --}}
                                @if(!$horario->enUsoEnPeriodosActivos())
                                    <a href="{{ route('coordinador.horarios.edit', $horario) }}"
                                       class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg transition-smooth flex items-center justify-center gap-2 text-sm w-full">
                                        <i class="fas fa-edit"></i>
                                        <span>Editar</span>
                                    </a>
                                @else
                                    <button disabled
                                            class="bg-gray-100 text-gray-400 px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm w-full cursor-not-allowed"
                                            title="No se puede editar porque está en uso en periodos activos">
                                        <i class="fas fa-edit"></i>
                                        <span>Editar</span>
                                    </button>
                                @endif

                                {{-- Eliminar --}}
                                @if($horario->sePuedeEliminar())
                                    <form action="{{ route('coordinador.horarios.destroy', $horario) }}" method="POST" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg transition-smooth flex items-center justify-center gap-2 text-sm"
                                                onclick="return confirm('¿Estás seguro de eliminar el horario \"{{ $horario->nombre }}\"?')">
                                            <i class="fas fa-trash"></i>
                                            <span>Eliminar</span>
                                        </button>
                                    </form>
                                @else
                                    <button disabled
                                            class="bg-gray-100 text-gray-400 px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm w-full cursor-not-allowed"
                                            title="No se puede eliminar porque está siendo usado en periodos">
                                        <i class="fas fa-trash"></i>
                                        <span>Eliminar</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    .transition-smooth {
        transition: all 0.3s ease;
    }
    .shadow-card {
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    .shadow-card-hover:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
</style>
@endpush