<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestJobsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!app()->environment('production')) {
            Job::factory()->count(5)->sequence(
                ['customer_id' => 1],
                ['customer_id' => 2],
                ['customer_id' => 3],
                ['customer_id' => 4],
                ['customer_id' => 5]

            )->create([
                'status_id' => 1,
            ]);
        }
    }
}
