<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestVehiclesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!app()->environment('production')) {
            Vehicle::factory()->count(4)->sequence(
                ['title' => 'Shteyman Bronco', 'vin' => '3FMCR9A66NRD52819', 'user_id' => 2],
                ['title' => 'Shteyman Bronco', 'vin' => '3FMCR9A66NRD51851', 'user_id' => 3],
                ['title' => 'Roman', 'vin' => '3FTTW8F97NRA32239', 'user_id' => 4],
                ['title' => 'Josh Collins', 'vin' => '3FTTW8F97NRA11691', 'user_id' => 5],
            )->create();
        }
    }
}
