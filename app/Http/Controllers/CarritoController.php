<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    /**
     * Display the cart.
     */
    public function index()
    {
        $carrito = session()->get('carrito', []);
        $subtotal = 0;

        foreach ($carrito as $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
        }

        $total = $subtotal; // Puedes agregar impuestos aquí si es necesario

        return view('carrito.index', compact('carrito', 'subtotal', 'total'));
    }

    /**
     * Add product to cart.
     */
    public function agregar(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        $producto = Producto::findOrFail($request->producto_id);
        
        // Verificar stock
        if ($producto->stock < $request->cantidad) {
            return response()->json([
                'success' => false,
                'message' => 'No hay suficiente stock disponible'
            ], 400);
        }

        $carrito = session()->get('carrito', []);

        // Si el producto ya existe en el carrito, actualizar cantidad
        if (isset($carrito[$producto->id])) {
            $nuevaCantidad = $carrito[$producto->id]['cantidad'] + $request->cantidad;
            
            if ($producto->stock < $nuevaCantidad) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente stock disponible'
                ], 400);
            }
            
            $carrito[$producto->id]['cantidad'] = $nuevaCantidad;
        } else {
            // Agregar nuevo producto al carrito
            $carrito[$producto->id] = [
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'cantidad' => $request->cantidad,
                'imagen' => $producto->imagen,
                'unidad_medida' => $producto->unidad_medida
            ];
        }

        session()->put('carrito', $carrito);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'carrito_count' => count($carrito),
            'carrito_total' => array_sum(array_map(function($item) {
                return $item['precio'] * $item['cantidad'];
            }, $carrito))
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function actualizar(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:0'
        ]);

        $carrito = session()->get('carrito', []);
        
        if ($request->cantidad == 0) {
            // Eliminar del carrito
            unset($carrito[$request->producto_id]);
        } else {
            // Verificar stock
            $producto = Producto::findOrFail($request->producto_id);
            
            if ($producto->stock < $request->cantidad) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente stock disponible'
                ], 400);
            }
            
            if (isset($carrito[$request->producto_id])) {
                $carrito[$request->producto_id]['cantidad'] = $request->cantidad;
            }
        }

        session()->put('carrito', $carrito);

        return response()->json([
            'success' => true,
            'message' => 'Carrito actualizado',
            'carrito_count' => count($carrito),
            'carrito_total' => array_sum(array_map(function($item) {
                return $item['precio'] * $item['cantidad'];
            }, $carrito))
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function eliminar($id)
    {
        $carrito = session()->get('carrito', []);
        
        if (isset($carrito[$id])) {
            unset($carrito[$id]);
            session()->put('carrito', $carrito);
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del carrito',
            'carrito_count' => count($carrito)
        ]);
    }

    /**
     * Clear the cart.
     */
    public function vaciar()
    {
        session()->forget('carrito');
        
        return response()->json([
            'success' => true,
            'message' => 'Carrito vaciado'
        ]);
    }
}