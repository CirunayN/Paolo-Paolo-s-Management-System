<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'name',
        'category_id',
        'vehicle_brand',
        'vehicle_model',
        'material_type',
        'unit_of_measure',
        'cost_price',
        'unit_price',
        'stock_alert_level',
        'image_path',
        'images',
        'description',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock_alert_level' => 'integer',
        'images' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockInItems()
    {
        return $this->hasMany(StockInItem::class);
    }

    public function getStockQuantityAttribute(): float
    {
        return $this->inventory ? (float)$this->inventory->quantity_on_hand : 0.0;
    }

    public function getStockStatusAttribute(): string
    {
        $qty = $this->stock_quantity;
        $alert = $this->stock_alert_level ?? 5;

        if ($qty <= 0) {
            return 'out_of_stock';
        } elseif ($qty <= $alert) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function getDisplayImageAttribute(): string
    {
        $all = $this->all_images;
        return $all[0] ?? asset('images/products/placeholder-matting.svg');
    }

    /**
     * Get up to 5 images for gallery/carousel
     *
     * @return array
     */
    public function getAllImagesAttribute(): array
    {
        $list = [];

        if (!empty($this->images) && is_array($this->images)) {
            foreach ($this->images as $img) {
                if (file_exists(public_path($img))) {
                    $list[] = asset($img);
                } elseif (file_exists(storage_path('app/public/' . $img))) {
                    $list[] = asset('storage/' . $img);
                } else {
                    $list[] = asset($img);
                }
            }
        }

        if (empty($list) && $this->image_path) {
            if (file_exists(public_path($this->image_path))) {
                $list[] = asset($this->image_path);
            } elseif (file_exists(storage_path('app/public/' . $this->image_path))) {
                $list[] = asset('storage/' . $this->image_path);
            } else {
                $list[] = asset($this->image_path);
            }
        }

        if (empty($list)) {
            $list[] = asset('images/products/placeholder-matting.svg');
        }

        return array_slice($list, 0, 5);
    }
}
