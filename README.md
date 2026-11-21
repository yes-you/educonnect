# EduConnect

EduConnect is a web platform where students, teachers, parents, school administrators and local organizations learn, communicate and help each other. It is built with Laravel 13, Blade views, Bootstrap 5 and MySQL, and runs on XAMPP.

## The five objectives and where each one lives

| # | Objective | Where to find it | Main files |
|---|---|---|---|
| 1 | Enhance learning resources: digital modules, practice quizzes, study guides | Modules (`/modules`), Quizzes (`/quizzes`), My results (`/my-results`) | `ModuleController`, `QuizController`, `QuestionController`, `QuizAttemptController` |
| 2 | Strengthen school-parent communication: announcements, grades, schedules | Announcements (`/announcements`), Grades (`/grades`), Report card (`/report-card/{student}`), Schedule (`/schedules`) | `AnnouncementController`, `GradeController`, `ScheduleController` |
| 3 | Support collaborative learning: forums and group study spaces | Forum (`/forum`), Study groups (`/study-groups`) | `ForumThreadController`, `ForumReplyController`, `StudyGroupController`, `GroupMessageController` |
| 4 | Encourage community involvement: scholarships, tutoring programs, events | Opportunities (`/opportunities`), My postings (`/my-opportunities`), Pending postings (`/admin/opportunities`) | `OpportunityController`, `Admin\OpportunityReviewController` |
| 5 | Promote accessibility: mobile-friendly and inclusive | Accessibility menu in the navbar (text size A-, A, A+ and high contrast), phone-friendly pages, labelled forms | `public/css/educonnect.css`, `public/js/educonnect.js`, `resources/views/layouts` |

## Roles

| Role | How the account is created | What they can do |
|---|---|---|
| admin | Seeder, or another admin | Everything: manage users and roles, link parents to students, approve community postings |
| teacher | Created by an admin | Upload modules and study guides, build quizzes, post announcements, encode grades, manage schedules, join the forum and groups |
| student | Self-registration | Read modules, take each quiz once, see their own grades and schedule, forum, study groups. A student only sees their own grade level |
| parent | Self-registration, then an admin links them to their child | See the linked children's grades, quiz results and schedule, and read announcements |
| organization | Self-registration | Post scholarships, tutoring programs and events (shown to the public only after admin approval) |

Self-registration offers only student, parent and organization, so nobody can make themselves a teacher or an admin.

## Requirements

- Windows with XAMPP (Apache and MySQL/MariaDB) installed at `C:\xampp`
- PHP 8.3 or newer (XAMPP's PHP). These extensions must be on: `pdo_mysql`, `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `fileinfo`, `zip`, `curl`
- Composer 2
- Git
- Node.js 20 or newer is optional. EduConnect's own pages use Bootstrap from a CDN and plain CSS and JS files, so no build step is needed

Bootstrap, Bootstrap Icons and the fonts load from the internet, so the pages need a connection to look right.

## Setup on XAMPP (Windows)

Make sure `C:\xampp\php` is on your PATH, so `php -v` shows XAMPP's PHP. Start MySQL in the XAMPP Control Panel, then run these in Git Bash or PowerShell from the project folder (`C:\xampp\htdocs\educonnect`).

1. Install the PHP packages:
   ```
   composer install
   ```
2. Create your settings file and the app key:
   ```
   copy .env.example .env
   php artisan key:generate
   ```
   The defaults in `.env.example` already point at MySQL on `127.0.0.1`, user `root`, no password, database `educonnect`.
3. Create the database:
   ```
   C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS educonnect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```
4. Create the tables and the demo data (the six accounts below plus sample modules, a quiz, grades and more):
   ```
   php artisan migrate --seed
   ```
5. Link the public storage folder, so uploaded files can be served:
   ```
   php artisan storage:link
   ```
6. Start the app and open http://127.0.0.1:8000:
   ```
   php artisan serve
   ```

To wipe everything and start again with fresh demo data, run `php artisan migrate:fresh --seed`. This deletes all data in the `educonnect` database, including accounts and content you added.

## Demo accounts

All accounts use the password `password`.

| Email | Role |
|---|---|
| admin@educonnect.test | admin |
| teacher@educonnect.test | teacher |
| student@educonnect.test | student (Grade 10, child of parent@) |
| student2@educonnect.test | student (Grade 10, not linked to parent@) |
| parent@educonnect.test | parent |
| org@educonnect.test | organization |

The demo data includes 3 modules and 1 study guide, a published 5-question Mathematics quiz (already taken once by student@), 2 announcements, Quarter 1 and 2 grades in 4 subjects for both students, a Grade 10 schedule for Monday to Friday, a forum thread with 2 replies, a study group with 3 messages, and 3 community postings (a scholarship and a tutoring program approved, an event still pending).

These accounts are for demonstration only. Change or remove them before the app is used with real people.

## Running the tests

```
php artisan test
```

The tests run on an in-memory SQLite database, so they never touch your MySQL data. They cover the rules the project depends on: roles and the role middleware, server-side quiz scoring, one attempt per quiz, parents seeing only their own children's grades, admin approval of community postings, members-only study groups, the demo data, and a sweep of forbidden URLs for every role.

Other useful commands:

```
php artisan route:list --except-vendor
php artisan optimize:clear
```

## Backup

Save a copy of the database to a file:

```
C:\xampp\mysql\bin\mysqldump.exe -u root educonnect > C:\xampp\htdocs\educonnect\educonnect_backup.sql
```

To restore it into an empty `educonnect` database:

```
C:\xampp\mysql\bin\mysql.exe -u root educonnect < C:\xampp\htdocs\educonnect\educonnect_backup.sql
```

Uploaded module files live in `storage/app/public/modules`. Copy that folder too if you want a full backup. Do not commit the backup file: it contains user data.

## Project documents

- `PLAN.md`: the build plan, with the table, route and file names used throughout.
- `docs/WHY.md`: why each part was built the way it was, in plain sentences.
- `CLAUDE.md`: the working rules used while building the project.

## Future enhancements

These are not built yet.

- English and Filipino language toggle (Laravel localization).
- Email or SMS alerts for new announcements and grades.
- Offline access (Progressive Web App).
- Real-time chat in study groups (WebSockets with Laravel Reverb).
- Saved modules: bookmark a module and list the saved ones. It was planned as the last suggested feature and was left out.
