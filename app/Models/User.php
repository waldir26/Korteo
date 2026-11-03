<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'barbershop_id',
        'username',
        'name',
        'email',
        'password',
        'activo',
        'debe_cambiar_password',
        'ultimo_login',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
            'debe_cambiar_password' => 'boolean',
            'ultimo_login' => 'datetime',
        ];
    }

    public function barbershop()
    {
        return $this->belongsTo(Barbershop::class);
    }

    // NUEVA RELACIÓN: Un usuario puede tener un perfil de barbero
    public function barber()
    {
        return $this->hasOne(Barber::class, 'user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->barbershop_id === null && $this->hasRole('superadmin');
    }

    public function isBarbershopUser(): bool
    {
        return $this->barbershop_id !== null;
    }
}
