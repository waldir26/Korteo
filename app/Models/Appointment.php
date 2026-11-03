<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBarbershop;

class Appointment extends Model
{
    // Quitamos SoftDeletes
    use BelongsToBarbershop;

    protected $fillable = [
        'barbershop_id',
        'client_id',
        'barber_id',
        'service_id',
        'fecha_hora',
        'estado',
        'notas'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
