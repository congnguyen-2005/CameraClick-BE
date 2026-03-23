<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products'; // Bắt buộc phải có vì DB dùng tiền tố ntc_
    public $timestamps = true; 

    protected $fillable = [
        'name', 'slug', 'price_buy', 'stock', 'category_id',
        'description', 'content', 'thumbnail', 'status',
    ];

    public function category() { 
        return $this->belongsTo(Category::class, 'category_id'); 
    }

    public function images() { 
        return $this->hasMany(ProductImage::class, 'product_id'); 
    }

    public function product_store() { 
        return $this->hasOne(ProductStore::class, 'product_id')->whereNull('type'); 
    }

    public function attributes() {
        return $this->belongsToMany(
            Attribute::class,
            'ntc_product_attributes', 
            'product_id',
            'attribute_id'
        )->withPivot('value'); // Đã xóa withTimestamps để hết lỗi 500
    }

    public function sales() { 
        return $this->hasMany(ProductSale::class, 'product_id'); 
    }
}