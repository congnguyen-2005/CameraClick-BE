<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStore extends Model
{
    protected $table = 'product_stores';

    protected $fillable = [
    'product_id', 
    'qty', 
   'price_root',
    'type', 
    'created_by'
];

    // Laravel tự xử lý created_at / updated_at
    public $timestamps = true;

    // ===== QUAN HỆ =====
  public function product() {
    return $this->belongsTo(Product::class, 'product_id');
}


    // public function stocks()
    // {
    //     return $this->hasMany(ProductStore::class, 'product_id', 'product_id');
    // }
}
