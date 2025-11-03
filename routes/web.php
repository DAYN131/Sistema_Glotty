<?php
use App\Http\Controllers\AuthController;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\PeriodoController;

// Rutas públicas
Route::get('/', function () {
   return view('login');
});

Route::get('/login', function () {
   return view('login');
})->name('login'); // ← ESTE ES EL NOMBRE QUE FALTA


Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post'); // ← CAMBIADO A 'register'


Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Rutas protegidas
Route::middleware(['auth:web'])->group(function () {
    Route::get('/alumno', function () {
        return view('alumno', [
            'nombre_completo' => Auth::user()->nombre_completo,
            'identificador' => Auth::user()->numero_control
        ]);
    })->name('alumno.dashboard');
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
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');