<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlexTask>
 */
class FlexTaskFactory extends Factory
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
            'age' => $this->faker->numberBetween(10, 100),
            'status' => $this->faker->text(10),
            'attributes' => ['from' => $this->faker->phoneNumber, 'to' => $this->faker->phoneNumber],
            'queue_name' => $this->faker->text(10),
            'channel_name' => $this->faker->text(10)
        ];
    }
}
