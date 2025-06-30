<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Prospecto;

class ProspectoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
        public function run(): void
        {
            Prospecto::factory(10)->create();
        }
}
