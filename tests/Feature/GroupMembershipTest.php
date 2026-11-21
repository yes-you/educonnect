<?php

namespace Tests\Feature;

use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupMembershipTest extends TestCase
{
    use RefreshDatabase;

    // A group owned by someone else, with the owner as its only member.
    private function groupWithOwner(): StudyGroup
    {
        $group = StudyGroup::factory()->create();
        $group->members()->attach($group->owner_id);

        return $group;
    }

    public function test_non_member_cannot_open_group(): void
    {
        $group = $this->groupWithOwner();

        $this->actingAs(User::factory()->create())
            ->get(route('study-groups.show', $group))
            ->assertForbidden();
    }

    public function test_non_member_cannot_post_message(): void
    {
        $group = $this->groupWithOwner();

        $this->actingAs(User::factory()->create())
            ->post(route('group-messages.store', $group), ['body' => 'Let me in'])
            ->assertForbidden();

        $this->assertDatabaseCount('group_messages', 0);
    }

    public function test_member_can_post_after_joining(): void
    {
        $group = $this->groupWithOwner();
        $student = User::factory()->create();

        $this->actingAs($student)->post(route('study-groups.join', $group))->assertRedirect();
        $this->post(route('group-messages.store', $group), ['body' => 'Hello group'])->assertRedirect();

        $this->get(route('study-groups.show', $group))
            ->assertOk()
            ->assertSee('Hello group');
    }

    public function test_parent_cannot_open_forum(): void
    {
        $this->actingAs(User::factory()->parent()->create())
            ->get(route('threads.index'))
            ->assertForbidden();
    }
}
