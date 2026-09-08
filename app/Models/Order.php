<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'user_id',
        'customer_id',
        'customer_name',
        'vehicle_details',
        'order_type',
        'subtotal',
        'installation_fee',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_type',
        'payment_reference',
        'amount_tendered',
        'amount_paid',
        'balance_due',
        'change_amount',
        'payment_status',
        'due_date',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'installation_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_tendered' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class)->orderBy('payment_number');
    }

    public function isInstallment(): bool
    {
        return $this->payment_type === 'Installment';
    }

    public function isFullyPaid(): bool
    {
        return $this->payment_status === 'Paid' || $this->balance_due <= 0;
    }

    public function isPartial(): bool
    {
        return $this->payment_status === 'Partial' || ($this->isInstallment() && $this->balance_due > 0);
    }
}
