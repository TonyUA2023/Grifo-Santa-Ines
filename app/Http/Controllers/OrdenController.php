<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class OrdenController extends Controller
{
    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        try {
            // Verificar que el usuario esté autenticado
            if (!Auth::check()) {
                return redirect()->route('login')
                    ->with('error', 'Debes iniciar sesión para ver tus órdenes');
            }

            // Obtener el usuario autenticado
            $user = Auth::user();
            
            // Log para debugging
            Log::info('Usuario accediendo a ordenes: ' . $user->email);
            
            // Obtener las órdenes del usuario con relaciones
            $ordenes = Orden::where('user_id', $user->id)
                ->with(['detalles' => function($query) {
                    $query->with('producto');
                }])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            // Log cantidad de órdenes
            Log::info('Órdenes encontradas: ' . $ordenes->total());

            return view('ordenes.index', [
                'ordenes' => $ordenes
            ]);
            
        } catch (\Exception $e) {
            // Log del error
            Log::error('Error en OrdenController@index: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Crear una paginación vacía para evitar errores en la vista
            $ordenes = new LengthAwarePaginator(
                collect([]), // items vacíos
                0,           // total items
                10,          // items por página
                1,           // página actual
                [
                    'path' => request()->url(),
                ]
            );
            
            return view('ordenes.index', [
                'ordenes' => $ordenes
            ])->with('error', 'Ocurrió un error al cargar las órdenes. Por favor, intenta nuevamente.');
        }
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (!Auth::check()) {
                return redirect()->route('login')
                    ->with('error', 'Debes iniciar sesión para ver esta orden');
            }

            // Buscar la orden
            $orden = Orden::with(['detalles.producto', 'user'])->findOrFail($id);
            
            // Verificar que la orden pertenezca al usuario actual
            if ($orden->user_id !== Auth::id()) {
                Log::warning('Usuario ' . Auth::id() . ' intentó acceder a orden ' . $id . ' que no le pertenece');
                abort(403, 'No tienes permiso para ver esta orden.');
            }
            
            // Log para debugging
            Log::info('Usuario ' . Auth::user()->email . ' accediendo a orden #' . $orden->id);

            return view('ordenes.show', [
                'orden' => $orden
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Orden no encontrada: ' . $id);
            return redirect()->route('ordenes.index')
                ->with('error', 'La orden solicitada no existe.');
                
        } catch (\Exception $e) {
            Log::error('Error en OrdenController@show: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('ordenes.index')
                ->with('error', 'Ocurrió un error al cargar la orden. Por favor, intenta nuevamente.');
        }
    }

    /**
     * Get order statistics for the authenticated user
     */
    public function estadisticas()
    {
        try {
            $user = Auth::user();
            
            $estadisticas = [
                'total_ordenes' => Orden::where('user_id', $user->id)->count(),
                'ordenes_pendientes' => Orden::where('user_id', $user->id)->where('estado', 'pendiente')->count(),
                'ordenes_completadas' => Orden::where('user_id', $user->id)->where('estado', 'completado')->count(),
                'gasto_total' => Orden::where('user_id', $user->id)->sum('total'),
            ];
            
            return response()->json($estadisticas);
            
        } catch (\Exception $e) {
            Log::error('Error en estadísticas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener estadísticas'], 500);
        }
    }

    /**
     * Cancel an order (if it's still pending)
     */
    public function cancelar($id)
    {
        try {
            $orden = Orden::where('user_id', Auth::id())->findOrFail($id);
            
            // Solo se pueden cancelar órdenes pendientes
            if ($orden->estado !== 'pendiente') {
                return back()->with('error', 'Solo se pueden cancelar órdenes pendientes.');
            }
            
            // Actualizar estado
            $orden->estado = 'cancelado';
            $orden->save();
            
            // Restaurar stock de productos
            foreach ($orden->detalles as $detalle) {
                $producto = $detalle->producto;
                if ($producto) {
                    $producto->increment('stock', $detalle->cantidad);
                }
            }
            
            Log::info('Orden #' . $orden->id . ' cancelada por usuario ' . Auth::user()->email);
            
            return back()->with('success', 'La orden ha sido cancelada exitosamente.');
            
        } catch (\Exception $e) {
            Log::error('Error al cancelar orden: ' . $e->getMessage());
            return back()->with('error', 'No se pudo cancelar la orden.');
        }
    }
}