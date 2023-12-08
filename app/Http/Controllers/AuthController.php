<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;


class AuthController extends Controller
{
    /**
     * Crea una nueva instancia de AuthController.
     * Este middleware permite el acceso a las rutas 'login' y 'register' sin autenticación.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Obtiene un JWT con las credenciales proporcionadas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        // Extrae las credenciales del cuerpo de la solicitud.
        $credentials = request(['email', 'password']);

        // Intenta autenticar al usuario y obtener un token.
        if (!$token = auth()->attempt($credentials)) {
            return ApiResponse::error('Credenciales incorrectas', 401);
        }

        // Responde con un mensaje de éxito y el token.
        return ApiResponse::success('Inicio de sesión exitoso', 200, $token);
    }

    /**
     * Obtiene el usuario autenticado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        // Retorna el usuario autenticado en formato JSON.
        return response()->json(auth()->user());
    }

    /**
     * Cierra la sesión del usuario (Invalida el token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        // Cierra la sesión del usuario y responde con un mensaje de éxito.
        auth()->logout();
        return ApiResponse::success('Cierre de sesión exitoso', 200);
    }

    /**
     * Refresca un token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        // Refresca el token y responde con la nueva información del token.
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Obtiene la estructura del array del token.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        // Responde con la estructura del token en formato JSON.
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Registra un nuevo usuario.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            // Valida los datos del formulario de registro.
            $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|string|email|max:100|unique:users,email',
                'password' => 'required|min:6',
            ]);

            // Crea una contraseña cifrada y registra al usuario.
            $encryptedPassword = Hash::make($request->input('password'));
            $user = User::create(array_merge($request->all(), ['password' => $encryptedPassword]));

            // Asigna automáticamente el rol de vendedor al nuevo usuario.
            $role = Role::find(2);
            $user->assignRole($role);

            // Responde con un mensaje de éxito y los detalles del usuario.
            return ApiResponse::success('El usuario se registró correctamente', 200, $user);
        } catch (ValidationException $e) {
            // Maneja errores de validación y responde con un mensaje de error.
            return ApiResponse::error('Error de validación', 400, $e->getMessage());
        }
    }
}
