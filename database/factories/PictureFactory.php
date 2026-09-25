<?php

namespace Database\Factories;

use App\Models\Picture;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Picture>
 */
class PictureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'path' => 'pictures/'.fake()->uuid().'.jpg',
            'caption' => fake()->optional()->sentence(),
        ];
    }
}
