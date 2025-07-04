<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario administrador
        User::create([
            'dni' => '12345678',
            'nombres' => 'Admin',
            'apellido_paterno' => 'Sistema',
            'apellido_materno' => 'Grifo',
            'email' => 'admin@grifosantaines.com',
            'password' => Hash::make('password123'),
            'telefono' => '999999999',
            'direccion' => 'Av. Principal 123',
            'distrito' => 'San Isidro',
            'provincia' => 'Lima',
            'departamento' => 'Lima',
            'email_verified_at' => now(),
        ]);

        // Usuario cliente de prueba
        User::create([
            'dni' => '87654321',
            'nombres' => 'Juan Carlos',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'García',
            'email' => 'cliente@example.com',
            'password' => Hash::make('password123'),
            'telefono' => '987654321',
            'direccion' => 'Jr. Las Flores 456',
            'distrito' => 'Miraflores',
            'provincia' => 'Lima',
            'departamento' => 'Lima',
            'email_verified_at' => now(),
        ]);

        // Usuarios adicionales de prueba
        $usuarios = [
            [
                'dni' => '11111111',
                'nombres' => 'María Elena',
                'apellido_paterno' => 'Rodriguez',
                'apellido_materno' => 'López',
                'email' => 'maria@example.com',
                'telefono' => '911111111',
                'direccion' => 'Calle Los Olivos 789',
                'distrito' => 'Surco',
                'provincia' => 'Lima',
                'departamento' => 'Lima',
            ],
            [
                'dni' => '22222222',
                'nombres' => 'Carlos Alberto',
                'apellido_paterno' => 'Mendoza',
                'apellido_materno' => 'Silva',
                'email' => 'carlos@example.com',
                'telefono' => '922222222',
                'direccion' => 'Av. Los Pinos 321',
                'distrito' => 'La Molina',
                'provincia' => 'Lima',
                'departamento' => 'Lima',
            ],
            [
                'dni' => '33333333',
                'nombres' => 'Ana Lucía',
                'apellido_paterno' => 'Torres',
                'apellido_materno' => 'Vargas',
                'email' => 'ana@example.com',
                'telefono' => '933333333',
                'direccion' => 'Jr. Las Rosas 654',
                'distrito' => 'San Borja',
                'provincia' => 'Lima',
                'departamento' => 'Lima',
            ],
        ];

        foreach ($usuarios as $usuario) {
            User::create(array_merge($usuario, [
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]));
        }

        $this->command->info('Usuarios creados exitosamente:');
        $this->command->table(
            ['Email', 'Contraseña'],
            [
                ['admin@grifosantaines.com', 'password123'],
                ['cliente@example.com', 'password123'],
                ['maria@example.com', 'password123'],
                ['carlos@example.com', 'password123'],
                ['ana@example.com', 'password123'],
            ]
        );
    }
}