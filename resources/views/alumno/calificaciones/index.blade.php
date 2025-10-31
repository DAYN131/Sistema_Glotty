<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Calificaciones</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f5f7fa;
    }
    
    .card-shadow {
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .gradient-header {
      background: linear-gradient(135deg, #0052cc 0%, #0066ff 100%);
    }
    
    .stat-card {
      transition: all 0.3s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-2px);
    }
    
    .course-card {
      transition: all 0.2s ease;
    }
    
    .course-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .grade-badge {
      position: relative;
      overflow: hidden;
    }
    
    .grade-badge::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: currentColor;
      opacity: 0.1;
      border-radius: 8px;
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen p-4 md:p-6">
  <div class="max-w-5xl mx-auto">
    <!-- Contenedor principal -->
    <div class="bg-white rounded-2xl card-shadow overflow-hidden border border-gray-100">
      
      <!-- Encabezado -->
      <div class="gradient-header p-6 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 opacity-10">
          <i class="fas fa-graduation-cap text-9xl transform -rotate-12 translate-x-6 -translate-y-6"></i>
        </div>
        <div class="relative z-10">
          <h2 class="text-2xl md:text-3xl font-bold flex items-center">
            <i class="fas fa-graduation-cap mr-3"></i>
            Mis Calificaciones
          </h2>
          <p class="text-sm md:text-base opacity-90 mt-2">Panel de seguimiento académico - Periodo actual</p>
        </div>
      </div>
      
      <!-- Resumen estadístico -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 border-b border-gray-100">
        <div class="stat-card bg-blue-50 rounded-xl p-4 text-center border border-blue-100">
          <p class="text-sm text-blue-700 font-medium mb-1">Total Cursos</p>
          <p class="text-3xl font-bold text-blue-900">{{ count($inscripciones) }}</p>
        </div>
        <div class="stat-card bg-green-50 rounded-xl p-4 text-center border border-green-100">
          <p class="text-sm text-green-700 font-medium mb-1">Aprobados</p>
          <p class="text-3xl font-bold text-green-600">
            {{ $inscripciones->where('calificacion_final', '>=', 70)->count() }}
          </p>
        </div>
        <div class="stat-card bg-yellow-50 rounded-xl p-4 text-center border border-yellow-100">
          <p class="text-sm text-yellow-700 font-medium mb-1">En Riesgo</p>
          <p class="text-3xl font-bold text-yellow-600">
            {{ $inscripciones->whereBetween('calificacion_final', [60, 69])->count() }}
          </p>
        </div>
        <div class="stat-card bg-red-50 rounded-xl p-4 text-center border border-red-100">
          <p class="text-sm text-red-700 font-medium mb-1">Reprobados</p>
          <p class="text-3xl font-bold text-red-600">
            {{ $inscripciones->where('calificacion_final', '<', 60)->count() }}
          </p>
        </div>
      </div>
      
      <!-- Filtros -->
      <div class="p-4 bg-gray-50 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
          <span class="text-sm text-gray-600 font-medium">Filtrar por:</span>
          <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            <option>Todos los periodos</option>
            <option>Periodo actual</option>
            <option>Periodos anteriores</option>
          </select>
        </div>
        <div class="relative">
          <input type="text" placeholder="Buscar curso..." class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
          <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>
      </div>
      
      <!-- Lista de calificaciones -->
      <div class="divide-y divide-gray-100">
        @forelse ($inscripciones as $insc)
        <div class="p-5 hover:bg-blue-50 transition-colors course-card">
          <!-- Información del grupo -->
          <div class="flex items-start mb-4">
            <div class="flex-shrink-0 h-16 w-16 rounded-xl gradient-header flex items-center justify-center text-white font-bold text-xl shadow-sm">
              {{ substr($insc->grupo->nivel_ingles, 0, 1) }}{{ $insc->grupo->letra_grupo }}
            </div>
            <div class="ml-4">
              <h3 class="font-semibold text-gray-800 text-lg">
                {{ $insc->grupo->nivel_ingles }}-{{ $insc->grupo->letra_grupo }}
              </h3>
              <div class="flex flex-wrap gap-x-6 gap-y-2 mt-2 text-sm text-gray-600">
                <div class="flex items-center">
                  <i class="fas fa-user-tie mr-2 text-blue-600"></i>
                  {{ $insc->grupo->profesor->nombre_profesor ?? 'Prof. No asignado' }}
                </div>
                <div class="flex items-center">
                  <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                  {{ $insc->grupo->periodo ?? 'Periodo no definido' }}
                </div>
                <div class="flex items-center">
                  <i class="fas fa-clock mr-2 text-blue-600"></i>
                  <span class="badge bg-blue-100 text-blue-800 px-2 py-1 rounded-md text-xs font-medium">
                    Lunes y Miércoles 18:00-20:00
                  </span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Tarjetas de calificaciones -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <!-- 1er Parcial -->
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm relative overflow-hidden grade-badge"
              style="color: {{ $insc->calificacion_parcial_1 === null ? '#6B7280' : ($insc->calificacion_parcial_1 >= 70 ? '#047857' : ($insc->calificacion_parcial_1 >= 60 ? '#B45309' : '#DC2626')) }}">
            <div class="flex justify-between items-end">
              <div>
                <div class="flex items-center">
                  <i class="fas fa-file-alt mr-2"></i>
                  <p class="text-sm font-medium text-gray-700">1er Parcial</p>
                </div>
                <p class="text-3xl font-bold mt-2">
                  {{ $insc->calificacion_parcial_1 ?? '--' }}
                </p>
              </div>
              <div class="flex flex-col items-end">
                <span class="text-xs text-gray-400">/100</span>
                @if($insc->calificacion_parcial_1 === null)
                  <span class="mt-3 text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full font-medium">Pendiente</span>
                @elseif($insc->calificacion_parcial_1 >= 70)
                  <span class="mt-3 text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full font-medium">Aprobado</span>
                @elseif($insc->calificacion_parcial_1 >= 60)
                  <span class="mt-3 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full font-medium">En riesgo</span>
                @else
                  <span class="mt-3 text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full font-medium">Reprobado</span>
                @endif
              </div>
            </div>
          </div>
            
            <!-- 2do Parcial -->
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm relative overflow-hidden grade-badge"
              style="color: {{ $insc->calificacion_parcial_2 === null ? '#6B7280' : ($insc->calificacion_parcial_2 >= 70 ? '#047857' : ($insc->calificacion_parcial_2 >= 60 ? '#B45309' : '#DC2626')) }}">
            <div class="flex justify-between items-end">
              <div>
                <div class="flex items-center">
                  <i class="fas fa-file-alt mr-2"></i>
                  <p class="text-sm font-medium text-gray-700">2do Parcial</p>
                </div>
                <p class="text-3xl font-bold mt-2">
                  {{ $insc->calificacion_parcial_2 ?? '--' }}
                </p>
              </div>
              <div class="flex flex-col items-end">
                <span class="text-xs text-gray-400">/100</span>
                @if($insc->calificacion_parcial_2 === null)
                  <span class="mt-3 text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full font-medium">Pendiente</span>
                @elseif($insc->calificacion_parcial_2 >= 70)
                  <span class="mt-3 text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full font-medium">Aprobado</span>
                @elseif($insc->calificacion_parcial_2 >= 60)
                  <span class="mt-3 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full font-medium">En riesgo</span>
                @else
                  <span class="mt-3 text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full font-medium">Reprobado</span>
                @endif
              </div>
            </div>
          </div>
            
            <!-- Final -->
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm relative overflow-hidden grade-badge"
              style="color: {{ $insc->calificacion_final === null ? '#6B7280' : ($insc->calificacion_final >= 70 ? '#047857' : ($insc->calificacion_final >= 60 ? '#B45309' : '#DC2626')) }}">
            <div class="flex justify-between items-end">
              <div>
                <div class="flex items-center">
                  <i class="fas fa-award mr-2"></i>
                  <p class="text-sm font-medium text-gray-700">Calificación Final</p>
                </div>
                <p class="text-3xl font-bold mt-2">
                  {{ $insc->calificacion_final ?? '--' }}
                </p>
              </div>
              <div class="flex flex-col items-end">
                <span class="text-xs text-gray-400">/100</span>
                @if($insc->calificacion_final === null)
                  <span class="mt-3 text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full font-medium">Pendiente</span>
                @elseif($insc->calificacion_final >= 70)
                  <span class="mt-3 text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full font-medium">Aprobado</span>
                @elseif($insc->calificacion_final >= 60)
                  <span class="mt-3 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full font-medium">En riesgo</span>
                @else
                  <span class="mt-3 text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full font-medium">Reprobado</span>
                @endif
              </div>
            </div>
          </div>
          
          <!-- Progreso y opciones -->
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center mt-4 pt-4 border-t border-gray-100">
            <div class="w-full md:w-1/2 mb-3 md:mb-0">
              <div class="flex items-center justify-between mb-1">
                <p class="text-xs font-medium text-gray-600">Progreso del curso</p>
                <p class="text-xs font-medium text-blue-600">75%</p>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
              </div>
            </div>
            <div class="flex gap-2">
              <button class="text-sm px-3 py-1.5 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors flex items-center">
                <i class="fas fa-book-open mr-1"></i>
                Detalles
              </button>
              <button class="text-sm px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                <i class="fas fa-chart-line mr-1"></i>
                Ver progreso
              </button>
            </div>
          </div>
        </div>
        @empty
        <div class="p-12 text-center">
          <div class="inline-flex items-center justify-center p-6 bg-blue-50 rounded-full mb-5">
            <i class="fas fa-graduation-cap text-4xl text-blue-500"></i>
          </div>
          <h3 class="text-xl font-medium text-gray-800 mb-3">No hay calificaciones registradas</h3>
          <p class="text-gray-500 max-w-md mx-auto">Tus calificaciones aparecerán aquí una vez que sean asignadas por tus profesores.</p>
          <button class="mt-6 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center mx-auto">
            <i class="fas fa-sync-alt mr-2"></i>
            Actualizar datos
          </button>
        </div>
        @endforelse
      </div>
      
      <!-- Leyenda -->
      <div class="p-6 bg-gray-50 border-t border-gray-200">
        <h4 class="text-sm font-medium text-gray-700 mb-3">Leyenda de calificaciones</h4>
        <div class="flex flex-wrap gap-6">
          <div class="flex items-center">
            <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
            <span class="text-sm text-gray-600">≥ 70: Aprobado</span>
          </div>
          <div class="flex items-center">
            <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
            <span class="text-sm text-gray-600">60-69: En riesgo</span>
          </div>
          <div class="flex items-center">
            <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
            <span class="text-sm text-gray-600">< 60: Reprobado</span>
          </div>
        </div>
        <div class="mt-4 text-xs text-gray-500">
          <p>Esta información representa un resumen de tu desempeño académico. Para más detalles, consulta con tu profesor o coordinador académico.</p>
        </div>
      </div>
      
      <!-- Footer -->
      <div class="p-4 bg-blue-800 text-white text-center text-sm">
        <p>&copy; 2025 Sistema de Gestión Académica | Última actualización: 24 abril, 2025</p>
      </div>
    </div>
  </div>
</body>
</html>