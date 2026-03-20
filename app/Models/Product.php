<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Laravel mặc định là true, khai báo rõ ràng giúp tránh nhầm lẫn
    public $timestamps = true; 

    protected $fillable = [
        'name',
        'slug',
        'price_buy',
        'stock',
        'category_id',
        'description',
        'content',
        'thumbnail',
        'status',
    ];

    /**
     * 1. Liên kết Category (Many-to-One)
     */
    public function category() 
    { 
        return $this->belongsTo(Category::class, 'category_id'); 
    }

    /**
     * 2. Liên kết Ảnh phụ (One-to-Many)
     */
    public function images() 
    { 
        return $this->hasMany(ProductImage::class, 'product_id'); 
    }

    /**
     * 3. Liên kết Kho (One-to-One) - Dùng để lấy tồn kho hiện tại
     * Bản ghi này có type = null trong bảng product_stores
     */
    public function product_store() 
    { 
        return $this->hasOne(ProductStore::class, 'product_id')->whereNull('type'); 
    }

    /**
     * 4. Liên kết Thuộc tính (Many-to-Many qua bảng trung gian)
     */
    public function attributes()
    {
        return $this->belongsToMany(
            Attribute::class,
            'ntc_product_attributes', // Đảm bảo tên bảng này khớp chính xác trong DB
            'product_id',
            'attribute_id'
        )
        ->withPivot('value'); // QUAN TRỌNG: để lấy được dữ liệu Color/Size từ bảng trung gian
      
    }

    /**
     * 5. Liên kết Sale (One-to-Many)
     */
    public function sales() 
    { 
        return $this->hasMany(ProductSale::class, 'product_id'); 
    }
}