<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Grupo - Glotty</title>
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
                <a href="" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-home text-xl"></i>
                </a>
                <a href="" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-user-graduate text-xl"></i>
                </a>
                <a href="{{ route('coordinador.grupos.index') }}" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-users text-xl"></i>
                </a>
                <a href="{{ route('coordinador.profesores') }}" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                </a>
                <a href="{{ route('coordinador.horarios.index') }}" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </a>
            </div>
            <div class="mt-auto">
                <a href="" class="text-white hover:text-blue-300 transition-colors">
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
                    <a href="" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-home text-sm"></i>
                        <span class="ml-1">Inicio</span>
                    </a>
                    <span class="text-gray-400 mx-2">/</span>
                    <a href="{{ route('coordinador.grupos.index') }}" class="text-gray-600 hover:text-gray-800">
                        <span>Gestión de Grupos</span>
                    </a>
                    <span class="text-gray-400 mx-2">/</span>
                    <span class="text-gray-600">Editar Grupo</span>
                </div>
                <div class="ml-auto flex items-center">
                    <span class="text-gray-700 font-medium">COORDINADOR ACADÉMICO</span>
                </div>
            </header>
            
            <!-- Content Area -->
            <div class="flex-1 p-6 overflow-auto">
                <!-- Form Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 max-w-4xl mx-auto">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Grupo</h1>
                    
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                            <strong class="font-bold">¡Error!</strong>
                            <ul class="mt-1 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('coordinador.grupos.update',$grupo->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Primera fila: Nivel y Letra -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="nivel_ingles" class="block text-sm font-medium text-gray-700 mb-1">Nivel de Inglés</label>
                                <!-- Nivel de Inglés -->
                                    <select id="nivel_ingles" name="nivel_ingles" required 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border">
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ $grupo->nivel_ingles == $i ? 'selected' : '' }}>
                                                Nivel {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                @error('nivel_ingles')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="letra_grupo" class="block text-sm font-medium text-gray-700 mb-1">Letra de Grupo</label>
                                <!-- Letra de Grupo -->
                                <input type="text" id="letra_grupo" name="letra_grupo" maxlength="1" required 
                                    placeholder="Ej: A, B, C" value="{{ $grupo->letra_grupo }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border uppercase"
                                    oninput="this.value = this.value.toUpperCase()">
                                @error('letra_grupo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Segunda fila: Año y Periodo -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="anio" class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                                <input type="number" id="anio" name="anio" required min="2023" max="2030" 
                                       value="{{ $grupo->anio}}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border">
                                @error('anio')ounded
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="periodo" class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
                                <!-- Periodo -->
                                <select id="periodo" name="periodo" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border">
                                    <option value="">Seleccione un periodo</option>
                                    <option value="Febrero-Junio" {{ $grupo->periodo == 'Febrero-Junio' ? 'selected' : '' }}>Febrero-Junio</option>
                                    <option value="Septiembre-Noviembre" {{ $grupo->periodo == 'Septiembre-Noviembre' ? 'selected' : '' }}>Septiembre-Noviembre</option>
                                    <option value="Invierno" {{ $grupo->periodo == 'Invierno' ? 'selected' : '' }}>Invierno</option>
                                    <option value="Verano1" {{ $grupo->periodo == 'Verano1' ? 'selected' : '' }}>Verano1</option>
                                    <option value="Verano2" {{ $grupo->periodo == 'Verano2' ? 'selected' : '' }}>Verano2</option>
                                </select>
                                @error('periodo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Tercera fila: Profesor -->
                        <div class="mb-6">
                            <label for="id_profesor" class="block text-sm font-medium text-gray-700 mb-1">Profesor</label>
                            <select id="id_profesor" name="id_profesor" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border">
                                <option value="">Seleccione un profesor</option>
                                @foreach($profesores as $profesor)
                                <option value="{{ $profesor->rfc_profesor }}" 
                                        {{ $grupo->rfc_profesor == $profesor->rfc_profesor ? 'selected' : '' }}>
                                    {{ $profesor->nombre_profesor }} {{ $profesor->apellidos_profesor }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_profesor')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Cuarta fila: Aula y Horario -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="id_aula" class="block text-sm font-medium text-gray-700 mb-1">Aula</label>
                                <select id="id_aula" name="id_aula" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border">
                                    <option value="">Seleccione un aula</option>
                                    @foreach($aulas as $aula)
                                    <option value="{{ $aula->id_aula }}" 
                                        {{ $grupo->id_aula == $aula->id_aula ? 'selected' : '' }}>
                                        {{ $aula->edificio }}{{ $aula->numero_aula }} (Capacidad: {{ $aula->capacidad }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('id_aula')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="id_horario" class="block text-sm font-medium text-gray-700 mb-1">Horario</label>
                                <!-- Horario -->
                                <select id="id_horario" name="id_horario" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border">
                                    <option value="">Seleccione un horario</option>
                                    @foreach($horarios as $horario)
                                        <option value="{{ $horario->id }}" 
                                                {{ $grupo->id_horario == $horario->id ? 'selected' : '' }}>
                                            {{ $horario->nombre }}: {{ $horario->hora_inicio }} - {{ $horario->hora_fin }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Quinta fila: Cupo Maximo y Cupo mínimo -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="cupo_maximo" class="block text-sm font-medium text-gray-700 mb-1">Cupo Máximo</label>
                               <!-- Cupo Máximo -->
                                <input type="number" id="cupo_maximo" name="cupo_maximo" min="1" max="50" 
                                    value="{{ $grupo->cupo_maximo }}" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border">
                                @error('cupo_maximo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="cupo_minimo" class="block text-sm font-medium text-gray-700 mb-1">Cupo Mínimo</label>
                                <input type="number" id="cupo_minimo" name="cupo_minimo" min="1" max="20" 
                                    value="{{ $grupo->cupo_minimo }}" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 px-3 border">
                                @error('cupo_minimo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                            <a href="{{ route('coordinador.grupos.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md transition-colors text-center">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-primary hover:bg-primary-dark text-white py-2 px-4 rounded-md transition-colors text-center">
                                <i class="fas fa-save mr-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer con copyright -->
    <footer class="bg-white text-center py-3 text-gray-600 border-t">
        &copy; {{ date('Y') }} Glotty - Sistema de Gestión de Idiomas
    </footer>
</body>
</html>
