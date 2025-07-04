@extends('layouts.app')

@section('title', 'Checkout - Grifo Santa Ines')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Confirmar Pedido</h1>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Order Details -->
                    <div class="lg:col-span-2">
                        <!-- Customer Information -->
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Información del Cliente</h2>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Nombre completo</p>
                                        <p class="font-medium">{{ Auth::user()->full_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">DNI</p>
                                        <p class="font-medium">{{ Auth::user()->dni }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Email</p>
                                        <p class="font-medium">{{ Auth::user()->email }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Teléfono</p>
                                        <p class="font-medium">{{ Auth::user()->telefono ?: 'No especificado' }}</p>
                                    </div>
                                    @if(Auth::user()->direccion)
                                    <div class="md:col-span-2">
                                        <p class="text-sm text-gray-600">Dirección</p>
                                        <p class="font-medium">
                                            {{ Auth::user()->direccion }}
                                            @if(Auth::user()->distrito)
                                                , {{ Auth::user()->distrito }}
                                            @endif
                                            @if(Auth::user()->provincia)
                                                , {{ Auth::user()->provincia }}
                                            @endif
                                            @if(Auth::user()->departamento)
                                                , {{ Auth::user()->departamento }}
                                            @endif
                                        </p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Productos</h2>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="space-y-4">
                                    @forelse($items as $item)
                                        <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-0">
                                            <div class="flex items-center">
                                                <img class="h-16 w-16 rounded object-cover mr-4" 
                                                     src="{{ asset('images/productos/' . $item['imagen']) }}" 
                                                     alt="{{ $item['nombre'] }}"
                                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/64x64?text={{ urlencode($item['nombre']) }}';">
                                                <div>
                                                    <h3 class="font-medium text-gray-900">{{ $item['nombre'] }}</h3>
                                                    <p class="text-sm text-gray-600">
                                                        {{ $item['cantidad'] }} 
                                                        @if($item['unidad_medida'] != 'unidad')
                                                            {{ $item['cantidad'] > 1 ? Str::plural($item['unidad_medida']) : $item['unidad_medida'] }}
                                                        @else
                                                            {{ $item['cantidad'] > 1 ? 'unidades' : 'unidad' }}
                                                        @endif
                                                        × S/ {{ number_format($item['precio'], 2) }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-medium text-gray-900">S/ {{ number_format($item['total'], 2) }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 text-center py-4">No hay productos en el carrito</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Notas adicionales (Opcional)</h2>
                            <form id="checkoutForm" action="{{ route('checkout.procesar') }}" method="POST">
                                @csrf
                                <textarea name="notas" 
                                          rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="¿Alguna instrucción especial para tu pedido?"></textarea>
                            </form>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-gray-50 rounded-lg p-6 sticky top-4">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Resumen del Pedido</h2>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal</span>
                                    <span>S/ {{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-xl font-semibold text-gray-900 pt-2 border-t">
                                    <span>Total</span>
                                    <span>S/ {{ number_format($total, 2) }}</span>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <button type="submit" 
                                        form="checkoutForm"
                                        class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Confirmar Pedido
                                </button>
                                
                                <a href="{{ route('carrito.index') }}" 
                                   class="block text-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition duration-200">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Volver al Carrito
                                </a>
                            </div>

                            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Al confirmar el pedido, aceptas nuestros términos y condiciones. 
                                    El pago se realizará al momento de recoger tu pedido.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Confirm before submitting
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (confirm('¿Estás seguro de confirmar este pedido?')) {
            this.submit();
        }
    });
</script>
@endpush