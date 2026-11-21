<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// The forbidden-URL sweep: pages a role must never be able to open. Each gets 403 (wrong role) or a redirect to log in (guest).
class ForbiddenUrlSweepTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_users_get_403_on_pages_of_other_roles(): void
    {
        $forbidden = [
            'student' => ['/modules/create', '/quizzes/create', '/grades/create', '/schedules/create', '/announcements/create',
                '/admin/users', '/admin/users/create', '/admin/links', '/admin/opportunities', '/my-opportunities', '/opportunities/create'],
            'teacher' => ['/admin/users', '/admin/links', '/admin/opportunities', '/my-opportunities', '/opportunities/create', '/my-results'],
            'parent' => ['/forum', '/study-groups', '/modules', '/quizzes', '/my-results', '/modules/create', '/grades/create',
                '/admin/users', '/my-opportunities'],
            'organization' => ['/grades', '/schedules', '/modules', '/quizzes', '/forum', '/study-groups', '/my-results', '/modules/create',
                '/admin/users', '/admin/opportunities'],
            'admin' => ['/my-opportunities', '/opportunities/create', '/my-results'],
        ];

        foreach ($forbidden as $role => $urls) {
            $user = $role === 'student' ? User::factory()->create() : User::factory()->{$role}()->create();

            foreach ($urls as $url) {
                $this->actingAs($user)->get($url)->assertForbidden("$role must get 403 on $url");
            }
        }
    }

    public function test_guests_are_sent_to_the_login_page(): void
    {
        foreach (['/dashboard', '/modules', '/modules/create', '/quizzes', '/grades', '/schedules', '/forum', '/study-groups',
            '/announcements', '/my-results', '/my-opportunities', '/opportunities/create', '/profile',
            '/admin/users', '/admin/links', '/admin/opportunities'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }
}
