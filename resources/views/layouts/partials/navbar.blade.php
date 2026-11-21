@php
    // Each link: route name, label, roles allowed to see it (null = every logged-in user).
    $links = [
        ['dashboard', 'Dashboard', null],
        ['announcements.index', 'Announcements', null],
        ['modules.index', 'Modules', ['student', 'teacher', 'admin']],
        ['quizzes.index', 'Quizzes', ['student', 'teacher', 'admin']],
        ['threads.index', 'Forum', ['student', 'teacher', 'admin']],
        ['study-groups.index', 'Study Groups', ['student', 'teacher', 'admin']],
        ['grades.index', 'Grades', ['student', 'parent', 'teacher', 'admin']],
        ['schedules.index', 'Schedule', ['student', 'parent', 'teacher', 'admin']],
        ['opportunities.mine', 'My Postings', ['organization']],
    ];
    $adminLinks = [
        ['admin.users.index', 'Users'],
        ['admin.links.index', 'Parent Links'],
        ['admin.opportunities.index', 'Pending Postings'],
    ];
    $user = auth()->user();
@endphp

<nav class="navbar navbar-expand-xl sticky-top ec-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">
            <i class="bi bi-mortarboard-fill" aria-hidden="true"></i> EduConnect
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-xl-0">
                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}"
                           @if (request()->is('/')) aria-current="page" @endif>Home</a>
                    </li>
                @endguest

                @auth
                    @foreach ($links as [$routeName, $label, $roles])
                        @if (Route::has($routeName) && ($roles === null || $user->hasRole(...$roles)))
                            @php $isActive = request()->routeIs(str_replace('.index', '.*', $routeName)); @endphp
                            <li class="nav-item">
                                <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ route($routeName) }}"
                                   @if ($isActive) aria-current="page" @endif>{{ $label }}</a>
                            </li>
                        @endif
                    @endforeach

                    @if ($user->hasRole('admin'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="#"
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">Admin</a>
                            <ul class="dropdown-menu">
                                @foreach ($adminLinks as [$routeName, $label])
                                    @if (Route::has($routeName))
                                        <li><a class="dropdown-item" href="{{ route($routeName) }}">{{ $label }}</a></li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @endauth

                @if (Route::has('opportunities.index'))
                    @php $oppActive = request()->routeIs('opportunities.index', 'opportunities.show'); @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $oppActive ? 'active' : '' }}" href="{{ route('opportunities.index') }}"
                           @if ($oppActive) aria-current="page" @endif>Opportunities</a>
                    </li>
                @endif
            </ul>

            <ul class="navbar-nav ms-auto mb-2 mb-xl-0 align-items-xl-center">
                <li class="nav-item me-xl-2 mb-2 mb-xl-0">
                    <button type="button" class="ec-theme-toggle" data-theme-toggle aria-pressed="false" aria-label="Switch to dark mode">
                        <i class="bi bi-moon-stars-fill ec-moon" aria-hidden="true"></i>
                        <i class="bi bi-sun-fill ec-sun" aria-hidden="true"></i>
                    </button>
                </li>
                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}"
                           @if (request()->routeIs('login')) aria-current="page" @endif>Log in</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}"
                           @if (request()->routeIs('register')) aria-current="page" @endif>Register</a>
                    </li>
                @endguest

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle" aria-hidden="true"></i>
                            {{ $user->name }}
                            <span class="badge text-bg-secondary">{{ $user->role }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Log out</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
