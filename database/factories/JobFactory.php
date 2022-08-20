<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->text(50),
            'description' => $this->faker->paragraph,
            'category_id' => Category::all()->random()->id,
            'status_id' => Status::all()->random()->id,
            'customer_id' => Customer::all()->random()->id,
            'sf_id' => $this->faker->numberBetween(0, 100),
            'sf_job_number' => $this->faker->numberBetween(0, 100),
            'start_date' => $this->faker->date,
            'end_date' => $this->faker->date,
            'start_time' => $this->faker->time,
            'end_time' => $this->faker->time,
            'po' => $this->faker->text(5)
        ];
    }
}
