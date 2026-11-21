<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>try{document.documentElement.setAttribute("data-bs-theme",localStorage.getItem("ec-theme")||(matchMedia("(prefers-color-scheme: dark)").matches?"dark":"light"))}catch(e){}</script>
    {{-- Apply the saved text size and high contrast before the page paints, so it never flashes. --}}
    <script>try{var r=document.documentElement,s=localStorage.getItem("ec-font-scale");if(["100","115","130"].indexOf(s)>-1)r.style.setProperty("--ec-font-scale",s+"%");if(localStorage.getItem("ec-high-contrast")==="1")r.classList.add("high-contrast")}catch(e){}</script>
    <title>@yield('title', 'EduConnect') — EduConnect</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible+Next:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,600;6..72,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/educonnect.css') }}?v={{ filemtime(public_path('css/educonnect.css')) }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <a href="#main" class="visually-hidden-focusable">Skip to main content</a>

    @include('layouts.partials.navbar')

    <main id="main" class="container py-4 flex-grow-1">
        @include('layouts.partials.flash')
        @yield('content')
    </main>

    @include('layouts.partials.footer', ['compact' => ! request()->is('/')])

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/educonnect.js') }}?v={{ filemtime(public_path('js/educonnect.js')) }}"></script>
    @stack('scripts')
</body>
</html>
