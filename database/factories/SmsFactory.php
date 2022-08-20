<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sms>
 */
class SmsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'job_id' => $this->faker->text(5),
            'from' => $this->faker->phoneNumber,
            'to' => $this->faker->phoneNumber,
            'body' => $this->faker->paragraph,
            'data' => [],
            'email_sent' => $this->faker->boolean
        ];
    }
}
