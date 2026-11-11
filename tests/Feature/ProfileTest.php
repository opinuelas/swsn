<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'userid' => 'newuserid',
                'email' => 'test@example.com',
                'cell_number' => $user->cell_number,
                'gender' => $user->gender->value,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('newuserid', $user->userid);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'userid' => $user->userid,
                'email' => $user->email,
                'cell_number' => $user->cell_number,
                'gender' => $user->gender->value,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_physical_description_and_finding_preference_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'userid' => $user->userid,
                'email' => $user->email,
                'cell_number' => $user->cell_number,
                'gender' => $user->gender->value,
                'physical_description' => 'Tall, brown hair.',
                'finding_preference' => 'Someone who likes hiking.',
            ]);

        $response->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertSame('Tall, brown hair.', $user->physical_description);
        $this->assertSame('Someone who likes hiking.', $user->finding_preference);
    }

    public function test_physical_description_and_finding_preference_are_limited_to_500_characters(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'userid' => $user->userid,
                'email' => $user->email,
                'cell_number' => $user->cell_number,
                'gender' => $user->gender->value,
                'physical_description' => str_repeat('a', 501),
                'finding_preference' => str_repeat('a', 501),
            ]);

        $response->assertSessionHasErrors(['physical_description', 'finding_preference']);
    }

    public function test_userid_cannot_be_changed_to_one_already_taken(): void
    {
        User::factory()->create(['userid' => 'taken']);
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'userid' => 'taken',
                'email' => $user->email,
                'cell_number' => $user->cell_number,
                'gender' => $user->gender->value,
            ]);

        $response->assertSessionHasErrors('userid');
    }

    public function test_password_change_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('profile.password'))->assertOk();
    }

    public function test_pictures_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('profile.pictures'))->assertOk();
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
