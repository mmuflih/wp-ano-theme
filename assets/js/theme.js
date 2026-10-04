document.addEventListener('DOMContentLoaded', function () {
    const searchToggle = document.querySelector('.search-link');
    const searchPanel = document.querySelector('.search-panel');
    const searchInput = document.getElementById('ano-search-input');
    const searchClose = document.querySelector('.search-close');

    function setSearch(open) {
        if (!searchToggle || !searchPanel) return;
        searchPanel.classList.toggle('is-open', open);
        searchPanel.setAttribute('aria-hidden', open ? 'false' : 'true');
        searchToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open && searchInput) {
            window.setTimeout(function () { searchInput.focus(); }, 30);
        }
    }

    if (searchToggle && searchPanel) {
        searchToggle.addEventListener('click', function () {
            setSearch(!searchPanel.classList.contains('is-open'));
        });
        if (searchClose) searchClose.addEventListener('click', function () { setSearch(false); });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && searchPanel.classList.contains('is-open')) setSearch(false);
        });
    }

    const toggle = document.querySelector('.mobile-toggle');
    const nav = document.querySelector('.main-nav');
    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', nav.classList.contains('is-open') ? 'true' : 'false');
        });
    }

    const slider = document.querySelector('.hero-slider');
    if (!slider) return;

    const slides = Array.from(slider.querySelectorAll('.hero-slide'));
    const dots = Array.from(slider.querySelectorAll('.hero-dot'));
    if (slides.length <= 1) return;

    let current = 0;
    let timer = null;

    function showSlide(index) {
        current = (index + slides.length) % slides.length;
        slides.forEach(function (slide, i) {
            const active = i === current;
            slide.classList.toggle('is-active', active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        });
        dots.forEach(function (dot, i) {
            dot.classList.toggle('is-active', i === current);
            dot.setAttribute('aria-current', i === current ? 'true' : 'false');
        });
    }

    function startAutoplay() {
        clearInterval(timer);
        timer = setInterval(function () { showSlide(current + 1); }, 6000);
    }

    dots.forEach(function (dot, index) {
        dot.addEventListener('click', function () {
            showSlide(index);
            startAutoplay();
        });
    });

    slider.addEventListener('mouseenter', function () { clearInterval(timer); });
    slider.addEventListener('mouseleave', startAutoplay);
    slider.addEventListener('focusin', function () { clearInterval(timer); });
    slider.addEventListener('focusout', function (event) {
        if (!slider.contains(event.relatedTarget)) startAutoplay();
    });

    showSlide(0);
    startAutoplay();
});
