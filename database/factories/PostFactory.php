<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;
use App\Models\Topic;

class PostFactory extends Factory
{
    protected $model = Post::class;
    public function definition()
    {
        return [
            'topic_id' => Topic::inRandomOrder()->first()->id ?? Topic::factory(),
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->slug(),
            'image' => 'posts/'.$this->faker->image('public/storage/posts',640,480, null, false),
            'content' => $this->faker->paragraphs(4,true),
            'description' => $this->faker->optional()->text(200),
            'post_type'=>'post',
            'created_at'=>now(),
            'created_by'=>1,
            'status'=>1
        ];
    }
}
