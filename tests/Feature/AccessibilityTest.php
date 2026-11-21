<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessibilityTest extends TestCase
{
    use RefreshDatabase;

    // The pages each role can open without needing a record id.
    private function pagesByRole(): array
    {
        return [
            'guest' => [null, ['/', route('login'), route('register'), route('password.request'), route('opportunities.index')]],
            'student' => [User::factory()->create(), [
                route('dashboard'), route('modules.index'), route('quizzes.index'), route('attempts.index'),
                route('grades.index'), route('schedules.index'), route('announcements.index'), route('threads.index'),
                route('threads.create'), route('study-groups.index'), route('study-groups.create'), route('profile.edit'),
            ]],
            'teacher' => [User::factory()->teacher()->create(), [
                route('dashboard'), route('modules.create'), route('quizzes.create'), route('grades.index'),
                route('grades.create'), route('schedules.create'), route('announcements.create'),
            ]],
            'parent' => [User::factory()->parent()->create(), [route('dashboard'), route('grades.index'), route('schedules.index')]],
            'organization' => [User::factory()->organization()->create(), [
                route('dashboard'), route('opportunities.mine'), route('opportunities.create'),
            ]],
            'admin' => [User::factory()->admin()->create(), [
                route('dashboard'), route('admin.users.index'), route('admin.users.create'),
                route('admin.links.index'), route('admin.opportunities.index'),
            ]],
        ];
    }

    public function test_every_page_has_exactly_one_h1(): void
    {
        foreach ($this->pagesByRole() as $role => [$user, $urls]) {
            foreach ($urls as $url) {
                $response = ($user ? $this->actingAs($user) : $this)->get($url);

                $response->assertOk();
                $this->assertSame(1, substr_count($response->getContent(), '<h1'), "$role: $url should have exactly one <h1>");
            }
        }
    }

    public function test_every_table_is_wrapped_so_it_scrolls_on_a_phone(): void
    {
        foreach ($this->pagesByRole() as $role => [$user, $urls]) {
            foreach ($urls as $url) {
                $html = ($user ? $this->actingAs($user) : $this)->get($url)->getContent();

                $this->assertSame(
                    substr_count($html, '<table'),
                    substr_count($html, '<div class="table-responsive'),
                    "$role: every table on $url must sit inside .table-responsive"
                );
            }
        }
    }

    public function test_text_size_and_contrast_controls_are_in_the_navbar(): void
    {
        $this->actingAs(User::factory()->create())->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-font-scale="100"', false)
            ->assertSee('data-font-scale="115"', false)
            ->assertSee('data-font-scale="130"', false)
            ->assertSee('data-contrast-toggle', false)
            ->assertSee('aria-pressed="false"', false)
            ->assertSee('High contrast');
    }

    public function test_required_field_stars_are_hidden_from_screen_readers(): void
    {
        $teacher = User::factory()->teacher()->create();

        foreach ([route('modules.create'), route('quizzes.create'), route('grades.create'), route('schedules.create')] as $url) {
            $this->actingAs($teacher)->get($url)
                ->assertOk()
                ->assertDontSee('<span class="text-danger">*</span>', false)
                ->assertSee('aria-hidden="true">*</span>', false);
        }
    }
}
