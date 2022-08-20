<?php

namespace Database\Seeders;

use App\Models\JobNumberAlert;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestJobNumberAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!app()->environment('production')) {
            JobNumberAlert::factory()->count('5')->create();
        }
    }
}
