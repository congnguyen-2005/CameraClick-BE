<?php
// app/Models/ProductImage.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{protected $table = 'ntc_product_images';
    public $timestamps = false;
    protected $fillable = ['product_id','image','alt','title'];
    public function product(){ return $this->belongsTo(Product::class,'product_id'); }
}
