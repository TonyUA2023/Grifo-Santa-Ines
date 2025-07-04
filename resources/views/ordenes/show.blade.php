@extends('layouts.app')

@section('title', 'Orden #' . str_pad($orden->id, 6, '0', STR_PAD_LEFT) . ' - Grifo Santa Ines')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">¡Éxito!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            Orden #{{ str_pad($orden->id, 6, '0', STR_PAD_LEFT) }}
                        </h1>
                        <p class="text-gray-600 mt-2">
                            Realizada el {{ $orden->created_at->format('d/m/Y') }} a las {{ $orden->created_at->format('h:i A') }}
                        </p>
                    </div>
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
                    @endphp
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $estadoClases[$orden->estado] }}">
                        {{ $estadoTextos[$orden->estado] }}
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Order Details -->
                    <div class="lg:col-span-2">
                        <!-- Products -->
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Productos</h2>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="space-y-4">
                                    @foreach($orden->detalles as $detalle)
                                        <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-0">
                                            <div class="flex items-center">
                                                <img class="h-16 w-16 rounded object-cover mr-4" 
                                                     src="{{ asset('images/productos/' . $detalle->producto->imagen) }}" 
                                                     alt="{{ $detalle->producto->nombre }}"
                                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/64x64?text={{ urlencode($detalle->producto->nombre) }}';">
                                                <div>
                                                    <h3 class="font-medium text-gray-900">{{ $detalle->producto->nombre }}</h3>
                                                    <p class="text-sm text-gray-600">
                                                        {{ $detalle->cantidad }} 
                                                        @if($detalle->producto->unidad_medida != 'unidad')
                                                            {{ $detalle->cantidad > 1 ? Str::plural($detalle->producto->unidad_medida) : $detalle->producto->unidad_medida }}
                                                        @else
                                                            {{ $detalle->cantidad > 1 ? 'unidades' : 'unidad' }}
                                                        @endif
                                                        × S/ {{ number_format($detalle->precio_unitario, 2) }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-medium text-gray-900">S/ {{ number_format($detalle->precio_total, 2) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($orden->notas)
                            <div class="mb-8">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">Notas del pedido</h2>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-700">{{ $orden->notas }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Timeline -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Historial del pedido</h2>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <ol class="relative border-l border-gray-200">
                                    <li class="mb-10 ml-4">
                                        <div class="absolute w-3 h-3 bg-green-500 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                        <time class="mb-1 text-sm font-normal leading-none text-gray-600">
                                            {{ $orden->created_at->format('d/m/Y h:i A') }}
                                        </time>
                                        <h3 class="text-lg font-semibold text-gray-900">Pedido creado</h3>
                                        <p class="text-base font-normal text-gray-600">Tu pedido ha sido recibido exitosamente.</p>
                                    </li>
                                    
                                    @if($orden->estado == 'procesando' || $orden->estado == 'completado')
                                        <li class="mb-10 ml-4">
                                            <div class="absolute w-3 h-3 bg-blue-500 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                            <time class="mb-1 text-sm font-normal leading-none text-gray-600">
                                                {{ $orden->updated_at->format('d/m/Y h:i A') }}
                                            </time>
                                            <h3 class="text-lg font-semibold text-gray-900">En proceso</h3>
                                            <p class="text-base font-normal text-gray-600">Tu pedido está siendo preparado.</p>
                                        </li>
                                    @endif
                                    
                                    @if($orden->estado == 'completado')
                                        <li class="ml-4">
                                            <div class="absolute w-3 h-3 bg-green-500 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                            <time class="mb-1 text-sm font-normal leading-none text-gray-600">
                                                {{ $orden->fecha_completado ? $orden->fecha_completado->format('d/m/Y h:i A') : $orden->updated_at->format('d/m/Y h:i A') }}
                                            </time>
                                            <h3 class="text-lg font-semibold text-gray-900">Completado</h3>
                                            <p class="text-base font-normal text-gray-600">Tu pedido ha sido completado exitosamente.</p>
                                        </li>
                                    @endif
                                    
                                    @if($orden->estado == 'cancelado')
                                        <li class="ml-4">
                                            <div class="absolute w-3 h-3 bg-red-500 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                            <time class="mb-1 text-sm font-normal leading-none text-gray-600">
                                                {{ $orden->updated_at->format('d/m/Y h:i A') }}
                                            </time>
                                            <h3 class="text-lg font-semibold text-gray-900">Cancelado</h3>
                                            <p class="text-base font-normal text-gray-600">El pedido ha sido cancelado.</p>
                                        </li>
                                    @endif
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-gray-50 rounded-lg p-6 sticky top-4">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Resumen del Pedido</h2>
                            
                            <div class="space-y-2 mb-6">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal</span>
                                    <span>S/ {{ number_format($orden->subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-xl font-semibold text-gray-900 pt-2 border-t">
                                    <span>Total</span>
                                    <span>S/ {{ number_format($orden->total, 2) }}</span>
                                </div>
                            </div>

                            <div class="mb-6">
                                <h3 class="font-semibold text-gray-900 mb-2">Cliente</h3>
                                <p class="text-sm text-gray-600">{{ $orden->user->full_name }}</p>
                                <p class="text-sm text-gray-600">{{ $orden->user->email }}</p>
                                @if($orden->user->telefono)
                                    <p class="text-sm text-gray-600">{{ $orden->user->telefono }}</p>
                                @endif
                            </div>

                            @if($orden->user->direccion)
                                <div class="mb-6">
                                    <h3 class="font-semibold text-gray-900 mb-2">Dirección</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $orden->user->direccion }}
                                        @if($orden->user->distrito)
                                            <br>{{ $orden->user->distrito }}
                                        @endif
                                        @if($orden->user->provincia)
                                            , {{ $orden->user->provincia }}
                                        @endif
                                        @if($orden->user->departamento)
                                            <br>{{ $orden->user->departamento }}
                                        @endif
                                    </p>
                                </div>
                            @endif

                            <div class="space-y-3">
                                <a href="{{ route('ordenes.index') }}" 
                                   class="block text-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition duration-200">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Volver a Mis Órdenes
                                </a>
                                
                                @if($orden->estado == 'pendiente' || $orden->estado == 'procesando')
                                    <a href="{{ route('productos.index') }}" 
                                       class="block text-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                                        <i class="fas fa-shopping-cart mr-2"></i>
                                        Hacer otro pedido
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection