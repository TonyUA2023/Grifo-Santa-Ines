<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            [
                'nombre' => 'Gasolina 95 Octanos',
                'descripcion' => 'Gasolina premium de 95 octanos, ideal para vehículos de alto rendimiento.',
                'precio' => 20.00,
                'unidad_medida' => 'galón',
                'imagen' => 'gasolina95.jpg',
                'caracteristicas' => ['Octanaje alto', 'Combustión limpia'],
                'stock' => 1000,
            ],
            [
                'nombre' => 'Diésel B5 S50',
                'descripcion' => 'Diésel limpio y eficiente para motores de carga y transporte.',
                'precio' => 18.50,
                'unidad_medida' => 'galón',
                'imagen' => 'diesel.png',
                'caracteristicas' => ['Bajo azufre', 'Alto rendimiento'],
                'stock' => 1000,
            ],
            [
                'nombre' => 'Lubricante 20W-50',
                'descripcion' => 'Lubricante multigrado mineral ideal para motores a gasolina.',
                'precio' => 35.00,
                'unidad_medida' => 'litro',
                'imagen' => 'lubricante.png',
                'caracteristicas' => ['Viscosidad 20W-50', 'Protección de motor'],
                'stock' => 50,
            ],
            [
                'nombre' => 'Aditivo de Octanaje',
                'descripcion' => 'Mejora el rendimiento y limpieza del sistema de combustión.',
                'precio' => 22.00,
                'unidad_medida' => 'unidad',
                'imagen' => 'aditivo.png',
                'caracteristicas' => ['Potencia extra', 'Ahorro de combustible'],
                'stock' => 30,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}