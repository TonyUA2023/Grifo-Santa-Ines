<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenDetalle extends Model
{
    use HasFactory;

    protected $table = 'orden_detalles';

    protected $fillable = [
        'orden_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'precio_total',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'precio_total' => 'decimal:2',
    ];

    /**
     * Get the order that owns the detail.
     */
    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    /**
     * Get the product that owns the detail.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Calculate total price before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detalle) {
            $detalle->precio_total = $detalle->cantidad * $detalle->precio_unitario;
        });

        static::updating(function ($detalle) {
            $detalle->precio_total = $detalle->cantidad * $detalle->precio_unitario;
        });
    }
}