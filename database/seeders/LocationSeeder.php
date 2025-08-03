<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;
class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::create(['name' => 'Head Office', 'latitude' => 18.784149, 'longitude' => 73.339881]);
        Location::create(['name' => 'Branch Office', 'latitude' => 18.751168, 'longitude' => 73.272405]);
    }
}
