<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToBarbershop;

class Barber extends Model
{
    use SoftDeletes, BelongsToBarbershop;

    protected $fillable = [
        'barbershop_id',
        'user_id', // <-- Agregamos esto para que el sistema permita guardar el ID del usuario
        'nombre',
        'telefono',
        'foto',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación con el usuario (para el login y credenciales).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
