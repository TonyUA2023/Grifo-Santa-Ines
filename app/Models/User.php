<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Orden;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dni',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'password',
        'telefono',
        'direccion',
        'distrito',
        'provincia',
        'departamento',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Set the user's password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        // Solo hashear si el valor no está ya hasheado
        if (!empty($value)) {
            // Verificar si ya está hasheado (bcrypt siempre empieza con $2y$)
            if (!preg_match('/^\$2[ayb]\$.{56}$/', $value)) {
                $this->attributes['password'] = bcrypt($value);
            } else {
                $this->attributes['password'] = $value;
            }
        }
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}";
    }

    /**
     * Get the orders for the user.
     */
    public function ordenes()
    {
        return $this->hasMany(Orden::class);
    }

    /**
     * Check if user has any orders
     *
     * @return bool
     */
    public function hasOrders()
    {
        return $this->ordenes()->exists();
    }

    /**
     * Get user's complete address
     *
     * @return string|null
     */
    public function getCompleteAddressAttribute()
    {
        $parts = array_filter([
            $this->direccion,
            $this->distrito,
            $this->provincia,
            $this->departamento
        ]);

        return !empty($parts) ? implode(', ', $parts) : null;
    }

    /**
     * Scope a query to only include users from a specific department.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $departamento
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromDepartamento($query, $departamento)
    {
        return $query->where('departamento', $departamento);
    }

    /**
     * Scope a query to only include users with verified emails.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }
}