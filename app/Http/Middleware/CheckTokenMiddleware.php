<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


class CheckTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Intenta ejecutar la siguiente capa del middleware (autenticación)
            return $next($request);
        } catch (UnauthorizedHttpException $exception) {
            // Manejar la excepción de falta de token aquí
            return response()->json(['error' => 'Token no proporcionado o inválido'], 401);
        }
    }
}
