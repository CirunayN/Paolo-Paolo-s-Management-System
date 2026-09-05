<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'supplier_id',
        'source',
        'user_id',
        'total_cost',
        'notes',
        'received_date',
    ];

    protected $casts = [
        'total_cost' => 'decimal:2',
        'received_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(StockInItem::class);
    }
}
