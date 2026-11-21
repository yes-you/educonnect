@extends('layouts.main')

@section('title', 'Home')

@section('content')
    <section class="ec-hero">
        <h1>EduConnect</h1>
        <p class="lead col-lg-8 mx-auto mb-4">
            One place where students, teachers, parents, school administrators and local organizations learn, communicate and help each other.
        </p>
        <div class="d-flex gap-2 justify-content-center flex-wrap">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-sand btn-lg">Go to Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sand btn-lg">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">Register</a>
            @endauth
        </div>
    </section>

    <section aria-labelledby="objectives-heading">
        <h2 id="objectives-heading" class="h4 mb-3">What you can do here</h2>
        <div class="row g-3">
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 ec-card-lift">
                    <div class="card-body">
                        <span class="ec-icon"><i class="bi bi-journal-bookmark" aria-hidden="true"></i></span>
                        <h3 class="h5">Learning resources</h3>
                        <p class="card-text mb-0">Digital modules, study guides and practice quizzes with instant scores.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 ec-card-lift">
                    <div class="card-body">
                        <span class="ec-icon"><i class="bi bi-megaphone" aria-hidden="true"></i></span>
                        <h3 class="h5">School-parent communication</h3>
                        <p class="card-text mb-0">Announcements, grades and class schedules shared with the right people.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 ec-card-lift">
                    <div class="card-body">
                        <span class="ec-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                        <h3 class="h5">Collaborative learning</h3>
                        <p class="card-text mb-0">A discussion forum and study groups with their own message boards.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="card h-100 ec-card-lift">
                    <div class="card-body">
                        <span class="ec-icon"><i class="bi bi-heart" aria-hidden="true"></i></span>
                        <h3 class="h5">Community involvement</h3>
                        <p class="card-text">Scholarships, tutoring programs and events from local organizations.</p>
                        @if (Route::has('opportunities.index'))
                            <a href="{{ route('opportunities.index') }}">Browse opportunities</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-6">
                <div class="card h-100 ec-card-lift">
                    <div class="card-body">
                        <span class="ec-icon"><i class="bi bi-universal-access" aria-hidden="true"></i></span>
                        <h3 class="h5">Accessible for everyone</h3>
                        <p class="card-text mb-0">Works on phones, with a keyboard and with screen readers.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
