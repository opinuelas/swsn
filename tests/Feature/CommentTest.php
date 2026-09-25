<?php

namespace Tests\Feature;

use App\Models\Picture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_comment_on_a_picture(): void
    {
        $user = User::factory()->create();
        $picture = Picture::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('pictures.comments.store', $picture), ['body' => 'Great picture!']);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'picture_id' => $picture->id,
            'body' => 'Great picture!',
        ]);
    }

    public function test_comment_body_is_required(): void
    {
        $user = User::factory()->create();
        $picture = Picture::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('pictures.comments.store', $picture), ['body' => '']);

        $response->assertSessionHasErrors('body');
    }
}
