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
        $table->tinyInteger('stock_status')
              ->default(1)
              ->comment('1: cho phép nhập xuất, 0: khóa kho')
              ->after('status');
    });
}

public function down(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn('stock_status');
    });
}

};
