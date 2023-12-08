<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Producto;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Validated;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductoController extends Controller
{
    /**
     * Crea una nueva instancia de ProductoController.
     * Este middleware permite el acceso solo a los usuarios con el permiso 'administrar'
     * para las acciones 'index', 'show', 'store', 'update' y 'destroy'.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('can:administrar')->only(['index', 'show', 'store', 'update', 'destroy']);
    }

    /**
     * Muestra un listado de todos los productos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $productos = Producto::all();
            return ApiResponse::success('Listado de productos', 200, $productos);
        } catch (Exception $e) {
            return ApiResponse::error('Error inesperado: ', $e->getMessage(), 400);
        }
    }

    /**
     * Almacena un nuevo producto en el almacenamiento de la cafeteria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre_producto' => 'required|string|unique:productos,nombre_producto',
                'referencia' => 'required',
                'precio' => 'required|integer',
                'peso' => 'required|integer',
                'categoria' => 'required|string',
                'stock' => 'required|integer'
            ]);
            $producto = Producto::create($request->all());
            return ApiResponse::success('Producto creado correctamente', 201, $producto);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponse::error('Error de validación: ', 400, $errors);
        } catch (Exception $e) {
            return ApiResponse::error('Error inesperado: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Muestra el producto especificado.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            $producto = Producto::findOrFail($id);
            return ApiResponse::success('Producto obtenido correctamente', 200, $producto);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Producto no encontrado', 400);
        }
    }

    /**
     * Actualiza el producto especificado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $request->validate([
                'nombre_producto' => ['required', Rule::unique('productos')->ignore($id)],
                'referencia' => 'required|string',
                'precio' => 'required|integer',
                'peso' => 'required|integer',
                'categoria' => 'required|string',
                'stock' => 'required|integer'
            ]);
            $producto->update($request->all());
            return ApiResponse::success('Producto actualizado correctamente', 200, $producto);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Producto no encontrado', 400);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponse::error('Error de validación', 400, $errors);
        } catch (Exception $e) {
            return ApiResponse::error('Error inesperado: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Elimina el producto especificado del almacenamiento.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $producto->delete();
            return ApiResponse::success('Producto eliminado correctamente', 200, $producto);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Producto no encontrado', 400);
        } catch (Exception $e) {
            return ApiResponse::error('Error inesperado: ' . $e->getMessage(), 500);
        }
    }
}
