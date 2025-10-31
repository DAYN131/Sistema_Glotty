<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Glotty</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-r from-blue-500 to-purple-700 h-screen flex items-center justify-center">
    <div class="bg-white/60 backdrop-blur-md rounded-2xl shadow-lg max-w-4xl w-full flex">
        
        <!-- Columna: Logo -->
        <div class="w-1/2 flex items-center justify-center p-8">
            <div class="bg-white rounded-xl p-4 shadow-md">
                <img src="/images/glotty.png" alt="Logo Glotty" class="w-95 h-auto">
            </div>
        </div>

        <!-- Columna: Formulario -->
        <div class="w-1/2 bg-white p-8 rounded-r-2xl">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Iniciar Sesión en Glotty</h2>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/login" method="POST" class="space-y-4">
                @csrf

                <input 
                    type="email" 
                    name="email" 
                    placeholder="Correo Electrónico" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value="{{ old('email') }}" 
                    required
                >

                <input 
                    type="password" 
                    name="password" 
                    placeholder="Contraseña" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    required
                >

                <button 
                    type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition duration-200"
                >
                    Iniciar Sesión
                </button>
            </form>

            <p class="text-gray-600 mt-4 text-center">
                ¿No tienes cuenta? 
                <a href="/register" class="text-purple-500 hover:underline">Regístrate</a>
            </p>
        </div>
    </div>
</body>
</html>