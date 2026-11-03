<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barbershop extends Model
{
    // Quitamos SoftDeletes
    protected $fillable = [
        'nombre',
        'slug',
        'logo',
        'telefono',
        'direccion',
        'estado',
        'motivo_estado',
        'suspendida_en',
        'cancelada_en',
    ];

    protected $casts = [
        'suspendida_en' => 'datetime',
        'cancelada_en' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function barbers()
    {
        return $this->hasMany(Barber::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
