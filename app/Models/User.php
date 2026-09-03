<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return in_array(strtolower($this->role), ['admin', 'owner']);
    }

    public function isCashier(): bool
    {
        return in_array(strtolower($this->role), ['cashier', 'admin', 'owner']);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
