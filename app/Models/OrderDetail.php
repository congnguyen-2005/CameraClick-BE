<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'ntc_order_details'; // Thêm dòng này
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'product_id', 'price', 'qty', 'amount', 'discount'
    ];

    public function order() { return $this->belongsTo(Order::class, 'order_id'); }
    public function product() { return $this->belongsTo(Product::class, 'product_id'); }
}