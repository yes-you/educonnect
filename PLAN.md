# EduConnect Build Plan (for Claude Code)

Versions: Laravel 13.x on PHP 8.5.7 (XAMPP, Windows), Laravel Breeze 2.x (Blade), MySQL/MariaDB, Bootstrap 5.3.3 by CDN.
Builder: Quit, solo. Mode: one phase at a time, stop after each phase (see CLAUDE.md).

## Progress

- [x] Phase 0 — Install and configure
- [x] Phase 1 — Layout, roles, registration
- [x] Phase 2 — Database: all tables, models, relationships, accounts
- [x] Phase 3 — Learning resources (modules, study guides, quizzes)
- [x] Phase 4 — School-parent communication (announcements, grades, schedules)
- [x] Phase 5 — Collaborative learning (forum, study groups)
- [x] Phase 6 — Community opportunities
- [x] Phase 7 — Role dashboards and admin panel
- [x] Phase 8 — Accessibility and mobile pass
- [x] Phase 9 — Suggested features (features 1 to 4 built; feature 5, saved modules, skipped)
- [ ] Phase 10 — Demo data, final checks, handover

Rough time per phase: P0 45m · P1 45m · P2 45m · P3 1h45m · P4 1h30m · P5 1h15m · P6 45m · P7 1h · P8 45m · P9 1h · P10 45m (about 11 hours).

---

## Project summary

EduConnect is a web platform for students, teachers, parents, school administrators and local organizations.

| # | Objective | Built in |
|---|---|---|
| 1 | Enhance learning resources: digital modules, practice quizzes, study guides | Phase 3 |
| 2 | Strengthen school-parent communication: announcements, grades, schedules | Phase 4 |
| 3 | Support collaborative learning: forums and group study spaces | Phase 5 |
| 4 | Encourage community involvement: scholarships, tutoring programs, events | Phase 6 |
| 5 | Promote accessibility: mobile-friendly and inclusive | Phase 8 (plus rules applied in every phase) |

## Roles

| Role | How the account is created | Main abilities |
|---|---|---|
| admin | Seeder, or another admin | Everything; manage users and roles; link parents to students; approve community posts |
| teacher | Created by admin | Upload modules and study guides; build quizzes; post announcements; encode grades; manage schedules; join forum and groups |
| student | Self-registration | Read/download modules; take quizzes; view own grades and schedule; forum; study groups |
| parent | Self-registration, then admin links them to their child | View linked children's grades, quiz results and schedules; read announcements |
| organization | Self-registration | Post scholarships, tutoring programs and events (shown only after admin approval) |

Self-registration allows only student, parent and organization, so nobody can make themselves a teacher or admin.

Grade levels (constant `User::GRADE_LEVELS`): Grade 7, Grade 8, Grade 9, Grade 10, Grade 11, Grade 12 (Philippine junior and senior high school).

---

## Phase 0 — Install and configure

Goal: a running Laravel 13 + Breeze app on MySQL with login and register working.

Note: this folder already holds CLAUDE.md and PLAN.md, and `composer create-project` refuses a non-empty folder. So the app is created in `_install` and moved up.

