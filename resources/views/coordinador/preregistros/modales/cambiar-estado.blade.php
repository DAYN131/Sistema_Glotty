{{-- resources/views/coordinador/preregistros/modales/cambiar-estado.blade.php --}}
<div id="modalCambiarEstado" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold text-slate-800 mb-4">Cambiar Estado del Preregistro</h3>
        <form id="formCambiarEstado" method="POST">
            @csrf
            <input type="hidden" name="preregistro_id" id="estadoPreregistroId">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nuevo Estado *</label>
                    <select name="estado" id="selectEstado" 
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Selecciona un estado</option>
                        @foreach(\App\Models\Preregistro::ESTADOS as $estado => $descripcion)
                            <option value="{{ $estado }}">{{ $descripcion }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 hidden" id="advertenciaEstado">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                        <span class="text-sm text-yellow-700 font-medium" id="textoAdvertencia"></span>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">Información Actual</h4>
                    <div class="text-sm text-blue-700 space-y-1">
                        <p id="infoActualEstudiante"><strong>Estudiante:</strong> <span></span></p>
                        <p id="infoActualEstado"><strong>Estado actual:</strong> <span></span></p>
                        <p id="infoActualGrupo"><strong>Grupo asignado:</strong> <span></span></p>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-3 mt-6">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition-colors flex items-center justify-center">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    Cambiar Estado
                </button>
                <button type="button" 
                        onclick="cerrarModalEstado()" 
                        class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 py-2 rounded-lg transition-colors">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>