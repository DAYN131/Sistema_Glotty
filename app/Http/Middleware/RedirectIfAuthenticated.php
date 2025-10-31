<?php
// app/Http/Middleware/RedirectIfAuthenticated.php
namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Redirigir según el tipo de usuario
                if ($guard === 'coordinador' || $user instanceof \App\Models\Coordinador) {
                    return redirect()->route('coordinador.dashboard');
                } elseif ($guard === 'profesor' || $user instanceof \App\Models\Profesor) {
                    return redirect()->route('profesor.dashboard');
                } else {
                    return redirect()->route('alumno.dashboard');
                }
            }
        }

        return $next($request);
    }
}