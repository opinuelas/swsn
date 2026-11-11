<?php

namespace Tests\Feature;

use App\Gender;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PictureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_feed_displays_most_recent_pictures_first(): void
    {
        $user = User::factory()->create();
        $older = Picture::factory()->for($user)->create(['created_at' => now()->subDay()]);
        $newer = Picture::factory()->for($user)->create(['created_at' => now()]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $pictures = $response->viewData('pictures');
        $this->assertTrue($pictures->first()->is($newer));
        $this->assertSame($older->id, $pictures->last()->id);
    }

    public function test_feed_ignores_filter_parameters(): void
    {
        $user = User::factory()->create();
        Picture::factory()->for(User::factory()->create(['gender' => Gender::Male]))->create();
        Picture::factory()->for(User::factory()->create(['gender' => Gender::Female]))->create();

        $response = $this->actingAs($user)->get(route('dashboard', ['gender' => 'male']));

        $this->assertCount(2, $response->viewData('pictures'));
    }

    public function test_user_can_upload_a_picture(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('pictures.store'), [
            'photo' => UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg'),
            'caption' => 'A nice picture',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pictures', [
            'user_id' => $user->id,
            'caption' => 'A nice picture',
        ]);

        $path = Picture::first()->path;
        Storage::disk('public')->assertExists($path);
    }

    public function test_uploading_a_picture_can_set_it_as_the_profile_picture(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)->post(route('pictures.store'), [
            'photo' => UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg'),
            'set_as_profile_picture' => '1',
        ]);

        $picture = Picture::first();
        $this->assertSame($picture->id, $user->fresh()->profile_picture_id);
    }

    public function test_picture_show_page_displays_comments(): void
    {
        $user = User::factory()->create();
        $picture = Picture::factory()->for($user)->create();
        $picture->comments()->create(['user_id' => $user->id, 'body' => 'Nice shot!']);

        $response = $this->actingAs($user)->get(route('pictures.show', $picture));

        $response->assertOk();
        $response->assertSee('Nice shot!');
    }

    public function test_user_can_delete_their_own_picture(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $picture = Picture::factory()->for($user)->create(['path' => 'pictures/photo.jpg']);
        Storage::disk('public')->put($picture->path, 'image');

        $response = $this->actingAs($user)
            ->from(route('profile.pictures'))
            ->delete(route('pictures.destroy', $picture));

        $response->assertRedirect(route('profile.pictures'));
        $this->assertModelMissing($picture);
        Storage::disk('public')->assertMissing('pictures/photo.jpg');
    }

    public function test_deleting_the_profile_picture_clears_it_from_the_user(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $picture = Picture::factory()->for($user)->create();
        $user->update(['profile_picture_id' => $picture->id]);

        $this->actingAs($user)->delete(route('pictures.destroy', $picture));

        $this->assertNull($user->fresh()->profile_picture_id);
    }

    public function test_user_cannot_delete_another_users_picture(): void
    {
        $user = User::factory()->create();
        $picture = Picture::factory()->create();

        $response = $this->actingAs($user)->delete(route('pictures.destroy', $picture));

        $response->assertForbidden();
        $this->assertModelExists($picture);
    }
}
