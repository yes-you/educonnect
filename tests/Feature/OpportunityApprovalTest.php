<?php

namespace Tests\Feature;

use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpportunityApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_posting_is_hidden_from_the_public(): void
    {
        $opportunity = Opportunity::factory()->create(['title' => 'Pending Scholarship', 'status' => 'pending']);

        $this->get(route('opportunities.index'))->assertOk()->assertDontSee('Pending Scholarship');
        $this->get(route('opportunities.show', $opportunity))->assertNotFound();
    }

    public function test_organization_cannot_approve(): void
    {
        $org = User::factory()->organization()->create();
        $opportunity = Opportunity::factory()->create(['user_id' => $org->id]);

        $this->actingAs($org)
            ->patch(route('admin.opportunities.approve', $opportunity))
            ->assertForbidden();

        $this->assertSame('pending', $opportunity->fresh()->status);
    }

    public function test_approved_posting_appears_on_the_public_page(): void
    {
        $opportunity = Opportunity::factory()->create(['title' => 'Approved Tutoring', 'status' => 'pending']);

        $this->actingAs(User::factory()->admin()->create())
            ->patch(route('admin.opportunities.approve', $opportunity))
            ->assertRedirect(route('admin.opportunities.index'));

        auth()->logout();

        $this->get(route('opportunities.index'))->assertOk()->assertSee('Approved Tutoring');
        $this->get(route('opportunities.show', $opportunity))->assertOk();
    }

    public function test_editing_an_approved_posting_sends_it_back_to_pending(): void
    {
        $org = User::factory()->organization()->create();
        $opportunity = Opportunity::factory()->approved()->create(['user_id' => $org->id]);

        $this->actingAs($org)->patch(route('opportunities.update', $opportunity), [
            'type' => 'event',
            'title' => 'Changed title',
            'description' => 'Changed description',
        ])->assertRedirect(route('opportunities.mine'));

        $this->assertSame('pending', $opportunity->fresh()->status);
    }
}
