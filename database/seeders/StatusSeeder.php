<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = ['Unscheduled', 'Pending', 'Assigned', 'Started', 'In Progress', 'Finished', 'Revisit'];
        foreach ($statuses as $status) {
            Status::create(['name' => $status, 'category'=>'OPEN_ACTIVE']);
        }
    }
}
