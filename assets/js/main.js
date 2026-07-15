// ============================================================
//  assets/js/main.js — School Website JavaScript
//  Intellectitech Ntinda Hub Training Project
// ============================================================

// ── MOBILE NAVIGATION TOGGLE ────────────────────────────────
const burger = document.getElementById('burger');
const nav    = document.getElementById('main-nav');

if (burger && nav) {
    burger.addEventListener('click', () => {
        const isOpen = nav.classList.toggle('open');
        burger.setAttribute('aria-expanded', isOpen);
    });
    // Close nav when any link is clicked (mobile)
    nav.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            nav.classList.remove('open');
            burger.setAttribute('aria-expanded', 'false');
        });
    });
}

// ── CLOSE NAV WHEN CLICKING OUTSIDE ─────────────────────────
document.addEventListener('click', (e) => {
    if (nav && !nav.contains(e.target) && !burger.contains(e.target)) {
        nav.classList.remove('open');
    }
});

// ── FADE-IN ON SCROLL ────────────────────────────────────────
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.style.opacity = '1';
            e.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.12 });

document.querySelectorAll('.card, .staff-card, .event-item, .testimonial-card').forEach(el => {
    el.style.opacity    = '0';
    el.style.transform  = 'translateY(18px)';
    el.style.transition = 'opacity .4s ease, transform .4s ease';
    observer.observe(el);
});

// ── GALLERY FULLSCREEN ON CLICK ──────────────────────────────
document.querySelectorAll('.gallery-item img').forEach(img => {
    img.addEventListener('click', () => {
        if (img.requestFullscreen) img.requestFullscreen();
    });
});

// ── TEXTAREA CHARACTER COUNTER ───────────────────────────────
document.querySelectorAll('textarea[maxlength]').forEach(ta => {
    const info = document.createElement('small');
    info.style.cssText = 'display:block;text-align:right;color:#5A6A80;margin-top:.25rem';
    ta.after(info);
    const update = () => info.textContent = `${ta.value.length} / ${ta.maxLength}`;
    ta.addEventListener('input', update);
    update();
});
// ── HERO SLIDER ──────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {

    const slider = document.getElementById('heroSlider');

    if (!slider) {
        return;
    }

    const slides = slider.querySelectorAll('.hero-slide');
    const dots = slider.querySelectorAll('.hero-dot');
    const prevButton = slider.querySelector('.hero-prev');
    const nextButton = slider.querySelector('.hero-next');

    let currentSlide = 0;
    let autoSlide;

    const slideDuration = 6000;

    function showSlide(index) {

        if (index >= slides.length) {
            index = 0;
        }

        if (index < 0) {
            index = slides.length - 1;
        }

        slides.forEach(function (slide) {
            slide.classList.remove('active');
        });

        dots.forEach(function (dot) {
            dot.classList.remove('active');
        });

        slides[index].classList.add('active');

        if (dots[index]) {
            dots[index].classList.add('active');
        }

        currentSlide = index;
    }


    function nextSlide() {
        showSlide(currentSlide + 1);
    }


    function previousSlide() {
        showSlide(currentSlide - 1);
    }


    function startAutoSlide() {

        stopAutoSlide();

        autoSlide = setInterval(function () {
            nextSlide();
        }, slideDuration);
    }


    function stopAutoSlide() {

        if (autoSlide) {
            clearInterval(autoSlide);
        }
    }


    if (nextButton) {

        nextButton.addEventListener('click', function () {

            nextSlide();

            startAutoSlide();

        });

    }


    if (prevButton) {

        prevButton.addEventListener('click', function () {

            previousSlide();

            startAutoSlide();

        });

    }


    dots.forEach(function (dot, index) {

        dot.addEventListener('click', function () {

            showSlide(index);

            startAutoSlide();

        });

    });


    slider.addEventListener('mouseenter', stopAutoSlide);

    slider.addEventListener('mouseleave', startAutoSlide);


    // Mobile swipe support

    let touchStartX = 0;
    let touchEndX = 0;


    slider.addEventListener('touchstart', function (event) {

        touchStartX = event.changedTouches[0].screenX;

    }, { passive: true });


    slider.addEventListener('touchend', function (event) {

        touchEndX = event.changedTouches[0].screenX;

        const swipeDistance = touchStartX - touchEndX;

        if (Math.abs(swipeDistance) < 50) {
            return;
        }

        if (swipeDistance > 0) {
            nextSlide();
        } else {
            previousSlide();
        }

        startAutoSlide();

    }, { passive: true });


    showSlide(currentSlide);

    startAutoSlide();

});

// ── PREMIUM STATS COUNT-UP ANIMATION ────────────────────────

document.addEventListener('DOMContentLoaded', function () {

    const counters = document.querySelectorAll('.stat-counter');
    const statsBar = document.querySelector('.stats-bar');

    if (!counters.length || !statsBar) {
        return;
    }

    let hasAnimated = false;

    function animateCounter(counter) {

        const target = parseInt(counter.dataset.target, 10);
        const suffix = counter.dataset.suffix || '';

        const duration = 1800;
        const startTime = performance.now();

        function updateCounter(currentTime) {

            const elapsed = currentTime - startTime;

            const progress = Math.min(elapsed / duration, 1);

            // Smooth premium easing
            const easedProgress = 1 - Math.pow(1 - progress, 4);

            const currentValue = Math.floor(target * easedProgress);

            counter.textContent =
                currentValue.toLocaleString() + suffix;

            if (progress < 1) {

                requestAnimationFrame(updateCounter);

            } else {

                counter.textContent =
                    target.toLocaleString() + suffix;

            }

        }

        requestAnimationFrame(updateCounter);

    }


    const statsObserver = new IntersectionObserver(
        function (entries) {

            entries.forEach(function (entry) {

                if (entry.isIntersecting && !hasAnimated) {

                    hasAnimated = true;

                    counters.forEach(function (counter, index) {

                        setTimeout(function () {

                            animateCounter(counter);

                        }, index * 150);

                    });

                    statsObserver.unobserve(statsBar);

                }

            });

        },
        {
            threshold: 0.35
        }
    );


    statsObserver.observe(statsBar);

});