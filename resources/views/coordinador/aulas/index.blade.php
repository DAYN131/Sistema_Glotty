@extends('layouts.coordinador')

@section('title', 'Gestión de Aulas')
@section('header-title', 'Gestión de Aulas')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-school text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Aulas</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $aulas->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-green-100 p-3 rounded-xl">
                    <i class="fas fa-users text-green-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Capacidad Total</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $aulas->sum('capacidad') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-card p-6">
            <div class="flex items-center space-x-4">
                <div class="bg-purple-100 p-3 rounded-xl">
                    <i class="fas fa-building text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Edificios Distintos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $aulas->pluck('edificio')->unique()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Listado de Aulas</h2>
            <a href="{{ route('coordinador.aulas.create') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Nueva Aula</span>
            </a>
        </div>

        {{-- Notificaciones mejoradas --}}
        @if(session('success'))
            <div id="success-notification" class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 relative">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                        <span class="text-green-800 font-medium">{{ session('success') }}</span>
                    </div>
                    <button type="button" 
                            onclick="document.getElementById('success-notification').style.display='none'"
                            class="text-green-600 hover:text-green-800 transition-smooth">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div id="error-notification" class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 relative">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-600 text-xl mr-3"></i>
                        <span class="text-red-800 font-medium">{{ session('error') }}</span>
                    </div>
                    <button type="button" 
                            onclick="document.getElementById('error-notification').style.display='none'"
                            class="text-red-600 hover:text-red-800 transition-smooth">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if($aulas->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-school text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No hay aulas registradas</h3>
                <p class="text-gray-500 mb-6">Comienza creando la primera aula</p>
                <a href="{{ route('coordinador.aulas.create') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-smooth inline-flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Crear Primera Aula</span>
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($aulas as $aula)
                    <div class="border border-gray-200 rounded-xl p-5 hover:shadow-card-hover transition-smooth">
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <h3 class="font-semibold text-lg text-gray-800">Aula: {{ $aula->id_aula }}</h3>
                                    <span class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-medium">
                                        {{ ucfirst($aula->tipo_aula) }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-building text-gray-500"></i>
                                        <span>Edificio: <span class="font-medium">{{ $aula->edificio }}</span></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-door-closed text-gray-500"></i>
                                        <span>Identificador: <span class="font-medium">{{ $aula->nombre_aula }}</span></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-users text-gray-500"></i>
                                        <span>Capacidad: <span class="font-medium">{{ $aula->capacidad }} personas</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 lg:flex-shrink-0">
                                <a href="{{ route('coordinador.aulas.edit', $aula->id_aula) }}"
                                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg transition-smooth flex items-center gap-2">
                                    <i class="fas fa-edit"></i>
                                    <span>Editar</span>
                                </a>

                                <form action="{{ route('coordinador.aulas.destroy', $aula->id_aula) }}" method="POST" class="inline" id="delete-form-{{ $aula->id_aula }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg transition-smooth flex items-center gap-2"
                                            onclick="confirmDelete('{{ $aula->id_aula }}')">
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

    {{-- Script para confirmación de eliminación --}}
    <script>
        function confirmDelete(aulaId) {
            if (confirm('¿Estás seguro de eliminar el aula ' + aulaId + '? Esta acción no se puede deshacer.')) {
                document.getElementById('delete-form-' + aulaId).submit();
            }
        }

        // Auto-ocultar notificaciones después de 5 segundos
        setTimeout(function() {
            const successNotification = document.getElementById('success-notification');
            const errorNotification = document.getElementById('error-notification');
            
            if (successNotification) {
                successNotification.style.display = 'none';
            }
            if (errorNotification) {
                errorNotification.style.display = 'none';
            }
        }, 5000);
    </script>
@endsection