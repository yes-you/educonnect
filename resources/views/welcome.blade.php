@extends('layouts.main')

@section('title', 'Home')

@section('content')
    <section class="ec-hero" style="--ec-hero-image: url('{{ asset('images/landing-school.jpg') }}')">
        <div class="ec-hero-copy">
            <h1>EduConnect</h1>
            <p class="lead mb-4">
                One place where students, teachers, parents, school administrators and local organizations learn, communicate and help each other.
            </p>
            <div class="d-flex gap-2 flex-wrap">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-sand btn-lg">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sand btn-lg">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">Register</a>
                @endauth
            </div>
        </div>
    </section>

    <section aria-labelledby="objectives-heading" class="row g-4 g-lg-5 mb-5">
        <div class="col-lg-4">
            <h2 id="objectives-heading" class="h2">What you can do here</h2>
            <p class="text-body-secondary">Everything a school community needs to learn, communicate and stay connected, in one place.</p>
        </div>
        <div class="col-lg-8">
            <div class="ec-feature">
                <span class="ec-icon"><i class="bi bi-journal-bookmark" aria-hidden="true"></i></span>
                <div>
                    <h3 class="h5">Learning resources</h3>
                    <p class="mb-0">Digital modules, study guides and practice quizzes with instant scores.</p>
                </div>
            </div>
            <div class="ec-feature">
                <span class="ec-icon"><i class="bi bi-megaphone" aria-hidden="true"></i></span>
                <div>
                    <h3 class="h5">School-parent communication</h3>
                    <p class="mb-0">Announcements, grades and class schedules shared with the right people.</p>
                </div>
            </div>
            <div class="ec-feature">
                <span class="ec-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                <div>
                    <h3 class="h5">Collaborative learning</h3>
                    <p class="mb-0">A discussion forum and study groups with their own message boards.</p>
                </div>
            </div>
            <div class="ec-feature">
                <span class="ec-icon"><i class="bi bi-heart" aria-hidden="true"></i></span>
                <div>
                    <h3 class="h5">Community involvement</h3>
                    <p class="mb-1">Scholarships, tutoring programs and events from local organizations.</p>
                    @if (Route::has('opportunities.index'))
                        <a href="{{ route('opportunities.index') }}">Browse opportunities</a>
                    @endif
                </div>
            </div>
            <div class="ec-feature">
                <span class="ec-icon"><i class="bi bi-universal-access" aria-hidden="true"></i></span>
                <div>
                    <h3 class="h5">Accessible for everyone</h3>
                    <p class="mb-0">Works on phones, with a keyboard and with screen readers.</p>
                </div>
            </div>
        </div>
    </section>

    <section aria-labelledby="roles-heading">
        <h2 id="roles-heading" class="h2 mb-3">Made for the whole school community</h2>
        <dl class="ec-roles">
            <div>
                <dt>Students</dt>
                <dd>Read modules, take quizzes, check grades and join study groups.</dd>
            </div>
            <div>
                <dt>Teachers</dt>
                <dd>Upload lessons, build quizzes, post announcements and encode grades.</dd>
            </div>
            <div>
                <dt>Parents</dt>
                <dd>Follow a child's grades, schedule and school announcements.</dd>
            </div>
            <div>
                <dt>Administrators</dt>
                <dd>Manage accounts, link parents to students and approve postings.</dd>
            </div>
            <div>
                <dt>Organizations</dt>
                <dd>Share scholarships, tutoring programs and events with students.</dd>
            </div>
        </dl>
    </section>

    @guest
        <section class="ec-cta text-center" aria-labelledby="cta-heading">
            <h2 id="cta-heading" class="h2">Join your school on EduConnect</h2>
            <p class="mx-auto mb-4">Create an account in a minute, or log in to pick up where you left off.</p>
            <div class="d-flex gap-2 justify-content-center flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-sand btn-lg">Register</a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Log in</a>
            </div>
        </section>
    @endguest

@endsection
