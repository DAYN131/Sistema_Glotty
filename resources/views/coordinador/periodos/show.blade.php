{{-- resources/views/coordinador/periodos/show.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Gestión de Horarios por periodo')
@section('header-title', 'Gestión Horarios por Periodo')

@section('content')
<div class="mb-6">
    {{-- Botón para regenerar horarios --}}
    @if($periodo->estaEnConfiguracion())
    <div class="mb-4">
        <form action="{{ route('coordinador.periodos.regenerar-horarios', $periodo) }}" method="POST">
            @csrf
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-smooth"
                    onclick="return confirm('¿Regenerar todos los horarios? Se eliminarán los actuales.')">
                <i class="fas fa-sync-alt mr-2"></i>Regenerar Horarios desde Plantillas
            </button>
        </form>
    </div>
    @endif

    {{-- Lista de horarios --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($horariosPeriodo as $horarioPeriodo)
        <div class="border border-gray-200 rounded-lg p-4 {{ $horarioPeriodo->activo ? '' : 'bg-gray-50 opacity-75' }}">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h4 class="font-semibold text-gray-800">{{ $horarioPeriodo->nombre }}</h4>
                    <p class="text-xs text-gray-500">
                        De plantilla: {{ $horarioPeriodo->horarioBase->nombre ?? 'N/A' }}
                    </p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $horarioPeriodo->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ $horarioPeriodo->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            
            {{-- Información del horario --}}
            <div class="text-sm text-gray-600 space-y-1 mb-3">
                <div>{{ $horarioPeriodo->horario_completo }}</div>
                <div>{{ $horarioPeriodo->duracion }} horas</div>
            </div>

            {{-- Acciones --}}
            @if($periodo->estaEnConfiguracion())
            <div class="flex gap-2 border-t border-gray-200 pt-3">
                {{-- Toggle Activo/Inactivo --}}
                <form action="{{ route('coordinador.periodos.toggle-horario', [$periodo, $horarioPeriodo]) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full text-sm {{ $horarioPeriodo->activo ? 'bg-yellow-100 hover:bg-yellow-200 text-yellow-700' : 'bg-green-100 hover:bg-green-200 text-green-700' }} px-3 py-1 rounded transition-smooth">
                        {{ $horarioPeriodo->activo ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>

                {{-- Eliminar --}}
                <form action="{{ route('coordinador.periodos.eliminar-horario', [$periodo, $horarioPeriodo]) }}" method="POST">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" 
                            class="text-sm bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded transition-smooth"
                            onclick="return confirm('¿Eliminar este horario del periodo?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-full text-center py-8 text-gray-500">
            <p>No hay horarios configurados para este período.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection