<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'gclid' => $this->faker->text(30),
            'source' => $this->faker->text(30),
            'email' => $this->faker->email,
            'phone_no' => $this->faker->phoneNumber
        ];
    }
}
