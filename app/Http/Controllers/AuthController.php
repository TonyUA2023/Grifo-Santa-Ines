<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            // Si hay un carrito, redirigir al checkout
            if (session()->has('carrito') && count(session()->get('carrito')) > 0) {
                return redirect()->intended(route('checkout.index'));
            }
            
            return redirect()->intended(route('productos.index'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|size:8|unique:users',
            'nombres' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'distrito' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'dni' => $request->dni,
            'nombres' => $request->nombres,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'distrito' => $request->distrito,
            'provincia' => $request->provincia,
            'departamento' => $request->departamento,
        ]);

        Auth::login($user);

        // Si hay un carrito, redirigir al checkout
        if (session()->has('carrito') && count(session()->get('carrito')) > 0) {
            return redirect()->route('checkout.index');
        }

        return redirect()->route('productos.index');
    }

    /**
     * Consultar DNI en API externa (RENIEC simulado).
     */
    public function consultarDni($dni)
    {
        // Aquí puedes integrar con una API real de RENIEC
        // Por ahora, simularemos una respuesta
        
        // Validar que el DNI tenga 8 dígitos
        if (strlen($dni) !== 8 || !is_numeric($dni)) {
            return response()->json([
                'success' => false,
                'message' => 'DNI inválido'
            ], 400);
        }

        // Simular respuesta de API
        // En producción, aquí harías la llamada real a la API de RENIEC
        $datosSimulados = [
            '12345678' => [
                'nombres' => 'JUAN CARLOS',
                'apellido_paterno' => 'PEREZ',
                'apellido_materno' => 'GARCIA'
            ],
            '87654321' => [
                'nombres' => 'MARIA ELENA',
                'apellido_paterno' => 'RODRIGUEZ',
                'apellido_materno' => 'LOPEZ'
            ]
        ];

        if (isset($datosSimulados[$dni])) {
            return response()->json([
                'success' => true,
                'data' => $datosSimulados[$dni]
            ]);
        }

        // Si no está en los datos simulados, devolver datos vacíos
        return response()->json([
            'success' => true,
            'data' => [
                'nombres' => '',
                'apellido_paterno' => '',
                'apellido_materno' => ''
            ]
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('productos.index');
    }
}