@extends('layouts.app')

@section('title', 'Mis Órdenes - Grifo Santa Ines')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Mensajes de sesión -->
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">¡Éxito!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">¡Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Mis Órdenes</h1>
                    <a href="{{ route('productos.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Nueva Compra
                    </a>
                </div>

                <!-- Verificar si hay órdenes -->
                @if(isset($ordenes) && $ordenes->count() > 0)
                    <!-- Vista Desktop -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        # Orden
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Productos
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($ordenes as $orden)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                #{{ str_pad($orden->id, 6, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $orden->created_at->format('d/m/Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $orden->created_at->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                @if($orden->detalles && $orden->detalles->count() > 0)
                                                    {{ $orden->detalles->count() }} 
                                                    {{ $orden->detalles->count() == 1 ? 'producto' : 'productos' }}
                                                @else
                                                    Sin productos
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                S/ {{ number_format($orden->total, 2) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $estadoClases = [
                                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                                    'procesando' => 'bg-blue-100 text-blue-800',
                                                    'completado' => 'bg-green-100 text-green-800',
                                                    'cancelado' => 'bg-red-100 text-red-800',
                                                ];
                                                $estadoTextos = [
                                                    'pendiente' => 'Pendiente',
                                                    'procesando' => 'Procesando',
                                                    'completado' => 'Completado',
                                                    'cancelado' => 'Cancelado',
                                                ];
                                                $claseEstado = $estadoClases[$orden->estado] ?? 'bg-gray-100 text-gray-800';
                                                $textoEstado = $estadoTextos[$orden->estado] ?? $orden->estado;
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $claseEstado }}">
                                                {{ $textoEstado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('ordenes.show', $orden->id) }}" 
                                               class="text-blue-600 hover:text-blue-900 mr-3">
                                                Ver detalles
                                            </a>
                                            @if($orden->estado == 'pendiente')
                                                <form action="{{ route('ordenes.cancelar', $orden->id) }}" 
                                                      method="POST" 
                                                      class="inline"
                                                      onsubmit="return confirm('¿Estás seguro de cancelar esta orden?');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        Cancelar
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Vista Mobile -->
                    <div class="md:hidden space-y-4">
                        @foreach($ordenes as $orden)
                            <div class="bg-gray-50 rounded-lg p-4 shadow">
                                <!-- Header de la orden -->
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">
                                            Orden #{{ str_pad($orden->id, 6, '0', STR_PAD_LEFT) }}
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            {{ $orden->created_at->format('d/m/Y h:i A') }}
                                        </p>
                                    </div>
                                    @php
                                        $estadoClases = [
                                            'pendiente' => 'bg-yellow-100 text-yellow-800',
                                            'procesando' => 'bg-blue-100 text-blue-800',
                                            'completado' => 'bg-green-100 text-green-800',
                                            'cancelado' => 'bg-red-100 text-red-800',
                                        ];
                                        $claseEstado = $estadoClases[$orden->estado] ?? 'bg-gray-100 text-gray-800';
                                        $textoEstado = $estadoTextos[$orden->estado] ?? $orden->estado;
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $claseEstado }}">
                                        {{ $textoEstado }}
                                    </span>
                                </div>

                                <!-- Detalles de la orden -->
                                <div class="space-y-2 mb-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Productos:</span>
                                        <span class="text-sm font-medium">
                                            @if($orden->detalles && $orden->detalles->count() > 0)
                                                {{ $orden->detalles->count() }} 
                                                {{ $orden->detalles->count() == 1 ? 'producto' : 'productos' }}
                                            @else
                                                Sin productos
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Total:</span>
                                        <span class="text-lg font-semibold text-gray-900">
                                            S/ {{ number_format($orden->total, 2) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="flex justify-between items-center pt-3 border-t">
                                    <a href="{{ route('ordenes.show', $orden->id) }}" 
                                       class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                                        Ver detalles →
                                    </a>
                                    @if($orden->estado == 'pendiente')
                                        <form action="{{ route('ordenes.cancelar', $orden->id) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('¿Estás seguro de cancelar esta orden?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                                Cancelar orden
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginación -->
                    @if($ordenes->hasPages())
                        <div class="mt-6">
                            {{ $ordenes->links() }}
                        </div>
                    @endif
                @else
                    <!-- Sin órdenes -->
                    <div class="text-center py-12">
                        <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                        <p class="text-xl text-gray-500 mb-6">No tienes órdenes aún</p>
                        <p class="text-gray-400 mb-8">¡Realiza tu primera compra y aparecerá aquí!</p>
                        <a href="{{ route('productos.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Ir a Comprar
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Información adicional -->
        @if(isset($ordenes) && $ordenes->count() > 0)
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Las órdenes pendientes pueden ser canceladas en cualquier momento. 
                            Una vez procesada, la orden no puede ser modificada.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-cerrar mensajes de alerta después de 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        });
    }, 5000);
</script>
@endpush