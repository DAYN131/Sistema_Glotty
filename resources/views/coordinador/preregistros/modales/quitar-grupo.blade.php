<div id="modalQuitarGrupo" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-card p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">Quitar Grupo Asignado</h3>
            <button type="button" onclick="cerrarModalQuitarGrupo()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mb-4">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-400 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm text-yellow-700 font-medium">¿Estás seguro de quitar el grupo asignado?</p>
                        <p class="text-xs text-yellow-600 mt-1">El preregistro volverá a estado "pendiente".</p>
                    </div>
                </div>
            </div>
            
            <div id="infoQuitarGrupo" class="text-sm text-slate-600">
                <!-- Información dinámica se insertará aquí -->
            </div>
        </div>
        
        <form id="formQuitarGrupo" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" id="quitarGrupoPreregistroId" name="preregistro_id">
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="cerrarModalQuitarGrupo()" 
                        class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-user-minus mr-2"></i>
                    Quitar Grupo
                </button>
            </div>
        </form>
    </div>
</div>