@extends('layouts.coordinador')

@section('title', 'Grupos Eliminados')
@section('header-title', 'Papelera de Grupos')

@section('content')
    <div class="bg-white rounded-2xl shadow-card p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Grupos Eliminados</h2>
                <p class="text-gray-600">Aquí puedes restaurar grupos o eliminarlos permanentemente.</p>
            </div>
            <a href="{{ route('coordinador.grupos.index') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Volver al índice</span>
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

        @if($gruposBorrados->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-trash-restore-alt text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">La papelera está vacía</h3>
                <p class="text-gray-500">No hay grupos eliminados para mostrar.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($gruposBorrados as $grupo)
                    <div class="border border-gray-200 rounded-xl p-5 bg-gray-50">
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <h3 class="font-semibold text-lg text-gray-800">
                                        Nivel {{ $grupo->nivel_ingles }} - Grupo "{{ $grupo->letra_grupo }}"
                                    </h3>
                                    <span class="bg-red-100 text-red-800 text-xs px-3 py-1 rounded-full font-medium">
                                Eliminado
                            </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar text-blue-500"></i>
                                        <span>Periodo: {{ $grupo->periodo->nombre ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-chalkboard-teacher text-purple-500"></i>
                                        <span>Profesor: {{ $grupo->profesor->nombre_completo ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 lg:flex-shrink-0">
                                <form action="{{ route('coordinador.grupos.restore', $grupo->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg transition-smooth flex items-center gap-2">
                                        <i class="fas fa-trash-restore"></i>
                                        <span>Restaurar</span>
                                    </button>
                                </form>

                                <form action="{{ route('coordinador.grupos.forceDelete', $grupo->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg transition-smooth flex items-center gap-2"
                                            onclick="return confirm('¿Estás seguro de ELIMINAR PERMANENTEMENTE este grupo? Esta acción no se puede deshacer.')">
                                        <i class="fas fa-times-circle"></i>
                                        <span>Eliminar Perm.</span>
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
