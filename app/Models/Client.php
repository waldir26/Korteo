<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBarbershop;

class Client extends Model
{
    // Quitamos SoftDeletes
    use BelongsToBarbershop;

    protected $fillable = [
        'barbershop_id',
        'nombre',
        'telefono',
        'email',
        'notas'
    ];

    // RELACIÓN: Un cliente tiene muchas citas
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
