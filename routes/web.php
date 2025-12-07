<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\PeriodoController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\PreregistroController;
use App\Http\Controllers\CoordinadorPreregistroController;
use App\Http\Controllers\CoordinadorGrupoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DocumentoController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (No autenticadas)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

/*
|--------------------------------------------------------------------------
| Rutas para Alumnos (auth:web)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:web'])->group(function () {
    // Dashboard alumno
    Route::get('/alumno', function () {
        return view('alumno', [
            'nombre_completo' => Auth::user()->nombre_completo,
            'identificador' => Auth::user()->numero_control
        ]);
    })->name('alumno.dashboard');

    // Preregistros del alumno
    Route::prefix('alumno/preregistro')->name('alumno.preregistro.')->group(function () {
        Route::get('/', [PreregistroController::class, 'index'])->name('index');
        Route::get('/crear', [PreregistroController::class, 'create'])->name('create');
        Route::post('/', [PreregistroController::class, 'store'])->name('store');
        Route::get('/{id}', [PreregistroController::class, 'show'])->name('show');
        Route::post('/{id}/cancelar', [PreregistroController::class, 'cancel'])->name('cancel');
    });
});

/*
|--------------------------------------------------------------------------
| Rutas para Profesores (auth:profesor)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:profesor'])->group(function () {
    Route::get('/profesor', function () {
        $profesor = Auth::guard('profesor')->user();
        return view('profesor', [
            'nombre_completo' => $profesor->nombre_profesor . ' ' . $profesor->apellidos_profesor,
            'rfc_profesor' => $profesor->rfc_profesor
        ]);
    })->name('profesor.dashboard');
});

/*
|--------------------------------------------------------------------------
| Rutas para Coordinadores (auth:coordinador)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:coordinador'])->group(function () {
    
    // Dashboard coordinador
    Route::get('/coordinador', [AuthController::class, 'coordinadorDashboard'])
        ->name('coordinador.dashboard');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: PROFESORES
    |--------------------------------------------------------------------------
    */
    Route::prefix('coordinador/profesores')->name('coordinador.profesores.')->group(function () {
        Route::get('/', [ProfesorController::class, 'index'])->name('index');
        Route::get('/crear', [ProfesorController::class, 'create'])->name('create');
        Route::post('/', [ProfesorController::class, 'store'])->name('store');
        Route::get('/{id}/editar', [ProfesorController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProfesorController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProfesorController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: PERIODOS
    |--------------------------------------------------------------------------
    */
    Route::prefix('coordinador/periodos')->name('coordinador.periodos.')->group(function () {
        // CRUD básico
        Route::get('/', [PeriodoController::class, 'index'])->name('index');
        Route::get('/crear', [PeriodoController::class, 'create'])->name('create');
        Route::post('/', [PeriodoController::class, 'store'])->name('store');
        Route::get('/{periodo}', [PeriodoController::class, 'show'])->name('show');
        Route::get('/{periodo}/editar', [PeriodoController::class, 'edit'])->name('edit');
        Route::put('/{periodo}', [PeriodoController::class, 'update'])->name('update');
        Route::delete('/{periodo}', [PeriodoController::class, 'destroy'])->name('destroy');
        
        // Gestión de horarios en periodo
        Route::post('/{periodo}/agregar-horarios', [PeriodoController::class, 'agregarHorarios'])
            ->name('agregar-horarios');
        Route::post('/{periodo}/regenerar-horarios', [PeriodoController::class, 'regenerarHorarios'])
            ->name('regenerar-horarios');
        Route::post('/{periodo}/toggle-horario/{horarioPeriodo}', [PeriodoController::class, 'toggleHorarioPeriodo'])
            ->name('toggle-horario');
        Route::delete('/{periodo}/eliminar-horario/{horarioPeriodo}', [PeriodoController::class, 'eliminarHorarioPeriodo'])
            ->name('eliminar-horario');
        
        // Gestión de estados del periodo
        Route::post('/{periodo}/cambiar-estado', [PeriodoController::class, 'cambiarEstado'])->name('cambiar-estado');
        Route::post('/{periodo}/activar-preregistros', [PeriodoController::class, 'activarPreregistros'])
            ->name('activar-preregistros');
        Route::post('/{periodo}/cerrar-preregistros', [PeriodoController::class, 'cerrarPreregistros'])
            ->name('cerrar-preregistros');
        Route::post('/{periodo}/iniciar-periodo', [PeriodoController::class, 'iniciarPeriodo'])
            ->name('iniciar-periodo');
        Route::post('/{periodo}/finalizar-periodo', [PeriodoController::class, 'finalizarPeriodo'])
            ->name('finalizar-periodo');
    });

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: AULAS
    |--------------------------------------------------------------------------
    */
    Route::prefix('coordinador/aulas')->name('coordinador.aulas.')->group(function () {
        Route::get('/', [AulaController::class, 'index'])->name('index');
        Route::get('/crear', [AulaController::class, 'create'])->name('create');
        Route::post('/', [AulaController::class, 'store'])->name('store');
        Route::get('/{aula}/editar', [AulaController::class, 'edit'])->name('edit');
        Route::put('/{aula}', [AulaController::class, 'update'])->name('update');
        Route::delete('/{aula}', [AulaController::class, 'destroy'])->name('destroy');
        Route::post('/{aula}/toggle-disponible', [AulaController::class, 'toggleDisponible'])
            ->name('toggle-disponible');
    });

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: HORARIOS
    |--------------------------------------------------------------------------
    */
    Route::prefix('coordinador/horarios')->name('coordinador.horarios.')->group(function () {
        Route::get('/', [HorarioController::class, 'index'])->name('index');
        Route::get('/crear', [HorarioController::class, 'create'])->name('create');
        Route::post('/', [HorarioController::class, 'store'])->name('store');
        Route::get('/{horario}', [HorarioController::class, 'show'])->name('show');
        Route::get('/{horario}/editar', [HorarioController::class, 'edit'])->name('edit');
        Route::put('/{horario}', [HorarioController::class, 'update'])->name('update');
        Route::delete('/{horario}', [HorarioController::class, 'destroy'])->name('destroy');
        Route::post('/{horario}/toggle-activo', [HorarioController::class, 'toggleActivo'])
            ->name('toggle-activo');
    });

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: GRUPOS
    |--------------------------------------------------------------------------
    */
    Route::prefix('coordinador/grupos')->name('coordinador.grupos.')->group(function () {
        Route::get('/', [CoordinadorGrupoController::class, 'index'])->name('index');
        Route::get('/crear', [CoordinadorGrupoController::class, 'create'])->name('create');
        Route::post('/', [CoordinadorGrupoController::class, 'store'])->name('store');
        Route::get('/{id}', [CoordinadorGrupoController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [CoordinadorGrupoController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CoordinadorGrupoController::class, 'update'])->name('update');
        
        // Acciones específicas de grupos
        Route::post('/{id}/asignar-estudiante', [CoordinadorGrupoController::class, 'asignarEstudiante'])
            ->name('asignar-estudiante');
        Route::post('/{id}/remover-estudiante', [CoordinadorGrupoController::class, 'removerEstudiante'])
            ->name('remover-estudiante');
        Route::post('/{id}/cambiar-estado', [CoordinadorGrupoController::class, 'cambiarEstado'])
            ->name('cambiar-estado');
    });

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: PREREGISTROS
    |--------------------------------------------------------------------------
    */
    Route::prefix('coordinador/preregistros')->name('coordinador.preregistros.')->group(function () {
        // Vistas principales
        Route::get('/', [CoordinadorPreregistroController::class, 'index'])->name('index');
        Route::get('/demanda', [CoordinadorPreregistroController::class, 'demanda'])->name('demanda');
        Route::get('/estado/{estado}', [CoordinadorPreregistroController::class, 'porEstado'])->name('porEstado');
        Route::get('/{id}', [CoordinadorPreregistroController::class, 'show'])->name('show');
        
        // Gestión de preregistros
        Route::post('/{id}/asignar-grupo', [CoordinadorPreregistroController::class, 'asignarGrupo'])
            ->name('asignar-grupo');
        Route::post('/{id}/cambiar-estado', [CoordinadorPreregistroController::class, 'cambiarEstado'])
            ->name('cambiar-estado');
        Route::post('/{id}/cambiar-pago', [CoordinadorPreregistroController::class, 'cambiarEstadoPago'])
            ->name('cambiar-pago');
        Route::post('/{id}/cancelar', [CoordinadorPreregistroController::class, 'cancelarPreregistro'])
            ->name('cancelar');
        Route::post('/{id}/reactivar', [CoordinadorPreregistroController::class, 'reactivar'])
            ->name('reactivar');
        
        // Gestión de grupos
        Route::delete('/{preregistro}/quitar-grupo', [CoordinadorPreregistroController::class, 'quitarGrupo'])
            ->name('quitar-grupo');
        
        // Consultas y reportes
        Route::get('/estudiantes-por-nivel/{nivel}', [CoordinadorPreregistroController::class, 'obtenerEstudiantesPorNivel'])
            ->name('estudiantes-por-nivel');
    });

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: USUARIOS (ALUMNOS)
    |--------------------------------------------------------------------------
    */
     Route::prefix('coordinador/usuarios')->name('coordinador.usuarios.')->group(function () {
        // ESTA RUTA DEBE IR PRIMERO
        Route::get('/export', [UsuarioController::class, 'export'])->name('export');
        
        // Luego las demás rutas
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        Route::get('/{id}', [UsuarioController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [UsuarioController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UsuarioController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-status', [UsuarioController::class, 'toggleStatus'])
            ->name('toggle-status');
        Route::get('/{id}/historial', [UsuarioController::class, 'historial'])->name('historial');
        Route::get('/search/quick', [UsuarioController::class, 'search'])->name('search');
    });

    /*
    |--------------------------------------------------------------------------
    | MÓDULO: DOCUMENTOS
    |--------------------------------------------------------------------------
    */
    Route::prefix('coordinador/documentos')->name('coordinador.documentos.')->group(function () {
        // Panel principal
        Route::get('/', [DocumentoController::class, 'panel'])->name('panel');
        
        // Listas de grupo
        Route::get('/grupo/lista', [DocumentoController::class, 'listaGrupo'])->name('grupo.lista');
        Route::get('/grupo/{grupo}/lista-preview', [DocumentoController::class, 'listaGrupoPreview'])
            ->name('grupo.lista.preview');
        
        // Constancias
        Route::get('/constancia/{preregistro}', [DocumentoController::class, 'constanciaIndividual'])
            ->name('constancia');
        
        // Estadísticas
        Route::get('/estadisticas', [DocumentoController::class, 'reporteEstadisticas'])
            ->name('estadisticas');
        Route::get('/estadisticas/preview', [DocumentoController::class, 'reporteEstadisticasPreview'])
            ->name('estadisticas.preview');
    });

});

/*
|--------------------------------------------------------------------------
| Rutas Globales (para todos los usuarios autenticados)
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');