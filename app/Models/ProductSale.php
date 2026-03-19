<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSale extends Model
{
    protected $fillable = [
        'name',
        'product_id',
        'price_sale',
        'date_begin',
        'date_end',
        'created_by',
        'updated_by',
        'status',
    ];

    public function product() {
    return $this->belongsTo(Product::class, 'product_id');
}
}
