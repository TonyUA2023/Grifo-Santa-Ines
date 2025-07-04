@extends('layouts.app')

@section('title', 'Iniciar Sesión - Grifo Santa Ines')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Iniciar Sesión
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                O
                <a href="#" onclick="mostrarRegistro()" class="font-medium text-blue-600 hover:text-blue-500">
                    crea una cuenta nueva
                </a>
            </p>
        </div>

        <!-- Login Form -->
        <form id="formLogin" class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                               class="appearance-none rounded-none relative block w-full px-10 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('email') border-red-500 @enderror" 
                               placeholder="Correo Electrónico"
                               value="{{ old('email') }}">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="sr-only">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               class="appearance-none rounded-none relative block w-full px-10 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('password') border-red-500 @enderror" 
                               placeholder="Contraseña">
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Recordarme
                    </label>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-blue-500 group-hover:text-blue-400"></i>
                    </span>
                    Iniciar Sesión
                </button>
            </div>

            <p class="text-center text-xs text-gray-500">
                Al iniciar sesión, aceptas nuestras 
                <a href="#" class="text-blue-600 hover:text-blue-500">Condiciones de uso</a> y 
                <a href="#" class="text-blue-600 hover:text-blue-500">Política de privacidad</a>.
            </p>
        </form>

        <!-- Registration Form (Initially Hidden) -->
        <form id="formRegistro" class="mt-8 space-y-6 hidden" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <!-- DNI -->
                <div>
                    <label for="dni" class="block text-sm font-medium text-gray-700">DNI</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-id-card text-gray-400"></i>
                        </div>
                        <input id="dni" name="dni" type="text" maxlength="8" required 
                               class="appearance-none block w-full px-10 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="DNI"
                               onblur="consultarDni(this.value)">
                    </div>
                </div>

                <!-- Nombres -->
                <div>
                    <label for="nombres" class="block text-sm font-medium text-gray-700">Nombres</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input id="nombres" name="nombres" type="text" required 
                               class="appearance-none block w-full px-10 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Nombres">
                    </div>
                </div>

                <!-- Apellidos -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="apellido_paterno" class="block text-sm font-medium text-gray-700">Apellido Paterno</label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="apellido_paterno" name="apellido_paterno" type="text" required 
                                   class="appearance-none block w-full px-10 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Apellido Paterno">
                        </div>
                    </div>
                    <div>
                        <label for="apellido_materno" class="block text-sm font-medium text-gray-700">Apellido Materno</label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="apellido_materno" name="apellido_materno" type="text" required 
                                   class="appearance-none block w-full px-10 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Apellido Materno">
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email_registro" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email_registro" name="email" type="email" autocomplete="email" required 
                               class="appearance-none block w-full px-10 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Correo Electrónico">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password_registro" class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password_registro" name="password" type="password" required 
                               class="appearance-none block w-full px-10 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Contraseña (mínimo 8 caracteres)">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password" required 
                               class="appearance-none block w-full px-10 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Confirmar Contraseña">
                    </div>
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input id="telefono" name="telefono" type="tel" 
                               class="appearance-none block w-full px-10 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Número de Celular">
                    </div>
                </div>

                <!-- Dirección -->
                <div>
                    <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-home text-gray-400"></i>
                        </div>
                        <input id="direccion" name="direccion" type="text" 
                               class="appearance-none block w-full px-10 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Dirección">
                    </div>
                </div>

                <!-- Ubicación -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="distrito" class="block text-sm font-medium text-gray-700">Distrito</label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                            </div>
                            <input id="distrito" name="distrito" type="text" 
                                   class="appearance-none block w-full px-10 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Distrito">
                        </div>
                    </div>
                    <div>
                        <label for="provincia" class="block text-sm font-medium text-gray-700">Provincia</label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-map text-gray-400"></i>
                            </div>
                            <input id="provincia" name="provincia" type="text" 
                                   class="appearance-none block w-full px-10 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Provincia">
                        </div>
                    </div>
                    <div>
                        <label for="departamento" class="block text-sm font-medium text-gray-700">Departamento</label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-globe-americas text-gray-400"></i>
                            </div>
                            <input id="departamento" name="departamento" type="text" 
                                   class="appearance-none block w-full px-10 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Departamento">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-blue-500 group-hover:text-blue-400"></i>
                    </span>
                    Registrarme
                </button>
            </div>

            <p class="text-center text-xs text-gray-500">
                Al registrarte, aceptas nuestras 
                <a href="#" class="text-blue-600 hover:text-blue-500">Condiciones de uso</a> y 
                <a href="#" class="text-blue-600 hover:text-blue-500">Política de privacidad</a>.
            </p>

            <p class="text-center text-sm text-gray-600">
                ¿Ya tienes cuenta? 
                <a href="#" onclick="mostrarLogin()" class="font-medium text-blue-600 hover:text-blue-500">
                    Iniciar Sesión
                </a>
            </p>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function mostrarRegistro() {
        document.getElementById('formLogin').classList.add('hidden');
        document.getElementById('formRegistro').classList.remove('hidden');
    }

    function mostrarLogin() {
        document.getElementById('formRegistro').classList.add('hidden');
        document.getElementById('formLogin').classList.remove('hidden');
    }

    function consultarDni(dni) {
        if (dni.length !== 8) {
            return;
        }

        // Show loading state
        document.getElementById('nombres').value = 'Consultando...';
        document.getElementById('apellido_paterno').value = 'Consultando...';
        document.getElementById('apellido_materno').value = 'Consultando...';

        fetch(`/api/consultar-dni/${dni}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    document.getElementById('nombres').value = data.data.nombres || '';
                    document.getElementById('apellido_paterno').value = data.data.apellido_paterno || '';
                    document.getElementById('apellido_materno').value = data.data.apellido_materno || '';
                } else {
                    // Clear fields if no data found
                    document.getElementById('nombres').value = '';
                    document.getElementById('apellido_paterno').value = '';
                    document.getElementById('apellido_materno').value = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Clear fields on error
                document.getElementById('nombres').value = '';
                document.getElementById('apellido_paterno').value = '';
                document.getElementById('apellido_materno').value = '';
            });
    }

    // Show registration form if there are old input values
    @if(old('dni'))
        mostrarRegistro();
    @endif
</script>
@endpush