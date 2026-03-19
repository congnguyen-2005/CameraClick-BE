<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            
            // 1. Lưu theo User (Mỗi user sẽ có những dòng cart riêng)
            $table->unsignedBigInteger('user_id');
            
            // 2. Lưu sản phẩm
            $table->unsignedBigInteger('product_id');
            
            // 3. Số lượng
            $table->integer('qty')->default(1);
            
            $table->timestamps();

            // --- RÀNG BUỘC KHÓA NGOẠI (QUAN TRỌNG) ---
            
            // Nếu xóa User -> Xóa luôn giỏ hàng của user đó
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Nếu xóa Product -> Xóa luôn sản phẩm đó trong giỏ hàng mọi người
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // --- RÀNG BUỘC DUY NHẤT (QUAN TRỌNG NHẤT) ---
            // Dòng này đảm bảo: 1 User chỉ có thể có 1 dòng cho 1 Product ID.
            // Ví dụ: User 1 không thể có 2 dòng chứa Product 5.
            // Điều này giúp tránh trùng lặp dữ liệu.
            $table->unique(['user_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};