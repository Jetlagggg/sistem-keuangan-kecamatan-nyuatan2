(function () {
    'use strict';

    var STORAGE_KEY = 'buku-kas-theme';
    var root = document.documentElement;
    var media = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

    function storedTheme() {
        try {
            var value = localStorage.getItem(STORAGE_KEY);
            return value === 'light' || value === 'dark' ? value : null;
        } catch (error) {
            return null;
        }
    }

    function systemTheme() {
        return media && media.matches ? 'dark' : 'light';
    }

    function currentTheme() {
        return storedTheme() || systemTheme();
    }

    function applyTheme(theme) {
        var isDark = theme === 'dark';
        root.setAttribute('data-theme', theme);
        root.style.colorScheme = theme;

        var meta = document.querySelector('meta[name="theme-color"]');
        if (meta) {
            meta.setAttribute('content', isDark ? '#141713' : '#f2f1ea');
        }

        document.dispatchEvent(new CustomEvent('themechange', { detail: { theme: theme } }));

        var button = document.querySelector('[data-theme-toggle]');
        if (!button) {
            return;
        }

        button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        button.setAttribute('aria-label', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
        button.setAttribute('title', isDark ? 'Mode terang' : 'Mode gelap');

        var icon = button.querySelector('[data-theme-icon]');
        if (icon) {
            icon.classList.toggle('fa-moon', !isDark);
            icon.classList.toggle('fa-sun', isDark);
        }
    }

    applyTheme(currentTheme());

    document.addEventListener('DOMContentLoaded', function () {
        applyTheme(currentTheme());

        var button = document.querySelector('[data-theme-toggle]');
        if (button) {
            button.addEventListener('click', function () {
                var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                try {
                    localStorage.setItem(STORAGE_KEY, next);
                } catch (error) {
                }
                applyTheme(next);
            });
        }

        if (media) {
            var onChange = function (event) {
                if (!storedTheme()) {
                    applyTheme(event.matches ? 'dark' : 'light');
                }
            };

            if (media.addEventListener) {
                media.addEventListener('change', onChange);
            } else if (media.addListener) {
                media.addListener(onChange);
            }
        }
    });
})();
