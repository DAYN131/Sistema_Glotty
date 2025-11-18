{{-- resources/views/coordinador/preregistros/modales/cambiar-pago.blade.php --}}
<div id="modalCambiarPago" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold text-slate-800 mb-4">Cambiar Estado de Pago</h3>
        <form id="formCambiarPago" method="POST">
            @csrf
            <input type="hidden" name="preregistro_id" id="pagoPreregistroId">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nuevo Estado de Pago *</label>
                    <select name="pago_estado" id="selectPagoEstado" 
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Selecciona un estado</option>
                        @foreach(\App\Models\Preregistro::PAGO_ESTADOS as $estado => $descripcion)
                            <option value="{{ $estado }}">{{ $descripcion }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-3 hidden" id="infoPagoPagado">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span class="text-sm text-green-700">
                            Al marcar como pagado, el estudiante podrá ser asignado a un grupo.
                        </span>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">Información Actual</h4>
                    <div class="text-sm text-blue-700 space-y-1">
                        <p id="infoPagoEstudiante"><strong>Estudiante:</strong> <span></span></p>
                        <p id="infoPagoActual"><strong>Estado de pago actual:</strong> <span></span></p>
                        <p id="infoPagoEstado"><strong>Estado preregistro:</strong> <span></span></p>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-3 mt-6">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition-colors flex items-center justify-center">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    Actualizar Pago
                </button>
                <button type="button" 
                        onclick="cerrarModalPago()" 
                        class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 py-2 rounded-lg transition-colors">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>