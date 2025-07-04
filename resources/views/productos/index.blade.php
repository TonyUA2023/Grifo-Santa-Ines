@extends('layouts.app')

@section('title', 'Productos - Grifo Santa Ines')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Nuestros Productos</h1>
            <p class="text-xl text-gray-600">Los mejores precios en combustibles y más</p>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($productos as $producto)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 cursor-pointer"
                     onclick="abrirModal({{ $producto->id }})">
                    <div class="h-48 bg-gray-200 flex items-center justify-center">
                        <img src="{{ asset('images/productos/' . $producto->imagen) }}" 
                             alt="{{ $producto->nombre }}"
                             class="h-full w-full object-cover"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/300x200?text={{ urlencode($producto->nombre) }}';">
                    </div>
                    <div class="p-4">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">{{ $producto->nombre }}</h2>
                        <p class="text-2xl font-bold text-blue-600">
                            S/ {{ number_format($producto->precio, 2) }}
                            @if($producto->unidad_medida != 'unidad')
                                <span class="text-sm text-gray-600">x {{ $producto->unidad_medida }}</span>
                            @endif
                        </p>
                        @if($producto->stock > 0)
                            <span class="inline-block mt-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                En stock
                            </span>
                        @else
                            <span class="inline-block mt-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                Agotado
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Location Section -->
        <div class="mt-16 bg-white rounded-lg shadow-lg p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Nuestra Ubicación</h3>
            <div class="aspect-w-16 aspect-h-9">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1955.3567655559486!2d-75.69374076121245!3d-11.428326318159158!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x910920b8f2715e37%3A0xb98d1fa17a676f45!2sPRIMAX%20ES%20SANTA%20INES!5e0!3m2!1ses-419!2spe!4v1750215195384!5m2!1ses-419!2spe" 
                    width="100%" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    class="rounded-lg">
                </iframe>
            </div>
        </div>
    </div>
</div>

<!-- Modal Producto -->
<div id="modalProducto" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Close button -->
            <button onclick="cerrarModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>

            <!-- Modal content -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Image -->
                <div>
                    <img id="modalImagen" src="" alt="" class="w-full h-64 object-cover rounded-lg">
                </div>

                <!-- Product Details -->
                <div>
                    <h2 id="modalTitulo" class="text-2xl font-bold text-gray-900 mb-3"></h2>
                    <p id="modalDescripcion" class="text-gray-600 mb-4"></p>
                    
                    <p class="text-3xl font-bold text-blue-600 mb-4">
                        S/ <span id="modalPrecio"></span>
                        <span id="modalUnidad" class="text-sm text-gray-600"></span>
                    </p>

                    <div class="mb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Características:</h3>
                        <ul id="modalCaracteristicas" class="list-disc list-inside text-gray-600">
                        </ul>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">
                            Stock disponible: <span id="modalStock" class="font-semibold"></span>
                        </p>
                    </div>

                    <!-- Quantity selector -->
                    <div class="flex items-center mb-4">
                        <label class="mr-3 text-gray-700">Cantidad:</label>
                        <div class="flex items-center">
                            <button onclick="cambiarCantidad(-1)" class="bg-gray-200 px-3 py-1 rounded-l hover:bg-gray-300">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="cantidad" value="1" min="1" class="w-16 text-center border-t border-b border-gray-200 py-1">
                            <button onclick="cambiarCantidad(1)" class="bg-gray-200 px-3 py-1 rounded-r hover:bg-gray-300">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Add to cart button -->
                    <button id="btnAgregarCarrito" onclick="agregarAlCarrito()" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Añadir al carrito
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let productoActual = null;

    function abrirModal(productoId) {
        // Fetch product data
        fetch(`/api/productos/${productoId}`)
            .then(response => response.json())
            .then(producto => {
                productoActual = producto;
                
                // Update modal content
                document.getElementById('modalImagen').src = producto.imagen;
                document.getElementById('modalTitulo').textContent = producto.nombre;
                document.getElementById('modalDescripcion').textContent = producto.descripcion;
                document.getElementById('modalPrecio').textContent = producto.precio.toFixed(2);
                document.getElementById('modalStock').textContent = producto.stock;
                
                // Update unit
                if (producto.unidad_medida !== 'unidad') {
                    document.getElementById('modalUnidad').textContent = `x ${producto.unidad_medida}`;
                } else {
                    document.getElementById('modalUnidad').textContent = '';
                }
                
                // Update characteristics
                const caracteristicasList = document.getElementById('modalCaracteristicas');
                caracteristicasList.innerHTML = '';
                if (producto.caracteristicas && producto.caracteristicas.length > 0) {
                    producto.caracteristicas.forEach(caract => {
                        const li = document.createElement('li');
                        li.textContent = caract;
                        caracteristicasList.appendChild(li);
                    });
                }
                
                // Reset quantity
                document.getElementById('cantidad').value = 1;
                document.getElementById('cantidad').max = producto.stock;
                
                // Disable button if out of stock
                const btnAgregar = document.getElementById('btnAgregarCarrito');
                if (producto.stock === 0) {
                    btnAgregar.disabled = true;
                    btnAgregar.textContent = 'Agotado';
                    btnAgregar.classList.add('bg-gray-400', 'cursor-not-allowed');
                    btnAgregar.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                } else {
                    btnAgregar.disabled = false;
                    btnAgregar.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i> Añadir al carrito';
                    btnAgregar.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    btnAgregar.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }
                
                // Show modal
                document.getElementById('modalProducto').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error al cargar el producto', 'error');
            });
    }

    function cerrarModal() {
        document.getElementById('modalProducto').classList.add('hidden');
        productoActual = null;
    }

    function cambiarCantidad(cambio) {
        const input = document.getElementById('cantidad');
        const nuevoValor = parseInt(input.value) + cambio;
        const max = parseInt(input.max);
        
        if (nuevoValor >= 1 && nuevoValor <= max) {
            input.value = nuevoValor;
        }
    }

    function agregarAlCarrito() {
        if (!productoActual) return;
        
        const cantidad = parseInt(document.getElementById('cantidad').value);
        
        fetch('{{ route("carrito.agregar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                producto_id: productoActual.id,
                cantidad: cantidad
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message);
                updateCartCount(data.carrito_count);
                cerrarModal();
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al agregar al carrito', 'error');
        });
    }

    // Close modal when clicking outside
    document.getElementById('modalProducto').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });
</script>
@endpush