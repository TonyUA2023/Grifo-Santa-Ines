<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;

    protected $table = 'ordenes';

    protected $fillable = [
        'user_id',
        'subtotal',
        'total',
        'estado',
        'fecha_completado',
        'notas',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_completado' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order details for the order.
     */
    public function detalles()
    {
        return $this->hasMany(OrdenDetalle::class);
    }

    /**
     * Calculate and update order totals
     */
    public function calcularTotales()
    {
        $subtotal = $this->detalles->sum('precio_total');
        $this->update([
            'subtotal' => $subtotal,
            'total' => $subtotal, // Puedes agregar impuestos u otros cargos aquí
        ]);
    }
}