<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChallanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          Challan::factory()->count(10)->create();
    }
}
