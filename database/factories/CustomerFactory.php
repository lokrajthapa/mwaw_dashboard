<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->email,
            'phone_no' => $this->faker->phoneNumber,
            'street_address' => $this->faker->streetAddress,
            'locality' => $this->faker->city,
            'province' => $this->faker->city,
            'postal_code' => $this->faker->postcode,
            'country' => $this->faker->country,
            'buzzer' => $this->faker->randomElement,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'authorized' => $this->faker->boolean,
            'owner_first_name' => $this->faker->firstName,
            'owner_last_name' => $this->faker->lastName,
            'owner_phone_no' => $this->faker->phoneNumber,
            'owner_email' => $this->faker->email,
            'sf_id' => $this->faker->numberBetween(0,100)
        ];
    }
}
