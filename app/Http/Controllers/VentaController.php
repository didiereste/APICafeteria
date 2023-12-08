<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Producto;
use App\Models\Venta;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    /**
     * Crea una nueva instancia de VentaController.
     * Este middleware permite el acceso solo a los usuarios con el permiso 'vender'
     * para la acción 'venta'.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('can:vender')->only('venta');
    }

    /**
     * Procesa una venta para un producto específico.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function venta(Request $request, string $id)
    {
        try {
            // Buscar el producto por su ID
            $producto = Producto::findOrFail($id);

            // Verificar si hay suficientes productos en stock
            $cantidadEnStock = $producto->stock;
            if ($cantidadEnStock <= 0) {
                return ApiResponse::error('No hay productos en stock para realizar la venta', 400);
            }

            // Validar la cantidad de productos a vender
            $request->validate([
                "cantidad" => "required|integer|min:1"
            ]);

            // Obtener precio del producto
            $precioProducto = $producto->precio;

            // Verificar si hay suficientes productos disponibles
            if ($cantidadEnStock < $request->cantidad) {
                return ApiResponse::error('No hay suficientes productos de este tipo', 400);
            }

            // Realizar la venta
            $venta = new Venta();
            $totalVenta = $precioProducto * $request->cantidad;

            // Actualizar el stock del producto
            $producto->stock -= $request->cantidad;
            $producto->save();

            // Registrar la venta
            $venta->cantidad = $request->cantidad;
            $venta->total_venta = $totalVenta;
            $venta->producto_id = $id;
            $venta->save();

            // Devolver una respuesta exitosa con los detalles de la venta
            return ApiResponse::success('La venta se ha realizado con éxito', 200, $venta);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Producto no encontrado', 404);
        } catch (ValidationException $e) {
            return ApiResponse::error('Error de validación: ' . $e->getMessage(), 400);
        } catch (QueryException $e) {
            return ApiResponse::error('Error en base de datos: ' . $e->getMessage(), 500);
        } catch (Exception $e) {
            return ApiResponse::error('Error inesperado: ' . $e->getMessage(), 500);
        }
    }
}
