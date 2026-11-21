@php $compact = $compact ?? false; @endphp

@if ($compact)
    {{-- Every page except the landing page: one slim line so the footer never gets in the way of the work. --}}
    <footer class="ec-footer ec-footer-slim mt-auto">
        <div class="container py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 small">
            <span class="text-body-secondary">&copy; {{ date('Y') }} EduConnect</span>
            <nav aria-label="Footer" class="d-flex gap-3">
                @auth
                    <a href="{{ route('announcements.index') }}">Announcements</a>
                    @if (Route::has('opportunities.index'))
                        <a href="{{ route('opportunities.index') }}">Opportunities</a>
                    @endif
                    <a href="{{ route('profile.edit') }}">Your profile</a>
                @else
                    <a href="{{ route('login') }}">Log in</a>
                    <a href="{{ route('register') }}">Register</a>
                @endauth
                <a href="#main">Back to top</a>
            </nav>
        </div>
    </footer>
@else
    {{-- Landing page: the full footer. --}}
    <footer class="ec-footer mt-auto">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-5">
                    <a class="ec-footer-brand" href="{{ url('/') }}">
                        <i class="bi bi-mortarboard-fill" aria-hidden="true"></i> EduConnect
                    </a>
                    <p class="text-body-secondary mt-2 mb-0">
                        One place where students, teachers, parents, school administrators and local organizations learn, communicate and help each other.
                    </p>
                </div>

                <nav class="col-6 col-lg-3 offset-lg-1" aria-label="Footer">
                    <h2 class="ec-footer-heading">Explore</h2>
                    <ul class="list-unstyled mb-0">
                        @auth
                            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li><a href="{{ route('announcements.index') }}">Announcements</a></li>
                            @if (Route::has('opportunities.index'))
                                <li><a href="{{ route('opportunities.index') }}">Opportunities</a></li>
                            @endif
                            <li><a href="{{ route('profile.edit') }}">Your profile</a></li>
                        @else
                            <li><a href="{{ url('/') }}">Home</a></li>
                            @if (Route::has('opportunities.index'))
                                <li><a href="{{ route('opportunities.index') }}">Opportunities</a></li>
                            @endif
                            <li><a href="{{ route('login') }}">Log in</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @endauth
                    </ul>
                </nav>

                <div class="col-6 col-lg-3">
                    <h2 class="ec-footer-heading">Accessible to all</h2>
                    <p class="text-body-secondary mb-0">
                        Made for phones, keyboards and screen readers. Use the Accessibility menu at the top to change text size or contrast.
                    </p>
                </div>
            </div>

            <p class="ec-footer-note mb-0">&copy; {{ date('Y') }} EduConnect</p>
        </div>
    </footer>
@endif
