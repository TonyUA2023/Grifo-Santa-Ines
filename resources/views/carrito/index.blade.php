@extends('layouts.app')

@section('title', 'Carrito de Compras - Grifo Santa Ines')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Carrito de Compras</h1>

                @if(empty($carrito))
                    <div class="text-center py-12">
                        <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                        <p class="text-xl text-gray-500 mb-6">Tu carrito está vacío</p>
                        <a href="{{ route('productos.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Seguir comprando
                        </a>
                    </div>
                @else
                    <!-- Cart Table for Desktop -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Producto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Precio
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cantidad
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($carrito as $id => $item)
                                    <tr id="producto-{{ $id }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-16 w-16">
                                                    <img class="h-16 w-16 rounded-md object-cover" 
                                                         src="{{ asset('images/productos/' . $item['imagen']) }}" 
                                                         alt="{{ $item['nombre'] }}"
                                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/64x64?text={{ urlencode($item['nombre']) }}';">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $item['nombre'] }}
                                                    </div>
                                                    @if($item['unidad_medida'] != 'unidad')
                                                        <div class="text-sm text-gray-500">
                                                            Por {{ $item['unidad_medida'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">S/ {{ number_format($item['precio'], 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <button onclick="actualizarCantidad({{ $id }}, -1)" 
                                                        class="bg-gray-200 px-2 py-1 rounded-l hover:bg-gray-300">
                                                    <i class="fas fa-minus text-xs"></i>
                                                </button>
                                                <input type="number" 
                                                       id="cantidad-{{ $id }}"
                                                       value="{{ $item['cantidad'] }}" 
                                                       min="1" 
                                                       class="w-16 text-center border-t border-b border-gray-200 py-1"
                                                       onchange="actualizarCantidadDirecta({{ $id }}, this.value)">
                                                <button onclick="actualizarCantidad({{ $id }}, 1)" 
                                                        class="bg-gray-200 px-2 py-1 rounded-r hover:bg-gray-300">
                                                    <i class="fas fa-plus text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">
                                                S/ <span id="total-{{ $id }}">{{ number_format($item['precio'] * $item['cantidad'], 2) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button onclick="eliminarProducto({{ $id }})" 
                                                    class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Cart Items for Mobile -->
                    <div class="md:hidden space-y-4">
                        @foreach($carrito as $id => $item)
                            <div id="producto-mobile-{{ $id }}" class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-3">
                                    <img class="h-16 w-16 rounded-md object-cover mr-4" 
                                         src="{{ asset('images/productos/' . $item['imagen']) }}" 
                                         alt="{{ $item['nombre'] }}"
                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/64x64?text={{ urlencode($item['nombre']) }}';">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">{{ $item['nombre'] }}</h3>
                                        <p class="text-sm text-gray-600">S/ {{ number_format($item['precio'], 2) }} 
                                            @if($item['unidad_medida'] != 'unidad')
                                                x {{ $item['unidad_medida'] }}
                                            @endif
                                        </p>
                                    </div>
                                    <button onclick="eliminarProducto({{ $id }})" 
                                            class="text-red-600 hover:text-red-900 ml-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <button onclick="actualizarCantidad({{ $id }}, -1)" 
                                                class="bg-gray-200 px-2 py-1 rounded-l hover:bg-gray-300">
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                        <input type="number" 
                                               id="cantidad-mobile-{{ $id }}"
                                               value="{{ $item['cantidad'] }}" 
                                               min="1" 
                                               class="w-16 text-center border-t border-b border-gray-200 py-1"
                                               onchange="actualizarCantidadDirecta({{ $id }}, this.value)">
                                        <button onclick="actualizarCantidad({{ $id }}, 1)" 
                                                class="bg-gray-200 px-2 py-1 rounded-r hover:bg-gray-300">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                    <div class="font-semibold text-gray-900">
                                        S/ <span id="total-mobile-{{ $id }}">{{ number_format($item['precio'] * $item['cantidad'], 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Cart Summary -->
                    <div class="mt-8 border-t pt-8">
                        <div class="max-w-md ml-auto">
                            <div class="space-y-2">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal:</span>
                                    <span>S/ <span id="subtotal">{{ number_format($subtotal, 2) }}</span></span>
                                </div>
                                <div class="flex justify-between text-xl font-semibold text-gray-900 pt-2 border-t">
                                    <span>Total:</span>
                                    <span>S/ <span id="total">{{ number_format($total, 2) }}</span></span>
                                </div>
                            </div>

                            <div class="mt-6 space-y-3">
                                <a href="{{ route('productos.index') }}" 
                                   class="block text-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition duration-200">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Seguir comprando
                                </a>
                                
                                @auth
                                    <a href="{{ route('checkout.index') }}" 
                                       class="block text-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                                        Confirmar compra
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" 
                                       class="block text-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                                        Iniciar sesión para comprar
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function actualizarCantidad(productoId, cambio) {
        const inputDesktop = document.getElementById(`cantidad-${productoId}`);
        const inputMobile = document.getElementById(`cantidad-mobile-${productoId}`);
        const input = inputDesktop || inputMobile;
        
        const nuevaCantidad = parseInt(input.value) + cambio;
        
        if (nuevaCantidad >= 1) {
            actualizarCantidadDirecta(productoId, nuevaCantidad);
        }
    }

    function actualizarCantidadDirecta(productoId, cantidad) {
        cantidad = parseInt(cantidad);
        
        if (cantidad < 1) {
            cantidad = 1;
        }

        fetch('{{ route("carrito.actualizar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                producto_id: productoId,
                cantidad: cantidad
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update quantity inputs
                const inputDesktop = document.getElementById(`cantidad-${productoId}`);
                const inputMobile = document.getElementById(`cantidad-mobile-${productoId}`);
                if (inputDesktop) inputDesktop.value = cantidad;
                if (inputMobile) inputMobile.value = cantidad;
                
                // Update cart
                actualizarVistaCarrito();
                updateCartCount(data.carrito_count);
            } else {
                showToast(data.message, 'error');
                // Reset to previous value
                actualizarVistaCarrito();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al actualizar el carrito', 'error');
        });
    }

    function eliminarProducto(productoId) {
        if (!confirm('¿Estás seguro de eliminar este producto del carrito?')) {
            return;
        }

        fetch(`{{ route('carrito.eliminar', '') }}/${productoId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove product from view
                const productoDesktop = document.getElementById(`producto-${productoId}`);
                const productoMobile = document.getElementById(`producto-mobile-${productoId}`);
                
                if (productoDesktop) productoDesktop.remove();
                if (productoMobile) productoMobile.remove();
                
                showToast(data.message);
                updateCartCount(data.carrito_count);
                
                // If cart is empty, reload page
                if (data.carrito_count === 0) {
                    window.location.reload();
                } else {
                    actualizarVistaCarrito();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al eliminar el producto', 'error');
        });
    }

    function actualizarVistaCarrito() {
        // This would normally recalculate totals via AJAX
        // For simplicity, we'll reload the page
        window.location.reload();
    }
</script>
@endpush