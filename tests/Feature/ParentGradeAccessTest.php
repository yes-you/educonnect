<?php

namespace Tests\Feature;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentGradeAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_sees_linked_childs_grade(): void
    {
        $parent = User::factory()->parent()->create();
        $child = User::factory()->create();
        $parent->children()->attach($child->id);
        Grade::factory()->create(['student_id' => $child->id, 'subject' => 'Linked Algebra']);

        $this->actingAs($parent)->get(route('grades.index'))
            ->assertOk()
            ->assertSee('Linked Algebra');
    }

    public function test_parent_does_not_see_unlinked_students_grade(): void
    {
        $parent = User::factory()->parent()->create();
        $child = User::factory()->create();
        $stranger = User::factory()->create();
        $parent->children()->attach($child->id);
        Grade::factory()->create(['student_id' => $stranger->id, 'subject' => 'Secret Chemistry']);

        $this->actingAs($parent)->get(route('grades.index'))
            ->assertOk()
            ->assertDontSee('Secret Chemistry');
    }

    public function test_student_sees_only_own_grades(): void
    {
        $student = User::factory()->create();
        $other = User::factory()->create();
        Grade::factory()->create(['student_id' => $student->id, 'subject' => 'My Biology']);
        Grade::factory()->create(['student_id' => $other->id, 'subject' => 'Their Physics']);

        $this->actingAs($student)->get(route('grades.index'))
            ->assertOk()
            ->assertSee('My Biology')
            ->assertDontSee('Their Physics');
    }

    public function test_student_cannot_open_grade_create_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('grades.create'))
            ->assertForbidden();
    }

    public function test_duplicate_student_subject_quarter_is_rejected(): void
    {
        $teacher = User::factory()->teacher()->create();
        $student = User::factory()->create();
        $data = ['student_id' => $student->id, 'subject' => 'Math', 'quarter' => 1, 'grade' => 88];

        $this->actingAs($teacher)->post(route('grades.store'), $data)->assertRedirect(route('grades.index'));
        $this->post(route('grades.store'), $data)->assertSessionHasErrors('quarter');

        $this->assertDatabaseCount('grades', 1);
    }
}
