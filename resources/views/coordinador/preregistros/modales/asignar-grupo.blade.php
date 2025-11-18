{{-- resources/views/coordinador/preregistros/modales/asignar-grupo.blade.php --}}
<div id="modalAsignarGrupo" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold text-slate-800 mb-4">Asignar Grupo</h3>
        <form id="formAsignarGrupo" method="POST">
            @csrf
            <input type="hidden" name="preregistro_id" id="preregistroId">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Seleccionar Grupo *</label>
                    <select name="grupo_id" id="selectGrupo" 
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Selecciona un grupo</option>
                        @foreach($gruposDisponibles as $grupo)
                            @if($grupo->estado === 'activo' || $grupo->estado === 'planificado')
                            <option value="{{ $grupo->id }}" 
                                    data-nivel="{{ $grupo->nivel_ingles }}"
                                    data-capacidad="{{ $grupo->capacidad_maxima }}"
                                    data-inscritos="{{ $grupo->estudiantes_inscritos }}"
                                    data-disponible="{{ $grupo->capacidad_maxima - $grupo->estudiantes_inscritos }}">
                                {{ $grupo->nombre_completo }} - 
                                {{ $grupo->horario->nombre ?? 'Sin horario' }} - 
                                ({{ $grupo->estudiantes_inscritos }}/{{ $grupo->capacidad_maxima }})
                            </option>
                            @endif
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500" id="infoGrupo"></p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">Información del Estudiante</h4>
                    <div class="text-sm text-blue-700 space-y-1">
                        <p id="infoEstudianteNombre"><strong>Nombre:</strong> <span></span></p>
                        <p id="infoEstudianteNivel"><strong>Nivel solicitado:</strong> <span></span></p>
                        <p id="infoEstudianteHorario"><strong>Horario preferido:</strong> <span></span></p>
                        <p id="infoEstudiantePago"><strong>Estado de pago:</strong> <span></span></p>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-3 mt-6">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition-colors flex items-center justify-center">
                    <i class="fas fa-users mr-2"></i>
                    Asignar Grupo
                </button>
                <button type="button" 
                        onclick="cerrarModalAsignar()" 
                        class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 py-2 rounded-lg transition-colors">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>