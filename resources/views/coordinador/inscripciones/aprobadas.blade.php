<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alumnos Inscritos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/script.js"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">
    <div class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <h1 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0l4.5 4.5M7.5 3v13.5m-3-3h13.5m0-13.5L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                </svg>
                Panel del Coordinador
            </h1>
            <p class="text-gray-600 text-sm">Gestión de alumnos inscritos y sus calificaciones.</p>
        </div>

        <!-- Barra de búsqueda agregada -->
        <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" id="searchControl" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Buscar por número de control o nombre">
                </div>
                <button id="clearSearch" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Limpiar
                </button>
            </div>
        </div>



         <!-- Modal para información del alumno -->
        <div id="studentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="flex justify-between items-center border-b px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-800">Información del Alumno</h3>
                    <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Número de Control</p>
                            <p id="modalControl" class="mt-1 text-sm text-gray-900">--</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nombre Completo</p>
                            <p id="modalName" class="mt-1 text-sm text-gray-900">--</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Carrera</p>
                            <p id="modalCareer" class="mt-1 text-sm text-gray-900">--</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Correo Institucional</p>
                            <p id="modalEmail" class="mt-1 text-sm text-gray-900">--</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Grupo Actual</p>
                            <p id="modalGroup" class="mt-1 text-sm text-gray-900">--</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3 flex justify-end border-t">
                    <button id="closeModalBtn" type="button" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>



                <div class="overflow-x-auto rounded-lg shadow-xl">
            <table class="min-w-full bg-white">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">N° Control</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Alumno</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Grupo</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">1er Parcial</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">2do Parcial</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Final</th>
                    </tr>
                </thead>
                <tbody id="studentsTable" class="divide-y divide-gray-200">
                    @foreach($inscripciones as $insc)
                    <tr class="student-row {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}" 
                        data-control="{{ $insc->alumno->no_control ?? '' }}" 
                        data-name="{{ $insc->alumno->nombre_alumno }} {{ $insc->alumno->apellidos_alumno }}"
                        data-career="{{ $insc->alumno->carrera ?? 'No especificado' }}"
                        data-email="{{ $insc->alumno->correo_institucional ?? 'No especificado' }}"
                        data-group="{{ $insc->grupo->nivel_ingles }}-{{ $insc->grupo->letra_grupo }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-medium cursor-pointer hover:underline control-number">
                            {{ $insc->alumno->no_control ?? '--' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $insc->alumno->nombre_alumno }} {{ $insc->alumno->apellidos_alumno }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $insc->grupo->nivel_ingles }}-{{ $insc->grupo->letra_grupo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $insc->calificacion_parcial_1 ?? '--' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $insc->calificacion_parcial_2 ?? '--' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-700">{{ $insc->calificacion_final ?? '--' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
           
            const searchInput = document.getElementById('searchControl');
            const clearButton = document.getElementById('clearSearch');
            const studentRows = document.querySelectorAll('.student-row');
            
            // Función para filtrar alumnos
            function filterStudents() {
                const searchTerm = searchInput.value.toLowerCase();
                
                studentRows.forEach(row => {
                    const controlNumber = row.getAttribute('data-control').toLowerCase();
                    const studentName = row.getAttribute('data-name');
                    
                    if (controlNumber.includes(searchTerm) || studentName.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
            
            // Evento para el input de búsqueda
            searchInput.addEventListener('input', filterStudents);
            
            // Evento para el botón de limpiar
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                filterStudents();
            });
        
            
            // Modal functionality
            const modal = document.getElementById('studentModal');
            const closeModal = document.getElementById('closeModal');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const controlNumbers = document.querySelectorAll('.control-number');
            
            controlNumbers.forEach(control => {
                control.addEventListener('click', function() {
                    const row = this.closest('tr');
                    document.getElementById('modalControl').textContent = row.getAttribute('data-control') || '--';
                    document.getElementById('modalName').textContent = row.getAttribute('data-name') || '--';
                    document.getElementById('modalCareer').textContent = row.getAttribute('data-career') || '--';
                    document.getElementById('modalEmail').textContent = row.getAttribute('data-email') || '--';
                    document.getElementById('modalGroup').textContent = row.getAttribute('data-group') || '--';
                    
                    modal.classList.remove('hidden');
                });
            });
            
            [closeModal, closeModalBtn].forEach(btn => {
                btn.addEventListener('click', function() {
                    modal.classList.add('hidden');
                });
            });
            
            // Cerrar modal al hacer clic fuera del contenido
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>