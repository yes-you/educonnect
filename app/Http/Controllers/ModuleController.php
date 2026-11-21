<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // A student only ever sees their own grade level. Teachers and admin can filter by any grade.
        $gradeLevel = $user->hasRole('student') ? $user->grade_level : $request->input('grade_level');

        $modules = Module::with('teacher')
            ->when($request->filled('q'), fn ($query) => $query->whereLike('title', '%'.$request->q.'%'))
            ->when($request->filled('subject'), fn ($query) => $query->whereLike('subject', '%'.$request->subject.'%'))
            ->when($gradeLevel, fn ($query) => $query->where('grade_level', $gradeLevel))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->type))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('modules.index', compact('modules', 'gradeLevel'));
    }

    public function show(Request $request, Module $module): View
    {
        $this->abortIfOtherGrade($request->user(), $module);

        $module->load('teacher');

        return view('modules.show', compact('module'));
    }

    public function download(Request $request, Module $module)
    {
        $this->abortIfOtherGrade($request->user(), $module);

        abort_unless(Storage::disk('public')->exists($module->file_path), 404);

        return Storage::disk('public')->download($module->file_path, $module->original_name);
    }

    public function create(): View
    {
        return view('modules.create', ['module' => new Module(['type' => 'module'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(fileRequired: true));

        $file = $request->file('file');

        Module::create([
            'teacher_id' => $request->user()->id,
            'title' => $data['title'],
            'subject' => $data['subject'],
            'grade_level' => $data['grade_level'],
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'file_path' => $file->store('modules', 'public'),
            'original_name' => $file->getClientOriginalName(),
        ]);

        return redirect()->route('modules.index')->with('success', 'Module uploaded.');
    }

    public function edit(Module $module): View
    {
        Gate::authorize('update', $module);

        return view('modules.edit', compact('module'));
    }

    public function update(Request $request, Module $module): RedirectResponse
    {
        Gate::authorize('update', $module);

        $data = $request->validate($this->rules(fileRequired: false));

        $module->fill([
            'title' => $data['title'],
            'subject' => $data['subject'],
            'grade_level' => $data['grade_level'],
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
        ]);

        if ($request->hasFile('file')) {
            $oldPath = $module->file_path;
            $file = $request->file('file');
            $module->file_path = $file->store('modules', 'public');
            $module->original_name = $file->getClientOriginalName();
            $module->save();
            $this->deleteFileIfUnused($oldPath, $module->id);
        } else {
            $module->save();
        }

        return redirect()->route('modules.show', $module)->with('success', 'Module updated.');
    }

    public function destroy(Module $module): RedirectResponse
    {
        Gate::authorize('delete', $module);

        $module->delete();
        $this->deleteFileIfUnused($module->file_path, $module->id);

        return redirect()->route('modules.index')->with('success', 'Module deleted.');
    }

    // A student may only open modules of their own grade level.
    private function abortIfOtherGrade(User $user, Module $module): void
    {
        abort_if($user->hasRole('student') && $module->grade_level !== $user->grade_level, 403);
    }

    private function rules(bool $fileRequired): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:100'],
            'grade_level' => ['required', Rule::in(User::GRADE_LEVELS)],
            'type' => ['required', Rule::in(Module::TYPES)],
            'description' => ['nullable', 'string', 'max:5000'],
            'file' => [$fileRequired ? 'required' : 'nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:20480'],
        ];
    }

    // Seeded demo modules share one sample file, so only delete a file that no other module uses.
    private function deleteFileIfUnused(string $path, int $exceptModuleId): void
    {
        $stillUsed = Module::where('file_path', $path)->where('id', '!=', $exceptModuleId)->exists();

        if (! $stillUsed) {
            Storage::disk('public')->delete($path);
        }
    }
}
