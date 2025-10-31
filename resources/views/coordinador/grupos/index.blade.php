<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Grupos - Glotty</title>
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
                    <span class="text-gray-600">Gestión de Grupos</span>
                </div>
                <div class="ml-auto flex items-center">
                    <span class="text-gray-700 font-medium">COORDINADOR ACADÉMICO</span>
                </div>
            </header>
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mx-6 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mx-6 mb-6" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Content Area -->
            <div class="flex-1 p-6 overflow-auto">

                <div class="mb-4">
                    <a href="{{ route('coordinador.grupos.eliminados') }}" 
                        class="text-gray-600 hover:text-gray-800 inline-flex items-center">
                        <i class="fas fa-trash-restore mr-2"></i> Ver grupos eliminados
                    </a>
                </div>
                <!-- Header with buttons -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Gestión de Grupos</h1>
                    <div class="flex flex-col sm:flex-row gap-3">


                        <a href="{{ route('coordinador.horarios.index') }}" class="bg-primary hover:bg-primary-dark text-white py-2 px-4 rounded-md transition-colors text-center flex items-center justify-center">
                            <i class="fas fa-building mr-2"></i> Gestionar Horarios
                        </a>


                        <a href="{{ route('coordinador.aulas.index') }}" class="bg-primary hover:bg-primary-dark text-white py-2 px-4 rounded-md transition-colors text-center flex items-center justify-center">
                            <i class="fas fa-building mr-2"></i> Gestionar Aulas
                        </a>


                        <a href="{{ route('coordinador.grupos.crear') }}" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md transition-colors text-center flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i> Agregar Grupo
                        </a>
                    </div>
                </div>
                
                <!-- Table -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Grupo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Letra</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Año</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periodo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profesor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aula</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horario</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumnos</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>



<tbody class="bg-white divide-y divide-gray-200">
    @foreach($grupos as $grupo)
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
            {{ $grupo->id ?? 'N/A' }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            Nivel {{ $grupo->nivel_ingles }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $grupo->letra_grupo }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $grupo->anio }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $grupo->periodo }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $grupo->profesor->nombre_profesor ?? 'N/A' }} {{ $grupo->profesor->apellidos_profesor ?? '' }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $grupo->aula->edificio ?? '' }}{{ $grupo->aula->numero_aula ?? 'N/A' }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $grupo->horario->nombre }}
            
            

            <div class="text-xs text-gray-500 mt-1">
            @if($grupo->horario->tipo == 'sabado')
            
            Sábados
            @else
            {{ implode(', ', array_map('ucfirst', $grupo->horario->dias)) }}
            @endif
            </div>

            <!-- Horas -->
            <div class="text-xs text-gray-500 mt-1">
                {{ $grupo->horario->hora_inicio->format('H:i') }} - 
                {{ $grupo->horario->hora_fin->format('H:i') }}
            </div>

        </td>
       
          <!-- Sidebar --> <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $grupo->alumnos_count ?? 0 }}
        </td>   <!-- Sidebar -->
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 space-x-2">
       
            <a href="{{ route ('coordinador.grupos.edit',$grupo->id)}}" class="text-blue-500 hover:text-blue-700">
                <i class="fas fa-edit"></i>
            </a>
           <form action="{{ route('coordinador.grupos.destroy',$grupo->id)}}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-700" 
                    onclick="return confirm('¿Estás seguro de eliminar este grupo?\n\n{{ $grupo->alumnos_count > 0 ? 'ADVERTENCIA: Este grupo tiene alumnos inscritos y no podrá ser eliminado' : '' }}')">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </td>
    </tr>
    @endforeach
</tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Anterior
                                </a>
                                <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Siguiente
                                </a>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Anterior</span>
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                        <a href="#" aria-current="page" class="z-10 bg-primary border-primary text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            1
                                        </a>
                                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            2
                                        </a>
                                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            3
                                        </a>
                                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Siguiente</span>
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </nav>
                                </div>
                            </div>
                        </div>
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