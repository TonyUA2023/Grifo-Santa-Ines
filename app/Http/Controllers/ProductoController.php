<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $productos = Producto::activo()->get();
        return view('productos.index', compact('productos'));
    }

    /**
     * Display the specified product.
     */
    public function show(Producto $producto)
    {
        if (!$producto->activo) {
            abort(404);
        }
        
        return view('productos.show', compact('producto'));
    }

    /**
     * Get product data for modal (AJAX).
     */
    public function getProducto($id)
    {
        try {
            $producto = Producto::activo()->findOrFail($id);
            
            return response()->json([
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'descripcion' => $producto->descripcion,
                'precio' => floatval($producto->precio),
                'unidad_medida' => $producto->unidad_medida,
                'imagen' => asset('images/productos/' . $producto->imagen),
                'caracteristicas' => $producto->caracteristicas ?: [],
                'stock' => intval($producto->stock),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Producto no encontrado'
            ], 404);
        }
    }
}