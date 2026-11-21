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

    // Text size (Normal, Large, Larger) and high contrast. Both are remembered in localStorage;
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
            if (on) {
                document.querySelectorAll('[data-font-status]').forEach(function (line) {
                    line.textContent = 'Text size: ' + button.getAttribute('data-size-name');
                });
            }
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

    // Every password field gets a "show password" button, so typos can be spotted.
    document.querySelectorAll('input[type="password"]').forEach(function (field) {
        var group = document.createElement('div');
        group.className = 'input-group';
        field.parentNode.insertBefore(group, field);
        group.appendChild(field);

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn ec-pw-toggle';
        button.setAttribute('aria-pressed', 'false');
        button.setAttribute('aria-label', 'Show password');
        button.innerHTML = '<i class="bi bi-eye" aria-hidden="true"></i>';
        group.appendChild(button);

        // The error message was a sibling of the field; keep it visible now the field sits in a group.
        var next = group.nextElementSibling;
        if (next && next.classList.contains('invalid-feedback')) { next.classList.add('d-block'); }

        button.addEventListener('click', function () {
            var show = field.type === 'password';
            field.type = show ? 'text' : 'password';
            button.setAttribute('aria-pressed', show ? 'true' : 'false');
            button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            button.firstChild.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
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

    // First-visit tips: shown until dismissed, remembered per user on this device.
    document.querySelectorAll('[data-welcome]').forEach(function (tip) {
        var key = tip.getAttribute('data-welcome');
        var dismissed = false;
        try { dismissed = localStorage.getItem(key) === '1'; } catch (e) { /* storage blocked: show the tip */ }
        if (dismissed) { return; }
        tip.hidden = false;
        tip.querySelector('[data-welcome-dismiss]').addEventListener('click', function () {
            tip.remove();
            try { localStorage.setItem(key, '1'); } catch (e) { /* private mode: it will show again next time */ }
        });
    });
})();