Steps
1. Prerequisite checks. Report the results; if anything is missing, tell Quit exactly what to fix and STOP.
   - `php -v` → 8.5.x, and `which php` points into `C:/xampp/php`.
   - `php -m` must list: `pdo_mysql`, `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `fileinfo`, `zip`, `curl`.
     If one is missing, tell Quit which `;extension=...` line to uncomment in `C:\xampp\php\php.ini`.
   - `composer -V`, `node -v` (20 or newer), `npm -v`, `git --version`.
   - MySQL reachable: `C:/xampp/mysql/bin/mysql.exe -u root -e "SELECT VERSION();"`. If it fails, ask Quit to start MySQL in the XAMPP Control Panel.
2. Create the app:
   ```
   composer create-project "laravel/laravel:^13.0" _install --no-interaction
   shopt -s dotglob
   mv -n _install/* .
   ls -A _install
   rmdir _install
   ```
   `mv -n` never overwrites our CLAUDE.md or PLAN.md. If `_install` is not empty, report what is left.
3. Create the database:
   ```
   C:/xampp/mysql/bin/mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS educonnect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```
4. Edit `.env`:
   ```
   APP_NAME=EduConnect
   APP_URL=http://127.0.0.1:8000
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=educonnect
   DB_USERNAME=root
   DB_PASSWORD=
   MAIL_MAILER=log
   FILESYSTEM_DISK=local
   ```
   Uncomment the DB_ lines if the skeleton has them commented. Make `.env.example` match (without APP_KEY).
5. In `config/app.php` set `'timezone' => 'Asia/Manila'`.
6. Install Breeze:
   ```
   composer require laravel/breeze --dev
   php artisan breeze:install blade --no-interaction
   npm install
   npm run build
   php artisan migrate
   php artisan storage:link
   ```
7. Create `docs/WHY.md` with the heading `# EduConnect — Why we built it this way`.
8. `git init` (Laravel's `.gitignore` already excludes `vendor`, `node_modules` and `.env`). Do not commit.

Checks
- `php artisan test` passes (Breeze's own tests).
- `php artisan about` shows Laravel 13.x, PHP 8.5.7, database mysql.

Browser test for Quit
1. Run `php artisan serve`, open http://127.0.0.1:8000
2. Register an account, log out, log in again.

WHY.md points
- Laravel 13 needs PHP 8.3 or newer; XAMPP's PHP 8.5.7 meets that, so we use the current supported version.
- Breeze publishes its login and register code into our own app folder, so every line of authentication is visible and explainable.

---

## Phase 1 — Layout, roles, registration

Goal: Bootstrap layout, a role on every user, role-protected routes, and registration that asks for the role.

Create
1. `database/migrations/2026_11_17_000001_add_role_and_grade_level_to_users_table.php`
   - `role` string(20), default `student`, after `email`, indexed.
   - `grade_level` string(20), nullable, after `role`.
   - `down()` drops both columns.
2. `app/Http/Middleware/RoleMiddleware.php`
   - `handle(Request $request, Closure $next, string ...$roles)`: `abort_unless($request->user() && in_array($request->user()->role, $roles, true), 403);`
3. `resources/views/layouts/main.blade.php`
   - `<html lang="en">`, viewport meta, `<title>@yield('title', 'EduConnect') — EduConnect</title>`.
   - Bootstrap 5.3.3 CSS + Bootstrap Icons 1.11.3 CSS (jsDelivr CDN), then `public/css/educonnect.css` via `asset()`.
   - Skip link `<a href="#main" class="visually-hidden-focusable">Skip to main content</a>`.
   - `@include('layouts.partials.navbar')`, `<main id="main" class="container py-4">`, `@include('layouts.partials.flash')`, `@yield('content')`.
   - Footer with "EduConnect" and year.
   - Bootstrap 5.3.3 bundle JS (CDN), then `public/js/educonnect.js`, then `@stack('scripts')`.
4. `resources/views/layouts/partials/navbar.blade.php`
   - Responsive `navbar-expand-lg` with toggler (`aria-controls`, `aria-expanded`, `aria-label="Toggle navigation"`).
   - Guest: Home, Opportunities, Log in, Register.
   - Logged in: links by role (each wrapped in `@if (Route::has(...))`):
     - all: Dashboard, Announcements
     - student/teacher/admin: Modules, Quizzes, Forum, Study Groups
     - student/parent/teacher/admin: Grades, Schedule
     - organization: My Postings
     - admin: Admin dropdown (Users, Parent Links, Pending Postings)
     - all: Opportunities
   - User dropdown: name + role badge, Profile (`profile.edit`), Log out (POST form to `logout`).
   - Active link gets `active` class and `aria-current="page"` (use `request()->routeIs(...)`).
5. `resources/views/layouts/partials/flash.blade.php` — dismissible `alert-success` for `session('success')`, `alert-danger` for `session('error')`.
6. `resources/views/partials/form-errors.blade.php` — `alert-danger` listing `$errors->all()` when any (`role="alert"`).
7. `public/css/educonnect.css` — base styles: visible focus outline (`:focus-visible { outline: 3px solid #0d6efd; outline-offset: 2px; }`), card spacing. (Accessibility toggles are added in Phase 8.)
8. `public/js/educonnect.js` — empty IIFE with a comment line naming the file's purpose (filled in Phase 8).
9. `tests/Feature/RoleAccessTest.php` (see Checks).

Change
1. `app/Models/User.php`
   - Add `role`, `grade_level` to `$fillable`.
   - Constants: `ROLES = ['admin','teacher','student','parent','organization']`, `SELF_REGISTER_ROLES = ['student','parent','organization']`, `GRADE_LEVELS = ['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12']`.
   - Method `hasRole(string ...$roles): bool`.
2. `bootstrap/app.php` — inside `withMiddleware`: `$middleware->alias(['role' => \App\Http\Middleware\RoleMiddleware::class]);`
3. `app/Http/Controllers/Auth/RegisteredUserController.php` — `store()` also validates
   `'role' => ['required', Rule::in(User::SELF_REGISTER_ROLES)]`,
   `'grade_level' => ['nullable', 'required_if:role,student', Rule::in(User::GRADE_LEVELS)]`
   and saves both (save `grade_level` only when role is student, otherwise null).
4. `resources/views/auth/register.blade.php` — add a "I am a" select (Student, Parent, Organization) and a "Grade level (students only)" select, using Breeze's existing `x-input-label`, `x-input-error` components and matching Tailwind classes.
5. `database/factories/UserFactory.php` — default `role` = `student`, `grade_level` = `Grade 10`; add states `admin()`, `teacher()`, `parent()`, `organization()` (non-students get `grade_level` null).
6. `app/Providers/AppServiceProvider.php` — in `boot()`: `Paginator::useBootstrapFive();`
7. `resources/views/welcome.blade.php` — replace with a Bootstrap landing page extending `layouts.main`: hero (title, one-line purpose, Log in / Register or Go to Dashboard), five cards for the five objectives with Bootstrap Icons, and a link to Opportunities (guarded with `Route::has`).
8. `resources/views/dashboard.blade.php` — replace with a page extending `layouts.main`: welcome card with user name and role badge. (Role widgets come in Phase 7.)

Checks
- `php artisan migrate`
- `tests/Feature/RoleAccessTest.php` covers:
  1. Registering with role `admin` fails with a `role` validation error and creates no user.
  2. Registering with role `parent` succeeds and saves role `parent`.
  3. Registering as `student` without grade level fails on `grade_level`.
  4. Role middleware: inside the test, register `Route::middleware(['web','auth','role:teacher'])->get('/_test/teacher-only', fn () => 'ok');` then: guest → redirect to login, student → 403, teacher → 200.
- `php artisan test` all green.

Browser test for Quit
1. Home page shows the Bootstrap landing page; shrink the browser to phone width, the navbar collapses.
2. Register as a student with a grade level; the dashboard shows your name and "student".

WHY.md points
- One `role` column plus one middleware: the five roles are fixed, so a permissions package would add setup without benefit.
- Self-registration is limited to student, parent and organization so no one can grant themselves teacher or admin power.
- Bootstrap by CDN: no build step for our pages, and its grid makes every page mobile-friendly.

---

## Phase 2 — Database: all tables, models, relationships, accounts

Goal: every table the app needs, with models, relationships, factories and the six seeded accounts.

Migrations (in this order, file prefix `2026_11_17_0000NN_`). All tables have `id()` and `timestamps()`.

| # | Table | Columns |
|---|---|---|
| 02 | parent_student | parent_id → users (cascade), student_id → users (cascade); unique(parent_id, student_id) |
| 03 | modules | teacher_id → users (cascade); title string; subject string(100); grade_level string(20); type string(20) default `module`; description text nullable; file_path string; original_name string |
| 04 | quizzes | teacher_id → users (cascade); module_id → modules nullable (nullOnDelete); title string; subject string(100); grade_level string(20); description text nullable; is_published boolean default false |
| 05 | questions | quiz_id → quizzes (cascade); question text; option_a, option_b, option_c, option_d string; correct_option string(1) |
| 06 | quiz_attempts | quiz_id → quizzes (cascade); user_id → users (cascade); score unsignedSmallInteger; total unsignedSmallInteger; answers json |
| 07 | announcements | user_id → users (cascade); title string; body text; audience string(20) default `all` |
| 08 | grades | student_id → users (cascade); teacher_id → users (cascade); subject string(100); quarter unsignedTinyInteger; grade decimal(5,2); remarks string nullable; unique(student_id, subject, quarter) |
| 09 | schedules | user_id → users (cascade); title string; subject string(100); grade_level string(20); day string(10); start_time time; end_time time; room string(50) nullable |
| 10 | forum_threads | user_id → users (cascade); title string; body text |
| 11 | forum_replies | forum_thread_id → forum_threads (cascade); user_id → users (cascade); body text |
| 12 | study_groups | owner_id → users (cascade); name string; subject string(100) nullable; description text nullable |
| 13 | study_group_user | study_group_id → study_groups (cascade); user_id → users (cascade); unique(study_group_id, user_id) |
| 14 | group_messages | study_group_id → study_groups (cascade); user_id → users (cascade); body text |
| 15 | opportunities | user_id → users (cascade); type string(20); title string; description text; location string nullable; event_date date nullable; deadline date nullable; link string nullable; status string(20) default `pending` |

Models (`app/Models`) with `$fillable`, `casts()`, constants and relationships

- User: `children()` belongsToMany(User, 'parent_student', 'parent_id', 'student_id'); `parents()` belongsToMany(User, 'parent_student', 'student_id', 'parent_id'); `modules()` hasMany(Module, 'teacher_id'); `quizzes()` hasMany(Quiz, 'teacher_id'); `quizAttempts()` hasMany; `grades()` hasMany(Grade, 'student_id'); `studyGroups()` belongsToMany(StudyGroup) withTimestamps; `ownedStudyGroups()` hasMany(StudyGroup, 'owner_id'); `opportunities()` hasMany; `forumThreads()` hasMany.
- Module: `TYPES = ['module','study_guide']`; `teacher()` belongsTo(User, 'teacher_id'); `quizzes()` hasMany.
- Quiz: casts `is_published` boolean; `teacher()`, `module()`, `questions()` hasMany, `attempts()` hasMany(QuizAttempt).
- Question: `OPTIONS = ['a','b','c','d']`; `quiz()`.
- QuizAttempt: casts `answers` array; `quiz()`, `user()`; accessor-free helper `percentage(): int` (rounded score / total * 100, 0 when total is 0).
- Announcement: `AUDIENCES = ['all','students','parents']`; `author()` belongsTo(User, 'user_id').
- Grade: casts `grade` 'decimal:2'; `QUARTERS = [1,2,3,4]`; `student()`, `teacher()` belongsTo(User, ...).
- Schedule: `DAYS = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']`; casts `start_time`, `end_time` 'datetime:H:i'; `creator()` belongsTo(User, 'user_id').
- ForumThread: `author()` belongsTo(User, 'user_id'); `replies()` hasMany(ForumReply).
- ForumReply: `thread()` belongsTo(ForumThread, 'forum_thread_id'); `author()` belongsTo(User, 'user_id').
- StudyGroup: `owner()` belongsTo(User, 'owner_id'); `members()` belongsToMany(User) withTimestamps; `messages()` hasMany(GroupMessage); helper `hasMember(User $user): bool`.
- GroupMessage: `group()` belongsTo(StudyGroup, 'study_group_id'); `author()` belongsTo(User, 'user_id').
- Opportunity: `TYPES = ['scholarship','tutoring','event']`, `STATUSES = ['pending','approved','rejected']`; casts `event_date`, `deadline` 'date'; `organization()` belongsTo(User, 'user_id'); scope `approved()`.

Factories (`database/factories`): ModuleFactory, QuizFactory, QuestionFactory, GradeFactory, StudyGroupFactory, OpportunityFactory, AnnouncementFactory. Use realistic values from the model constants. ModuleFactory uses `file_path` `modules/sample.pdf`, `original_name` `sample.pdf`.

Seeders
- `database/seeders/UserSeeder.php` — the six accounts in CLAUDE.md (password `password` via `Hash::make`), and links parent@ to student@ in `parent_student`.
- `database/seeders/DatabaseSeeder.php` — calls `UserSeeder` only (demo content comes in Phase 10).

Checks
- `php artisan migrate:fresh --seed` (allowed in this phase; say so in the report).
- `php artisan test` all green.
- `php artisan tinker --execute="echo App\Models\User::where('email','parent@educonnect.test')->first()->children->pluck('email');"` prints student@.

Browser test for Quit
1. phpMyAdmin → database `educonnect` shows all 15 tables plus Laravel's own.
2. Log in as each seeded account; the dashboard shows the right role.

WHY.md points
- All tables are created in one phase so later features never force a table redesign.
- Study guides share the modules table with a `type` column, so one controller and one page serve both.
- `parent_student` is a pivot table because one parent can have several children and one child can have two parents.

---

## Phase 3 — Learning resources (Objective 1)

Goal: teachers upload modules/study guides and build multiple-choice quizzes; students download, take quizzes and see scored results.

Controllers
1. `ModuleController` (resource)
   - index: all roles in the route group; filters by `subject`, `grade_level`, `type` from the query string; students default to their own grade level (they can clear it).
   - show: details and download button.
   - download: `Storage::disk('public')->download($module->file_path, $module->original_name)`.
   - create/store/edit/update/destroy: teacher, admin. Upload rule: `required|file|mimes:pdf,doc,docx,ppt,pptx|max:20480` (optional on update). On update with a new file or on destroy, delete the old file from the disk.
2. `QuizController` (resource)
   - index: students see only published quizzes for any grade (filter by grade); teachers see their own quizzes plus a "Create quiz" button; admin sees all.
   - show: teacher/admin → `quizzes/manage` (questions list, add-question form, publish toggle); student → `quizzes/take` (only if published, else 404).
   - create/store/edit/update/destroy: teacher, admin (QuizPolicy for owner).
   - publish (PATCH): toggles `is_published`; cannot publish with zero questions (redirect back with error).
3. `QuestionController`: store (POST `quizzes/{quiz}/questions`), destroy (DELETE `questions/{question}`), owner teacher or admin.
4. `QuizAttemptController`
   - store (student): validate `answers` required array, and `answers.{question_id}` required `in:a,b,c,d` for every question of the quiz. Score on the server by comparing with `correct_option`. Save `answers` JSON, `score`, `total`. Redirect to show.
   - show: per question → student's choice, correct choice, check/cross icon; score and percentage. QuizAttemptPolicy `view`: the student who took it, the quiz's teacher, admin, or a parent linked to that student.
   - index (student): "My results" list with quiz title, score, percentage, date.

The take form must not contain `correct_option` anywhere in the HTML.

Policies: `ModulePolicy` (update, delete: owner teacher), `QuizPolicy` (update, delete, manage: owner teacher), `QuizAttemptPolicy` (view as above). Each has `before()` that allows admin.

Routes (names are fixed)
- auth + role:student,teacher,admin → `modules.index`, `modules.show`, `modules.download` (GET `modules/{module}/download`), `quizzes.index`, `quizzes.show`
- auth + role:teacher,admin → `modules.create|store|edit|update|destroy`, `quizzes.create|store|edit|update|destroy`, `quizzes.publish` (PATCH `quizzes/{quiz}/publish`), `questions.store`, `questions.destroy`
- auth + role:student → `attempts.store` (POST `quizzes/{quiz}/attempts`), `attempts.index` (GET `my-results`)
- auth → `attempts.show` (GET `attempts/{attempt}`)
- Register `create` routes before `{module}` / `{quiz}` wildcard routes so `/modules/create` is not read as a module id.

Views: `modules/index, show, create, edit, _form`; `quizzes/index, create, edit, _form, manage, take`; `attempts/index, show`.

Tests: `tests/Feature/QuizScoringTest.php`
1. Quiz with 3 questions; student answers 2 correctly → attempt saved with score 2, total 3.
2. Missing an answer → validation error, no attempt saved.
3. Student opening an unpublished quiz → 404.
4. Student cannot open `modules.create` → 403.

Checks: `php artisan test`, `php artisan route:list --except-vendor`.

Browser test for Quit
1. As teacher@: upload a PDF module, create a quiz, add 3 questions, publish it.
2. As student@: filter modules, download the PDF, take the quiz, see the result page with correct answers.

WHY.md points
- Multiple-choice quizzes are scored by the server, so students get instant feedback and cannot change their score from the browser.
- Correct answers are never sent to the quiz page, so they cannot be read from the page source.
- Files download through a controller route so only logged-in users get them and the original file name is kept.

---

## Phase 4 — School-parent communication (Objective 2)

Goal: announcements by audience, grades visible to the right people only, and class schedules.

Controllers
1. `AnnouncementController`: index, create, store, destroy.
   - index: students see `all` + `students`; parents see `all` + `parents`; organizations see `all`; teachers and admin see every audience (with an audience badge).
   - create/store/destroy: teacher, admin. AnnouncementPolicy delete: author.
2. `GradeController`: index, create, store, edit, update, destroy.
   - index: student → own grades in a subject × quarter table; parent → one card per linked child with the same table; teacher → grades they encoded, filterable by student and subject; admin → all with the same filters.
   - store: teacher picks a student (role student) from a dropdown, subject, quarter (1-4), grade `numeric|between:60,100`, remarks. Unique per student + subject + quarter (`Rule::unique('grades')->where(...)`, ignore current row on update).
   - GradePolicy update/delete: the teacher who encoded it.
3. `ScheduleController`: index, create, store, edit, update, destroy.
   - index grouped by day in `Schedule::DAYS` order: student → own grade level; parent → each linked child's grade level; teacher/admin → all, filterable by grade level.
   - store/update: `end_time` must be after `start_time`.
   - SchedulePolicy update/delete: creator.

Routes
- auth → `announcements.index`
- auth + role:teacher,admin → `announcements.create|store|destroy`, `grades.create|store|edit|update|destroy`, `schedules.create|store|edit|update|destroy`
- auth + role:student,parent,teacher,admin → `grades.index`, `schedules.index`

Views: `announcements/index, create`; `grades/index, create, edit, _form, _table`; `schedules/index, create, edit, _form`.

Tests: `tests/Feature/ParentGradeAccessTest.php`
1. parent sees the linked child's grade on `grades.index`.
2. parent does not see an unlinked student's grade (subject text absent from the page).
3. student sees only own grades.
4. A student cannot open `grades.create` → 403.
5. Duplicate student + subject + quarter is rejected.

Browser test for Quit
1. As teacher@: post an announcement for parents; encode Q1 grades for student@ and student2@; add a Monday schedule for Grade 10.
2. As parent@: see the announcement, student@'s grades only, and the Grade 10 schedule.
3. As student2@: the parent-only announcement is not shown.

WHY.md points
- A parent sees grades only through the parent_student link that the admin confirms, so a stranger cannot claim a child and read their grades.
- The unique rule on student + subject + quarter prevents two different grades for the same period.

---

## Phase 5 — Collaborative learning (Objective 3)

Goal: a discussion forum and study groups with a members-only message board.

Who: student, teacher, admin (parents and organizations do not use these).

Controllers
1. `ForumThreadController` (resource: index, create, store, show, destroy). Route `Route::resource('forum', ...)->parameters(['forum' => 'thread'])->names('threads')`. Show lists replies oldest first with a reply form. ForumThreadPolicy delete: author.
2. `ForumReplyController`: store (POST `forum/{thread}/replies`, name `replies.store`), destroy (DELETE `replies/{reply}`, name `replies.destroy`). ForumReplyPolicy delete: author.
3. `StudyGroupController` (resource: index, create, store, show, destroy) plus `join` (POST `study-groups/{study_group}/join`, name `study-groups.join`) and `leave` (DELETE `study-groups/{study_group}/leave`, name `study-groups.leave`).
   - store: the owner automatically becomes a member.
   - index: all groups with member count and a "Joined" badge or a Join button.
   - show: members only (StudyGroupPolicy `view`); shows description, members, messages (oldest first, paginated 20) and a post form.
   - The owner cannot leave; the owner can delete the group (StudyGroupPolicy `delete`).
4. `GroupMessageController`: store (POST `study-groups/{study_group}/messages`, name `group-messages.store`), StudyGroupPolicy `post` = member.

Views: `forum/index, create, show`; `study-groups/index, create, show`.

Tests: `tests/Feature/GroupMembershipTest.php`
1. Non-member opening a group → 403.
2. Non-member posting a message → 403, nothing saved.
3. After joining, the member can post and the message appears on the group page.
4. Parent opening `threads.index` → 403.

Browser test for Quit
1. As student@: create a thread and a study group; post a message in the group.
2. As student2@: reply to the thread; open the group (403 page), join it from the list, then post.

WHY.md points
- Study group boards are members-only, so students can discuss freely inside their group.
- Messages post with a normal page refresh instead of live chat, because live chat needs a WebSocket server that XAMPP does not provide.

---

## Phase 6 — Community opportunities (Objective 4)

Goal: organizations post scholarships, tutoring programs and events; the admin approves them before the public sees them.

Controllers
1. `OpportunityController`
   - index (public, no login): approved only; filter by type; upcoming `event_date`/`deadline` first.
   - show (public): approved only; the owner organization and admin can also see pending/rejected ones (else 404).
   - mine (organization): own postings with status badges.
   - create/store (organization): status forced to `pending`. `link` uses the `url` rule.
   - edit/update (owner): editing sets status back to `pending`.
   - destroy (owner or admin). OpportunityPolicy for update/delete.
2. `Admin\OpportunityReviewController`: index (pending list), approve (PATCH), reject (PATCH).

Routes
- public → `opportunities.index` (GET `opportunities`), `opportunities.show` (GET `opportunities/{opportunity}`)
- auth + role:organization → `opportunities.mine` (GET `my-opportunities`), `opportunities.create|store|edit|update`
- auth + role:organization,admin → `opportunities.destroy`
- auth + role:admin, prefix `admin`, name `admin.` → `admin.opportunities.index`, `admin.opportunities.approve`, `admin.opportunities.reject`
- Register `opportunities/create` before `opportunities/{opportunity}`.

Views: `opportunities/index, show, mine, create, edit, _form`; `admin/opportunities/index`.

Tests: `tests/Feature/OpportunityApprovalTest.php`
1. A pending posting is not on the public index, and its show page returns 404 for a guest.
2. An organization cannot approve (403).
3. After admin approves, it appears on the public index.
4. Editing an approved posting sets it back to pending.

Browser test for Quit
1. As org@: post a scholarship; it shows "pending" in My Postings and is missing from the public page.
2. As admin@: approve it in Pending Postings.
3. Logged out: it now shows on Opportunities.

WHY.md points
- Postings come from outside the school, so the admin screens them before students see them.
- Editing sends a posting back to pending so an approved post cannot be changed into something unapproved.

---

## Phase 7 — Role dashboards and admin panel

Goal: each role lands on a useful dashboard; the admin manages users and parent links.

Controllers
1. `DashboardController` (invokable). Replace Breeze's dashboard closure in `routes/web.php`, keep the name `dashboard` and the `auth`, `verified` middleware. Returns `dashboard` view with role data; the view includes `dashboard/_admin`, `_teacher`, `_student`, `_parent` or `_organization`.
   - all: latest 3 announcements for the user's audience.
   - student: latest modules for their grade, published quizzes not yet attempted, last 5 attempts.
   - parent: a card per child with latest grades and last 3 quiz results (links to `attempts.show`).
   - teacher: counts of own modules, quizzes, encoded grades; quick-action buttons.
   - organization: own postings with status.
   - admin: user counts per role, totals for modules/quizzes/threads, pending postings count with a link.
2. `Admin\UserController`: index (search name/email, filter role, paginated), create/store (create a teacher or admin account with a password), updateRole (PATCH; an admin cannot change their own role; grade level required when the new role is student).
3. `Admin\ParentLinkController`: index (list of links + form with a parent select and a student select), store (no duplicates), destroy.

Routes (auth + role:admin, prefix `admin`, name `admin.`): `admin.users.index|create|store`, `admin.users.role` (PATCH `users/{user}/role`), `admin.links.index|store|destroy`.

Views: `dashboard.blade.php` + `dashboard/_*.blade.php`; `admin/users/index, create`; `admin/links/index`.

Tests: add to `RoleAccessTest.php`
1. A teacher opening `admin.users.index` → 403.
2. An admin cannot change their own role.
3. Admin links a parent to a student, and the parent then sees that child's grades.

Browser test for Quit
1. Log in as each of the six accounts; each dashboard shows its own widgets.
2. As admin@: create a new teacher; link parent@ to student2@; parent@ now sees both children.

WHY.md points
- Teachers and admins are created by the admin, not by sign-up, so school staff accounts are controlled by the school.
- One dashboard route chooses widgets by role, so every user has one starting page.

---

## Phase 8 — Accessibility and mobile pass (Objective 5)

Goal: every page works on a phone, with a keyboard and with assistive technology; add text-size and high-contrast controls.

Build
1. Navbar: an "Accessibility" dropdown with buttons A-, A, A+ (font size 100%, 115%, 130%) and a "High contrast" toggle (`aria-pressed`).
2. `public/js/educonnect.js`: apply the saved settings on load and on click by setting a CSS variable on `<html>` and toggling `html.high-contrast`. Save in `localStorage` inside `try/catch` (works without storage too).
3. `public/css/educonnect.css`: `html { font-size: var(--ec-font-scale, 100%); }` and `.high-contrast` rules (black background, white text, yellow links and focus, visible borders on cards, buttons and inputs).

Audit every view and fix
- One `<h1>` per page; headings in order.
- Every input has a `<label for>`; required fields marked; errors use `aria-describedby`.
- Icon-only buttons have `aria-label`; decorative icons have `aria-hidden="true"`.
- Images have `alt`.
- Tables are wrapped in `.table-responsive` and have `<th scope="col">`.
- Buttons and links reachable and visible with Tab; no hover-only actions.
- Flash and error alerts use `role="alert"`.
- Text colour contrast at least 4.5:1 (no light grey body text).

Checks: `php artisan test` all green. List every view you changed in the report.

Browser test for Quit
1. Chrome DevTools → device toolbar → iPhone size: every page fits without sideways scrolling.
2. Use only Tab/Enter to log in and take a quiz.
3. Try A+ and High contrast; reload; the setting stays.
4. DevTools → Lighthouse → Accessibility on Dashboard, Modules and a quiz page. Screenshot the scores for documentation (aim for 90+).

WHY.md points
- Text size and contrast are user-controlled because learners with low vision need them, and the setting is saved in their browser so it stays on every page.
- Every form input has a label so screen readers announce what each field is for.

---

## Phase 9 — Suggested features

Build in this order. Stop and report after each feature (Quit may stop any time).

1. Keyword search: a `q` search box on modules, forum threads and opportunities (`where('title', 'like', "%{$q}%")`, combined with existing filters).
2. Student progress card: on student and parent dashboards, attempts count, average percentage and best score per quiz.
3. Printable report card: route `grades.report` (GET `report-card/{student}`), view `grades/report` with its own minimal print layout (school name, student, grade level, subject × quarter table, general average). Visible to the student, linked parents, teachers and admin (GradePolicy `viewReport`). "Print" button calls `window.print()`.
4. "New" announcements badge: migration adds nullable `announcements_seen_at` timestamp to users; visiting `announcements.index` updates it; navbar shows a badge with the count of newer announcements for that user's audience.
5. Saved modules (bookmarks): migration for `module_user` (module_id, user_id, unique pair); POST `modules/{module}/bookmark` (name `modules.bookmark`) toggles; GET `saved-modules` (name `bookmarks.index`) lists them; bookmark button on module cards (`aria-pressed`).

Tests: one test per feature in `tests/Feature/SuggestedFeaturesTest.php` (search filters results; report card 403 for an unrelated parent; badge count drops to 0 after visiting; bookmark toggles on and off).

Future enhancements (do not build; list them in README):
- English/Filipino language toggle (Laravel localization).
- Email or SMS alerts for new announcements and grades.
- Offline access (Progressive Web App).
- Real-time chat in study groups (WebSockets with Laravel Reverb).

WHY.md points: one line per feature built, saying which objective it supports.

---

## Phase 10 — Demo data, final checks, handover

Build
1. `database/seeders/DemoContentSeeder.php`, called from `DatabaseSeeder` after `UserSeeder`:
   - Writes a tiny valid one-page PDF to `storage/app/public/modules/sample.pdf` using plain PHP (no package).
   - 3 modules (Mathematics, Science, English; Grade 10) and 1 study guide by teacher@.
   - 1 published 5-question quiz (Mathematics) and 1 attempt by student@.
   - 2 announcements (all, parents).
   - Q1 and Q2 grades for student@ and student2@ in 4 subjects.
   - Grade 10 schedule Monday to Friday.
   - 1 forum thread with 2 replies; 1 study group with student@ and student2@ and 3 messages.
   - 3 opportunities by org@: scholarship (approved), tutoring (approved), event (pending).
2. `README.md` (replace Laravel's): what EduConnect is, the five objectives with where each lives, requirements, setup steps on XAMPP (Windows commands), seeded accounts, how to run tests, backup command, future enhancements.

Checks
1. `php artisan migrate:fresh --seed` (allowed; say so in the report).
2. `php artisan test` all green.
3. `php artisan route:list --except-vendor` — every route has the middleware listed in this plan.
4. Forbidden-URL sweep (do with feature tests or `php artisan tinker` plus HTTP tests): student → `/modules/create`, `/admin/users`; parent → `/forum`; organization → `/grades`; guest → `/dashboard`. All must be 403 or redirect to login.

Handover for Quit
1. Walk through each role once in the browser.
2. Backup the database:
   ```
   C:\xampp\mysql\bin\mysqldump.exe -u root educonnect > C:\xampp\htdocs\educonnect\educonnect_backup.sql
   ```
3. Zip the project folder without `vendor` and `node_modules`.

---

## If behind schedule: cut in this order

1. Phase 9 features 4 and 5.
2. Phase 9 features 1 to 3.
3. Study group message board (keep create, join and leave).
4. Schedule editing (keep seeded schedules and the view).
5. Text-size and contrast toggles (keep labels, responsive layout and keyboard access).

Never cut: roles and the role middleware, server-side quiz scoring, parents seeing only their own children's grades, admin approval of community postings, and the key-rule tests. These are what a professor tests.

---

## Route ordering rule

In `routes/web.php`, keep groups in this order: public → auth (all roles) → role groups. Inside each resource, register `create` before any `{model}` wildcard route.
