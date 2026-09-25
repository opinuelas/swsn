<?php

namespace Tests\Feature;

use App\Models\Picture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_a_picture(): void
    {
        $user = User::factory()->create();
        $picture = Picture::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('pictures.like', $picture));

        $response->assertOk()->assertJson(['liked' => true, 'count' => 1]);
        $this->assertDatabaseHas('likes', ['user_id' => $user->id, 'picture_id' => $picture->id]);
    }

    public function test_user_can_unlike_a_previously_liked_picture(): void
    {
        $user = User::factory()->create();
        $picture = Picture::factory()->create();
        $picture->likes()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->postJson(route('pictures.like', $picture));

        $response->assertOk()->assertJson(['liked' => false, 'count' => 0]);
        $this->assertDatabaseMissing('likes', ['user_id' => $user->id, 'picture_id' => $picture->id]);
    }
}
