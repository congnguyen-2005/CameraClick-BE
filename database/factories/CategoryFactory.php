<?php
// database/factories/CategoryFactory.php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;
    public function definition()
    {
        return [
            'name'=>$this->faker->word(),
            'slug'=> $this->faker->slug(),
            'image'=> null,
            'parent_id'=>0,
            'sort_order'=>0,
            'description'=> $this->faker->optional()->sentence(),
            'created_at'=> now(),
            'created_by'=>1,
            'status'=>1
        ];
    }
}
