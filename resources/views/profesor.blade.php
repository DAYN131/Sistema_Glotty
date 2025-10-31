<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Profesor - Glotty</title>
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
                <i class="fas fa-chalkboard-teacher text-white"></i>
            </div>
            <div class="flex flex-col items-center space-y-8">
                <a href="#" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-home text-xl"></i>
                </a>
                <a href="#" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-users text-xl"></i>
                </a>
                <a href="#" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-book text-xl"></i>
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
                </div>
                <div class="ml-auto flex items-center">
                    <span class="text-gray-700 font-medium"></span>
                </div>
            </header>
            
            <!-- Content Area -->
            <div class="flex-1 p-6 overflow-auto">
                
                
                 <!-- Información del Profesor -->
                 <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="col-span-2">
                            <div class="text-sm text-gray-500"></div>
                            
                         
                           <h2 class="text-xl font-bold text-gray-800 mb-1"> Bienvenido {{ $nombre_completo}} </h2>
                        
                            <div class="text-sm text-gray-500">RFC: {{ $rfc_profesor }}</div>
                   
                            
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                
                            </div>
                        </div>
                       
                    </div>
                </div>
                

                <!-- Main Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Mis Grupos -->
                    <div class="bg-white p-5 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-users text-primary mr-3 text-xl"></i>
                            <h3 class="text-lg font-medium text-gray-700">Mis Grupos</h3>
                        </div>
                        <p class="text-gray-600 mb-4">Visualiza todos los grupos asignados a tu carga académica.</p>
                        <a href="" class="block w-full bg-primary hover:bg-primary-dark text-white py-2 rounded-md transition-colors text-center">
                            Acceder <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    
                    <!-- Asignar Calificaciones -->
                    <div class="bg-white p-5 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-edit text-primary mr-3 text-xl"></i>
                            <h3 class="text-lg font-medium text-gray-700">Asignar Calificaciones</h3>
                        </div>
                        <p class="text-gray-600 mb-4">Registra y actualiza las calificaciones de tus alumnos.</p>
                        <a href="#" class="block w-full bg-primary hover:bg-primary-dark text-white py-2 rounded-md transition-colors text-center">
                            Acceder <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    
                    <!-- Lista de Alumnos -->
                    <div class="bg-white p-5 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-user-graduate text-primary mr-3 text-xl"></i>
                            <h3 class="text-lg font-medium text-gray-700">Lista de Alumnos</h3>
                        </div>
                        <p class="text-gray-600 mb-4">Consulta la información detallada de tus estudiantes.</p>
                        <a href="#" class="block w-full bg-primary hover:bg-primary-dark text-white py-2 rounded-md transition-colors text-center">
                            Acceder <i class="fas fa-arrow-right ml-1"></i>
                        </a>
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