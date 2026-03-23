<?php
// app/Models/Attribute.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{protected $table = 'attributes';
    public $timestamps = false;
    protected $fillable = ['name'];
 public function products()
    {
        return $this->belongsToMany(Product::class, 'ntc_product_attributes', 'attribute_id', 'product_id')
                    ->withPivot('value');
    }
}