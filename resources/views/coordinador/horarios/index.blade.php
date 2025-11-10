@extends('layouts.coordinador')

@section('title', 'Gestión de Horarios')
@section('header-title', 'Gestión de Horarios')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-clock text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Horarios</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $horarios->total() }}</p>
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
                    <p class="text-2xl font-bold text-gray-800">{{ $horarios->where('activo', true)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-purple-100 p-3 rounded-xl">
                    <i class="fas fa-calendar-day text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Horarios Sabatinos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $horarios->where('tipo', 'sabatino')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800">Listado de Horarios</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('coordinador.horarios.eliminados') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                    <i class="fas fa-trash-restore"></i>
                    <span>Ver Eliminados</span>
                </a>
                <a href="{{ route('coordinador.horarios.create') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Nuevo Horario</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($horarios->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-clock text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No hay horarios registrados</h3>
                <p class="text-gray-500 mb-6">Comienza creando el primer horario</p>
                <a href="{{ route('coordinador.horarios.create') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth inline-flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Crear Primer Horario</span>
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($horarios as $horario)
                    @php
                        // Decodificar el JSON de días
                        $diasArray = is_string($horario->dias) ? json_decode($horario->dias, true) : $horario->dias;
                        $diasArray = $diasArray ?? [];
                    @endphp
                    
                    <div class="border border-gray-200 rounded-xl p-5 hover:shadow-card-hover transition-smooth {{ !$horario->activo ? 'bg-gray-50' : '' }}">
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <h3 class="font-semibold text-lg text-gray-800">{{ $horario->nombre }}</h3>
                                    @if($horario->activo)
                                        <span class="bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-medium flex items-center gap-1.5">
                                            <i class="fas fa-circle text-[6px]"></i>
                                            <span>Activo</span>
                                        </span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full font-medium">
                                            Inactivo
                                        </span>
                                    @endif
                                    <span class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-medium">
                                        {{ ucfirst($horario->tipo) }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-hourglass-start text-blue-500"></i>
                                        <span>Inicio: <span class="font-medium">
                                            @if($horario->hora_inicio instanceof \DateTime)
                                                {{ $horario->hora_inicio->format('h:i A') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('h:i A') }}
                                            @endif
                                        </span></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-hourglass-end text-green-500"></i>
                                        <span>Fin: <span class="font-medium">
                                            @if($horario->hora_fin instanceof \DateTime)
                                                {{ $horario->hora_fin->format('h:i A') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($horario->hora_fin)->format('h:i A') }}
                                            @endif
                                        </span></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar-alt text-purple-500"></i>
                                        <span>Días: <span class="font-medium">
                                            @if($horario->tipo == 'sabatino')
                                                Sábado
                                            @elseif(!empty($diasArray) && is_array($diasArray))
                                                {{ implode(', ', $diasArray) }}
                                            @else
                                                N/A
                                            @endif
                                        </span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 lg:flex-shrink-0">
                                <form action="{{ route('coordinador.horarios.toggleActivo', $horario->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="bg-{{ $horario->activo ? 'yellow' : 'green' }}-100 hover:bg-{{ $horario->activo ? 'yellow' : 'green' }}-200 text-{{ $horario->activo ? 'yellow' : 'green' }}-700 px-4 py-2 rounded-lg transition-smooth flex items-center gap-2">
                                        <i class="fas fa-{{ $horario->activo ? 'pause' : 'play' }}"></i>
                                        <span>{{ $horario->activo ? 'Desactivar' : 'Activar' }}</span>
                                    </button>
                                </form>

                                <a href="{{ route('coordinador.horarios.edit', $horario->id) }}"
                                   class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg transition-smooth flex items-center gap-2">
                                    <i class="fas fa-edit"></i>
                                    <span>Editar</span>
                                </a>

                                <form action="{{ route('coordinador.horarios.destroy', $horario->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg transition-smooth flex items-center gap-2"
                                            onclick="return confirm('¿Estás seguro de eliminar el horario {{ $horario->nombre }}?')">
                                        <i class="fas fa-trash"></i>
                                        <span>Eliminar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Paginación -->
                <div class="mt-6">
                    {{ $horarios->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection