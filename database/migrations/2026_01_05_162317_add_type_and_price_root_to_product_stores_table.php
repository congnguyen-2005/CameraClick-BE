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
    Schema::table('product_stores', function (Blueprint $table) {
        // Chỉ thêm 'type' nếu chưa có
        if (!Schema::hasColumn('product_stores', 'type')) {
            $table->string('type')->nullable()->after('qty');
        }

        // Chỉ thêm 'price_root' nếu chưa có
        if (!Schema::hasColumn('product_stores', 'price_root')) {
            $table->decimal('price_root', 15, 2)->default(0)->after('qty');
        }

        // Chỉ thêm 'created_by' nếu chưa có
        if (!Schema::hasColumn('product_stores', 'created_by')) {
            $table->unsignedBigInteger('created_by')->nullable()->after('price_root');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_stores', function (Blueprint $table) {
            //
        });
    }
};
