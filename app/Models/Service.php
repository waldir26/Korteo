<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBarbershop;

class Service extends Model
{
    // Quitamos SoftDeletes
    use BelongsToBarbershop;

    protected $fillable = [
        'barbershop_id',
        'nombre',
        'precio',
        'duracion',
        'activo'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];
}
