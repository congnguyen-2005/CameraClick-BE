<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    // Tên bảng (đảm bảo khớp với DB của bạn)
    protected $table = 'ntc_carts'; 

    protected $fillable = ['user_id', 'product_id', 'qty', 'options'];

    // ÉP KIỂU: Tự động JSON Encode khi lưu và JSON Decode khi lấy ra
    protected $casts = [
        'options' => 'array', 
    ];

    // Quan hệ lấy thông tin sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}