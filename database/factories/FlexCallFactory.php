<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlexCall>
 */
class FlexCallFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'sid' => $this->faker->text(10),
            'status' => $this->faker->text(5),
            'direction' => $this->faker->text(5),
            'from' => $this->faker->phoneNumber,
            'to' => $this->faker->phoneNumber,
            'duration' => $this->faker->text(5),
        ];
    }
}
