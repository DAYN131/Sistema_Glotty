{{-- resources/views/coordinador/preregistros/index.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Gestión de Preregistros - Glotty')
@section('header-title', 'Gestión de Preregistros')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- CAMBIO AQUÍ: Botón superior para regresar al Panel --}}
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

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-400 text-xl mr-3"></i>
                <span class="text-red-800 font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-card p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Filtrar por nivel</label>
                    <select id="filtroNivel" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos los niveles</option>
                        @foreach(\App\Models\Preregistro::NIVELES as $nivel => $descripcion)
                            <option value="{{ $nivel }}" {{ request('nivel') == $nivel ? 'selected' : '' }}>
                                {{ $descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Filtrar por estado</label>
                    <select id="filtroEstado" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos los estados</option>
                        @foreach(\App\Models\Preregistro::ESTADOS as $estado => $descripcion)
                            <option value="{{ $estado }}" {{ request('estado') == $estado ? 'selected' : '' }}>
                                {{ $descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Filtrar por pago</label>
                    <select id="filtroPago" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos los estados</option>
                        @foreach(\App\Models\Preregistro::PAGO_ESTADOS as $estado => $descripcion)
                            <option value="{{ $estado }}" {{ request('pago_estado') == $estado ? 'selected' : '' }}>
                                {{ $descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('coordinador.preregistros.demanda') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Ver Demanda
                </a>
                <button id="aplicarFiltros" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Aplicar Filtros
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600 mr-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-xl font-bold text-blue-700">{{ $preregistros->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg text-green-600 mr-3">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pendientes</p>
                    <p class="text-xl font-bold text-green-700">{{ $preregistros->where('estado', 'pendiente')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg text-orange-600 mr-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Asignados</p>
                    <p class="text-xl font-bold text-orange-700">{{ $preregistros->where('estado', 'asignado')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg text-purple-600 mr-3">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pagados</p>
                    <p class="text-xl font-bold text-purple-700">{{ $preregistros->where('pago_estado', 'pagado')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card overflow-hidden">
        <div class="bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Lista de Preregistros</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Estudiante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nivel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Horario Preferido</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Pago</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Grupo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($preregistros as $preregistro)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-slate-900">
                                        {{ $preregistro->usuario->nombre_completo ?? 'No disponible' }}
                                    </div>
                                    <div class="text-sm text-slate-500">
                                        {{ $preregistro->usuario->numero_control ?? 'N/A' }}
                                    </div>
                                    @if($preregistro->semestre_carrera)
                                    <div class="text-xs text-slate-400 mt-1">
                                        {{ $preregistro->semestre_carrera }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-bold mr-2">
                                    {{ $preregistro->nivel_solicitado }}
                                </span>
                                <span class="text-sm font-medium text-slate-700">
                                    {{ $preregistro->nivel_formateado }}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($preregistro->horarioPreferido)
                                <div class="text-sm text-slate-900">{{ $preregistro->horarioPreferido->nombre }}</div>
                                <div class="text-xs text-slate-500">
                                    @php
                                        $diasArray = [];
                                        if (is_array($preregistro->horarioPreferido->dias)) {
                                            $diasArray = $preregistro->horarioPreferido->dias;
                                        } elseif (is_string($preregistro->horarioPreferido->dias)) {
                                            $decoded = json_decode($preregistro->horarioPreferido->dias, true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                $diasArray = $decoded;
                                            } else {
                                                $diasArray = array_map('trim', explode(',', $preregistro->horarioPreferido->dias));
                                            }
                                        }
                                        $diasArray = array_filter($diasArray);
                                        $diasTexto = !empty($diasArray) ? implode(', ', $diasArray) : 'No especificado';
                                    @endphp
                                    {{ $diasTexto }}
                                </div>
                            @else
                                <span class="text-sm text-slate-400">No asignado</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $estadoColors = [
                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                    'asignado' => 'bg-blue-100 text-blue-800',
                                    'cursando' => 'bg-green-100 text-green-800',
                                    'finalizado' => 'bg-gray-100 text-gray-800',
                                    'cancelado' => 'bg-red-100 text-red-800'
                                ];
                                $color = $estadoColors[$preregistro->estado] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                                {{ $preregistro->estado_formateado }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $pagoColors = [
                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                    'pagado' => 'bg-green-100 text-green-800',
                                    'rechazado' => 'bg-red-100 text-red-800'
                                ];
                                $colorPago = $pagoColors[$preregistro->pago_estado] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $colorPago }}">
                                {{ $preregistro->pago_estado_formateado }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($preregistro->grupoAsignado)
                                <div class="text-sm font-medium text-slate-900">
                                    {{ $preregistro->grupoAsignado->nombre_completo ?? 'Grupo asignado' }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ $preregistro->grupoAsignado->horario->nombre ?? 'Sin horario' }}
                                </div>
                            @else
                                <span class="text-sm text-slate-400">Sin asignar</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('coordinador.preregistros.show', $preregistro->id) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($preregistro->pago_estado === 'pagado')
                                    @if($preregistro->estado === 'pendiente' || $preregistro->estado === 'asignado')
                                    <button onclick="mostrarModalAsignar({{ $preregistro->id }})" 
                                            class="text-green-600 hover:text-green-900" 
                                            title="{{ $preregistro->grupoAsignado ? 'Reasignar grupo' : 'Asignar grupo' }}">
                                        <i class="fas fa-users"></i>
                                    </button>
                                    @endif
                                @endif

                                @if($preregistro->grupoAsignado && $preregistro->estado === 'asignado')
                                <button onclick="mostrarModalQuitarGrupo({{ $preregistro->id }})" 
                                        class="text-orange-600 hover:text-orange-900" title="Quitar grupo">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                                @endif

                                <button onclick="mostrarModalPago({{ $preregistro->id }}, '{{ $preregistro->pago_estado }}')" 
                                        class="text-purple-600 hover:text-purple-900" title="Cambiar estado de pago">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>

                                @if($preregistro->puedeSerCancelado())
                                <form action="{{ route('coordinador.preregistros.cancelar', $preregistro->id) }}" 
                                      method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de cancelar este preregistro?')">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Cancelar preregistro">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                            <i class="fas fa-users text-4xl mb-3"></i>
                            <p class="text-lg">No se encontraron preregistros</p>
                            <p class="text-sm mt-2">No hay preregistros que coincidan con los filtros aplicados</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($preregistros->hasPages())
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
            {{ $preregistros->links() }}
        </div>
        @endif
    </div>
</div>

@include('coordinador.preregistros.modales.asignar-grupo')
@include('coordinador.preregistros.modales.cambiar-pago')
@include('coordinador.preregistros.modales.quitar-grupo') {{-- NUEVO --}}

@push('scripts')
<script>
// Datos de preregistros
const preregistros = {!! $preregistros->keyBy('id')->map(function($p) {
    return [
        'nombre' => $p->usuario->nombre_completo ?? 'No disponible',
        'nivel' => $p->nivel_solicitado,
        'nivel_texto' => $p->nivel_formateado,
        'horario_preferido' => $p->horarioPreferido->nombre ?? 'No especificado',
        'pago_estado' => $p->pago_estado,
        'pago_estado_texto' => $p->pago_estado_formateado,
        'estado' => $p->estado,
        'estado_texto' => $p->estado_formateado,
        'grupo_asignado' => $p->grupoAsignado ? $p->grupoAsignado->nombre_completo : 'Sin asignar'
    ];
})->toJson() !!};

// Filtros
document.getElementById('aplicarFiltros').addEventListener('click', function() {
    const nivel = document.getElementById('filtroNivel').value;
    const estado = document.getElementById('filtroEstado').value;
    const pago = document.getElementById('filtroPago').value;
    
    let url = new URL(window.location.href);
    let params = new URLSearchParams();
    
    if (nivel) params.set('nivel', nivel);
    if (estado) params.set('estado', estado);
    if (pago) params.set('pago_estado', pago);
    
    window.location.href = url.pathname + '?' + params.toString();
});

// Modal de Asignar Grupo
function mostrarModalAsignar(preregistroId) {
    const preregistro = preregistros[preregistroId];
    if (!preregistro) return;
    
    document.getElementById('preregistroId').value = preregistroId;
    document.getElementById('formAsignarGrupo').action = `/coordinador/preregistros/${preregistroId}/asignar-grupo`;
    
    // Actualizar información del estudiante
    document.getElementById('infoEstudianteNombre').querySelector('span').textContent = preregistro.nombre;
    document.getElementById('infoEstudianteNivel').querySelector('span').textContent = preregistro.nivel_texto;
    document.getElementById('infoEstudianteHorario').querySelector('span').textContent = preregistro.horario_preferido;
    document.getElementById('infoEstudiantePago').querySelector('span').textContent = preregistro.pago_estado_texto;
    
    // Filtrar grupos por nivel
    const selectGrupo = document.getElementById('selectGrupo');
    const options = selectGrupo.querySelectorAll('option');
    options.forEach(option => {
        if (option.value) {
            const nivelGrupo = option.getAttribute('data-nivel');
            option.style.display = nivelGrupo == preregistro.nivel ? '' : 'none';
        }
    });
    
    // Resetear selección
    selectGrupo.value = '';
    document.getElementById('infoGrupo').textContent = '';
    
    document.getElementById('modalAsignarGrupo').classList.remove('hidden');
}

function cerrarModalAsignar() {
    document.getElementById('modalAsignarGrupo').classList.add('hidden');
}

// Info del grupo seleccionado
document.getElementById('selectGrupo').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const infoGrupo = document.getElementById('infoGrupo');
    
    if (selectedOption.value) {
        const capacidad = selectedOption.getAttribute('data-capacidad');
        const inscritos = selectedOption.getAttribute('data-inscritos');
        const disponible = selectedOption.getAttribute('data-disponible');
        
        infoGrupo.textContent = `Capacidad: ${inscritos}/${capacidad} (${disponible} disponibles)`;
        infoGrupo.className = 'mt-1 text-xs ' + (disponible > 0 ? 'text-green-600' : 'text-red-600');
    } else {
        infoGrupo.textContent = '';
    }
});

// Modal de Quitar Grupo
function mostrarModalQuitarGrupo(preregistroId) {
    const preregistro = preregistros[preregistroId];
    if (!preregistro) return;
    
    document.getElementById('quitarGrupoPreregistroId').value = preregistroId;
    document.getElementById('formQuitarGrupo').action = `/coordinador/preregistros/${preregistroId}/quitar-grupo`;
    
    // Actualizar información en el modal
    const infoDiv = document.getElementById('infoQuitarGrupo');
    infoDiv.innerHTML = `
        <div class="space-y-2">
            <p><strong>Estudiante:</strong> ${preregistro.nombre}</p>
            <p><strong>Grupo actual:</strong> ${preregistro.grupo_asignado}</p>
            <p><strong>Nivel:</strong> ${preregistro.nivel_texto}</p>
            <p><strong>Estado actual:</strong> ${preregistro.estado_texto}</p>
        </div>
    `;
    
    document.getElementById('modalQuitarGrupo').classList.remove('hidden');
}

function cerrarModalQuitarGrupo() {
    document.getElementById('modalQuitarGrupo').classList.add('hidden');
}

// Modal de Cambiar Pago
function mostrarModalPago(preregistroId, estadoPagoActual) {
    const preregistro = preregistros[preregistroId];
    if (!preregistro) return;
    
    document.getElementById('pagoPreregistroId').value = preregistroId;
    document.getElementById('formCambiarPago').action = `/coordinador/preregistros/${preregistroId}/cambiar-pago`;
    document.getElementById('selectPagoEstado').value = estadoPagoActual;
    
    // Actualizar información
    document.getElementById('infoPagoEstudiante').querySelector('span').textContent = preregistro.nombre;
    document.getElementById('infoPagoActual').querySelector('span').textContent = preregistro.pago_estado_texto;
    document.getElementById('infoPagoEstado').querySelector('span').textContent = preregistro.estado_texto;
    
    // Mostrar info si se selecciona pagado
    const infoPagado = document.getElementById('infoPagoPagado');
    const selectPagoEstado = document.getElementById('selectPagoEstado');
    
    // Configurar evento change
    selectPagoEstado.addEventListener('change', function() {
        if (this.value === 'pagado') {
            infoPagado.classList.remove('hidden');
        } else {
            infoPagado.classList.add('hidden');
        }
    });
    
    // Estado inicial
    if (selectPagoEstado.value === 'pagado') {
        infoPagado.classList.remove('hidden');
    } else {
        infoPagado.classList.add('hidden');
    }
    
    document.getElementById('modalCambiarPago').classList.remove('hidden');
}

function cerrarModalPago() {
    document.getElementById('modalCambiarPago').classList.add('hidden');
}

// Cerrar modales al hacer click fuera y con ESC
document.addEventListener('click', function(e) {
    const modales = ['modalAsignarGrupo', 'modalCambiarEstado', 'modalCambiarPago', 'modalQuitarGrupo'];
    modales.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('modalAsignarGrupo').classList.add('hidden');
        // document.getElementById('modalCambiarEstado').classList.add('hidden'); // No usado en este archivo
        document.getElementById('modalCambiarPago').classList.add('hidden');
        document.getElementById('modalQuitarGrupo').classList.add('hidden');
    }
});
</script>
@endpush
@endsection