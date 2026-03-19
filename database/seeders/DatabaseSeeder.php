<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
{
    $this->call([
        UserTableSeeder::class,
        BannerTableSeeder::class,
        CategoryTableSeeder::class,
        ProductTableSeeder::class,
        ProductImageTableSeeder::class,
        AttributeTableSeeder::class,
        ProductAttributeTableSeeder::class,
        ProductSaleTableSeeder::class,
        ProductStoreTableSeeder::class,
        MenuTableSeeder::class,
        TopicTableSeeder::class,
        PostTableSeeder::class,
        ContactTableSeeder::class,
        OrderTableSeeder::class,
        OrderDetailTableSeeder::class,
        ConfigTableSeeder::class,
    ]);
}

}