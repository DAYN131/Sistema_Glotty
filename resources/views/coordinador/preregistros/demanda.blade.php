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
                    @forelse($demandaPorNivel as $nivel => $cantidad)
                    <div class="flex items-center justify-between py-3 border-b border-slate-200 last:border-b-0">
                        <div class="flex items-center">
                            <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-bold mr-3">
                                {{ $nivel }}
                            </span>
                            <div>
                                <span class="font-medium text-slate-700">{{ \App\Models\Preregistro::NIVELES[$nivel] ?? "Nivel $nivel" }}</span>
                                <p class="text-xs text-slate-500 mt-1">{{ $cantidad }} estudiantes</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded text-sm">
                                {{ ceil($cantidad / 20) }} grupos sugeridos
                            </span>
                            <!-- Botón para ver estudiantes -->
                            <button onclick="mostrarEstudiantesNivel({{ $nivel }})" 
                                    class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1 rounded text-sm flex items-center transition-colors">
                                <i class="fas fa-eye mr-1"></i>
                                Ver
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500">
                        <i class="fas fa-users text-4xl mb-3"></i>
                        <p>No hay preregistros para mostrar</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Demanda por Horario -->
<div class="bg-white rounded-2xl shadow-card overflow-hidden">
    <div class="bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-4">
        <h2 class="text-xl font-bold text-white">Demanda por Horario</h2>
    </div>
    <div class="p-6">
        @forelse($demandaPorHorario as $horarioId => $data)
        <div class="border border-slate-200 rounded-lg p-4 mb-4 last:mb-0 bg-slate-50 hover:bg-slate-100 transition-colors">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <!-- Header con nombre y tipo -->
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-orange-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-1">
                                <h3 class="font-semibold text-slate-800 text-lg">{{ $data['nombre'] ?? 'Horario no disponible' }}</h3>
                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-medium">
                                    {{ $data['tipo'] ?? 'Sin tipo' }}
                                </span>
                            </div>
                            
                            <!-- Información de días y horario -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                <div class="flex items-center text-slate-600">
                                    <i class="fas fa-calendar-day text-slate-400 mr-2 w-4"></i>
                                    <div>
                                        <span class="font-medium">Días:</span>
                                        <span class="ml-2 text-slate-700">
                                            @php
                                                $diasArray = $data['dias'] ?? [];
                                                $diasTexto = !empty($diasArray) ? implode(', ', $diasArray) : 'No especificado';
                                            @endphp
                                            {{ $diasTexto }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center text-slate-600">
                                    <i class="fas fa-clock text-slate-400 mr-2 w-4"></i>
                                    <div>
                                        <span class="font-medium">Horario:</span>
                                        <span class="ml-2 text-slate-700">
                                            {{ $data['hora_inicio'] ?? '--:--' }} - {{ $data['hora_fin'] ?? '--:--' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contador de estudiantes -->
                <div class="flex flex-col items-center justify-center bg-white border border-slate-200 rounded-lg px-4 py-3 min-w-[100px] ml-4">
                    <span class="text-2xl font-bold text-slate-800">{{ $data['cantidad'] ?? 0 }}</span>
                    <span class="text-xs text-slate-500 font-medium">estudiantes</span>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-slate-500 border-2 border-dashed border-slate-300 rounded-xl bg-slate-50">
            <i class="fas fa-calendar-times text-slate-400 text-4xl mb-3"></i>
            <p class="text-slate-500 text-lg mb-2">No hay horarios con demanda</p>
            <p class="text-slate-400 text-sm">Los estudiantes aún no han seleccionado horarios preferidos</p>
        </div>
        @endforelse
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
                            Basado en la demanda actual, se sugieren los siguientes grupos (mínimo 20 estudiantes por grupo):
                        </p>
                    </div>

                    @forelse($gruposSugeridos as $sugerencia)
                    <div class="border border-slate-200 rounded-xl p-4 mb-4 bg-slate-50">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-bold mr-3">
                                    {{ $sugerencia['nivel'] }}
                                </span>
                                <div>
                                    <h4 class="font-semibold text-slate-800">{{ $sugerencia['descripcion_nivel'] }}</h4>
                                    <p class="text-sm text-slate-500">{{ $sugerencia['estudiantes'] }} estudiantes</p>
                                </div>
                            </div>
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-sm font-medium">
                                {{ $sugerencia['grupos_sugeridos'] }} grupos
                            </span>
                        </div>
                        
                        <!-- Horarios más demandados para este nivel -->
                        @if(isset($sugerencia['horarios_populares']) && count($sugerencia['horarios_populares']) > 0)
                        <div class="mb-3">
                            <p class="text-sm text-slate-600 mb-2">Horarios más solicitados:</p>
                            <div class="space-y-2">
                                @foreach($sugerencia['horarios_populares'] as $horario)
                                <div class="flex items-center justify-between text-xs bg-white px-3 py-2 rounded border">
                                    <span class="font-medium">{{ $horario['nombre'] ?? 'Horario no disponible' }}</span>
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded">
                                        {{ $horario['cantidad'] ?? 0 }} est.
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500">
                        <i class="fas fa-chart-pie text-4xl mb-3"></i>
                        <p>No hay suficiente demanda para sugerir grupos</p>
                        <p class="text-sm mt-2">Se requieren al menos 20 estudiantes por nivel</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
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
            mostrarListaEstudiantes(data.estudiantes, data.total, data.nivel);
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

function mostrarListaEstudiantes(estudiantes, total, nivel) {
    const lista = document.getElementById('listaEstudiantes');
    const totalElement = document.getElementById('totalEstudiantes');
    
    totalElement.textContent = `Total: ${total} estudiantes`;
    document.getElementById('modalTitulo').textContent = `Estudiantes - ${nivel}`;
    
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
                        <h4 class="font-semibold text-slate-800">${estudiante.nombre || 'Nombre no disponible'}</h4>
                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded">${estudiante.numero_control || 'N/C'}</span>
                        <span class="bg-${estudiante.pago_estado === 'pagado' ? 'green' : 'yellow'}-100 text-${estudiante.pago_estado === 'pagado' ? 'green' : 'yellow'}-700 text-xs px-2 py-1 rounded">
                           Pago:  ${estudiante.pago_estado === 'pagado' ? 'Pagado' : 'Pendiente'}
                        </span>
                        ${estudiante.puede_ser_asignado ? `
                        <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded">Listo para asignar</span>
                        ` : ''}
                    </div>
                    
                    <!-- Información académica -->
                    <div class="flex items-center space-x-4 text-sm text-slate-600 mb-3">
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-slate-400 mr-2"></i>
                            <span>${estudiante.correo || 'Sin correo'}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-graduation-cap text-slate-400 mr-2"></i>
                            <span>${estudiante.especialidad || 'Sin especialidad'}</span>
                            ${estudiante.semestre_actual ? `
                            <span class="ml-2 bg-slate-200 text-slate-700 px-2 py-0.5 rounded text-xs">
                                Semestre: ${estudiante.semestre_actual}
                            </span>
                            ` : ''}
                        </div>
                    </div>
                    
                    <!-- Información del horario -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <!-- Horario preferido -->
                        <div class="flex items-center">
                            <i class="fas fa-clock text-slate-400 mr-2 w-4"></i>
                            <span class="font-medium">${estudiante.horario_preferido || 'No especificado'}</span>
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
document.addEventListener('click', function(e) {
   
    const modalEstudiantes = document.getElementById('modalEstudiantes');

    
    if (modalEstudiantes && e.target === modalEstudiantes) {
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

// Validación del formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCrearGrupoRapido');
    if (form) {
        form.addEventListener('submit', function(e) {
            const letra = document.querySelector('select[name="letra_grupo"]').value;
            const horario = document.querySelector('select[name="horario_id"]').value;
            
            if (!letra || !horario) {
                e.preventDefault();
                alert('Por favor, completa todos los campos requeridos.');
            }
        });
    }
});
</script>
@endpush
@endsection