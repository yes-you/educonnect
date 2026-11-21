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

    // Text size (A-, A, A+) and high contrast. Both are remembered in localStorage;
    // if storage is blocked the buttons still work, they just are not remembered.
    var SIZES = ['100', '115', '130'];

    var load = function (key) {
        try { return localStorage.getItem(key); } catch (e) { return null; }
    };
    var save = function (key, value) {
        try { localStorage.setItem(key, value); } catch (e) { /* storage blocked: skip saving */ }
    };

    var applySize = function (size) {
        if (SIZES.indexOf(size) === -1) { size = '100'; }
        root.style.setProperty('--ec-font-scale', size + '%');
        document.querySelectorAll('[data-font-scale]').forEach(function (button) {
            var on = button.getAttribute('data-font-scale') === size;
            button.setAttribute('aria-pressed', on ? 'true' : 'false');
            button.classList.toggle('active', on);
        });
    };

    var applyContrast = function (on) {
        root.classList.toggle('high-contrast', on);
        document.querySelectorAll('[data-contrast-toggle]').forEach(function (button) {
            button.setAttribute('aria-pressed', on ? 'true' : 'false');
            button.classList.toggle('active', on);
        });
    };

    applySize(load('ec-font-scale'));
    applyContrast(load('ec-high-contrast') === '1');

    document.querySelectorAll('[data-font-scale]').forEach(function (button) {
        button.addEventListener('click', function () {
            var size = button.getAttribute('data-font-scale');
            applySize(size);
            save('ec-font-scale', size);
        });
    });

    document.querySelectorAll('[data-contrast-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            var on = !root.classList.contains('high-contrast');
            applyContrast(on);
            save('ec-high-contrast', on ? '1' : '0');
        });
    });

    // Link each field that has an error to its message, so a screen reader reads the error with the field.
    document.querySelectorAll('.is-invalid').forEach(function (field, index) {
        var message = field.nextElementSibling;
        while (message && !message.classList.contains('invalid-feedback')) {
            message = message.nextElementSibling;
        }
        if (!message) { return; }

        if (!message.id) { message.id = (field.id || 'field-' + index) + '-error'; }
        field.setAttribute('aria-describedby', message.id);
        field.setAttribute('aria-invalid', 'true');
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
