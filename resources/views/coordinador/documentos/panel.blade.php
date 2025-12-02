{{-- resources/views/coordinador/documentos/panel.blade.php --}}
@extends('layouts.coordinador')
@section('title', 'Panel de Documentos')
@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center mb-2">
            <div class="p-3 bg-indigo-100 rounded-lg mr-4">
                <i class="fas fa-file-pdf text-indigo-600 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Módulo de Documentos</h2>
                <p class="text-gray-600 mt-1">Genera documentos del sistema en formato PDF</p>
            </div>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Documento 1: Lista de Grupo -->
        <div class="bg-white rounded-xl shadow-lg border-2 border-blue-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4 rounded-t-xl">
                <h5 class="text-lg font-bold flex items-center">
                    <i class="fas fa-users mr-2"></i>
                    Lista de Grupo
                </h5>
            </div>
            
            <!-- Body -->
            <div class="p-6 flex-grow">
                <p class="text-gray-600 mb-4">
                    Genera un listado de todos los estudiantes asignados a un grupo específico.
                </p>
                
                <form action="{{ route('coordinador.documentos.grupo.lista') }}" method="GET" id="form-lista-grupo">
                    <div class="mb-4">
                        <label for="grupo_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Seleccionar Grupo:
                        </label>
                        <select name="grupo_id" id="grupo_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                required>
                            <option value="">-- Seleccione un grupo --</option>
                            @foreach($grupos as $grupo)
                                <option value="{{ $grupo->id }}">
                                    {{ $grupo->nivel_ingles }} - 
                                    {{ $grupo->letra_grupo }} - 
                                    {{ $grupo->horario->nombre ?? 'Sin horario' }}
                                    ({{ $grupo->preregistros_count ?? 0 }} estudiantes)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl border-t border-gray-200">
                <div class="flex gap-2">
                    <button type="submit" form="form-lista-grupo" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>
                        Generar PDF
                    </button>
                    <button type="button" 
                            class="flex-1 bg-white hover:bg-gray-50 text-blue-600 font-semibold py-2 px-4 rounded-lg border-2 border-blue-600 transition-colors flex items-center justify-center"
                            data-toggle="modal" data-target="#previewModal" onclick="previewDocument()">
                        <i class="fas fa-eye mr-2"></i>
                        Vista Previa
                    </button>
                </div>
            </div>
        </div>

        <!-- Documento 2: Constancia Individual -->
        <div class="bg-white rounded-xl shadow-lg border-2 border-green-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-t-xl">
                <h5 class="text-lg font-bold flex items-center">
                    <i class="fas fa-user-graduate mr-2"></i>
                    Constancia Individual
                </h5>
            </div>
            
            <!-- Body -->
            <div class="p-6 flex-grow">
                <p class="text-gray-600 mb-4">
                    Genera una constancia de inscripción para un estudiante específico.
                </p>
                
                <form action="{{ route('coordinador.documentos.constancia', ':id') }}" method="GET" id="form-constancia">
                    <div class="mb-4">
                        <label for="estudiante_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Seleccionar Estudiante:
                        </label>
                        <select name="preregistro_id" id="estudiante_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" 
                                required
                                onchange="actualizarFormularioConstancia(this.value)">
                            <option value="">-- Seleccione un estudiante --</option>
                            @foreach($estudiantes as $estudiante)
                                <option value="{{ $estudiante->id }}">
                                    {{ $estudiante->usuario->name }} - 
                                    {{ $estudiante->nivel_solicitado }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl border-t border-gray-200">
                <button type="button" onclick="generarConstancia()" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                    <i class="fas fa-download mr-2"></i>
                    Generar PDF
                </button>
            </div>
        </div>

        <!-- Documento 3: Estadísticas -->
        <div class="bg-white rounded-xl shadow-lg border-2 border-cyan-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col">
            <!-- Header -->
            <div class="bg-gradient-to-r from-cyan-500 to-cyan-600 text-white px-6 py-4 rounded-t-xl">
                <h5 class="text-lg font-bold flex items-center">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Reporte de Estadísticas
                </h5>
            </div>
            
            <!-- Body -->
            <div class="p-6 flex-grow">
                <p class="text-gray-600 mb-4">
                    Genera un reporte estadístico de los preregistros del período activo.
                </p>
                
                <div class="bg-cyan-50 border border-cyan-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-cyan-600 mt-1 mr-2"></i>
                        <p class="text-sm text-cyan-800">
                            Incluye gráficos de distribución por nivel y horario.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl border-t border-gray-200">
                <div class="flex gap-2">
                    <a href="{{ route('coordinador.documentos.estadisticas') }}" 
                       class="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>
                        Generar
                    </a>
                    <a href="{{ route('coordinador.documentos.estadisticas.preview') }}" 
                       class="flex-1 bg-white hover:bg-gray-50 text-cyan-600 font-semibold py-2 px-4 rounded-lg border-2 border-cyan-600 transition-colors flex items-center justify-center"
                       target="_blank">
                        <i class="fas fa-eye mr-2"></i>
                        Previa
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Vista Previa -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-xl border-0 shadow-2xl">
                <div class="modal-header bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-t-xl">
                    <h5 class="modal-title font-bold" id="previewModalLabel">
                        <i class="fas fa-eye mr-2"></i>
                        Vista Previa - Lista de Grupo
                    </h5>
                   <button type="button" class="text-white hover:text-gray-200 transition-colors" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-3xl">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="previewFrame" style="width: 100%; height: 600px; border: none;"></iframe>
                </div>
                <div class="modal-footer bg-gray-50 rounded-b-xl">
                    <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors" data-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Card para Dashboard del Coordinador -->
{{-- 
IMPORTANTE: Agrega este código en tu vista de dashboard del coordinador:

<div class="col-xl-3 col-md-6 mb-4">
    <a href="{{ route('coordinador.documentos.panel') }}" class="text-decoration-none">
        <div class="bg-white rounded-xl shadow-lg border-2 border-amber-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-amber-100 rounded-lg">
                    <i class="fas fa-file-pdf text-amber-600 text-3xl"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-amber-600 uppercase tracking-wide mb-1">
                        Documentos
                    </p>
                    <p class="text-2xl font-bold text-gray-800">
                        PDFs
                    </p>
                </div>
            </div>
            <div class="pt-3 border-t border-gray-200">
                <div class="flex items-center justify-between text-amber-600 font-semibold">
                    <span>Generar documentos</span>
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>
</div>
--}}

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Select2 custom styling */
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border-radius: 0.5rem !important;
        border: 1px solid #d1d5db !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 16px !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
    
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Inicializar Select2
        $('#grupo_id').select2({
            placeholder: 'Seleccione un grupo',
            width: '100%',
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                }
            }
        });
        
        $('#estudiante_id').select2({
            placeholder: 'Seleccione un estudiante',
            width: '100%',
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                }
            }
        });
    });

    function previewDocument() {
    let grupoId = $('#grupo_id').val();
    if(!grupoId) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Por favor seleccione un grupo primero',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }
    
    // CORREGIR ESTA LÍNEA:
    let url = "{{ route('coordinador.documentos.grupo.lista.preview', ['grupo' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', grupoId);
    
    // O esta alternativa más simple:
    // let url = `/coordinador/documentos/grupo/${grupoId}/lista-preview`;
    
    console.log('URL de preview:', url); // ← Para depuración
    
    $('#previewFrame').attr('src', url);
    
    // También verifica si el modal se está abriendo
    $('#previewModal').modal('show'); // ← Asegúrate de que el modal se abra
}

    function actualizarFormularioConstancia(preregistroId) {
        let form = document.getElementById('form-constancia');
        form.action = form.action.replace(':id', preregistroId);
    }

    function generarConstancia() {
        let estudianteId = $('#estudiante_id').val();
        if(!estudianteId) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor seleccione un estudiante primero',
                confirmButtonColor: '#10b981'
            });
            return;
        }
        
        let url = "{{ route('coordinador.documentos.constancia', ':id') }}".replace(':id', estudianteId);
        window.location.href = url;
    }
</script>
@endpush