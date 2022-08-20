<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlexRecording>
 */
class FlexRecordingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'sid' => $this->faker->text(20),
            'conference_sid' => $this->faker->text(20),
            'call_sid' => $this->faker->text(20),
            'account_sid' => $this->faker->text(20),
            'duration' => $this->faker->text(5),
            'source' => $this->faker->randomElement(['Conference', 'RecordVerb']),
            'media_url' => $this->faker->url,
            'json' => []
        ];
    }
}
