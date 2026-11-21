<?php

namespace Tests\Feature;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function registerData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test Person',
            'email' => 'person@example.com',
            'role' => 'student',
            'grade_level' => 'Grade 10',
            'password' => 'password',
            'password_confirmation' => 'password',
        ], $overrides);
    }

    public function test_cannot_register_as_admin(): void
    {
        $response = $this->post('/register', $this->registerData(['role' => 'admin']));

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_can_register_as_parent(): void
    {
        $this->post('/register', $this->registerData(['role' => 'parent', 'grade_level' => null]));

        $this->assertDatabaseHas('users', ['email' => 'person@example.com', 'role' => 'parent', 'grade_level' => null]);
    }

    public function test_student_needs_a_grade_level(): void
    {
        $response = $this->post('/register', $this->registerData(['grade_level' => null]));

        $response->assertSessionHasErrors('grade_level');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_role_middleware_allows_only_listed_roles(): void
    {
        Route::middleware(['web', 'auth', 'role:teacher'])->get('/_test/teacher-only', fn () => 'ok');

        $this->get('/_test/teacher-only')->assertRedirect('/login');

        $this->actingAs(User::factory()->create())->get('/_test/teacher-only')->assertForbidden();

        $this->actingAs(User::factory()->teacher()->create())->get('/_test/teacher-only')->assertOk();
    }

    public function test_teacher_cannot_open_admin_user_list(): void
    {
        $this->actingAs(User::factory()->teacher()->create())
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_cannot_change_own_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.role', $admin), ['role' => 'student', 'grade_level' => 'Grade 10'])
            ->assertSessionHas('error');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_admin_links_parent_to_student_and_parent_sees_grades(): void
    {
        $parent = User::factory()->parent()->create();
        $child = User::factory()->create();
        Grade::factory()->create(['student_id' => $child->id, 'subject' => 'Linked Chemistry']);

        // Before the link the parent sees nothing.
        $this->actingAs($parent)->get(route('grades.index'))->assertDontSee('Linked Chemistry');

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.links.store'), ['parent_id' => $parent->id, 'student_id' => $child->id])
            ->assertRedirect(route('admin.links.index'));

        $this->actingAs($parent)->get(route('grades.index'))->assertSee('Linked Chemistry');
    }
}
