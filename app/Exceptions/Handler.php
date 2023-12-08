<?php

namespace App\Exceptions;

use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception)
    {

        // Si la excepción es una RouteNotFoundException, retornar un error de ruta no disponible.
        if ($exception instanceof RouteNotFoundException) {
            return ApiResponse::error('Token no proporcionado o inválido', 401);
        }
        
        // Si la excepción es una AuthorizationException, retornar un error de sin permisos.
        if ($exception instanceof AuthorizationException) {
            return ApiResponse::error('No cuentas con los permisos para acceder a esta ruta', 403);
        }

       
        return parent::render($request, $exception);
    }
}
