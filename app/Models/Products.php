<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'detail',
        'image',
        'in_stock',
        'available_colors',
        'size',
    ];

    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->product_id)) {
                $product->product_id = static::generateProductId($product->name);
            }
        });
    }

    private static function generateProductId($name)
    {
        $slug = Str::slug($name);
        $uniqueNumber = rand(1000, 9999); 
        return $slug . '-' . $uniqueNumber;
    }
}
