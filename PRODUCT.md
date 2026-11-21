# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Five audiences share one app, with no single main audience:

- **Students** (Grade 7 to 12, Philippine junior and senior high school) read modules, take quizzes, check their own grades and schedule, and join the forum and study groups. Mostly on phones.
- **Teachers** upload modules and study guides, build quizzes, post announcements, encode grades and manage schedules.
- **Parents** see their linked children's grades, quiz results and schedules, and read announcements.
- **School administrators** manage users, link parents to students and approve community postings.
- **Local organizations** post scholarships, tutoring programs and events, shown only after admin approval.

## Product Purpose

EduConnect is one place where students, teachers, parents, administrators and local organizations learn, communicate and help each other. Intended for a real school pilot. Success means each role finds what it needs without help and the school and its community stay connected.

## Positioning

One app that joins learning resources, school-parent communication, collaborative study and community opportunities, and is accessible by design (phone, keyboard and screen reader friendly, with text-size and contrast settings).

## Operating Context

Session-authenticated web app (Laravel, Blade, Bootstrap), no public API. Accounts: students, parents and organizations self-register; admins create teachers; admins link parents to students. Timezone Asia/Manila.

## Capabilities and Constraints

- Five roles with route-level and record-level authorization.
- Learning: modules, study guides, quizzes with instant scores.
- Communication: announcements, grades, schedules.
- Collaboration: forum, study groups with message boards (Phase 5, not built yet).
- Community: opportunities from organizations, admin-approved (Phase 6, not built yet).
- Constraints: Bootstrap 5.3.3 and Bootstrap Icons by CDN, plain `public/css/educonnect.css` and `public/js/educonnect.js`, no extra packages, no Tailwind. The builder must be able to explain every line to a professor.

## Evidence on Hand

Seeded demo accounts for each role (see CLAUDE.md). No real school content, logo, testimonials or usage data exist; future work must not invent them.

## Product Principles

1. Every role sees only what it needs, in plain words.
2. Accessible for everyone: works on phones, with a keyboard and with screen readers.
3. One consistent system across every page.
4. Readable, ordinary code over clever code.

## Accessibility & Inclusion

Mobile-friendly, keyboard operable and screen-reader friendly are product requirements; text-size and high-contrast settings are planned in Phase 8.
