<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('banners')->insert([
            [
                'name'        => 'Banner 1',
                'image'       => 'banner1.jpg',
                'link'        => '#',
                'position'    => 'slideshow',
                'sort_order'  => 1,
                'description' => 'Slideshow banner số 1',
                'status'      => 1,
                'created_by'  => 1,
            ],
            [
                'name'        => 'Banner 2',
                'image'       => 'banner2.jpg',
                'link'        => '#',
                'position'    => 'slideshow',
                'sort_order'  => 2,
                'description' => 'Slideshow banner số 2',
                'status'      => 1,
                'created_by'  => 1,
            ],
            [
                'name'        => 'QC Sidebar 1',
                'image'       => 'ads1.jpg',
                'link'        => 'https://example.com',
                'position'    => 'ads',
                'sort_order'  => 1,
                'description' => 'Banner quảng cáo sidebar',
                'status'      => 1,
                'created_by'  => 1,
            ],
        ]);
    }
}
