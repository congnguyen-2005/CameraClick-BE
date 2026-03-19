<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('product_stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->decimal('price_root', 12, 2);
            $table->unsignedInteger('qty');
            $table->timestamps();
            $table->unsignedInteger('created_by')->default(1);
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
        });
    }

    public function down() {
        Schema::dropIfExists('product_stores');
    }
};
