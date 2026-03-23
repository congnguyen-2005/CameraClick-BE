<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories'; // Thêm dòng này
    public $timestamps = false;
    protected $fillable = [
        'name','slug','image','parent_id','sort_order','description',
        'created_at','created_by','updated_at','updated_by','status'
    ];

    public function products(){ return $this->hasMany(Product::class,'category_id'); }
    public function parent(){ return $this->belongsTo(Category::class,'parent_id'); }
    public function children(){ return $this->hasMany(Category::class,'parent_id'); }
}