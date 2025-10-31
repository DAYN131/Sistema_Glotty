<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Horarios - Glotty</title>
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
                    <span class="text-gray-600">Listado de Horarios</span>
                </div>
                <div class="ml-auto flex items-center">
                    <span class="text-gray-700 font-medium">COORDINADOR ACADÉMICO</span>
                </div>
            </header>
            
            <!-- Content Area -->
            <div class="flex-1 p-6 overflow-auto">
                <!-- Main Content Card -->
                <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-sm">
                    <!-- Header with title and button -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-6 border-b">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 sm:mb-0">Listado de Horarios</h2>
                        <a href="{{ route('coordinador.horarios.crear') }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md transition-colors flex items-center justify-center sm:justify-start">
                            <i class="fas fa-plus mr-2"></i> Nuevo Horario
                        </a>
                    </div>
                    
                  
                    <!-- Table -->
                    <div class="p-6 pt-2">
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                <thead class="bg-gray-50 text-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Nombre</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Días</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Horario</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Vigencia</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Acciones</th>
                                    </tr>
                                </thead>

                                <div class="mb-4">
                                    <a href="{{ route('coordinador.horarios.eliminados') }}" 
                                    class="text-gray-600 hover:text-gray-800 inline-flex items-center">
                                        <i class="fas fa-trash-restore mr-2"></i> Ver horarios eliminados
                                    </a>
                                </div>


                                <tbody class="divide-y divide-gray-200">
                                @forelse($horarios as $horario)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $horario->nombre }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $horario->tipo == 'semanal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ ucfirst($horario->tipo) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($horario->tipo == 'semanal')
                                            @foreach($horario->dias as $dia)
                                                <span class="inline-block px-2 py-1 bg-gray-100 rounded-md text-xs mr-1 mb-1">{{ $dia }}</span>
                                            @endforeach
                                        @else
                                            <span class="inline-block px-2 py-1 bg-gray-100 rounded-md text-xs">Sábado</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <i class="far fa-clock text-gray-400 mr-1"></i>
                                        {{ $horario->hora_inicio }} - {{ $horario->hora_fin }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($horario->trashed())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <span class="h-1.5 w-1.5 rounded-full bg-red-600 mr-1.5"></span>
                                                Eliminado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $horario->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $horario->activo ? 'bg-green-600' : 'bg-red-600' }} mr-1.5"></span>
                                                {{ $horario->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex flex-col">
                                            <span><i class="fas fa-calendar-day text-gray-400 mr-1"></i> {{ $horario->inicio_vigencia }}</span>
                                            <span><i class="fas fa-calendar-times text-gray-400 mr-1"></i> {{ $horario->fin_vigencia }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        @if($horario->trashed())
                                            <form action="{{ route('coordinador.horarios.restaurar', $horario->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-undo"></i> Restaurar
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('coordinador.horarios.editar', $horario->id) }}" class="text-blue-600 hover:text-blue-800 mr-3">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <form action="{{ route('coordinador.horarios.eliminar', $horario->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" 
                                                        onclick="return confirm('¿Estás seguro de eliminar este horario?')">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-calendar-alt text-gray-300 text-5xl mb-3"></i>
                                            <p class="text-lg">No hay horarios registrados.</p>
                                            <p class="text-sm mt-1">Haga clic en "Nuevo Horario" para agregar uno.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Mostrando <span class="font-medium">5</span> de <span class="font-medium">5</span> horarios
                            </div>
                            <div class="flex space-x-1">
                                <button class="px-3 py-1 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="px-3 py-1 rounded-md bg-primary text-white">1</button>
                                <button class="px-3 py-1 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
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