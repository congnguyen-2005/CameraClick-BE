<?php
// database/factories/ProductFactory.php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Category;

class ProductFactory extends Factory
{
    protected $model = Product::class;
    public function definition()
    {
        return [
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'name' => $this->faker->productName ?? $this->faker->word(),
            'slug' => $this->faker->slug(),
            'thumbnail' => 'products/'.$this->faker->image('public/storage/products',640,480, null, false),
            'content' => $this->faker->paragraphs(3,true),
            'description' => $this->faker->optional()->text(200),
            'price_buy' => $this->faker->randomFloat(2,10,1000),
            'created_at'=> now(),
            'created_by'=>1,
            'status'=>1
        ];
    }
}
