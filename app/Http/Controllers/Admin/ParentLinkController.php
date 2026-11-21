<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ParentLinkController extends Controller
{
    public function index(): View
    {
        // parent_student is a plain pivot table with no model, so read it with the query builder.
        $links = DB::table('parent_student')
            ->join('users as parents', 'parents.id', '=', 'parent_student.parent_id')
            ->join('users as students', 'students.id', '=', 'parent_student.student_id')
            ->select(
                'parent_student.id',
                'parents.name as parent_name',
                'parents.email as parent_email',
                'students.name as student_name',
                'students.grade_level as student_grade'
            )
            ->orderByDesc('parent_student.id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.links.index', [
            'links' => $links,
            'parents' => User::where('role', 'parent')->orderBy('name')->get(),
            'students' => User::where('role', 'student')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'parent_id' => ['required', Rule::exists('users', 'id')->where('role', 'parent')],
            'student_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', 'student'),
                // The same parent cannot be linked to the same student twice.
                Rule::unique('parent_student')->where('parent_id', $request->parent_id),
            ],
        ], [
            'student_id.unique' => 'This parent is already linked to that student.',
        ]);

        User::find($data['parent_id'])->children()->attach($data['student_id'], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.links.index')->with('success', 'Parent linked to student.');
    }

    public function destroy(int $link): RedirectResponse
    {
        $deleted = DB::table('parent_student')->where('id', $link)->delete();

        abort_if($deleted === 0, 404);

        return redirect()->route('admin.links.index')->with('success', 'Link removed.');
    }
}
