<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('product_id');
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('qty');
            $table->decimal('amount', 12, 2);
            $table->decimal('discount', 12, 2);
        });
    }

    public function down() {
        Schema::dropIfExists('order_details');
    }
};
