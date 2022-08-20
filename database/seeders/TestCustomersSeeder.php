<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class TestCustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!app()->environment('production')) {
            Customer::factory()->count(5)->sequence(
                [
                    'latitude' => '43.612691',
                    'longitude' => '-79.538733'
                ],
                [
                    'latitude' => '43.777020',
                    'longitude' => '-79.378058'
                ],

                [
                    'latitude' => '43.752722',
                    'longitude' => '-79.282614'
                ],

                [
                    'latitude' => '43.796849',
                    'longitude' => '-79.179618'
                ],

                [
                    'latitude' => '43.815184',
                    'longitude' => '-79.318320'
                ]

            )->create([
                'locality' => 'Toronto',
                'province' => 'ON',
                'country' => 'Canada'
            ]);
        }
    }
}
