<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!app()->environment('production')) {
            User::create([
                'name' => 'User',
                'email' => 'user@gmail.com',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'role' => 'admin'
            ]);

            //create fake technicians
            User::factory()->count(5)->sequence(
                [
                    'latitude' => '43.740395',
                    'longitude' => '-79.572901'
                ],
                [
                    'latitude' => '43.714393',
                    'longitude' => '-79.406982'
                ],

                [
                    'latitude' => '43.740395',
                    'longitude' => '-79.256054'
                ],

                [
                    'latitude' => '43.668861',
                    'longitude' => '-79.325021'
                ],

                [
                    'latitude' => '43.706445',
                    'longitude' => '-79.458956'
                ]

            )->create([
                'role' => 'technician',
                'locality' => 'Toronto',
                'province' => 'ON',
                'country' => 'Canada'
            ]);
        }
    }
}
