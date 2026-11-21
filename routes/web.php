<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ForumReplyController;
use App\Http\Controllers\ForumThreadController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\GroupMessageController;
use App\Http\Controllers\Admin\OpportunityReviewController;
use App\Http\Controllers\Admin\ParentLinkController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudyGroupController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', function () {
    return view('welcome');
});

// Public: anyone may read approved opportunities.
// whereNumber keeps /opportunities/create from being read as an opportunity id.
Route::get('opportunities', [OpportunityController::class, 'index'])->name('opportunities.index');
Route::get('opportunities/{opportunity}', [OpportunityController::class, 'show'])
    ->whereNumber('opportunity')
    ->name('opportunities.show');

// Logged in, every role
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // The policy decides who may open a given attempt.
    Route::get('attempts/{attempt}', [QuizAttemptController::class, 'show'])->name('attempts.show');

    // Every role reads announcements; the controller picks the audience.
    Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
});

// Teacher and admin (create routes come before the {module} / {quiz} wildcard routes)
Route::middleware(['auth', 'role:teacher,admin'])->group(function () {
    Route::resource('modules', ModuleController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('quizzes', QuizController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::patch('quizzes/{quiz}/publish', [QuizController::class, 'publish'])->name('quizzes.publish');
    Route::post('quizzes/{quiz}/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::delete('questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

    Route::resource('announcements', AnnouncementController::class)->only(['create', 'store', 'destroy']);
    Route::resource('grades', GradeController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('schedules', ScheduleController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
});

// Student, parent, teacher and admin
Route::middleware(['auth', 'role:student,parent,teacher,admin'])->group(function () {
    Route::resource('grades', GradeController::class)->only(['index']);
    Route::resource('schedules', ScheduleController::class)->only(['index']);
});

// Student, teacher and admin
Route::middleware(['auth', 'role:student,teacher,admin'])->group(function () {
    Route::resource('modules', ModuleController::class)->only(['index', 'show']);
    Route::get('modules/{module}/download', [ModuleController::class, 'download'])->name('modules.download');
    Route::resource('quizzes', QuizController::class)->only(['index', 'show']);

    // Forum: /forum, /forum/create, /forum/{thread}
    Route::resource('forum', ForumThreadController::class)
        ->parameters(['forum' => 'thread'])
        ->names('threads')
        ->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::post('forum/{thread}/replies', [ForumReplyController::class, 'store'])->name('replies.store');
    Route::delete('replies/{reply}', [ForumReplyController::class, 'destroy'])->name('replies.destroy');

    // Study groups
    Route::resource('study-groups', StudyGroupController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::post('study-groups/{study_group}/join', [StudyGroupController::class, 'join'])->name('study-groups.join');
    Route::delete('study-groups/{study_group}/leave', [StudyGroupController::class, 'leave'])->name('study-groups.leave');
    Route::post('study-groups/{study_group}/messages', [GroupMessageController::class, 'store'])->name('group-messages.store');
});

// Student only
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::post('quizzes/{quiz}/attempts', [QuizAttemptController::class, 'store'])->name('attempts.store');
    Route::get('my-results', [QuizAttemptController::class, 'index'])->name('attempts.index');
});

// Organization only
Route::middleware(['auth', 'role:organization'])->group(function () {
    Route::get('my-opportunities', [OpportunityController::class, 'mine'])->name('opportunities.mine');
    Route::resource('opportunities', OpportunityController::class)->only(['create', 'store', 'edit', 'update']);
});

// Organization (own postings) and admin (any posting)
Route::middleware(['auth', 'role:organization,admin'])->group(function () {
    Route::resource('opportunities', OpportunityController::class)->only(['destroy']);
});

// Admin only: /admin/...
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('opportunities', [OpportunityReviewController::class, 'index'])->name('opportunities.index');
    Route::patch('opportunities/{opportunity}/approve', [OpportunityReviewController::class, 'approve'])->name('opportunities.approve');
    Route::patch('opportunities/{opportunity}/reject', [OpportunityReviewController::class, 'reject'])->name('opportunities.reject');

    // Users (create comes before any {user} route)
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');

    // Parent-student links
    Route::get('links', [ParentLinkController::class, 'index'])->name('links.index');
    Route::post('links', [ParentLinkController::class, 'store'])->name('links.store');
    Route::delete('links/{link}', [ParentLinkController::class, 'destroy'])->name('links.destroy');
});

require __DIR__.'/auth.php';
