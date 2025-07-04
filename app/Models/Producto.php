<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'unidad_medida',
        'imagen',
        'caracteristicas',
        'activo',
        'stock',
    ];

    protected $casts = [
        'caracteristicas' => 'array',
        'activo' => 'boolean',
        'precio' => 'decimal:2',
    ];

    /**
     * Get the orden details for the product.
     */
    public function ordenDetalles()
    {
        return $this->hasMany(OrdenDetalle::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Check if product is in stock
     */
    public function enStock()
    {
        return $this->stock > 0;
    }
}