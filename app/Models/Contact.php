<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    // Thêm dòng này nếu tên bảng trong Database của bạn là 'contact'
    protected $table = 'ntc_contacts'; 

    // Nếu bạn có cột created_at và updated_at, hãy để là true
    public $timestamps = true; 

    protected $fillable = [
        'user_id','name','email','phone','content','reply_id',
        'created_at','created_by','updated_at','updated_by','status'
    ];

    public function user(){ return $this->belongsTo(User::class,'user_id'); }
}