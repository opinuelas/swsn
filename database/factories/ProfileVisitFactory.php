<?php

namespace Database\Factories;

use App\Models\ProfileVisit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProfileVisit>
 */
class ProfileVisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'profile_user_id' => User::factory(),
            'visitor_id' => User::factory(),
        ];
    }
}
