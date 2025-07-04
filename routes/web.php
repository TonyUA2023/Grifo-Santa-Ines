<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrdenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ruta principal - redirige a productos
Route::get('/', function () {
    return redirect()->route('productos.index');
});

// Rutas de productos
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');
Route::get('/api/productos/{id}', [ProductoController::class, 'getProducto'])->name('productos.get');

// Rutas del carrito
Route::prefix('carrito')->name('carrito.')->group(function () {
    Route::get('/', [CarritoController::class, 'index'])->name('index');
    Route::post('/agregar', [CarritoController::class, 'agregar'])->name('agregar');
    Route::post('/actualizar', [CarritoController::class, 'actualizar'])->name('actualizar');
    Route::delete('/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('eliminar');
    Route::post('/vaciar', [CarritoController::class, 'vaciar'])->name('vaciar');
});

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::get('/api/consultar-dni/{dni}', [AuthController::class, 'consultarDni'])->name('consultar.dni');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/procesar', [CheckoutController::class, 'procesar'])->name('checkout.procesar');
    
    // Órdenes
    Route::get('/mis-ordenes', [OrdenController::class, 'index'])->name('ordenes.index');
    Route::get('/ordenes/{orden}', [OrdenController::class, 'show'])->name('ordenes.show');
});

Route::put('/ordenes/{orden}/cancelar', [OrdenController::class, 'cancelar'])
    ->name('ordenes.cancelar')
    ->middleware('auth');