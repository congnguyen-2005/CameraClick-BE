<?php
// app/Models/Topic.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name','slug','sort_order','description','created_at','created_by','updated_at','updated_by','status'
    ];
    public function posts(){ return $this->hasMany(Post::class,'topic_id'); }
}
