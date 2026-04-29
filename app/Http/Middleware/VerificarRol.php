<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Si el usuario no está autenticado, redirigir a /login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Si está autenticado, verificar su rol
        $usuario = Auth::user(); // Este objeto es instancia de App\Models\Usuario

        if (!in_array($usuario->rol, $roles)) {
            // 3. Si su rol no está entre los permitidos, abortar con 403
            abort(403, 'No tienes permiso para acceder a esta sección');
        }

        // 4. Si el rol es válido, continuar la petición
        return $next($request);
    }
}
