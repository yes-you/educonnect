# EduConnect — Why we built it this way

## Phase 0 — Install and configure
- Laravel 13 on PHP 8.5: Laravel 13 needs PHP 8.3 or newer; XAMPP's PHP 8.5 meets that, so we use the current supported version.
- Breeze for authentication: Breeze publishes its login and register code into our own app folder, so every line of authentication is visible and explainable.

## Phase 1 — Layout, roles, registration
- One `role` column plus one middleware: the five roles are fixed, so a permissions package would add setup without benefit.
- Self-registration is limited to student, parent and organization so no one can grant themselves teacher or admin power.
- Bootstrap by CDN: no build step for our pages, and its grid makes every page mobile-friendly.

## Phase 2 — Database: all tables, models, relationships, accounts
- All tables are created in one phase so later features never force a table redesign.
- Study guides share the modules table with a `type` column, so one controller and one page serve both.
- `parent_student` is a pivot table because one parent can have several children and one child can have two parents.

## Phase 3 — Learning resources
- Multiple-choice quizzes are scored by the server, so students get instant feedback and cannot change their score from the browser.
- Correct answers are never sent to the quiz page, so they cannot be read from the page source.
- Files download through a controller route so only logged-in users get them and the original file name is kept.

## Phase 4 — School-parent communication
- A parent sees grades only through the parent_student link that the admin confirms, so a stranger cannot claim a child and read their grades.
- The unique rule on student + subject + quarter prevents two different grades for the same period.
- Announcements are filtered on the server by audience, so a student or parent never receives a message meant for another group.

## Phase 5 — Collaborative learning
- Study group boards are members-only, so students can discuss freely inside their group.
- Messages post with a normal page refresh instead of live chat, because live chat needs a WebSocket server that XAMPP does not provide.
- Forum and study groups are limited to students, teachers and admin, so parents and organizations cannot post in student spaces.

## Phase 6 — Community opportunities
- Postings come from outside the school, so the admin screens them before students see them.
- Editing sends a posting back to pending so an approved post cannot be changed into something unapproved.
- The status is set by the server and never read from the form, so an organization cannot approve its own posting.
- Links must start with http or https, so a posting cannot hide a script in its link.

## Phase 7 — Role dashboards and admin panel
- Teachers and admins are created by the admin, not by sign-up, so school staff accounts are controlled by the school.
- One dashboard route chooses widgets by role, so every user has one starting page.
- An admin cannot change their own role, so the school cannot be left with no admin by accident.
- The audience rule for announcements lives in one place (a model scope), so the dashboard and the announcements page can never disagree about who sees what.
- A parent is linked to a child only by the admin on the Parent Links page, and that link is what lets the parent see the child's grades.
