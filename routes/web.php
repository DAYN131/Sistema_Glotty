<?php
use App\Http\Controllers\AuthController;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\PeriodoController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\PreregistroController;
use App\Http\Controllers\CoordinadorPreregistroController;
use App\Http\Controllers\GrupoController;

// Rutas públicas
Route::get('/', function () {
   return view('login');
});

Route::get('/login', function () {
   return view('login');
})->name('login');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Rutas protegidas
Route::middleware(['auth:web'])->group(function () {
    Route::get('/alumno', function () {
        return view('alumno', [
            'nombre_completo' => Auth::user()->nombre_completo,
            'identificador' => Auth::user()->numero_control
        ]);
    })->name('alumno.dashboard');

    // Rutas de preregistro para alumnos
    Route::prefix('alumno/preregistro')->name('alumno.preregistro.')->group(function () {
        Route::get('/', [PreregistroController::class, 'index'])->name('index');
        Route::get('/crear', [PreregistroController::class, 'create'])->name('create');
        Route::post('/', [PreregistroController::class, 'store'])->name('store');
        Route::get('/{id}', [PreregistroController::class, 'show'])->name('show');
        Route::post('/{id}/cancelar', [PreregistroController::class, 'cancel'])->name('cancel');
    });
});

Route::middleware(['auth:profesor'])->group(function () {
    Route::get('/profesor', function () {
        $profesor = Auth::guard('profesor')->user();

        return view('profesor', [
            'nombre_completo' => $profesor->nombre_profesor . ' ' . $profesor->apellidos_profesor,
            'rfc_profesor' => $profesor->rfc_profesor
        ]);
    })->name('profesor.dashboard');
});

