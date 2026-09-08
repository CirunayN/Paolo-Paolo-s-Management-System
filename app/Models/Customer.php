<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'contact_number',
        'email',
        'vehicle_make_model',
        'plate_number',
        'address',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class)->orderBy('payment_date', 'desc');
    }

    public function activeInstallmentOrders()
    {
        return $this->orders()->where('payment_type', 'Installment')->where('balance_due', '>', 0);
    }

    public function totalOutstandingBalance(): float
    {
        return (float) $this->orders()->where('balance_due', '>', 0)->sum('balance_due');
    }
}
