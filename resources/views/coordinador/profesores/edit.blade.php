{{-- resources/views/coordinador/profesores/edit.blade.php --}}
@extends('layouts.coordinador')

@section('title', 'Editar Profesor')
@section('header-title', 'Editar Profesor')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header del formulario -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-soft p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Editar Profesor</h1>
                <p class="text-blue-100">Actualice la información del profesor {{ $profesor->nombre_profesor }}</p>
            </div>
            <div class="bg-white/20 p-4 rounded-xl">
                <i class="fas fa-user-edit text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-2xl shadow-card p-8 border border-gray-100">
        <form action="{{ route('coordinador.profesores.update', $profesor->id_profesor) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <!-- Información Personal -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                    Información Personal
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- RFC -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-id-card text-gray-400 mr-2 text-sm"></i>
                            RFC *
                        </label>
                        <input type="text" name="rfc_profesor" required
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               value="{{ old('rfc_profesor', $profesor->rfc_profesor) }}">
                    </div>

                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-signature text-gray-400 mr-2 text-sm"></i>
                            Nombre(s) *
                        </label>
                        <input type="text" name="nombre_profesor" required
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               value="{{ old('nombre_profesor', $profesor->nombre_profesor) }}">
                    </div>

                    <!-- Apellidos -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-signature text-gray-400 mr-2 text-sm"></i>
                            Apellidos *
                        </label>
                        <input type="text" name="apellidos_profesor" required
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               value="{{ old('apellidos_profesor', $profesor->apellidos_profesor) }}">
                    </div>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-address-book text-green-500 mr-2"></i>
                    Información de Contacto
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Correo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-envelope text-gray-400 mr-2 text-sm"></i>
                            Correo Electrónico *
                        </label>
                        <input type="email" name="correo_profesor" required
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               value="{{ old('correo_profesor', $profesor->correo_profesor) }}">
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-phone text-gray-400 mr-2 text-sm"></i>
                            Teléfono *
                        </label>
                        <input type="tel" name="num_telefono" required
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               value="{{ old('num_telefono', $profesor->num_telefono) }}">
                    </div>
                </div>
            </div>

            <!-- Seguridad (Opcional) -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-shield-alt text-purple-500 mr-2"></i>
                    Cambiar Contraseña (Opcional)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Contraseña -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-lock text-gray-400 mr-2 text-sm"></i>
                            Nueva Contraseña
                        </label>
                        <input type="password" name="password"
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               placeholder="Dejar en blanco para no cambiar">
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-lock text-gray-400 mr-2 text-sm"></i>
                            Confirmar Contraseña
                        </label>
                        <input type="password" name="password_confirmation"
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               placeholder="Confirmar nueva contraseña">
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('coordinador.profesores.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-md hover:shadow-lg">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i>
                    Actualizar Profesor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection