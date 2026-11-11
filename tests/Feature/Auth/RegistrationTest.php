<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'userid' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'cell_number' => '5555550100',
            'gender' => 'male',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'userid' => 'testuser']);
    }

    public function test_registration_requires_a_userid(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'cell_number' => '5555550100',
            'gender' => 'male',
        ]);

        $response->assertSessionHasErrors('userid');
        $this->assertGuest();
    }

    public function test_registration_rejects_a_userid_longer_than_15_characters(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'userid' => str_repeat('a', 16),
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'cell_number' => '5555550100',
            'gender' => 'male',
        ]);

        $response->assertSessionHasErrors('userid');
        $this->assertGuest();
    }

    public function test_registration_rejects_a_userid_that_is_already_taken(): void
    {
        User::factory()->create(['userid' => 'taken']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'userid' => 'taken',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'cell_number' => '5555550100',
            'gender' => 'male',
        ]);

        $response->assertSessionHasErrors(['userid' => 'This User ID is already taken. Please choose another one.']);
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }
}
