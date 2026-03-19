<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('attributes')->insert([
            ['name' => 'Brand'],
            ['name' => 'Model'],
            ['name' => 'Camera Type'],
            ['name' => 'Resolution (MP)'],
            ['name' => 'Sensor Type'],
            ['name' => 'Sensor Size'],
            ['name' => 'Image Processor'],
            ['name' => 'Lens Mount'],

            ['name' => 'ISO Range'],
            ['name' => 'Shutter Speed'],
            ['name' => 'Continuous Shooting'],
            ['name' => 'Autofocus System'],
            ['name' => 'Image Stabilization'],
            ['name' => 'Video Resolution'],
            ['name' => 'Video Frame Rate'],

            ['name' => 'LCD Screen Size'],
            ['name' => 'Touch / Articulating Screen'],
            ['name' => 'Viewfinder'],

            ['name' => 'Battery Type'],
            ['name' => 'Battery Life'],
            ['name' => 'Connectivity'],
            ['name' => 'Memory Card Type'],

            ['name' => 'Weight'],
            ['name' => 'Dimensions'],
            ['name' => 'Body Material'],
            ['name' => 'Weather Sealing'],
            ['name' => 'Color'],

            ['name' => 'Warranty'],
            ['name' => 'Origin'],
        ]);
    }
}
