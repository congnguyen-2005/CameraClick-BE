<?php
// app/Models/ProductAttribute.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    public $timestamps = false;
    protected $fillable = ['product_id','attribute_id','value'];
    public function product(){ return $this->belongsTo(Product::class,'product_id'); }
    public function attribute(){ return $this->belongsTo(Attribute::class,'attribute_id'); }
}
