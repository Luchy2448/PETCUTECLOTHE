<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder al panel de administración.');
        }

        // Verificar que el usuario sea administrador
        // Puedes usar un campo 'is_admin' en la tabla users o un rol
        $user = Auth::user();
        
        // Verificar si es admin por el email o un campo is_admin
        if ($user->is_admin ?? false || $user->email === 'admin@petcute.com') {
            return $next($request);
        }

        // Si no es admin, redirigir al home con mensaje
        return redirect()->route('home')
            ->with('error', 'No tienes permisos para acceder al panel de administración.');
    }
}
