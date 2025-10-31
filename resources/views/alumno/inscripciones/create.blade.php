<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Inscripción</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .grupo-card {
            transition: all 0.3s ease;
        }
        .grupo-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .grupo-card.selected {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        .nivel-btn.selected {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Solicitud de Inscripción</h1>
            <a href="{{ route('alumno.dashboard') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Volver al panel
            </a>
        </div>

        <!-- Panel informativo -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-blue-500 mr-3 text-xl"></i>
                <div>
                    <p class="font-medium text-blue-800">Estás solicitando inscripción para:</p>
                    <p class="text-blue-700">Nivel recomendado: <span class="font-bold">{{ $nivelRecomendado }}</span></p>
                </div>
            </div>
        </div>

        <!-- Formulario de inscripción -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <form action="{{ route('alumno.inscripciones.store') }}" method="POST">
                @csrf
                
                <!-- Paso 1: Selección de nivel -->
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Selecciona tu nivel</h2>
                    <div class="grid grid-cols-5 gap-3" id="niveles-container">
                        @foreach(['1', '2', '3', '4', '5'] as $nivel)
                        <div class="nivel-btn text-center p-3 border rounded-lg cursor-pointer transition-colors
                            {{ $nivel == $nivelRecomendado ? 'selected border-blue-500 bg-blue-50' : 'border-gray-300 hover:bg-gray-50' }}"
                            data-nivel="{{ $nivel }}"
                            onclick="seleccionarNivel('{{ $nivel }}')">
                            <span class="block font-medium">Nivel {{ $nivel }}</span>
                            @if($nivel == $nivelRecomendado)
                            <span class="text-xs text-blue-600">(Recomendado)</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="nivel_seleccionado" id="nivel_seleccionado" value="{{ $nivelRecomendado }}">
                </div>

                <!-- Paso 2: Grupos disponibles (se carga dinámicamente) -->
<div id="grupos-container" class="p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Grupos disponibles para Nivel <span id="nivel-actual">{{ $nivelRecomendado }}</span></h2>
    
    @if(count($gruposDisponibles) > 0)
        <div class="space-y-4">
        @foreach($gruposDisponibles as $grupo)
<label class="block grupo-card border rounded-lg p-4 cursor-pointer">
    <div class="flex items-start">
        <input type="radio" name="id_grupo" value="{{ $grupo['id'] }}" class="mt-1 mr-3" required>
        <div class="flex-1">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-gray-800">{{ $grupo['nombre_grupo'] }}</h3>
                <div class="flex space-x-2">
                    <span class="text-sm px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                        {{ $grupo['periodo'] ?? 'Periodo no definido' }}
                    </span>
                    <span class="text-sm px-2 py-1 rounded-full 
                        {{ $grupo['cupo_disponible'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $grupo['cupo_disponible'] }} cupos
                    </span>
                </div>
            </div>
                        
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="flex items-start">
                                <i class="fas fa-clock text-gray-500 mt-1 mr-2"></i>
                                <div>
                                    <p class="font-medium text-gray-700">Horario</p>
                                    <p class="text-sm text-gray-600">{{ $grupo['horario'] }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <i class="fas fa-chalkboard-teacher text-gray-500 mt-1 mr-2"></i>
                                <div>
                                    <p class="font-medium text-gray-700">Profesor</p>
                                    <p class="text-sm text-gray-600">{{ $grupo['profesor'] }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <i class="fas fa-door-open text-gray-500 mt-1 mr-2"></i>
                                <div>
                                    <p class="font-medium text-gray-700">Aula</p>
                                    <p class="text-sm text-gray-600">{{ $grupo['aula'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </label>
            @endforeach
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        No hay grupos disponibles para el nivel {{ $nivelRecomendado }} en este momento.
                        Por favor contacta al departamento de lenguas extranjeras.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>

            <!-- Agregar esto antes del </form> -->
            <div class="p-6 bg-gray-50 border-t flex justify-end">
                <button type="submit" id="btn-enviar"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-md transition duration-300 flex items-center">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Solicitar Inscripción
                </button>
            </div>

            </form>
        </div>
    </div>

    <script>
        // Función para seleccionar nivel
        function seleccionarNivel(nivel) {
            // Actualizar selección visual
            document.querySelectorAll('.nivel-btn').forEach(btn => {
                btn.classList.remove('selected', 'border-blue-500', 'bg-blue-50');
                btn.classList.add('border-gray-300');
                
                if(btn.getAttribute('data-nivel') === nivel) {
                    btn.classList.add('selected', 'border-blue-500', 'bg-blue-50');
                    btn.classList.remove('border-gray-300');
                }
            });
            
            // Actualizar valor oculto
            document.getElementById('nivel_seleccionado').value = nivel;
            
            // Cargar grupos del nivel seleccionado
            cargarGrupos(nivel);
        }

        function cargarGrupos(nivel) {
    const container = document.getElementById('grupos-container');
    const btnEnviar = document.getElementById('btn-enviar');
    
    // Deshabilitar botón mientras carga
    btnEnviar.disabled = true;
    btnEnviar.classList.add('opacity-50', 'cursor-not-allowed');
    
    container.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-blue-500 text-2xl mb-2"></i>
            <p class="text-gray-600">Buscando grupos disponibles...</p>
        </div>
    `;
    
    fetch(`/alumno/inscripciones/grupos-por-nivel?nivel=${nivel}`)
        .then(response => response.json())
        .then(data => {
            container.innerHTML = data.html;
            
            // Habilitar botón solo si hay grupos
            btnEnviar.disabled = data.grupos.length === 0;
            btnEnviar.classList.toggle('opacity-50', data.grupos.length === 0);
            btnEnviar.classList.toggle('cursor-not-allowed', data.grupos.length === 0);
        })
        .catch(error => {
            container.innerHTML = `
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                Error al cargar grupos: ${error.message}
                            </p>
                        </div>
                    </div>
                </div>
            `;
            btnEnviar.disabled = true;
        });
}
    </script>
</body>
</html>