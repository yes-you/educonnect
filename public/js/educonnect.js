// EduConnect's own JavaScript: text-size and high-contrast settings are added here in Phase 8.
(function () {
    'use strict';

    // Give the navbar a shadow once the page has scrolled.
    var navbar = document.querySelector('.ec-navbar');
    if (navbar) {
        var update = function () {
            navbar.classList.toggle('is-scrolled', window.scrollY > 4);
        };
        window.addEventListener('scroll', update, { passive: true });
        update();
    }

    // Light / dark switch. The first choice comes from the small script in <head>;
    // here we only flip it and remember it for next time.
    var root = document.documentElement;
    document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
        var sync = function () {
            var dark = root.getAttribute('data-bs-theme') === 'dark';
            button.setAttribute('aria-pressed', dark ? 'true' : 'false');
            button.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
            button.setAttribute('title', dark ? 'Light mode' : 'Dark mode');
        };
        sync();
        button.addEventListener('click', function () {
            var next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-bs-theme', next);
            try { localStorage.setItem('ec-theme', next); } catch (e) { /* private mode: skip saving */ }
            document.querySelectorAll('[data-theme-toggle]').forEach(function (b) { b.dispatchEvent(new Event('themechange')); });
            sync();
        });
        button.addEventListener('themechange', sync);
    });

    // Flash messages (saved, published, approved...) fade away on their own.
    // The timer pauses while the pointer or keyboard focus is on the box, so it can be read.
    document.querySelectorAll('[data-auto-dismiss]').forEach(function (box) {
        var delay = parseInt(box.getAttribute('data-auto-dismiss'), 10) || 5000;
        var timer;
        var start = function () {
            timer = setTimeout(function () {
                bootstrap.Alert.getOrCreateInstance(box).close(); // Bootstrap fades it out, then removes it
            }, delay);
        };
        var stop = function () { clearTimeout(timer); };

        box.addEventListener('mouseenter', stop);
        box.addEventListener('focusin', stop);
        box.addEventListener('mouseleave', start);
        box.addEventListener('focusout', start);
        start();
    });
})();
