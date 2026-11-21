<?php

namespace Tests\Feature;

use App\Models\Grade;
use App\Models\StudyGroup;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditRound2Test extends TestCase
{
    use RefreshDatabase;

    public function test_the_only_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertSessionHasErrorsIn('userDeletion', 'password');
        $this->assertModelExists($admin);

        // With a second admin the first may leave.
        User::factory()->admin()->create();
        $this->actingAs($admin)->delete(route('profile.destroy'), ['password' => 'password']);
        $this->assertModelMissing($admin);
    }

    public function test_owner_of_a_study_group_cannot_delete_own_account(): void
    {
        $student = User::factory()->create();
        StudyGroup::factory()->create(['owner_id' => $student->id]);

        $this->actingAs($student)->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertSessionHasErrorsIn('userDeletion', 'password');
        $this->assertModelExists($student);
    }

    public function test_teacher_opens_report_card_only_of_students_they_graded(): void
    {
        $teacher = User::factory()->teacher()->create();
        $graded = User::factory()->create();
        $stranger = User::factory()->create();
        Grade::factory()->create(['teacher_id' => $teacher->id, 'student_id' => $graded->id]);

        $this->actingAs($teacher)->get(route('grades.report', $graded))->assertOk();
        $this->actingAs($teacher)->get(route('grades.report', $stranger))->assertForbidden();
    }

    public function test_production_seeder_creates_one_admin_and_demands_a_strong_password(): void
    {
        $this->app['env'] = 'production';

        putenv('ADMIN_PASSWORD=short');
        try {
            $this->artisan('db:seed', ['--force' => true]);
            $this->fail('A weak ADMIN_PASSWORD must stop the seeder.');
        } catch (\RuntimeException) {
            $this->assertDatabaseCount('users', 0);
        }

        putenv('ADMIN_PASSWORD=a-long-secret-123');
        $this->artisan('db:seed', ['--force' => true]);
        putenv('ADMIN_PASSWORD');

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['role' => 'admin']);
    }

    public function test_registration_is_rate_limited(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->post('/register', []);
        }

        $this->post('/register', [])->assertStatus(429);
    }
}
