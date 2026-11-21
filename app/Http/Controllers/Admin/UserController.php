<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(fn ($inner) => $inner
                    ->whereLike('name', '%'.$request->q.'%')
                    ->orWhereLike('email', '%'.$request->q.'%'));
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->role))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    // Teacher and admin accounts are created here, never by self-registration.
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', Rule::in(User::STAFF_ROLES)],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'grade_level' => null,
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Account created.');
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        // Stops an admin from locking themselves out of the admin panel.
        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $data = $request->validate([
            'role' => ['required', Rule::in(User::ROLES)],
            'grade_level' => ['nullable', 'required_if:role,student', Rule::in(User::GRADE_LEVELS)],
        ]);

        $user->update([
            'role' => $data['role'],
            'grade_level' => $data['role'] === 'student' ? $data['grade_level'] : null,
        ]);

        return back()->with('success', $user->name.' is now '.$data['role'].'.');
    }
}
