<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Thêm cột price_sale kiểu decimal, cho phép null (nếu sản phẩm không giảm giá)
            // Cột này sẽ đứng sau cột price_buy
            $table->decimal('price_sale', 10, 2)->nullable()->after('price_buy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Lệnh xóa cột khi bạn muốn rollback
            $table->dropColumn('price_sale');
        });
    }
};