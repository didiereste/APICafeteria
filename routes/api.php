<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Este grupo de rutas está protegido por el middleware 'api' y utiliza el prefijo 'auth'
Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function () {
    
    Route::post('login',[AuthController::class, 'login']);
    Route::post('logout',[AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me',  [AuthController::class, 'me']);
    Route::post('register', [AuthController::class, 'register']);
});



Route::middleware(['auth:api'])->group(function () {
    
    Route::apiResource('/productos',ProductoController::class);
    Route::post('/producto/{id}/venta',[VentaController::class, 'venta']);
    Route::get('/producto/masstock',[ConsultaController::class, 'masstock']);
    Route::get('/producto/masvendido',[ConsultaController::class, 'masvendido']);
});



   

