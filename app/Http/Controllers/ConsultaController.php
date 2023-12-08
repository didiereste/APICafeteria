<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Producto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultaController extends Controller
{
    /**
     * Crea una nueva instancia de ConsultaController.
     * Este middleware permite el acceso solo a los usuarios con el permiso 'consultar'
     * para las acciones 'masstock' y 'masvendido'.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('can:consultar')->only(['masstock', 'masvendido']);
    }
    
    /**
     * Obtiene el producto con mayor stock.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function masstock()
    {
        try{
            // Obtiene el producto con mayor stock ordenando de forma descendente.
            $productomasstock = DB::table('productos')
                                    ->orderBy('stock', 'desc')
                                    ->first();

            // Responde con un mensaje de éxito y los detalles del producto con más stock.
            return ApiResponse::success('Producto con más stock obtenido correctamente', 200, $productomasstock);
        }  catch(Exception $e){
            return ApiResponse::error('Error al obtener el producto con más stock', 500, $e->getMessage());
        }
        
    }

    /**
     * Obtiene el producto más vendido.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function masvendido()
    {

        try{
            // Obtiene el producto más vendido uniendo la tabla de productos con la de ventas.
            $productoMasVendido = DB::table('productos')
                                    ->join('ventas', 'productos.id', '=', 'ventas.producto_id')
                                    ->select('productos.id', 'productos.nombre_producto', DB::raw('SUM(ventas.cantidad) as total_ventas'))
                                    ->groupBy('productos.id', 'productos.nombre_producto')
                                    ->orderByDesc('total_ventas')
                                    ->first();

            // Responde con un mensaje de éxito y los detalles del producto más vendido.
            return ApiResponse::success('Producto más vendido encontrado correctamente', 200, $productoMasVendido);
        }catch(Exception $e){
            return ApiResponse::error('Error al obtener el producto más vendido', 500, $e->getMessage()); 
        }
        
    }
}
