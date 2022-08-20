<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobNumberAlert>
 */
class JobNumberAlertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'no_of_jobs' => $this->faker->numberBetween(0, 10),
            'days' => $this->faker->numberBetween(0, 10),
            'condition' => $this->faker->randomElement(['above', 'below']),
            'category_id' => Category::all()->random()->id,
            'receivers' => ['emails' => [$this->faker->email], 'phone_no' => [$this->faker->phoneNumber]],
        ];
    }
}