Route::middleware(['auth:coordinador'])->group(function () {
    Route::get('/coordinador', [AuthController::class, 'coordinadorDashboard'])
        ->name('coordinador.dashboard');

    // Gestión de Profesores - RUTAS COMPLETAS
    Route::prefix('coordinador/profesores')->group(function () {
        Route::get('/', [ProfesorController::class, 'index'])
            ->name('coordinador.profesores.index');
        Route::get('/crear', [ProfesorController::class, 'create'])
            ->name('coordinador.profesores.create');
        Route::post('/', [ProfesorController::class, 'store'])
            ->name('coordinador.profesores.store');
        Route::get('/{id}/editar', [ProfesorController::class, 'edit'])
            ->name('coordinador.profesores.edit');
        Route::put('/{id}', [ProfesorController::class, 'update'])
            ->name('coordinador.profesores.update');
        Route::delete('/{id}', [ProfesorController::class, 'destroy'])
            ->name('coordinador.profesores.destroy');
    });

    // Gestión de Periodos
    Route::prefix('coordinador/periodos')->group(function () {
        Route::get('/', [PeriodoController::class, 'index'])
            ->name('coordinador.periodos.index');
        Route::get('/crear', [PeriodoController::class, 'create'])
            ->name('coordinador.periodos.create');
        Route::post('/', [PeriodoController::class, 'store'])
            ->name('coordinador.periodos.store');
        Route::get('/{id}/editar', [PeriodoController::class, 'edit'])
            ->name('coordinador.periodos.edit');
        Route::put('/{id}', [PeriodoController::class, 'update'])
            ->name('coordinador.periodos.update');
        Route::delete('/{id}', [PeriodoController::class, 'destroy'])
            ->name('coordinador.periodos.destroy');
    });

    // Gestión de Aulas
    Route::prefix('coordinador/aulas')->group(function () {
        Route::get('/', [AulaController::class, 'index'])
            ->name('coordinador.aulas.index');
        Route::get('/crear', [AulaController::class, 'create'])
            ->name('coordinador.aulas.create');
        Route::post('/', [AulaController::class, 'store'])
            ->name('coordinador.aulas.store');
        Route::get('/{id_aula}/editar', [AulaController::class, 'edit'])
            ->name('coordinador.aulas.edit');
        Route::put('/{id_aula}', [AulaController::class, 'update'])
            ->name('coordinador.aulas.update');
        Route::delete('/{id_aula}', [AulaController::class, 'destroy'])
            ->name('coordinador.aulas.destroy');
    });

    // Gestión de Horarios
    Route::prefix('coordinador/horarios')->group(function () {
        // Rutas estándar del CRUD
        Route::get('/', [HorarioController::class, 'index'])
            ->name('coordinador.horarios.index');
        Route::get('/crear', [HorarioController::class, 'create'])
            ->name('coordinador.horarios.create');
        Route::post('/', [HorarioController::class, 'store'])
            ->name('coordinador.horarios.store');
        Route::get('/{id}/editar', [HorarioController::class, 'edit'])
            ->name('coordinador.horarios.edit');
        Route::put('/{id}', [HorarioController::class, 'update'])
            ->name('coordinador.horarios.update');
        Route::delete('/{id}', [HorarioController::class, 'destroy'])
            ->name('coordinador.horarios.destroy');
        Route::put('/{id}/toggle-activo', [HorarioController::class, 'toggleActivo'])
            ->name('coordinador.horarios.toggleActivo');

        // Rutas para Soft Deletes (Papelera)
        Route::get('/eliminados', [HorarioController::class, 'eliminados'])
            ->name('coordinador.horarios.eliminados');
        Route::put('/{id}/restore', [HorarioController::class, 'restore'])
            ->name('coordinador.horarios.restore');
        Route::delete('/{id}/force-delete', [HorarioController::class, 'forceDelete'])
            ->name('coordinador.horarios.forceDelete');
    });

    // Gestión de Grupos
    Route::prefix('coordinador/grupos')->name('coordinador.grupos.')->group(function () {
        Route::get('/', [GrupoController::class, 'index'])
            ->name('index');
        Route::get('/crear', [GrupoController::class, 'create'])
            ->name('create');
        Route::post('/', [GrupoController::class, 'store'])
            ->name('store');
        Route::get('/{grupo}/editar', [GrupoController::class, 'edit'])
            ->name('edit');
        Route::put('/{grupo}', [GrupoController::class, 'update'])
            ->name('update');
        Route::delete('/{grupo}', [GrupoController::class, 'destroy'])
            ->name('destroy');

        // Rutas para Soft Deletes (Papelera)
        Route::get('/eliminados', [GrupoController::class, 'eliminados'])
            ->name('eliminados');
        Route::put('/{grupo}/restaurar', [GrupoController::class, 'restore'])
            ->name('restore');
        Route::delete('/{grupo}/eliminar-permanente', [GrupoController::class, 'forceDelete'])
            ->name('forceDelete');
    });


    Route::prefix('coordinador/preregistros')->name('coordinador.preregistros.')->group(function () {
    // PÁGINA PRINCIPAL - Análisis de demanda
    Route::get('/demanda', [CoordinadorPreregistroController::class, 'demanda'])->name('demanda');
    
    // LISTA DETALLADA - Para gestión individual
    Route::get('/', [CoordinadorPreregistroController::class, 'index'])->name('index');
    Route::get('/estado/{estado}', [CoordinadorPreregistroController::class, 'porEstado'])->name('porEstado');
    
    // En routes/web.php, dentro del grupo de preregistros del coordinador:
    Route::post('/crear-grupo-rapido', [CoordinadorPreregistroController::class, 'crearGrupoRapido'])
    ->name('coordinador.preregistros.crearGrupoRapido');
    
    // GESTIÓN INDIVIDUAL
    Route::get('/{id}', [CoordinadorPreregistroController::class, 'show'])->name('show');
    Route::post('/{id}/asignar-grupo', [CoordinadorPreregistroController::class, 'asignarGrupo'])->name('asignarGrupo');
    Route::post('/{id}/cambiar-estado', [CoordinadorPreregistroController::class, 'cambiarEstado'])->name('cambiarEstado');
    });

    Route::get('/coordinador/preregistros/estudiantes-por-nivel/{nivel}', 
    [CoordinadorPreregistroController::class, 'obtenerEstudiantesPorNivel'])
    ->name('coordinador.preregistros.estudiantesPorNivel');


});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');