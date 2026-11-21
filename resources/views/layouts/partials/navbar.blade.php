@php
    // Links are grouped so the bar stays short. Each link: route name, label,
    // roles allowed to see it (null = every logged-in user), and route patterns that mark it active.
    $groups = [
        ['label' => 'Learn', 'links' => [
            ['modules.index', 'Modules', ['student', 'teacher', 'admin'], ['modules.*']],
            ['quizzes.index', 'Quizzes', ['student', 'teacher', 'admin'], ['quizzes.*', 'attempts.*']],
        ]],
        ['label' => 'School', 'links' => [
            ['announcements.index', 'Announcements', null, ['announcements.*']],
            ['grades.index', 'Grades', ['student', 'parent', 'teacher', 'admin'], ['grades.*']],
            ['schedules.index', 'Schedule', ['student', 'parent', 'teacher', 'admin'], ['schedules.*']],
        ]],
        ['label' => 'Community', 'links' => [
            ['threads.index', 'Forum', ['student', 'teacher', 'admin'], ['threads.*']],
            ['study-groups.index', 'Study Groups', ['student', 'teacher', 'admin'], ['study-groups.*']],
            ['opportunities.mine', 'My Postings', ['organization'], ['opportunities.mine']],
            ['opportunities.index', 'Opportunities', null, ['opportunities.index', 'opportunities.show']],
        ]],
    ];
    $adminLinks = [
        ['admin.users.index', 'Users'],
        ['admin.links.index', 'Parent Links'],
        ['admin.opportunities.index', 'Pending Postings'],
    ];
    $user = auth()->user();
    $newAnnouncements = $user ? $user->newAnnouncementsCount() : 0;

    // Keep only the links this user may see; a group left with one or two links shows them directly.
    $menu = [];
    if ($user) {
        foreach ($groups as $group) {
            $visible = [];
            foreach ($group['links'] as [$routeName, $label, $roles, $patterns]) {
                if (Route::has($routeName) && ($roles === null || $user->hasRole(...$roles))) {
                    $visible[] = [
                        'url' => route($routeName),
                        'label' => $label,
                        'active' => request()->routeIs(...$patterns),
                        'badge' => $routeName === 'announcements.index' ? $newAnnouncements : 0,
                    ];
                }
            }
            if ($visible) {
                $menu[] = ['label' => $group['label'], 'links' => $visible];
            }
        }
    }
@endphp

<nav class="navbar navbar-expand-lg sticky-top ec-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">
            <i class="bi bi-mortarboard-fill" aria-hidden="true"></i> EduConnect
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}"
                           @if (request()->is('/')) aria-current="page" @endif>Home</a>
                    </li>
                    @if (Route::has('opportunities.index'))
                        @php $oppActive = request()->routeIs('opportunities.index', 'opportunities.show'); @endphp
                        <li class="nav-item">
                            <a class="nav-link {{ $oppActive ? 'active' : '' }}" href="{{ route('opportunities.index') }}"
                               @if ($oppActive) aria-current="page" @endif>Opportunities</a>
                        </li>
                    @endif
                @endguest

                @auth
                    @if (Route::has('dashboard'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"
                               @if (request()->routeIs('dashboard')) aria-current="page" @endif>Dashboard</a>
                        </li>
                    @endif

                    @foreach ($menu as $group)
                        {{-- One or two links are shown directly: a dropdown would only add a tap. --}}
                        @if (count($group['links']) <= 2)
                            @foreach ($group['links'] as $link)
                                <li class="nav-item">
                                    <a class="nav-link {{ $link['active'] ? 'active' : '' }}" href="{{ $link['url'] }}"
                                       @if ($link['active']) aria-current="page" @endif>{{ $link['label'] }}@include('layouts.partials.new-badge', ['count' => $link['badge']])</a>
                                </li>
                            @endforeach
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ collect($group['links'])->contains('active', true) ? 'active' : '' }}" href="#"
                                   role="button" data-bs-toggle="dropdown" aria-expanded="false">{{ $group['label'] }}@include('layouts.partials.new-badge', ['count' => collect($group['links'])->sum('badge')])</a>
                                <ul class="dropdown-menu">
                                    @foreach ($group['links'] as $link)
                                        <li>
                                            <a class="dropdown-item {{ $link['active'] ? 'active' : '' }}" href="{{ $link['url'] }}"
                                               @if ($link['active']) aria-current="page" @endif>{{ $link['label'] }}@include('layouts.partials.new-badge', ['count' => $link['badge']])</a>
                                        </li>
                                    @endforeach
                                </ul>
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
            </ul>


            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                {{-- Text size and high contrast. The buttons are wired up in educonnect.js and the choice is saved in the browser. --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                       data-bs-auto-close="outside" aria-expanded="false">
                        <i class="bi bi-universal-access" aria-hidden="true"></i> Accessibility
                    </a>
                    <div class="dropdown-menu dropdown-menu-end ec-a11y-menu">
                        <p class="dropdown-header px-2" id="ec-text-size-label">Text size</p>
                        <div class="btn-group px-2 mb-1" role="group" aria-labelledby="ec-text-size-label">
                            <button type="button" class="btn btn-outline-primary ec-size-btn" data-font-scale="100" data-size-name="Normal" aria-pressed="false">Normal</button>
                            <button type="button" class="btn btn-outline-primary ec-size-btn" data-font-scale="115" data-size-name="Large" aria-pressed="false">Large</button>
                            <button type="button" class="btn btn-outline-primary ec-size-btn" data-font-scale="130" data-size-name="Larger" aria-pressed="false">Larger</button>
                        </div>
                        <p class="small text-body-secondary px-2 mb-2" data-font-status aria-live="polite"></p>
                        <button type="button" class="dropdown-item" data-contrast-toggle aria-pressed="false">
                            <i class="bi bi-circle-half" aria-hidden="true"></i> High contrast
                        </button>
                    </div>
                </li>
                <li class="nav-item me-lg-2 mb-2 mb-lg-0">
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
