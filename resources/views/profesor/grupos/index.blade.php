<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Grupos - Glotty</title>
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
    <style>
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .gradient-header {
            background: linear-gradient(135deg, #2980b9 0%, #1a5276 100%);
        }
        
        .progress-bar {
            transition: width 1s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Encabezado de la página -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Mis Grupos Asignados</h1>
                <p class="text-gray-600">Gestiona tus grupos y calificaciones de alumnos</p>
            </div>
            
            <div class="mt-4 md:mt-0 flex items-center space-x-4">
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200 flex items-center">
                    <i class="fas fa-calendar-alt text-primary mr-2"></i>
                    <span class="text-gray-700">Periodo: {{ $grupo->periodo ?? 'Actual' }}</span>
                    
                </div>
                
                <a href="#" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg flex items-center transition-colors shadow-sm">
                    <i class="fas fa-download mr-2"></i>
                    Exportar Datos
                </a>
            </div>
        </div>
        
        <!-- Resumen de grupos -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-primary flex items-center">
                <div class="p-3 bg-primary-light bg-opacity-20 rounded-full text-primary mr-4">
                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total de Grupos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ count($grupos) }}</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500 flex items-center">
                <div class="p-3 bg-green-100 rounded-full text-green-600 mr-4">
                    <i class="fas fa-user-graduate text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total de Alumnos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $grupos->sum('alumnos_count') }}</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500 flex items-center">
                <div class="p-3 bg-purple-100 rounded-full text-purple-600 mr-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Horas Semanales</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $horasSemanales ?? count($grupos) * 5 }}</p>
                </div>
            </div>
        </div>
        
        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-8 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center">
                <span class="text-gray-700 mr-2">Filtrar por:</span>
                <select class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-sm">
                    <option value="">Todos los niveles</option>
                    <option value="1">Nivel 1</option>
                    <option value="2">Nivel 2</option>
                    <option value="3">Nivel 3</option>
                    <option value="4">Nivel 4</option>
                    <option value="5">Nivel 5</option>
                </select>
            </div>
            
            <div class="relative">
                <input type="text" placeholder="Buscar grupo..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-sm">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        
        <!-- Grid de grupos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($grupos as $grupo)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 card-hover">
                <div class="gradient-header p-5 text-white relative">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="font-bold text-xl">Grupo {{ $grupo->nivel_ingles }}-{{ $grupo->letra_grupo }}</h2>
                            <p class="text-sm opacity-90 mt-1">{{ $grupo->horario->descripcion }}</p>
                        </div>
                        <span class="bg-white bg-opacity-20 text-white text-xs font-bold px-3 py-1 rounded-full">
                            {{ $grupo->periodo }}
                        </span>
                    </div>
                    
    @if($grupo->horario)
    <div class="horario-info">
        @if($grupo->horario->tipo == 'sabado')
            <span>Sábados</span>
        @else
            <span>
                @if(!empty($grupo->horario->dias))
                    {{ implode(', ', array_map('ucfirst', (array)$grupo->horario->dias)) }}
                @else
                    Días no definidos
                @endif
            </span>
        @endif
        <div class="horas">
            {{ $grupo->horario->hora_inicio->format('H:i') }} - 
            {{ $grupo->horario->hora_fin->format('H:i') }}
        </div>
    </div>
@else
    <span class="text-warning">Sin horario asignado</span>
@endif
                </div>
                
                <div class="p-5">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-full text-primary mr-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Alumnos</p>
                                <p class="font-semibold text-gray-800">{{ $grupo->alumnos_count }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-full text-green-600 mr-3">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Aula</p>
                                <p class="font-semibold text-gray-800">{{ $grupo->aula->edificio }}-{{ $grupo->aula->numero_aula }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Barra de progreso -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-500">Progreso del curso</span>
                            <span class="text-xs font-medium text-gray-700">{{ $grupo->progreso ?? '65' }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full progress-bar" style="width: {{ $grupo->progreso ?? '65' }}%"></div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('profesor.grupos.alumnos.show', $grupo->id) }}" 
                           class="flex-1 bg-primary hover:bg-primary-dark text-white text-center py-2.5 px-4 rounded-lg transition-colors flex items-center justify-center">
                            <i class="fas fa-list-ol mr-2"></i> Calificaciones
                        </a>
                        
                        <a href="#" 
                           class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-center py-2.5 px-4 rounded-lg transition-colors flex items-center justify-center">
                            <i class="fas fa-info-circle mr-2"></i> Detalles
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
            
            @if(count($grupos) == 0)
            <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white rounded-xl shadow-sm p-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="p-6 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-chalkboard text-5xl text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No tienes grupos asignados</h3>
                    <p class="text-gray-500 mb-6">Actualmente no tienes grupos asignados para este periodo.</p>
                    <a href="#" class="bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-lg transition-colors">
                        Contactar a coordinación
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <script>
        // Script para animar las barras de progreso al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('.progress-bar');
            setTimeout(() => {
                progressBars.forEach(bar => {
                    const width = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = width;
                    }, 100);
                });
            }, 300);
        });
    </script>
</body>
</html>