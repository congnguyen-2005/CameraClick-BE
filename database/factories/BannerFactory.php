<?php
// database/factories/BannerFactory.php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Banner;
use Illuminate\Support\Str;

class BannerFactory extends Factory
{
    protected $model = Banner::class;
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'image'=> 'banners/'.$this->faker->image('public/storage/banners',640,480, null, false),
            'link' => $this->faker->optional()->url(),
            'position' => $this->faker->randomElement(['slideshow','ads']),
            'sort_order' => $this->faker->numberBetween(0,10),
            'description' => $this->faker->optional()->text(200),
            'created_at' => now(),
            'created_by' => 1,
            'status' => 1
        ];
    }
}
