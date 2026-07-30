
document.addEventListener('DOMContentLoaded', function() {
    console.log('Namugongo Parents School website loaded successfully!');

    // 0. Header transparency on scroll - show images behind on top, solid when scrolled
    const header = document.querySelector('header');
    function updateHeaderScroll() {
        if (header) {
            if (window.scrollY > 60) {
                header.style.background = 'linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 58, 138, 0.92) 100%)';
                header.style.borderBottomColor = 'rgba(251, 191, 36, 0.35)';
            } else {
                header.style.background = 'linear-gradient(135deg, rgba(15, 23, 42, 0.75) 0%, rgba(30, 58, 138, 0.7) 100%)';
                header.style.borderBottomColor = 'rgba(251, 191, 36, 0.2)';
            }
        }
    }
    window.addEventListener('scroll', updateHeaderScroll);
    updateHeaderScroll();

    // 1. Add animation when elements come into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -60px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe features, stats, etc.
    document.querySelectorAll('.feature, .stat, .two-col, .three-col, .message-card, .gallery-grid img').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
        observer.observe(el);
    });

    // 2. Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // 3. Lightbox for gallery images
    const galleryImages = document.querySelectorAll('.gallery-grid img');
    if (galleryImages.length > 0) {
        const lightbox = document.createElement('div');
        lightbox.id = 'lightbox';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <span class="lightbox-close">&times;</span>
                <img src="" alt="Full size image" id="lightbox-img">
            </div>
        `;
        document.body.appendChild(lightbox);

        galleryImages.forEach(img => {
            img.addEventListener('click', function() {
                lightbox.style.display = 'flex';
                document.getElementById('lightbox-img').src = this.src;
                document.body.style.overflow = 'hidden';
            });
        });

        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox || e.target.classList.contains('lightbox-close')) {
                lightbox.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }
});

// 4. FAQ accordion toggle
document.querySelectorAll('.faq-question').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var item = btn.closest('.faq-item');
        if (item) item.classList.toggle('open');
    });
});
