<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders'; // Đảm bảo đúng tên bảng của bạn
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'name', 'email', 'phone', 'address', 'note',
        'total_money', 'payment_method', 'status', 'created_at'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }
}