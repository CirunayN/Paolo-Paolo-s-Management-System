<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Generate the next continuous sequential product code (e.g. PRD-0001, PRD-0013)
     * Checks both active and trashed products to prevent any code re-use.
     */
    public static function generateNextProductCode(): string
    {
        $allCodes = static::withTrashed()->pluck('product_code');
        $maxNum = 0;

        foreach ($allCodes as $code) {
            if (preg_match('/^PRD-(\d+)$/i', trim($code), $matches)) {
                $num = intval($matches[1]);
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        // If no PRD-XXXX format found, start from count + 1
        if ($maxNum === 0) {
            $maxNum = static::withTrashed()->count();
        }

        $nextNum = $maxNum + 1;
        do {
            $candidate = 'PRD-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
            $nextNum++;
        } while (static::withTrashed()->where('product_code', $candidate)->exists());

        return $candidate;
    }

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
        'is_service',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_service' => 'boolean',
        'stock_alert_level' => 'integer',
        'images' => 'array',
    ];

    public function isService(): bool
    {
        return (bool) $this->is_service;
    }

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
        if ($this->is_service) {
            return 'service';
        }
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
