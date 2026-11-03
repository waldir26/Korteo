<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBarbershop;

class Product extends Model
{
    // Ya no usamos SoftDeletes
    use BelongsToBarbershop;

    protected $fillable = [
        'barbershop_id',
        'codigo',
        'nombre',
        'categoria',
        'categoria_id',
        'marca',
        'foto',
        'precio',
        'stock',
        'stock_minimo',
        'activo'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /**
     * Relación con la categoría del producto.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'categoria_id');
    }
}
