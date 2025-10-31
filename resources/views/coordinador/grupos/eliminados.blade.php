



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupos Eliminados - Glotty</title>
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
                    <a href="{{ route('coordinador.grupos.index') }}" class="text-gray-600 hover:text-gray-800">
                        <span>Gestión de Grupos</span>
                    </a>
                    <span class="text-gray-400 mx-2">/</span>
                    <span class="text-gray-600">Grupos Eliminados</span>
                </div>
                <div class="ml-auto flex items-center">
                    <span class="text-gray-700 font-medium">COORDINADOR ACADÉMICO</span>
                </div>
            </header>
            
            <!-- Content Area -->
            <div class="flex-1 p-6 overflow-auto">
                <!-- Header with buttons -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Grupos Eliminados</h1>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('coordinador.grupos.index') }}" class="bg-primary hover:bg-primary-dark text-white py-2 px-4 rounded-md transition-colors text-center flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i> Volver a Grupos Activos
                        </a>
                    </div>
                </div>
                
                <!-- Table -->
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eliminado el</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($grupos as $grupo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $grupo->id }}
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




                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $grupo->deleted_at->format('d/m/Y H:i') }}
                                    </td>



                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 space-x-2">
                                        <form action="{{ route('coordinador.grupos.restore', $grupo->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-800" title="Restaurar grupo">
                                                <i class="fas fa-trash-restore"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('coordinador.grupos.forceDelete', $grupo->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" 
                                                    onclick="return confirm('¿Estás seguro de eliminar permanentemente este grupo? Esta acción no se puede deshacer.')"
                                                    title="Eliminar permanentemente">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No hay grupos eliminados
                                    </td>
                                </tr>
                                @endforelse
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
                                
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Alert -->
                @if(session('success'))
                <div class="mt-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm" role="alert">
                    <p class="font-medium">¡Éxito!</p>
                    <p>{{ session('success') }}</p>
                </div>
                @endif

                @if(session('error'))
                <div class="mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm" role="alert">
                    <p class="font-medium">¡Error!</p>
                    <p>{{ session('error') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-white text-center py-3 text-gray-600 border-t">
        &copy; {{ date('Y') }} Glotty - Sistema de Gestión de Idiomas
    </footer>

    <script>
        // Confirmación antes de restaurar
        document.querySelectorAll('form[action*="restore"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                if(!confirm('¿Estás seguro de restaurar este grupo?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>