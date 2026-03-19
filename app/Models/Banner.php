<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    // Nếu bảng trong DB có cột created_at/updated_at thì nên để true
    public $timestamps = true; 

    protected $fillable = [
        'name', 'image', 'link', 'position', 'sort_order', 'description',
        'status', 'created_by', 'updated_by'
    ];

    // Tự động thêm image_url vào kết quả JSON
    protected $appends = ['image_url'];

    public function getImageUrlAttribute() {
        if (!$this->image) {
            return asset('no-image.jpg');
        }
        
        // Nếu image đã là một URL (ví dụ link ngoài)
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // Trả về link từ thư mục storage
        return asset('storage/' . $this->image);
    }
}