{{-- resources/views/layouts/coordinador.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Coordinador - Glotty')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            light: '#dbeafe',
                            DEFAULT: '#60a5fa',
                            dark: '#3b82f6'
                        },
                        secondary: {
                            light: '#d1fae5',
                            DEFAULT: '#6ee7b7',
                            dark: '#34d399'
                        },
                        accent: {
                            light: '#e0f2fe',
                            DEFAULT: '#7dd3fc',
                            dark: '#38bdf8'
                        },
                        neutral: {
                            light: '#f8fafc',
                            DEFAULT: '#f1f5f9',
                            dark: '#e2e8f0'
                        },
                        text: {
                            primary: '#334155',
                            secondary: '#64748b'
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                        'card': '0 2px 8px rgba(15, 23, 42, 0.08)',
                        'card-hover': '0 4px 12px rgba(15, 23, 42, 0.12)'
                    },
                    borderRadius: {
                        'xl': '12px',
                        '2xl': '16px'
                    }
                }
            }
        }
    </script>
    <style>
        .transition-smooth {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col font-sans antialiased text-text-primary">

    
    <!-- Main Content sin sidebar -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Header con colores suaves -->
        <header class="bg-gradient-to-r from-slate-600 to-slate-700 shadow-sm h-20 flex items-center px-8 text-white">
            <div class="flex items-center space-x-4">
                <div class="bg-white/10 p-3 rounded-xl backdrop-blur-sm">
                    <i class="fas fa-user-tie text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-white font-bold text-2xl">@yield('header-title', 'Panel del Coordinador')</h1>
                    <p class="text-slate-200 text-sm">Sistema Académico Glotty</p>
                </div>
            </div>
            <div class="ml-auto flex items-center space-x-6">
                <div class="text-right">
                    <p class="text-white font-semibold">{{ session('user_fullname') ?? 'Coordinador' }}</p>
                    <p class="text-slate-200 text-sm">RFC: {{ session('user_identifier') ?? 'N/A' }}</p>
                </div>
                <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center border-2 border-white/20 backdrop-blur-sm">
                    <i class="fas fa-user text-white text-lg"></i>
                </div>
                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST" class="flex items-center">
                    @csrf
                    <button type="submit" class="text-white/90 hover:text-white transition-smooth flex items-center space-x-2 bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg backdrop-blur-sm">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Salir</span>
                    </button>
                </form>
            </div>
        </header>
        
        <!-- Content Area -->
        <main class="flex-1 p-8 overflow-auto bg-neutral-light">
            
            <!-- Mensajes de éxito/error -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-emerald-600 text-xl mr-3"></i>
                        <span class="text-emerald-800 font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-rose-600 text-xl mr-3"></i>
                        <div>
                            <h4 class="text-rose-800 font-semibold">Errores encontrados:</h4>
                            <ul class="text-rose-700 text-sm mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
            
        </main>
    </div>
    
    <!-- Footer con colores más suaves -->
    <footer class="bg-slate-700 text-white py-4 px-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center space-x-2 mb-4 md:mb-0">
                <div class="w-8 h-8 rounded-full bg-slate-600 flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-slate-200"></i>
                </div>
                <span class="font-medium">Sistema Académico Glotty</span>
            </div>
            <div class="text-sm text-slate-300">
                &copy; {{ date('Y') }} Sistema Glotty. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>