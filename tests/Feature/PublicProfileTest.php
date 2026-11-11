<?php

namespace Tests\Feature;

use App\Gender;
use App\Models\ProfileVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_profile_shows_basic_information_without_email(): void
    {
        $viewer = User::factory()->create();
        $user = User::factory()->create([
            'userid' => 'janedoe',
            'name' => 'Jane Doe',
            'gender' => Gender::Female,
            'city' => 'Tucson',
            'state' => 'Arizona',
            'country' => 'USA',
            'physical_description' => 'Short and curly hair.',
            'finding_preference' => 'Loves photography.',
        ]);

        $response = $this->actingAs($viewer)->get(route('profiles.show', $user));

        $response->assertOk();
        $response->assertSee('janedoe');
        $response->assertSee('Jane Doe');
        $response->assertSee('Female');
        $response->assertSee('Tucson, Arizona, USA');
        $response->assertSee('Short and curly hair.');
        $response->assertSee('Loves photography.');
        $response->assertDontSee($user->email);
    }

    public function test_visiting_another_users_profile_increments_counter_and_logs_visit(): void
    {
        $visitor = User::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($visitor)->get(route('profiles.show', $user))->assertOk();
        $this->actingAs($visitor)->get(route('profiles.show', $user))->assertOk();

        $this->assertSame(2, $user->fresh()->profile_views_count);
        $this->assertSame(2, ProfileVisit::query()
            ->where('profile_user_id', $user->id)
            ->where('visitor_id', $visitor->id)
            ->count());
    }

    public function test_viewing_own_public_profile_is_not_counted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('profiles.show', $user))->assertOk();

        $this->assertSame(0, $user->fresh()->profile_views_count);
        $this->assertDatabaseEmpty('profile_visits');
    }

    public function test_who_visited_me_lists_only_visits_to_my_profile(): void
    {
        $user = User::factory()->create();
        $visitor = User::factory()->create(['userid' => 'myvisitor']);
        $otherVisitor = User::factory()->create(['userid' => 'othervisitor']);

        ProfileVisit::factory()->create(['profile_user_id' => $user->id, 'visitor_id' => $visitor->id]);
        ProfileVisit::factory()->create(['visitor_id' => $otherVisitor->id]);

        $response = $this->actingAs($user)->get(route('profiles.visitors'));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('visits'));
        $response->assertSee('myvisitor');
        $response->assertDontSee('othervisitor');
    }

    public function test_search_profiles_lists_other_users_without_email(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profiles.index'));

        $response->assertOk();
        $profiles = $response->viewData('profiles');
        $this->assertCount(1, $profiles);
        $this->assertTrue($profiles->first()->is($other));
        $response->assertSee($other->userid);
        $response->assertDontSee($other->email);
    }

    public function test_search_profiles_can_be_filtered_by_gender(): void
    {
        $user = User::factory()->create();
        $male = User::factory()->create(['gender' => Gender::Male]);
        User::factory()->create(['gender' => Gender::Female]);

        $response = $this->actingAs($user)->get(route('profiles.index', ['gender' => 'male']));

        $profiles = $response->viewData('profiles');
        $this->assertCount(1, $profiles);
        $this->assertTrue($profiles->first()->is($male));
    }

    public function test_search_profiles_can_be_searched_by_userid(): void
    {
        $user = User::factory()->create();
        $match = User::factory()->create(['userid' => 'alice99']);
        User::factory()->create(['userid' => 'bob42']);

        $response = $this->actingAs($user)->get(route('profiles.index', ['search' => 'alice']));

        $profiles = $response->viewData('profiles');
        $this->assertCount(1, $profiles);
        $this->assertTrue($profiles->first()->is($match));
    }
}
