<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page.
     */
    public function index()
    {
        try {
            // Obtener el carrito de la sesión
            $carrito = session()->get('carrito', []);
            
            // Verificar si el carrito está vacío
            if (empty($carrito)) {
                return redirect()->route('carrito.index')
                    ->with('warning', 'Tu carrito está vacío');
            }

            // Inicializar variables
            $items = [];
            $subtotal = 0;

            // Procesar cada item del carrito
            foreach ($carrito as $productoId => $itemCarrito) {
                // Buscar el producto en la base de datos
                $producto = Producto::find($productoId);
                
                // Solo procesar si el producto existe y está activo
                if ($producto && $producto->activo) {
                    // Crear el item con toda la información necesaria
                    $item = [
                        'id' => $productoId,
                        'nombre' => $itemCarrito['nombre'] ?? $producto->nombre,
                        'precio' => floatval($itemCarrito['precio'] ?? $producto->precio),
                        'cantidad' => intval($itemCarrito['cantidad'] ?? 1),
                        'imagen' => $itemCarrito['imagen'] ?? $producto->imagen,
                        'unidad_medida' => $itemCarrito['unidad_medida'] ?? $producto->unidad_medida,
                        'total' => 0
                    ];
                    
                    // Calcular el total del item
                    $item['total'] = $item['precio'] * $item['cantidad'];
                    
                    // Agregar al array de items
                    $items[] = $item;
                    
                    // Sumar al subtotal
                    $subtotal += $item['total'];
                }
            }

            // Verificar si hay items válidos después del procesamiento
            if (empty($items)) {
                return redirect()->route('carrito.index')
                    ->with('error', 'No hay productos válidos en tu carrito');
            }

            // Calcular el total (por ahora igual al subtotal)
            $total = $subtotal;

            // Retornar la vista con los datos
            return view('checkout.index', [
                'items' => $items,
                'subtotal' => $subtotal,
                'total' => $total
            ]);

        } catch (\Exception $e) {
            Log::error('Error en checkout: ' . $e->getMessage());
            return redirect()->route('carrito.index')
                ->with('error', 'Ocurrió un error al procesar tu pedido. Por favor, intenta nuevamente.');
        }
    }

    /**
     * Process the checkout.
     */
    public function procesar(Request $request)
    {
        $request->validate([
            'notas' => 'nullable|string|max:500'
        ]);

        $carrito = session()->get('carrito', []);
        
        if (empty($carrito)) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío');
        }

        DB::beginTransaction();

        try {
            // Verificar stock disponible
            foreach ($carrito as $productoId => $item) {
                $producto = Producto::findOrFail($productoId);
                
                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("No hay suficiente stock de {$producto->nombre}. Stock disponible: {$producto->stock}");
                }
            }

            // Crear la orden
            $subtotal = 0;
            foreach ($carrito as $item) {
                $subtotal += floatval($item['precio']) * intval($item['cantidad']);
            }

            $orden = Orden::create([
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'total' => $subtotal, // Aquí puedes agregar impuestos
                'estado' => 'pendiente',
                'notas' => $request->notas
            ]);

            // Crear los detalles de la orden y actualizar stock
            foreach ($carrito as $productoId => $item) {
                $producto = Producto::findOrFail($productoId);
                
                // Crear detalle de orden
                OrdenDetalle::create([
                    'orden_id' => $orden->id,
                    'producto_id' => $productoId,
                    'cantidad' => intval($item['cantidad']),
                    'precio_unitario' => floatval($item['precio']),
                    'precio_total' => floatval($item['precio']) * intval($item['cantidad'])
                ]);

                // Actualizar stock
                $producto->decrement('stock', intval($item['cantidad']));
            }

            // Limpiar el carrito
            session()->forget('carrito');

            DB::commit();

            return redirect()->route('ordenes.show', $orden)
                ->with('success', '¡Orden creada exitosamente! Tu pedido está siendo procesado.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar orden: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }
}