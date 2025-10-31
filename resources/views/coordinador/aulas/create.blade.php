<!DOCTYPE html>
<html lang="es">
<head>*
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Aula - Glotty</title>
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
                        <span>Gestión de Grupos</span>
                    </a>
                    <span class="text-gray-400 mx-2">/</span>
                    <a href="#" class="text-gray-600 hover:text-gray-800">
                        <span>Listado de Aulas</span>
                    </a>
                    <span class="text-gray-400 mx-2">/</span>
                    <span class="text-gray-600">Registrar Aula</span>
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
                        <h2 class="text-2xl font-bold text-gray-800 text-center">Registrar Aula</h2>
                    </div>
                    
   
                    
                    <!-- Form -->
                    <div class="p-6 pt-2">
                    <form action="{{ route('coordinador.aulas.guardar') }}" method="POST">
                    @csrf <!-- Esto es esencial -->
                            
                            <!-- Edificio -->
                            <div class="mb-4">
                                <label for="edificio" class="block text-sm font-medium text-gray-700 mb-1">Edificio</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-building text-gray-400"></i>
                                    </div>
                                    <input type="text" id="edificio" name="edificio" required placeholder="Ej: A, B, C"
                                           class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border"
                                           value="">
                                </div>
                             
                                @error('edificio')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                          
                            </div>
                            
                            <!-- Número de Aula -->
                            <div class="mb-4">
                                <label for="numero_aula" class="block text-sm font-medium text-gray-700 mb-1">Número de Aula</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-door-open text-gray-400"></i>
                                    </div>
                                    <input type="number" id="numero_aula" name="numero_aula" required placeholder="Ej: 101, 202"
                                           class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border"
                                           value="">
                                </div>
                              
                             
                                @error('numero_aula')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                          
                            </div>
                            
                            <!-- Capacidad -->
                            <div class="mb-4">
                                <label for="capacidad" class="block text-sm font-medium text-gray-700 mb-1">Capacidad</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-users text-gray-400"></i>
                                    </div>
                                    <input type="number" id="capacidad" name="capacidad" required min="1" max="100" placeholder="Ej: 30"
                                           class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border"
                                           value="">
                                </div>
                           
                                @error('capacidad')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            
                            </div>
                            
                            <!-- Tipo de Aula -->
                            <div class="mb-6">
                                <label for="tipo_aula" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Aula</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-chalkboard text-gray-400"></i>
                                    </div>
                                    <select id="tipo_aula" name="tipo_aula" required
                                            class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border">
                                        <option value="">Seleccione un tipo</option>
                                        <option value="regular">Regular</option>
                                        <option value="laboratorio">Laboratorio</option>
                                        <option value="multimedia">Multimedia</option>
                                        <option value="conferencia">Sala de Conferencias</option>
                                    </select>
                                </div>
                           
                                @error('tipo_aula')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                          
                            </div>
                            
                            <!-- Botones de acción -->
                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-between">
                                <a href="{{ route('coordinador.aulas.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md transition-colors text-center">
                                    <i class="fas fa-arrow-left mr-2"></i> Volver
                                </a>
                                <button type="submit" href="" class="bg-primary hover:bg-primary-dark text-white py-2 px-4 rounded-md transition-colors text-center">
                                    <i class="fas fa-save mr-2"></i> Guardar Aula
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
</body>
</html>