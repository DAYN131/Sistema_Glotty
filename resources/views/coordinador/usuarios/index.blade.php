{{-- resources/views/coordinador/usuarios/index.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Gestión de Usuarios - Glotty')
@section('header-title', 'Gestión de Usuarios')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- Botón para regresar al Panel --}}
    <div class="flex justify-end mb-6">
        <a href="{{ url('/coordinador') }}" 
           class="bg-white text-gray-600 hover:bg-gray-50 border border-gray-200 px-4 py-2 rounded-lg shadow-sm transition-colors flex items-center space-x-2 text-sm font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Volver al Panel</span>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-400 text-xl mr-3"></i>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600 mr-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Usuarios</p>
                    <p class="text-xl font-bold text-blue-700">{{ $estadisticas['total'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg text-green-600 mr-3">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Internos</p>
                    <p class="text-xl font-bold text-green-700">{{ $estadisticas['internos'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg text-orange-600 mr-3">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Externos</p>
                    <p class="text-xl font-bold text-orange-700">{{ $estadisticas['externos'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg text-purple-600 mr-3">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Activos</p>
                    <p class="text-xl font-bold text-purple-700">{{ $estadisticas['activos'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                {{-- Búsqueda --}}
                <div class="flex-1">
                    <form method="GET" action="{{ route('coordinador.usuarios.index') }}">
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   placeholder="Buscar por nombre, email o número de control..." 
                                   value="{{ request('search') }}"
                                   class="w-full border border-slate-300 rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Filtro tipo --}}
                <div class="flex-1">
                    <form method="GET" action="{{ route('coordinador.usuarios.index') }}" id="filtroTipoForm">
                        <select name="tipo" 
                                onchange="document.getElementById('filtroTipoForm').submit()"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="todos" {{ request('tipo') == 'todos' ? 'selected' : '' }}>Todos los tipos</option>
                            <option value="interno" {{ request('tipo') == 'interno' ? 'selected' : '' }}>Alumnos Internos</option>
                            <option value="externo" {{ request('tipo') == 'externo' ? 'selected' : '' }}>Alumnos Externos</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="flex gap-2">
                <button onclick="window.location.href='{{ route('coordinador.usuarios.index') }}'" 
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-redo mr-2"></i>
                    Limpiar Filtros
                </button>
                <button onclick="exportarUsuarios()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-file-export mr-2"></i>
                    Exportar
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <div class="bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Lista de Usuarios</h2>
            <p class="text-slate-200 text-sm mt-1">{{ $usuarios->total() }} usuarios encontrados</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Carrera</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Preregistros</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Estado Actual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($usuarios as $usuario)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-slate-900">
                                        {{ $usuario->nombre_completo }}
                                    </div>
                                    <div class="text-sm text-slate-500">
                                        @if($usuario->numero_control)
                                            {{ $usuario->numero_control }}
                                        @else
                                            <span class="text-orange-600">EXTERNO</span>
                                        @endif
                                    </div>
                                    @if($usuario->semestre_carrera)
                                    <div class="text-xs text-slate-400 mt-1">
                                        Semestre: {{ $usuario->semestre_carrera }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($usuario->numero_control)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-user-graduate mr-1"></i> Interno
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                    <i class="fas fa-user-tie mr-1"></i> Externo
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-900">{{ $usuario->correo_institucional ?? $usuario->correo_personal}}</div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-900">{{ $usuario->especialidad ?? 'EXTERNO'}}</div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-blue-600">{{ $usuario->preregistros_count ?? 0 }}</div>
                                    <div class="text-xs text-slate-500">Total</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-green-600">{{ $usuario->preregistros_activos ?? 0 }}</div>
                                    <div class="text-xs text-slate-500">Activos</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-600">{{ $usuario->preregistros_finalizados ?? 0 }}</div>
                                    <div class="text-xs text-slate-500">Finalizados</div>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $preregistroActivo = $usuario->preregistros->firstWhere('estado', 'cursando') 
                                                   ?? $usuario->preregistros->firstWhere('estado', 'asignado');
                            @endphp
                            
                            @if($preregistroActivo)
                                <div class="text-sm font-medium text-slate-900">
                                    {{ $preregistroActivo->estado_formateado }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    @if($preregistroActivo->grupoAsignado)
                                        Grupo: {{ $preregistroActivo->grupoAsignado->nombre_completo }}
                                    @else
                                        Sin grupo asignado
                                    @endif
                                </div>
                            @else
                                <span class="text-sm text-slate-400">Sin actividad reciente</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('coordinador.usuarios.show', $usuario->id) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>

                                 <a href="{{ route('coordinador.usuarios.edit', $usuario->id) }}" 
                                    class="text-yellow-600 hover:text-yellow-900" title="Editar usuario">
                                        <i class="fas fa-edit"></i>
                                </a>
                                                            
                                @if($usuario->email)
                                <a href="mailto:{{ $usuario->email }}" 
                                   class="text-green-600 hover:text-green-900" title="Enviar correo">
                                    <i class="fas fa-envelope"></i>
                                </a>
                                @endif
                                
                                <button onclick="mostrarHistorial({{ $usuario->id }})" 
                                        class="text-purple-600 hover:text-purple-900" title="Ver historial">
                                    <i class="fas fa-history"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            <i class="fas fa-users text-4xl mb-3"></i>
                            <p class="text-lg">No se encontraron usuarios</p>
                            <p class="text-sm mt-2">Intenta ajustar los filtros de búsqueda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($usuarios->hasPages())
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
            {{ $usuarios->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal para historial --}}
<div id="modalHistorial" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Historial de Preregistros</h3>
            <button onclick="cerrarHistorial()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="contenidoHistorial" class="max-h-96 overflow-y-auto">
            <!-- Contenido dinámico -->
        </div>
    </div>
</div>

@push('scripts')
<script>
// Mostrar historial de usuario
function mostrarHistorial(usuarioId) {
    fetch(`/coordinador/usuarios/${usuarioId}/historial`)
        .then(response => response.json())
        .then(data => {
            const contenido = document.getElementById('contenidoHistorial');
            contenido.innerHTML = data.html;
            document.getElementById('modalHistorial').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar el historial');
        });
}

// Cerrar modal de historial
function cerrarHistorial() {
    document.getElementById('modalHistorial').classList.add('hidden');
}

function exportarUsuarios() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = `/coordinador/usuarios/export?${params.toString()}`;
}

// Cerrar modal al hacer click fuera
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalHistorial');
    if (e.target === modal) {
        cerrarHistorial();
    }
});

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarHistorial();
    }
});
</script>
@endpush
@endsection