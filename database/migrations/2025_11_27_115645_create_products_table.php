<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('category_id');
            $table->string('name');
            $table->string('slug');
            $table->string('thumbnail');
            $table->longText('content');
            $table->tinyText('description')->nullable();
            $table->decimal('price_buy', 12, 2);
             $table->integer('stock')->default(0);
            $table->timestamps();
            $table->unsignedInteger('created_by')->default(1);
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
        });
    }

    public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn('stock');
    });
}
};