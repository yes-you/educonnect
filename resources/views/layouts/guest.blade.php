<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>try{document.documentElement.setAttribute("data-bs-theme",localStorage.getItem("ec-theme")||(matchMedia("(prefers-color-scheme: dark)").matches?"dark":"light"))}catch(e){}</script>
    {{-- Apply the saved text size and high contrast before the page paints, so it never flashes. --}}
    <script>try{var r=document.documentElement,s=localStorage.getItem("ec-font-scale");if(["100","115","130"].indexOf(s)>-1)r.style.setProperty("--ec-font-scale",s+"%");if(localStorage.getItem("ec-high-contrast")==="1")r.classList.add("high-contrast")}catch(e){}</script>
    <title>EduConnect</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible+Next:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,600;6..72,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/educonnect.css') }}?v={{ filemtime(public_path('css/educonnect.css')) }}" rel="stylesheet">
</head>
<body class="ec-auth">
    <button type="button" class="ec-theme-toggle" data-theme-toggle aria-pressed="false" aria-label="Switch to dark mode"><i class="bi bi-moon-stars-fill ec-moon" aria-hidden="true"></i><i class="bi bi-sun-fill ec-sun" aria-hidden="true"></i></button>
    <div class="ec-auth-box">
        <a href="{{ url('/') }}" class="ec-auth-brand">
            <i class="bi bi-mortarboard-fill" aria-hidden="true"></i> EduConnect
        </a>

        <div class="card ec-auth-card">
            <div class="card-body p-4">
                {{ $slot }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/educonnect.js') }}?v={{ filemtime(public_path('js/educonnect.js')) }}"></script>
</body>
</html>
