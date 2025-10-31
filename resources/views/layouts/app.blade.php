<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema Glotty')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#003399',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        secondary: {
                            50: '#f0fdf4',
                            500: '#22c55e',
                            600: '#16a34a',
                        }
                    },
                    boxShadow: {
                        'soft': '0 2px 4px rgba(0,0,0,0.05)',
                        'card': '0 4px 6px rgba(0,0,0,0.07)',
                    }
                }
            }
        }
    </script>
    <style>
        .transition-smooth {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">
    @yield('content')
    
    @stack('scripts')
</body>
</html>