<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Horario - Glotty</title>
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
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <div class="flex flex-1">
        <!-- Sidebar -->
        <div class="w-16 bg-sidebar text-white min-h-screen flex flex-col items-center py-6 space-y-8">
            <div class="rounded-full bg-blue-400 w-10 h-10 flex items-center justify-center">
                <i class="fas fa-user-tie text-white"></i>
            </div>
            <div class="flex flex-col items-center space-y-8">
                <a href="#" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-home text-xl"></i>
                </a>
                <a href="#" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-user-graduate text-xl"></i>
                </a>
                <a href="#" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                </a>
                <a href="#" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </a>
            </div>
            <div class="mt-auto">
                <a href="#" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-sign-out-alt text-xl"></i>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Header -->
            <header class="bg-white shadow-sm h-16 flex items-center px-6 border-b">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-bars text-gray-500"></i>
                    <a href="#" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-home text-sm"></i>
                        <span class="ml-1">Inicio</span>
                    </a>
                    <span class="text-gray-400 mx-2">/</span>
                    <a href="#" class="text-gray-600 hover:text-gray-800">
                        <span>Gestión de Horarios</span>
                    </a>
                    <span class="text-gray-400 mx-2">/</span>
                    <a href="#" class="text-gray-600 hover:text-gray-800">
                        <span>Listado de Horarios</span>
                    </a>
                    <span class="text-gray-400 mx-2">/</span>
                    <span class="text-gray-600">Editar Horario</span>
                </div>
                <div class="ml-auto flex items-center">
                    <span class="text-gray-700 font-medium">COORDINADOR ACADÉMICO</span>
                </div>
            </header>
            
            <!-- Content Area -->
            <div class="flex-1 p-6 overflow-auto">
                <!-- Form Card -->
                <div class="max-w-md mx-auto bg-white rounded-lg shadow-sm">
                    <!-- Header -->
                    <div class="p-6 border-b">
                        <h2 class="text-2xl font-bold text-gray-800 text-center">Editar Horario</h2>
                    </div>
                    
                    <!-- Form -->
                    <div class="p-6 pt-2">
                    <form action="{{ route('coordinador.horarios.actualizar', $horario->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    
                    <!-- Nombre del Horario -->
                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Horario</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-tag text-gray-400"></i>
                            </div>
                            <input type="text" id="nombre" name="nombre" required 
                                class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border"
                                value="{{ old('nombre', $horario->nombre) }}">
                        </div>
                        @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Tipo de Horario -->
                    <div class="mb-4">
                        <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Horario</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-week text-gray-400"></i>
                            </div>
                            <select id="tipo" name="tipo" required
                                    class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border">
                                <option value="">Seleccione un tipo</option>
                                <option value="semanal" {{ old('tipo') == 'semanal' ? 'selected' : '' }}>Semanal</option>
                                <option value="sabado" {{ old('tipo') == 'sabado' ? 'selected' : '' }}>Sábado</option>
                            </select>
                        </div>
                        @error('tipo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Días de la semana (solo para tipo semanal) -->
                    <div class="mb-4" id="dias_container">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Días de clase</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(['lunes', 'martes', 'miercoles', 'jueves', 'viernes'] as $dia)
                            <div class="flex items-center">
                                <input type="checkbox" id="{{ $dia }}" name="dias[]" value="{{ $dia }}" 
                                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                                    {{ is_array(old('dias')) && in_array($dia, old('dias')) ? 'checked' : '' }}>
                                <label for="{{ $dia }}" class="ml-2 block text-sm text-gray-700">
                                    {{ ucfirst($dia) }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('dias')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Horario -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-1">Hora inicio</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-clock text-gray-400"></i>
                                </div>
                                <input type="time" id="hora_inicio" name="hora_inicio" required
                                    class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border"
                                    value="{{ old('hora_inicio', '08:00') }}">
                            </div>
                            @error('hora_inicio')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-1">Hora fin</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-clock text-gray-400"></i>
                                </div>
                                <input type="time" id="hora_fin" name="hora_fin" required
                                    class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border"
                                    value="{{ old('hora_fin', '14:00') }}">
                            </div>
                            @error('hora_fin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Vigencia -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="inicio_vigencia" class="block text-sm font-medium text-gray-700 mb-1">Inicio vigencia</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar-day text-gray-400"></i>
                                </div>
                                <input type="date" id="inicio_vigencia" name="inicio_vigencia" required
                                    class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border"
                                    value="{{ old('inicio_vigencia') }}">
                            </div>
                            @error('inicio_vigencia')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="fin_vigencia" class="block text-sm font-medium text-gray-700 mb-1">Fin vigencia</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar-times text-gray-400"></i>
                                </div>
                                <input type="date" id="fin_vigencia" name="fin_vigencia" required
                                    class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border"
                                    value="{{ old('fin_vigencia') }}">
                            </div>
                            @error('fin_vigencia')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Estado -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado inicial</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input type="radio" id="activo_si" name="activo" value="1" 
                                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300"
                                    {{ old('activo', '1') == '1' ? 'checked' : '' }}>
                                <label for="activo_si" class="ml-2 block text-sm text-gray-700">Activar este horario</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="activo_no" name="activo" value="0"
                                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300"
                                    {{ old('activo', '1') == '0' ? 'checked' : '' }}>
                                <label for="activo_no" class="ml-2 block text-sm text-gray-700">Mantener inactivo</label>
                            </div>
                        </div>
                        @error('activo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Botones -->
                    <div class="flex flex-col sm:flex-row gap-3 sm:justify-between">
                        <a href="{{ route('coordinador.horarios.index') }}" 
                        class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md transition-colors text-center">
                            <i class="fas fa-arrow-left mr-2"></i> Volver
                        </a>
                        <button type="submit" 
                                class="bg-primary hover:bg-primary-dark text-white py-2 px-4 rounded-md transition-colors text-center">
                            <i class="fas fa-save mr-2"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer con copyright -->
    <footer class="bg-white text-center py-3 text-gray-600 border-t">
        &copy; Empresa Datalinker 2025
    </footer>

    <!-- Script para mostrar/ocultar días según el tipo de horario -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo');
        const diasContainer = document.getElementById('dias_container');
        
        // Mostrar/ocultar días según tipo
        function toggleDiasContainer() {
            diasContainer.style.display = tipoSelect.value === 'semanal' ? 'block' : 'none';
        }
        
        // Validar fechas al cambiar
        document.getElementById('inicio_vigencia').addEventListener('change', function() {
            const finVigencia = document.getElementById('fin_vigencia');
            if (this.value && (!finVigencia.value || finVigencia.value < this.value)) {
                finVigencia.value = this.value;
            }
        });
        
        // Inicializar
        toggleDiasContainer();
        tipoSelect.addEventListener('change', toggleDiasContainer);
    });
    </script>
</body>
</html>