@extends('layouts.coordinador')

@section('title', 'Horarios Eliminados')
@section('header-title', 'Horarios Eliminados')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-red-100 p-3 rounded-xl">
                    <i class="fas fa-trash-alt text-red-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Eliminados</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $horariosEliminados->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Papelera de Horarios</h2>
            <a href="{{ route('coordinador.horarios.index') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Volver al Listado</span>
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($horariosEliminados->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-recycle text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">La papelera está vacía</h3>
                <p class="text-gray-500">No hay horarios eliminados para mostrar.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($horariosEliminados as $horario)
                    <div class="border border-gray-200 rounded-xl p-5 bg-gray-50">
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-gray-600 line-through">{{ $horario->nombre }}</h3>
                                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-500 mt-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $horario->hora_inicio->format('h:i A') }} - {{ $horario->hora_fin->format('h:i A') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>
                                    @if($horario->tipo == 'sabado')
                                                Sábado
                                            @elseif(!empty($horario->dias))
                                                {{ implode(', ', $horario->dias) }}
                                            @else
                                                N/A
                                            @endif
                                </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-red-600">
                                        <i class="fas fa-trash-alt"></i>
                                        <span>Eliminado: {{ $horario->deleted_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 lg:flex-shrink-0">
                                <form action="{{ route('coordinador.horarios.restore', $horario->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg transition-smooth flex items-center gap-2"
                                            onclick="return confirm('¿Estás seguro de restaurar este horario?')">
                                        <i class="fas fa-trash-restore"></i>
                                        <span>Restaurar</span>
                                    </button>
                                </form>

                                <form action="{{ route('coordinador.horarios.forceDelete', $horario->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg transition-smooth flex items-center gap-2"
                                            onclick="return confirm('¡Acción irreversible! ¿Estás seguro de ELIMINAR PERMANENTEMENTE este horario?')">
                                        <i class="fas fa-eraser"></i>
                                        <span>Borrar</span>
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
