<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('posts')->insert([
            [
                'topic_id' => 1,
                'title' => 'Bài viết 1',
                'slug' => 'bai-viet-1',
                'image' => 'post1.jpg',
                'content' => 'Nội dung bài viết 1',
                'description' => 'Mô tả bài viết 1',
                'post_type' => 'post',
                'created_by' => 1,
                'status' => 1,
            ],
            [
                'topic_id' => 2,
                'title' => 'Bài viết 2',
                'slug' => 'bai-viet-2',
                'image' => 'post2.jpg',
                'content' => 'Nội dung bài viết 2',
                'description' => 'Mô tả bài viết 2',
                'post_type' => 'post',
                'created_by' => 1,
                'status' => 1,
            ],
        ]);
    }
}
