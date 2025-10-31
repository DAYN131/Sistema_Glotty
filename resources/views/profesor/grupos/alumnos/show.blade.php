<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificaciones - Glotty</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sidebar: '#2c3e50',
                        primary: {
                            light: '#3498db',
                            DEFAULT: '#2980b9',
                            dark: '#1f6ca6'
                        },
                        secondary: {
                            light: '#f8fafc',
                            DEFAULT: '#f1f5f9',
                            dark: '#e2e8f0'
                        },
                        danger: {
                            DEFAULT: '#dc2626',
                            dark: '#b91c1c'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-header {
            background: linear-gradient(135deg, #2980b9 0%, #1a5276 100%);
        }
        
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            opacity: 1;
        }
        
        .grade-input {
            transition: all 0.2s ease;
        }
        
        .grade-input:focus {
            transform: scale(1.05);
        }
        
        .grade-cell {
            position: relative;
        }
        
        .grade-indicator {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            transition: all 0.3s ease;
        }
        
        .table-hover tr:hover {
            background-color: rgba(41, 128, 185, 0.05);
        }
        
        .sticky-header th {
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .flash-message {
            animation: fadeOut 5s forwards;
        }
        
        @keyframes fadeOut {
            0% { opacity: 1; }
            90% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Mostrar mensajes de sesión -->
        @if(session('success'))
        <div class="flash-message bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            <div class="flex justify-between items-center">
                <div>
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
                <button onclick="this.parentElement.parentElement.style.display='none'" class="text-green-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        @endif
        
        @if(session('error'))
        <div class="flash-message bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <div class="flex justify-between items-center">
                <div>
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
                <button onclick="this.parentElement.parentElement.style.display='none'" class="text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        @endif

        <!-- Encabezado con información del grupo -->
        <div class="gradient-header rounded-xl shadow-md p-6 mb-8 text-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center">
                        <div class="p-3 bg-white bg-opacity-20 rounded-full mr-4">
                            <i class="fas fa-chalkboard-teacher text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Grupo {{ $grupo->nivel_ingles }}-{{ $grupo->letra_grupo }}</h1>
                            <p class="mt-1 opacity-90">{{ $grupo->periodo }} | {{ $grupo->horario->descripcion }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profesor.grupos.index') }}" 
                       class="bg-white text-primary hover:bg-gray-100 px-4 py-2 rounded-lg flex items-center transition-all shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver a Grupos
                    </a>
                </div>
            </div>
            
            <!-- Información adicional del grupo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="bg-white bg-opacity-10 rounded-lg p-4 flex items-center">
                    <div class="p-2 bg-white bg-opacity-20 rounded-full mr-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <p class="text-xs opacity-80">Total de Alumnos</p>
                        <p class="font-semibold">{{ count($grupo->alumnos) }}</p>
                    </div>
                </div>
                
                <div class="bg-white bg-opacity-10 rounded-lg p-4 flex items-center">
                    <div class="p-2 bg-white bg-opacity-20 rounded-full mr-3">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div>
                        <p class="text-xs opacity-80">Aula</p>
                        <p class="font-semibold">{{ $grupo->aula->edificio ?? 'A' }}-{{ $grupo->aula->numero_aula ?? '101' }}</p>
                    </div>
                </div>
                
                <div class="bg-white bg-opacity-10 rounded-lg p-4 flex items-center">
                    <div class="p-2 bg-white bg-opacity-20 rounded-full mr-3">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <p class="text-xs opacity-80">Días</p>
                        <p class="font-semibold">{{ $grupo->horario->dias_semana ?? 'Lunes y Miércoles' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panel de información y ayuda -->
        <div class="bg-white rounded-xl shadow-sm p-5 mb-8 border-l-4 border-primary">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-primary text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Registro de Calificaciones</h3>
                    <p class="text-gray-600 mt-1">Ingrese las calificaciones para cada parcial. La calificación final se calculará automáticamente como el promedio de ambos parciales.</p>
                    <div class="mt-3 flex flex-wrap gap-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                            <span class="text-sm text-gray-600">≥ 70: Aprobado</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
                            <span class="text-sm text-gray-600">60-69: En riesgo</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                            <span class="text-sm text-gray-600">< 60: Reprobado</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-gray-500 mr-2"></div>
                            <span class="text-sm text-gray-600">Sin calificar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de calificaciones -->
        <form action="{{ route('profesor.calificaciones.update', $grupo->id) }}" method="POST" id="calificacionesForm">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h2 class="font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-graduation-cap mr-2 text-primary"></i>
                        Calificaciones de Alumnos
                    </h2>
                    <div class="flex items-center space-x-2">
                        <button type="button" id="calcularPromedios" class="text-primary hover:text-primary-dark p-2">
                            <i class="fas fa-calculator mr-1"></i> Calcular Promedios
                        </button>
                        <button type="button" class="text-gray-500 hover:text-gray-700 p-2" onclick="window.print()">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 table-hover">
                        <thead class="bg-gray-50 sticky-header">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <span>Alumno</span>
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-center">
                                        <span>1er Parcial</span>
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-center">
                                        <span>2do Parcial</span>
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-center">
                                        <span>Final</span>
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-center">
                                        <span>Estado</span>
                                    </div>
                                </th>

                                <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-center">
                                        <span>Guardado</span>
                                    </div>
                                </th>

                             
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($inscripciones as $inscripcion)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-light flex items-center justify-center text-white">
                                            {{ substr($inscripcion->alumno->nombre_alumno, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $inscripcion->alumno->nombre_alumno }}</div>
                                            <div class="text-xs text-gray-500">{{ $inscripcion->alumno->no_control }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center grade-cell">
                                    <div class="flex flex-col items-center">
                                        <input type="number" 
                                               name="calificaciones[{{ $inscripcion->id }}][parcial_1]"
                                               value="{{ $inscripcion->calificacion_parcial_1 ?? '' }}"
                                               min="0" max="100" step="1" 
                                               class="w-20 text-center border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary grade-input"
                                               data-inscripcion="{{ $inscripcion->id }}"
                                               onchange="updateGradeIndicator(this)">
                                        <button type="button" 
                                                onclick="cancelGrade(this, '{{ $inscripcion->id }}', 1)"
                                                class="mt-1 text-xs text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-ban mr-1"></i> Cancelar
                                        </button>
                                        <div class="grade-indicator w-full" id="indicator-1-{{ $inscripcion->id }}"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center grade-cell">
                                    <div class="flex flex-col items-center">
                                        <input type="number" 
                                               name="calificaciones[{{ $inscripcion->id }}][parcial_2]"
                                               value="{{ $inscripcion->calificacion_parcial_2 ?? '' }}"
                                               min="0" max="100" step="1" 
                                               class="w-20 text-center border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary grade-input"
                                               data-inscripcion="{{ $inscripcion->id }}"
                                               onchange="updateGradeIndicator(this)"
                                               @if(!$inscripcion->calificacion_parcial_1) disabled @endif>
                                        <button type="button" 
                                                onclick="cancelGrade(this, '{{ $inscripcion->id }}', 2)"
                                                class="mt-1 text-xs text-gray-500 hover:text-gray-700"
                                                @if(!$inscripcion->calificacion_parcial_1) disabled @endif>
                                            <i class="fas fa-ban mr-1"></i> Cancelar
                                        </button>
                                        <div class="grade-indicator w-full" id="indicator-2-{{ $inscripcion->id }}"></div>
                                    </div>
                                </td>

                                 <td class="px-6 py-4 whitespace-nowrap text-center grade-cell">
                                    <div class="flex flex-col items-center">
                                        <!-- Mostrar calificación actual -->
                                        <div class="text-sm font-medium text-gray-900" id="current-final-{{ $inscripcion->id }}">
                                            {{ $inscripcion->calificacion_final ?? '--' }}
                                        </div>
                                        <div class="grade-indicator w-full" id="current-indicator-final-{{ $inscripcion->id }}"></div>
                                        
                                        <!-- Mostrar vista previa de cambios -->
                                        <div class="mt-2 text-sm font-medium" id="preview-final-{{ $inscripcion->id }}" style="display: none;">
                                            --
                                        </div>
                                        <div class="grade-indicator w-full" id="preview-indicator-final-{{ $inscripcion->id }}"></div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                                id="status-{{ $inscripcion->id }}">
                                                {{ $inscripcion->calificacion_final === null ? 'Sin calificar' : 
                                                ($inscripcion->calificacion_final >= 70 ? 'Aprobado' : 
                                                ($inscripcion->calificacion_final >= 60 ? 'En riesgo' : 'Reprobado')) }}
                                            </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                                        id="saved-status-{{ $inscripcion->id }}">
                                        <i class="fas fa-check-circle mr-1"></i> Guardado
                                    </span>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="mt-8 flex justify-end space-x-4">
                <button type="button" onclick="window.location.href='{{ route('profesor.grupos.index') }}'" 
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </button>
                
                <button type="submit" id="submitBtn"
                        class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
    
    <script>
    // Objeto para almacenar cambios temporales
    const pendingChanges = {};
    
    // Función para determinar el color según la calificación
    function getColorForGrade(grade) {
        if (!grade || grade === '--') return 'transparent';
        grade = parseFloat(grade);
        if (grade >= 70) return '#10b981'; // Verde
        if (grade >= 60) return '#f59e0b'; // Amarillo
        return '#ef4444'; // Rojo
    }

    // Función para determinar el texto de estado académico
    function getStatusText(grade) {
        if (!grade || grade === '--' || grade === null) return 'Sin calificar';
        grade = parseFloat(grade);
        if (grade >= 70) return 'Aprobado';
        if (grade >= 60) return 'En riesgo';
        return 'Reprobado';
    }

    // Función para actualizar el estado de guardado
    function updateSavedStatus(inscripcionId, hasChanges) {
        const savedStatus = document.getElementById(`saved-status-${inscripcionId}`);
        
        if (hasChanges) {
            savedStatus.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> Cambios pendientes';
            savedStatus.classList.remove('bg-green-100', 'text-green-800');
            savedStatus.classList.add('bg-yellow-100', 'text-yellow-800');
        } else {
            savedStatus.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Guardado';
            savedStatus.classList.remove('bg-yellow-100', 'text-yellow-800');
            savedStatus.classList.add('bg-green-100', 'text-green-800');
        }
    }

    // Función para verificar si hay cambios reales
    function hasRealChanges(inscripcionId) {
        if (!pendingChanges[inscripcionId]) return false;
        
        const parcial1Input = document.querySelector(`input[name="calificaciones[${inscripcionId}][parcial_1]"]`);
        const parcial2Input = document.querySelector(`input[name="calificaciones[${inscripcionId}][parcial_2]"]`);
        
        // Comparar con los valores originales
        const originalParcial1 = parcial1Input.defaultValue === '' ? null : parseFloat(parcial1Input.defaultValue);
        const originalParcial2 = parcial2Input.defaultValue === '' ? null : parseFloat(parcial2Input.defaultValue);
        
        const currentParcial1 = pendingChanges[inscripcionId].parcial_1 !== undefined ? 
                              pendingChanges[inscripcionId].parcial_1 : 
                              (parcial1Input.value === '' ? null : parseFloat(parcial1Input.value));
        
        const currentParcial2 = pendingChanges[inscripcionId].parcial_2 !== undefined ? 
                              pendingChanges[inscripcionId].parcial_2 : 
                              (parcial2Input.value === '' ? null : parseFloat(parcial2Input.value));
        
        return (currentParcial1 !== originalParcial1) || (currentParcial2 !== originalParcial2);
    }

    // Función para actualizar el indicador de calificación
    function updateGradeIndicator(input) {
        const inscripcionId = input.dataset.inscripcion;
        const parcial = input.name.includes('parcial_1') ? 1 : 2;
        const currentValue = input.value === '' ? null : parseFloat(input.value);
        const originalValue = input.defaultValue === '' ? null : parseFloat(input.defaultValue);
        
        // Actualizar indicador visual
        const indicator = document.getElementById(`indicator-${parcial}-${inscripcionId}`);
        indicator.style.backgroundColor = getColorForGrade(input.value);
        
        // Habilitar/deshabilitar parcial 2 según parcial 1
        if (parcial === 1) {
            const parcial2Input = document.querySelector(`input[name="calificaciones[${inscripcionId}][parcial_2]"]`);
            const cancelParcial2Btn = document.querySelector(`button[onclick="cancelGrade(this, '${inscripcionId}', 2)"]`);
            
            if (input.value === '') {
                parcial2Input.disabled = true;
                cancelParcial2Btn.disabled = true;
                parcial2Input.value = '';
                document.getElementById(`indicator-2-${inscripcionId}`).style.backgroundColor = 'transparent';
            } else {
                parcial2Input.disabled = false;
                cancelParcial2Btn.disabled = false;
            }
        }
        
        // Actualizar cambios pendientes solo si hay diferencia con el valor original
        if (currentValue !== originalValue) {
            if (!pendingChanges[inscripcionId]) {
                pendingChanges[inscripcionId] = {};
            }
            pendingChanges[inscripcionId][`parcial_${parcial}`] = currentValue;
        } else {
            // Si vuelve al valor original, eliminar de cambios pendientes
            if (pendingChanges[inscripcionId]) {
                delete pendingChanges[inscripcionId][`parcial_${parcial}`];
                
                // Si no quedan cambios, eliminar el objeto
                if (Object.keys(pendingChanges[inscripcionId]).length === 0) {
                    delete pendingChanges[inscripcionId];
                }
            }
        }
        
        // Actualizar estado de guardado
        updateSavedStatus(inscripcionId, hasRealChanges(inscripcionId));
        
        // Calcular promedio
        calculateFinalGrade(inscripcionId);
    }

    // Función para calcular la calificación final
    function calculateFinalGrade(inscripcionId) {
        const changes = pendingChanges[inscripcionId] || {};
        const parcial1Input = document.querySelector(`input[name="calificaciones[${inscripcionId}][parcial_1]"]`);
        const parcial2Input = document.querySelector(`input[name="calificaciones[${inscripcionId}][parcial_2]"]`);
        
        // Obtener valores actuales (considerando cambios pendientes)
        const parcial1 = changes.parcial_1 !== undefined ? 
                        changes.parcial_1 : 
                        (parcial1Input.value === '' ? null : parseFloat(parcial1Input.value));
        
        const parcial2 = changes.parcial_2 !== undefined ? 
                        changes.parcial_2 : 
                        (parcial2Input.value === '' ? null : parseFloat(parcial2Input.value));
        
        const finalElement = document.getElementById(`final-${inscripcionId}`);
        const finalIndicator = document.getElementById(`indicator-final-${inscripcionId}`);
        const statusElement = document.getElementById(`status-${inscripcionId}`);
        
        // Calcular solo si al menos un parcial tiene valor
        if (parcial1 !== null || parcial2 !== null) {
            let final;
            if (parcial1 !== null && parcial2 !== null) {
                final = (parcial1 + parcial2) / 2;
            } else if (parcial1 !== null) {
                final = parcial1;
            } else {
                final = parcial2;
            }
            
            finalElement.textContent = final.toFixed(1);
            finalIndicator.style.backgroundColor = getColorForGrade(final);
            
            // Actualizar estado académico
            const statusText = getStatusText(final);
            statusElement.textContent = statusText;
            
            // Actualizar clases de estado
            statusElement.className = 'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full';
            if (statusText === 'Aprobado') {
                statusElement.classList.add('bg-green-100', 'text-green-800');
            } else if (statusText === 'En riesgo') {
                statusElement.classList.add('bg-yellow-100', 'text-yellow-800');
            } else if (statusText === 'Reprobado') {
                statusElement.classList.add('bg-red-100', 'text-red-800');
            } else {
                statusElement.classList.add('bg-gray-100', 'text-gray-800');
            }
        } else {
            finalElement.textContent = '--';
            finalIndicator.style.backgroundColor = 'transparent';
            statusElement.textContent = 'Sin calificar';
            statusElement.className = 'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800';
        }
    }

    // Función para cancelar una calificación
    function cancelGrade(button, inscripcionId, parcial) {
        const input = document.querySelector(`input[name="calificaciones[${inscripcionId}][parcial_${parcial}]"]`);
        input.value = '';
        
        // Actualizar indicador
        const indicator = document.getElementById(`indicator-${parcial}-${inscripcionId}`);
        indicator.style.backgroundColor = 'transparent';
        
        // Registrar cambio pendiente solo si difiere del original
        const originalValue = input.defaultValue === '' ? null : parseFloat(input.defaultValue);
        if (null !== originalValue) {
            if (!pendingChanges[inscripcionId]) {
                pendingChanges[inscripcionId] = {};
            }
            pendingChanges[inscripcionId][`parcial_${parcial}`] = null;
        } else {
            // Si el valor original ya era null, eliminar de cambios pendientes
            if (pendingChanges[inscripcionId]) {
                delete pendingChanges[inscripcionId][`parcial_${parcial}`];
                
                // Si no quedan cambios, eliminar el objeto
                if (Object.keys(pendingChanges[inscripcionId]).length === 0) {
                    delete pendingChanges[inscripcionId];
                }
            }
        }
        
        // Recalcular final
        calculateFinalGrade(inscripcionId);
        
        // Actualizar estado de guardado
        updateSavedStatus(inscripcionId, hasRealChanges(inscripcionId));
        
        // Mostrar mensaje de confirmación
        button.innerHTML = '<i class="fas fa-check mr-1"></i> Cancelado';
        button.classList.remove('text-gray-500', 'hover:text-gray-700');
        button.classList.add('text-green-500');
        
        setTimeout(() => {
            button.innerHTML = '<i class="fas fa-ban mr-1"></i> Cancelar';
            button.classList.remove('text-green-500');
            button.classList.add('text-gray-500', 'hover:text-gray-700');
        }, 2000);
    }
    
    // Calcular todos los promedios
    document.getElementById('calcularPromedios').addEventListener('click', function() {
        const inputs = document.querySelectorAll('[data-inscripcion]');
        const inscripcionIds = Array.from(new Set(Array.from(inputs).map(input => input.dataset.inscripcion)));
        
        inscripcionIds.forEach(id => {
            calculateFinalGrade(id);
        });
        
        // Mostrar notificación
        showNotification('Promedios calculados correctamente', 'success');
    });
    
    // Confirmación al enviar el formulario
    document.getElementById('calificacionesForm').addEventListener('submit', function(e) {
        // Filtrar solo los cambios reales
        const realChanges = {};
        Object.keys(pendingChanges).forEach(id => {
            if (hasRealChanges(id)) {
                realChanges[id] = pendingChanges[id];
            }
        });
        
        if (Object.keys(realChanges).length === 0) {
            e.preventDefault();
            showNotification('No hay cambios para guardar', 'warning');
            return;
        }
        
        // Mostrar confirmación
        if (!confirm('¿Está seguro de que desea guardar los cambios?')) {
            e.preventDefault();
        } else {
            // Mostrar loader
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...';
            
            // Actualizar estados a "Guardado" después de enviar
            setTimeout(() => {
                const inputs = document.querySelectorAll('[data-inscripcion]');
                const inscripcionIds = Array.from(new Set(Array.from(inputs).map(input => input.dataset.inscripcion)));
                
                inscripcionIds.forEach(id => {
                    updateSavedStatus(id, false);
                });
            }, 1000);
        }
    });

    // Función para mostrar notificaciones
    function showNotification(message, type = 'success') {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        
        const notification = document.createElement('div');
        notification.className = `fixed bottom-4 right-4 ${colors[type]} text-white px-4 py-2 rounded-lg shadow-lg flex items-center animate-fade-in`;
        notification.innerHTML = `<i class="fas ${icons[type]} mr-2"></i> ${message}`;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('animate-fade-out');
            setTimeout(() => {
                notification.remove();
            }, 500);
        }, 3000);
    }

    // Inicializar indicadores al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.grade-input');
        
        // Inicializar indicadores de calificación
        inputs.forEach(input => {
            if (input.value) {
                const indicator = document.getElementById(`indicator-${input.name.includes('parcial_1') ? 1 : 2}-${input.dataset.inscripcion}`);
                indicator.style.backgroundColor = getColorForGrade(input.value);
            }
            
            // Guardar el valor original como defaultValue
            input.defaultValue = input.value;
        });
        
        // Inicializar estados de guardado
        const inscripcionIds = Array.from(new Set(Array.from(inputs).map(input => input.dataset.inscripcion)));
        inscripcionIds.forEach(id => {
            updateSavedStatus(id, false);
            
            // Inicializar indicadores de calificación final
            const finalGrade = document.getElementById(`final-${id}`).textContent;
            document.getElementById(`indicator-final-${id}`).style.backgroundColor = getColorForGrade(finalGrade);
        });
    });
</script>
</body>
</html>