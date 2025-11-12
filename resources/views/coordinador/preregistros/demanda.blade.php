{{-- resources/views/coordinador/preregistros/demanda.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Análisis de Demanda - Glotty')
@section('header-title', 'Análisis de Demanda de Preregistros')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Alertas -->
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

    <!-- Información del periodo activo -->
    @if($periodoActivo)
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-blue-700 font-medium">Periodo Activo: {{ $periodoActivo->nombre }}</p>
                <p class="text-blue-600 text-sm mt-1">
                    Fecha: {{ \Carbon\Carbon::parse($periodoActivo->fecha_inicio)->format('d/m/Y') }} - 
                    {{ \Carbon\Carbon::parse($periodoActivo->fecha_fin)->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Resumen de Demanda -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg text-blue-600 mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Preregistros</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $totalPreregistros }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg text-green-600 mr-4">
                    <i class="fas fa-user-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Por Asignar</p>
                    <p class="text-2xl font-bold text-green-700">{{ $preregistrosPendientes }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg text-purple-600 mr-4">
                    <i class="fas fa-chart-bar text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Niveles Solicitados</p>
                    <p class="text-2xl font-bold text-purple-700">{{ $nivelesUnicos }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-card border border-slate-200">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg text-orange-600 mr-4">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Horarios Solicitados</p>
                    <p class="text-2xl font-bold text-orange-700">{{ $horariosUnicos }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Columna Izquierda: Análisis de Demanda -->
        <div>
            <div class="bg-white rounded-2xl shadow-card overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Demanda por Nivel</h2>
                </div>
                <div class="p-6">
                    @foreach($demandaPorNivel as $nivel => $cantidad)
                    <div class="flex items-center justify-between py-3 border-b border-slate-200 last:border-b-0">
                        <div class="flex items-center">
                            <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-bold mr-3">
                                {{ $nivel }}
                            </span>
                            <span class="font-medium text-slate-700">Nivel {{ $nivel }}</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-slate-600 font-medium">{{ $cantidad }} estudiantes</span>
                            <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded text-sm">
                                {{ ceil($cantidad / 25) }} grupos sugeridos
                            </span>
                            <!-- Botón para ver estudiantes -->
                            <button onclick="mostrarEstudiantesNivel({{ $nivel }})" 
                                    class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1 rounded text-sm flex items-center transition-colors">
                                <i class="fas fa-eye mr-1"></i>
                                Ver
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

          <!-- Demanda por Horario - VERSIÓN OPTIMIZADA -->
            <div class="bg-white rounded-2xl shadow-card overflow-hidden">
                <div class="bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Demanda por Horario</h2>
                </div>
                <div class="p-6">
                    @foreach($demandaPorHorario as $horarioId => $data)
                    <div class="flex items-center justify-between py-3 border-b border-slate-200 last:border-b-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-clock text-orange-600"></i>
                            </div>
                            <div>
                                <!-- Solo mostrar información esencial -->
                                <div class="flex items-center space-x-4 text-sm text-slate-600 mb-1">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-medium">
                                        {{ $data['tipo'] }}
                                    </span>
                                    @php
                                        // Decodificar el JSON de días
                                        $diasArray = is_string($data['dias'] ?? '') ? json_decode($data['dias'], true) : ($data['dias'] ?? []);
                                        $diasArray = $diasArray ?? [];
                                        $diasTexto = !empty($diasArray) ? implode(', ', $diasArray) : 'Días no especificados';
                                    @endphp
                                    <span class="font-medium">{{ $diasTexto }}</span>
                                </div>
                                <div class="text-sm text-slate-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $data['hora_inicio'] }} - {{ $data['hora_fin'] }}
                                </div>
                            </div>
                        </div>
                        <span class="text-slate-600 font-medium">{{ $data['cantidad'] }} estudiantes</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Grupos Sugeridos y Creación Rápida -->
        <div>
            <div class="bg-white rounded-2xl shadow-card overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Grupos Sugeridos</h2>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-slate-600 text-sm mb-3">
                            Basado en la demanda actual, se sugieren los siguientes grupos (mínimo 25 estudiantes por grupo):
                        </p>
                    </div>

                    @foreach($gruposSugeridos as $sugerencia)
                    <div class="border border-slate-200 rounded-xl p-4 mb-4 bg-slate-50">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-bold mr-3">
                                    {{ $sugerencia['nivel'] }}
                                </span>
                                <div>
                                    <h4 class="font-semibold text-slate-800">Nivel {{ $sugerencia['nivel'] }}</h4>
                                    <p class="text-sm text-slate-500">{{ $sugerencia['estudiantes'] }} estudiantes</p>
                                </div>
                            </div>
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-sm font-medium">
                                {{ $sugerencia['grupos_sugeridos'] }} grupos
                            </span>
                        </div>
                        
                        <!-- Horarios más demandados para este nivel -->
                        <div class="flex items-center space-x-4 text-sm text-slate-600 mb-1">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-medium">
                                        {{ $data['tipo'] }}
                                    </span>
                                    @php
                                        // Decodificar el JSON de días
                                        $diasArray = is_string($data['dias'] ?? '') ? json_decode($data['dias'], true) : ($data['dias'] ?? []);
                                        $diasArray = $diasArray ?? [];
                                        $diasTexto = !empty($diasArray) ? implode(', ', $diasArray) : 'Días no especificados';
                                    @endphp
                                    <span class="font-medium">{{ $diasTexto }}</span>
                                </div>
                                <div class="text-sm text-slate-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $data['hora_inicio'] }} - {{ $data['hora_fin'] }}
                                </div>

                        <!-- Acciones rápidas -->
                        <div class="flex space-x-2">
                            <button onclick="mostrarModalCrearGrupo({{ $sugerencia['nivel'] }})" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center transition-colors">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Crear Grupo
                            </button>
                            <a href="{{ route('coordinador.grupos.create') }}?nivel={{ $sugerencia['nivel'] }}" 
                               class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded-lg text-sm flex items-center transition-colors">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                Crear con Opciones
                            </a>
                        </div>
                    </div>
                    @endforeach

                    @if(empty($gruposSugeridos))
                    <div class="text-center py-8 text-slate-500">
                        <i class="fas fa-chart-pie text-4xl mb-3"></i>
                        <p>No hay suficiente demanda para sugerir grupos</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="bg-white rounded-2xl shadow-card overflow-hidden mt-6">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Acciones Rápidas</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('coordinador.preregistros.index') }}" 
                           class="bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-list mr-3 text-blue-600"></i>
                            <div>
                                <p class="font-medium">Ver Todos los Preregistros</p>
                                <p class="text-sm">Gestionar preregistros individualmente</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('coordinador.grupos.index') }}" 
                           class="bg-green-50 hover:bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-users mr-3 text-green-600"></i>
                            <div>
                                <p class="font-medium">Gestionar Grupos Existentes</p>
                                <p class="text-sm">Ver y editar grupos actuales</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para creación rápida de grupo - VERSIÓN CORREGIDA -->
<div id="modalCrearGrupo" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold text-slate-800 mb-4">Crear Grupo Rápido</h3>
        <form id="formCrearGrupoRapido" action="" method="POST">
            @csrf
            <input type="hidden" name="nivel_ingles" id="nivelGrupo">
            <input type="hidden" name="periodo_id" value="{{ $periodoActivo->id ?? '' }}">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Letra del Grupo *</label>
                    <select name="letra_grupo" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Selecciona letra</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Horario *</label>
                    <select name="horario_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Selecciona horario</option>
                        @foreach($horariosDisponibles as $horario)
                            @php
                                $diasArray = is_string($horario->dias) ? json_decode($horario->dias, true) : $horario->dias;
                                $diasArray = $diasArray ?? [];
                                $diasTexto = !empty($diasArray) ? implode(', ', $diasArray) : 'Días no especificados';
                            @endphp
                            <option value="{{ $horario->id }}">
                                {{ $horario->tipo }} - {{ $diasTexto }} ({{ $horario->hora_inicio->format('H:i') }}-{{ $horario->hora_fin->format('H:i') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Capacidad Máxima *</label>
                    <input type="number" name="capacidad_maxima" value="25" min="20" max="50" 
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <p class="mt-1 text-xs text-slate-500">Mínimo 20, máximo 50 estudiantes</p>
                </div>
            </div>
            
            <div class="flex space-x-3 mt-6">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Crear Grupo
                </button>
                <button type="button" onclick="cerrarModalGrupo()" class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 py-2 rounded-lg transition-colors">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para ver estudiantes por nivel -->
<div id="modalEstudiantes" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-4xl mx-4 max-h-[80vh] overflow-hidden flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-slate-800" id="modalTitulo">Estudiantes - Nivel </h3>
            <button onclick="cerrarModalEstudiantes()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            <div class="space-y-3" id="listaEstudiantes">
                <!-- Los estudiantes se cargarán aquí dinámicamente -->
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-t border-slate-200">
            <div class="flex justify-between items-center text-sm text-slate-600">
                <span id="totalEstudiantes">Total: 0 estudiantes</span>
                <button onclick="cerrarModalEstudiantes()" 
                        class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded-lg transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Funciones para el modal de creación de grupo
function mostrarModalCrearGrupo(nivel) {
    document.getElementById('nivelGrupo').value = nivel;
    document.getElementById('modalCrearGrupo').classList.remove('hidden');
}

function cerrarModalGrupo() {
    document.getElementById('modalCrearGrupo').classList.add('hidden');
}

// Funciones para el modal de estudiantes
function mostrarEstudiantesNivel(nivel) {
    // Mostrar loading
    document.getElementById('listaEstudiantes').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-blue-500 text-2xl mb-2"></i>
            <p class="text-slate-500">Cargando estudiantes...</p>
        </div>
    `;
    
    // Actualizar título
    document.getElementById('modalTitulo').textContent = `Estudiantes - Nivel ${nivel}`;
    
    // Mostrar modal
    document.getElementById('modalEstudiantes').classList.remove('hidden');
    
    // Hacer petición AJAX
    fetch(`/coordinador/preregistros/estudiantes-por-nivel/${nivel}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la petición');
            }
            return response.json();
        })
        .then(data => {
            mostrarListaEstudiantes(data.estudiantes, data.total);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('listaEstudiantes').innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                    <p>Error al cargar los estudiantes</p>
                    <p class="text-sm mt-2">Por favor, intenta nuevamente</p>
                </div>
            `;
        });
}

function mostrarListaEstudiantes(estudiantes, total) {
    const lista = document.getElementById('listaEstudiantes');
    const totalElement = document.getElementById('totalEstudiantes');
    
    totalElement.textContent = `Total: ${total} estudiantes`;
    
    if (estudiantes.length === 0) {
        lista.innerHTML = `
            <div class="text-center py-8 text-slate-500">
                <i class="fas fa-users text-2xl mb-2"></i>
                <p>No hay estudiantes para este nivel</p>
            </div>
        `;
        return;
    }
    
    lista.innerHTML = estudiantes.map(estudiante => `
        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <!-- Header con nombre y número de control -->
                    <div class="flex items-center space-x-3 mb-3">
                        <h4 class="font-semibold text-slate-800">${estudiante.nombre}</h4>
                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded">${estudiante.numero_control}</span>
                    </div>
                    
                    <!-- Información académica -->
                    <div class="flex items-center space-x-4 text-sm text-slate-600 mb-3">
                        <div class="flex items-center">
                            <i class="fas fa-graduation-cap text-slate-400 mr-2"></i>
                            <span>${estudiante.especialidad || 'Sin especialidad'}</span>
                            ${estudiante.semestre_carrera ? `
                            <span class="ml-2 bg-slate-200 text-slate-700 px-2 py-0.5 rounded text-xs">
                                ${estudiante.semestre_carrera} semestre
                            </span>
                            ` : ''}
                        </div>
                    </div>
                    
                    <!-- Información del horario -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                        <!-- Tipo de horario -->
                        <div class="flex items-center">
                            <i class="fas fa-tag text-slate-400 mr-2 w-4"></i>
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-medium">
                                ${estudiante.tipo_horario}
                            </span>
                        </div>
                        
                        <!-- Días -->
                        <div class="flex items-center">
                            <i class="fas fa-calendar-day text-slate-400 mr-2 w-4"></i>
                            <span class="font-medium">${estudiante.dias_horario}</span>
                        </div>
                        
                        <!-- Horas -->
                         <div class="text-sm text-slate-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $data['hora_inicio'] }} - {{ $data['hora_fin'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function cerrarModalEstudiantes() {
    document.getElementById('modalEstudiantes').classList.add('hidden');
}

// Cerrar modales al hacer click fuera
document.getElementById('modalCrearGrupo').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalGrupo();
    }
});

document.getElementById('modalEstudiantes').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalEstudiantes();
    }
});

// Cerrar modales con ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalGrupo();
        cerrarModalEstudiantes();
    }
});

// Funciones para el modal de creación de grupo - MEJORADO
function mostrarModalCrearGrupo(nivel) {
    document.getElementById('nivelGrupo').value = nivel;
    
    // Actualizar título con el nivel
    const titulo = document.querySelector('#modalCrearGrupo h3');
    titulo.textContent = `Crear Grupo Rápido - Nivel ${nivel}`;
    
    document.getElementById('modalCrearGrupo').classList.remove('hidden');
}

function cerrarModalGrupo() {
    document.getElementById('modalCrearGrupo').classList.add('hidden');
}

// Validación del formulario
document.getElementById('formCrearGrupoRapido').addEventListener('submit', function(e) {
    const letra = document.querySelector('select[name="letra_grupo"]').value;
    const horario = document.querySelector('select[name="horario_id"]').value;
    
    if (!letra || !horario) {
        e.preventDefault();
        alert('Por favor, completa todos los campos requeridos.');
    }
});

</script>



@endpush

@endsection