<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_in_id',
        'product_id',
        'quantity_received',
        'cost_per_unit',
        'subtotal',
    ];

    protected $casts = [
        'quantity_received' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function stockIn()
    {
        return $this->belongsTo(StockIn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
