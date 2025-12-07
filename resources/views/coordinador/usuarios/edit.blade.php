{{-- resources/views/coordinador/usuarios/edit.blade.php --}}
@extends('layouts.coordinador')

@section('title', "Editar Usuario - {$usuario->nombre_completo}")
@section('header-title', "Editar Usuario: {$usuario->nombre_completo}")

@section('content')
<div class="max-w-4xl mx-auto">
    
    {{-- Navegación --}}
    <div class="flex justify-between mb-6">
        <a href="{{ route('coordinador.usuarios.show', $usuario->id) }}" 
           class="bg-white text-gray-600 hover:bg-gray-50 border border-gray-200 px-4 py-2 rounded-lg shadow-sm transition-colors flex items-center space-x-2 text-sm font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Volver a Detalles</span>
        </a>
        
        <a href="{{ route('coordinador.usuarios.index') }}" 
           class="bg-slate-100 text-slate-600 hover:bg-slate-200 px-4 py-2 rounded-lg flex items-center transition-colors text-sm">
            <i class="fas fa-users mr-2"></i>
            Lista de Usuarios
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-400 text-xl mr-3"></i>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-400 text-xl mr-3"></i>
                <div>
                    <span class="text-red-800 font-medium">Por favor corrige los siguientes errores:</span>
                    <ul class="mt-2 text-red-700 text-sm">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-card p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4 pb-3 border-b border-slate-200">
            Información Básica del Usuario
        </h3>
        
        <form action="{{ route('coordinador.usuarios.update', $usuario->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nombre completo --}}
                <div>
                    <label for="nombre_completo" class="block text-sm font-medium text-slate-700 mb-1">
                        Nombre Completo *
                    </label>
                    <input type="text" 
                           id="nombre_completo" 
                           name="nombre_completo" 
                           value="{{ old('nombre_completo', $usuario->nombre_completo) }}"
                           required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                {{-- Correo personal --}}
                <div>
                    <label for="correo_personal" class="block text-sm font-medium text-slate-700 mb-1">
                        Correo Personal *
                    </label>
                    <input type="email" 
                           id="correo_personal" 
                           name="correo_personal" 
                           value="{{ old('correo_personal', $usuario->correo_personal) }}"
                           required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                {{-- Tipo de usuario --}}
                <div>
                    <label for="tipo_usuario" class="block text-sm font-medium text-slate-700 mb-1">
                        Tipo de Usuario *
                    </label>
                    <select id="tipo_usuario" 
                            name="tipo_usuario" 
                            required
                            onchange="toggleCamposTipo()"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="interno" {{ old('tipo_usuario', $usuario->tipo_usuario) == 'interno' ? 'selected' : '' }}>
                            Alumno Interno
                        </option>
                        <option value="externo" {{ old('tipo_usuario', $usuario->tipo_usuario) == 'externo' ? 'selected' : '' }}>
                            Alumno Externo
                        </option>
                    </select>
                </div>
                
                {{-- Número telefónico --}}
                <div>
                    <label for="numero_telefonico" class="block text-sm font-medium text-slate-700 mb-1">
                        Teléfono
                    </label>
                    <input type="tel" 
                           id="numero_telefonico" 
                           name="numero_telefonico" 
                           value="{{ old('numero_telefonico', $usuario->numero_telefonico) }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                {{-- Género --}}
                <div>
                    <label for="genero" class="block text-sm font-medium text-slate-700 mb-1">
                        Género
                    </label>
                    <select id="genero" 
                            name="genero" 
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccionar...</option>
                        <option value="M" {{ old('genero', $usuario->genero) == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('genero', $usuario->genero) == 'F' ? 'selected' : '' }}>Femenino</option>
                        <option value="Otro" {{ old('genero', $usuario->genero) == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
                
                {{-- Fecha de nacimiento --}}
                <div>
                    <label for="fecha_nacimiento" class="block text-sm font-medium text-slate-700 mb-1">
                        Fecha de Nacimiento
                    </label>
                    <input type="date" 
                           id="fecha_nacimiento" 
                           name="fecha_nacimiento" 
                           value="{{ old('fecha_nacimiento', 
                               $usuario->fecha_nacimiento 
                                   ? (\Carbon\Carbon::parse($usuario->fecha_nacimiento)->format('Y-m-d'))
                                   : ''
                           ) }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                {{-- Campos para INTERNOS (se muestran solo si es interno) --}}
                <div id="campos_internos" style="{{ old('tipo_usuario', $usuario->tipo_usuario) != 'interno' ? 'display: none;' : '' }}" class="md:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Correo institucional --}}
                        <div>
                            <label for="correo_institucional" class="block text-sm font-medium text-slate-700 mb-1">
                                Correo Institucional
                            </label>
                            <input type="email" 
                                   id="correo_institucional" 
                                   name="correo_institucional" 
                                   value="{{ old('correo_institucional', $usuario->correo_institucional) }}"
                                   class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        {{-- Número de control --}}
                        <div>
                            <label for="numero_control" class="block text-sm font-medium text-slate-700 mb-1">
                                Número de Control
                            </label>
                            <input type="text" 
                                   id="numero_control" 
                                   name="numero_control" 
                                   value="{{ old('numero_control', $usuario->numero_control) }}"
                                   class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    {{-- Especialidad/Carrera --}}
                    <div class="mt-4">
                        <label for="especialidad" class="block text-sm font-medium text-slate-700 mb-1">
                            Especialidad/Carrera
                        </label>
                        <select id="especialidad" 
                                name="especialidad" 
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecciona una carrera</option>
                            <option value="Ingeniería en Sistemas Computacionales" {{ old('especialidad', $usuario->especialidad) == 'Ingeniería en Sistemas Computacionales' ? 'selected' : '' }}>Ingeniería en Sistemas Computacionales</option>
                            <option value="Ingeniería en Tecnologias de la Informacion y Comunicaciones" {{ old('especialidad', $usuario->especialidad) == 'Ingeniería en Tecnologias de la Informacion y Comunicaciones' ? 'selected' : '' }}>Ingeniería en Tecnologías de la Información y Comunicaciones</option>
                            <option value="Ingeniería Industrial" {{ old('especialidad', $usuario->especialidad) == 'Ingeniería Industrial' ? 'selected' : '' }}>Ingeniería Industrial</option>
                            <option value="Ingeniería Electrónica" {{ old('especialidad', $usuario->especialidad) == 'Ingeniería Electrónica' ? 'selected' : '' }}>Ingeniería Electrónica</option>
                            <option value="Ingeniería Electromecanica" {{ old('especialidad', $usuario->especialidad) == 'Ingeniería Electromecanica' ? 'selected' : '' }}>Ingeniería Electromecánica</option>
                            <option value="Ingeniería en Gestion Empresarial" {{ old('especialidad', $usuario->especialidad) == 'Ingeniería en Gestion Empresarial' ? 'selected' : '' }}>Ingeniería en Gestión Empresarial</option>
                        </select>
                    </div>
                </div>
            </div>
            
            {{-- Información de registro --}}
            <div class="mt-6 pt-6 border-t border-slate-200">
                <h4 class="text-sm font-medium text-slate-500 mb-2">Información de Registro</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-slate-600">ID de Usuario:</span>
                        <span class="font-medium ml-2">{{ $usuario->id }}</span>
                    </div>
                    <div>
                        <span class="text-slate-600">Fecha de Registro:</span>
                        <span class="font-medium ml-2">
                            @php
                                try {
                                    echo \Carbon\Carbon::parse($usuario->created_at)->format('d/m/Y H:i');
                                } catch (Exception $e) {
                                    echo $usuario->created_at ?? 'N/A';
                                }
                            @endphp
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-600">Última Actualización:</span>
                        <span class="font-medium ml-2">
                            @php
                                try {
                                    echo \Carbon\Carbon::parse($usuario->updated_at)->format('d/m/Y H:i');
                                } catch (Exception $e) {
                                    echo $usuario->updated_at ?? 'N/A';
                                }
                            @endphp
                        </span>
                    </div>
                </div>
            </div>
            
            {{-- Botones de acción --}}
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('coordinador.usuarios.show', $usuario->id) }}" 
                   class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleCamposTipo() {
    const tipo = document.getElementById('tipo_usuario').value;
    const camposInternos = document.getElementById('campos_internos');
    const selectCarrera = document.getElementById('especialidad');
    
    if (tipo === 'interno') {
        camposInternos.style.display = 'block';
        // Si se cambia a interno, establecer carrera como requerida
        selectCarrera.required = true;
    } else {
        camposInternos.style.display = 'none';
        // Si se cambia a externo, quitar requerido y limpiar campos
        selectCarrera.required = false;
        document.getElementById('correo_institucional').value = '';
        document.getElementById('numero_control').value = '';
        document.getElementById('especialidad').value = '';
    }
}

// Inicializar estado al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    toggleCamposTipo();
});
</script>
@endpush
@endsection