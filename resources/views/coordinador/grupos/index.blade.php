@extends('layouts.coordinador')

@section('title', 'Gestión de Grupos')
@section('header-title', 'Gestión de Grupos Académicos')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Grupos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $grupos->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-green-100 p-3 rounded-xl">
                    <i class="fas fa-play-circle text-green-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Grupos Activos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $grupos->where('estado', 'activo')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-yellow-100 p-3 rounded-xl">
                    <i class="fas fa-pause-circle text-yellow-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Grupos Planificados</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $grupos->where('estado', 'planificado')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Grupos Académicos</h2>
                <p class="text-gray-600">Gestione los grupos, asigne profesores y aulas.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('coordinador.grupos.eliminados') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                    <i class="fas fa-trash-alt"></i>
                    <span>Papelera</span>
                </a>
                <a href="{{ route('coordinador.grupos.create') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Nuevo Grupo</span>
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
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-times-circle text-red-600 text-xl mr-3"></i>
                    <span class="text-red-800 font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if($grupos->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-users-slash text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No hay grupos registrados</h3>
                <p class="text-gray-500 mb-6">Comienza creando el primer grupo académico.</p>
                <a href="{{ route('coordinador.grupos.create') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth inline-flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Crear Primer Grupo</span>
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($grupos as $grupo)
                    <div class="border border-gray-200 rounded-xl p-5 hover:shadow-card-hover transition-smooth {{ $grupo->estado == 'activo' ? 'bg-blue-50 border-blue-200' : '' }}">
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <h3 class="font-semibold text-lg text-gray-800">
                                        Nivel {{ $grupo->nivel_ingles }} - Grupo "{{ $grupo->letra_grupo }}"
                                    </h3>
                                    <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full font-medium">
                                {{ $grupo->periodo->nombre ?? 'Sin Periodo' }} - {{ $grupo->periodo->anio ?? '' }}
                            </span>
                                    <span class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-medium">
                                {{ Str::title(str_replace('_', ' ', $grupo->estado)) }}
                            </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600">
                                    <div class="flex items-center gap-2" title="Profesor">
                                        <i class="fas fa-chalkboard-teacher text-blue-500"></i>
                                        <span>{{ $grupo->profesor->nombre_completo ?? 'Profesor no asignado' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2" title="Horario">
                                        <i class="fas fa-clock text-purple-500"></i>
                                        <span>{{ $grupo->horario->nombre ?? 'Horario no asignado' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2" title="Aula">
                                        <i class="fas fa-door-open text-green-500"></i>
                                        <span>{{ $grupo->aula->id_aula ?? 'Aula no asignada' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2" title="Capacidad">
                                        <i class="fas fa-user-friends text-yellow-500"></i>
                                        <span>{{ $grupo->estudiantes_inscritos }} / {{ $grupo->capacidad_maxima }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 lg:flex-shrink-0">
                                <a href="{{ route('coordinador.grupos.edit', $grupo->id) }}"
                                   class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg transition-smooth flex items-center gap-2">
                                    <i class="fas fa-edit"></i>
                                    <span>Editar</span>
                                </a>

                                <form action="{{ route('coordinador.grupos.destroy', $grupo->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg transition-smooth flex items-center gap-2"
                                            onclick="return confirm('¿Estás seguro de enviar el grupo Nivel {{ $grupo->nivel_ingles }} - {{ $grupo->letra_grupo }} a la papelera?')">
                                        <i class="fas fa-trash"></i>
                                        <span>Eliminar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
