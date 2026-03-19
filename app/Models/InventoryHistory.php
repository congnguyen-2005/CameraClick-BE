<?php
// app/Models/InventoryHistory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'qty',
        'price_root'
    ];
}
