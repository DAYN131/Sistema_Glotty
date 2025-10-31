<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Glotty</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<!-- Aplicando gradiente con azul marino institucional (blue-900 a blue-700) -->
<body class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-2xl max-w-md w-full">
        <!-- Título con azul marino principal (blue-900) -->
        <h2 class="text-2xl font-bold text-blue-900 mb-6 text-center">Regístrate en Glotty</h2>

        <!-- Errores con rojo institucional (red-600) -->
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-600 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario de registro -->
        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Selección de Tipo de Usuario -->
            <div class="mb-4">
                <!-- Label con azul marino (blue-900) -->
                <label class="block text-blue-900 text-sm font-bold mb-2">Tipo de Usuario</label>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <!-- Radio button con azul institucional (blue-600) -->
                        <input type="radio" name="tipo_usuario" value="interno" 
                               class="form-radio text-blue-600" 
                               {{ old('tipo_usuario') == 'interno' ? 'checked' : 'checked' }}>
                        <span class="ml-2">Interno</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="tipo_usuario" value="externo"
                               class="form-radio text-blue-600"
                               {{ old('tipo_usuario') == 'externo' ? 'checked' : '' }}>
                        <span class="ml-2">Externo</span>
                    </label>
                </div>
            </div>

            <!-- Campo: Nombre Completo -->
            <div>
                <!-- Focus ring con azul institucional (blue-600) y bordes slate-300 -->
                <input 
                    type="text" 
                    name="nombre_completo" 
                    placeholder="Nombre Completo" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all"
                    value="{{ old('nombre_completo') }}" 
                    required
                >
            </div>

            <!-- Campo: Correo Personal -->
            <div>
                <input 
                    type="email" 
                    name="correo_personal" 
                    placeholder="Correo Personal" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all"
                    value="{{ old('correo_personal') }}" 
                    required
                >
            </div>

            <!-- Campos para Usuarios Internos (mostrados por defecto) -->
            <div id="campos-interno">
                <!-- Campo: Número de Control -->
                <div>
                    <input 
                        type="text" 
                        name="numero_control" 
                        placeholder="Número de Control" 
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all interno-field"
                        value="{{ old('numero_control') }}"
                    >
                </div>

                <!-- Campo: Correo Institucional -->
                <div>
                    <input 
                        type="email" 
                        name="correo_institucional" 
                        placeholder="Correo Institucional" 
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all interno-field"
                        value="{{ old('correo_institucional') }}"
                    >
                </div>

                <!-- Campo: Especialidad -->
                <!-- Campo: Carrera / Especialidad -->
                <div>
                    <select 
                        name="especialidad" 
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all interno-field"
                        required
                    >
                        <option value="">Selecciona tu carrera</option>
                        <option value="Ingeniería en Sistemas Computacionales" {{ old('especialidad') == 'Ingeniería en Sistemas Computacionales' ? 'selected' : '' }}>Ingeniería en Sistemas Computacionales</option>
                        <option value="Ingeniería e Tecnologias de la Informacion y Comunicaciones" {{ old('especialidad') == 'Ingeniería e Tecnologias de la Informacion y Comunicaciones' ? 'selected' : '' }}>Ingeniería e Tecnologias de la Informacion y Comunicaciones</option>
                        <option value="Ingeniería Industrial" {{ old('especialidad') == 'Ingeniería Industrial' ? 'selected' : '' }}>Ingeniería Industrial</option>
                        <option value="Ingeniería Electrónica" {{ old('especialidad') == 'Ingeniería Electrónica' ? 'selected' : '' }}>Ingeniería Electrónica</option>
                        <option value="Ingeniería Electromecanica" {{ old('especialidad') == 'Ingeniería Electromecanica' ? 'selected' : '' }}>Ingeniería Electromecanica</option>
                        <option value="Ingeniería en Gestion Empresarial" {{ old('especialidad') == 'Ingeniería en Gestion Empresarial' ? 'selected' : '' }}>Ingeniería en Gestion Empresarial</option>
      
                    </select>
                </div>

            </div>

            <!-- Campos adicionales para todos -->
            <!-- Campo: Número Telefónico -->
            <div>
                <input 
                    type="tel" 
                    name="numero_telefonico" 
                    placeholder="Número Telefónico" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all"
                    value="{{ old('numero_telefonico') }}"
                >
            </div>

            <!-- Campo: Género -->
            <div>
                <select name="genero" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all">
                    <option value="">Selecciona tu género</option>
                    <option value="M" {{ old('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                    <option value="Otro" {{ old('genero') == 'Otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>

            <!-- Campo: Fecha de Nacimiento -->
            <div>
                <input 
                    type="date" 
                    name="fecha_nacimiento" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all"
                    value="{{ old('fecha_nacimiento') }}"
                >
            </div>

            <!-- Campo: Contraseña -->
            <div>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Contraseña" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all"
                    required
                >
            </div>

            <!-- Campo: Confirmar Contraseña -->
            <div>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    placeholder="Confirmar Contraseña" 
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all"
                    required
                >
            </div>

            <!-- Botón con azul institucional (blue-600) -->
            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition-colors shadow-md hover:shadow-lg"
            >
                Registrarse
            </button>
        </form>

        <!-- Enlace con azul institucional (blue-600) y texto en gris neutro (slate-600) -->
        <p class="text-slate-600 mt-4 text-center">
            ¿Ya tienes cuenta? 
            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold hover:underline transition-colors">Inicia Sesión</a>
        </p>
    </div>

    <!-- JavaScript para mostrar/ocultar campos según tipo de usuario -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoUsuarioRadios = document.querySelectorAll('input[name="tipo_usuario"]');
            const camposInterno = document.getElementById('campos-interno');
            const camposInternoInputs = document.querySelectorAll('.interno-field');

            function toggleCamposInterno() {
                const esInterno = document.querySelector('input[name="tipo_usuario"]:checked').value === 'interno';
                
                if (esInterno) {
                    camposInterno.style.display = 'block';
                    camposInternoInputs.forEach(input => {
                        input.required = true;
                    });
                } else {
                    camposInterno.style.display = 'none';
                    camposInternoInputs.forEach(input => {
                        input.required = false;
                        input.value = ''; // Limpiar campos al cambiar
                    });
                }
            }

            // Inicializar estado
            toggleCamposInterno();

            // Escuchar cambios en los radio buttons
            tipoUsuarioRadios.forEach(radio => {
                radio.addEventListener('change', toggleCamposInterno);
            });
        });
    </script>

    <style>
        /* Transición suave para mostrar/ocultar campos */
        #campos-interno {
            transition: all 0.3s ease-in-out;
        }
        
        /* Borde izquierdo con azul institucional para campos requeridos */
        .interno-field:required {
            border-left: 4px solid #2563eb;
        }
    </style>
</body>
</html>
