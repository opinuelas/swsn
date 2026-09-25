<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_send_a_message(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $response = $this->actingAs($sender)
            ->post(route('messages.store', $recipient), ['body' => 'Hello there']);

        $response->assertRedirect();
        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'body' => 'Hello there',
        ]);
    }

    public function test_user_cannot_message_themselves(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('messages.store', $user), ['body' => 'Hello there']);

        $response->assertNotFound();
    }

    public function test_user_can_start_a_new_conversation_by_email(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $response = $this->actingAs($sender)
            ->post(route('messages.send'), ['to' => $recipient->email, 'body' => 'Hi there!']);

        $response->assertRedirect(route('messages.show', $recipient));
        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'body' => 'Hi there!',
        ]);
    }

    public function test_new_conversation_requires_an_existing_email(): void
    {
        $sender = User::factory()->create();

        $response = $this->actingAs($sender)
            ->post(route('messages.send'), ['to' => 'nobody@example.com', 'body' => 'Hi there!']);

        $response->assertSessionHasErrors('to');
    }

    public function test_user_cannot_start_a_new_conversation_with_themselves(): void
    {
        $sender = User::factory()->create();

        $response = $this->actingAs($sender)
            ->post(route('messages.send'), ['to' => $sender->email, 'body' => 'Hi there!']);

        $response->assertSessionHasErrors('to');
    }

    public function test_viewing_a_thread_marks_received_messages_as_read(): void
    {
        $user = User::factory()->create();
        $partner = User::factory()->create();

        $message = Message::factory()->create([
            'sender_id' => $partner->id,
            'recipient_id' => $user->id,
        ]);

        $this->actingAs($user)->get(route('messages.show', $partner))->assertOk();

        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_conversation_list_shows_partners_and_unread_counts(): void
    {
        $user = User::factory()->create();
        $partner = User::factory()->create();

        Message::factory()->create([
            'sender_id' => $partner->id,
            'recipient_id' => $user->id,
            'body' => 'Hi!',
        ]);

        $response = $this->actingAs($user)->get(route('messages.index'));

        $response->assertOk();
        $conversations = $response->viewData('conversations');
        $this->assertCount(1, $conversations);
        $this->assertTrue($conversations->first()->partner->is($partner));
        $this->assertSame(1, $conversations->first()->unread_count);
    }
}
