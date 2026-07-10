// assets/js/main.js

// ============================================
// 1. Navigation Toggle
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            const isOpen = navMenu.classList.toggle('open');
            this.setAttribute('aria-expanded', isOpen);
        });
    }
});

// ============================================
// 2. Hero Slider
// ============================================
(function() {
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slider img');
    const dots = document.querySelectorAll('.hero-dots .dot');
    let slideInterval;
    
    if (slides.length === 0) return;
    
    function goToSlide(index) {
        // Remove active from all slides
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        
        // Set active
        currentSlide = (index + slides.length) % slides.length;
        slides[currentSlide].classList.add('active');
        if (dots[currentSlide]) {
            dots[currentSlide].classList.add('active');
        }
    }
    
    function nextSlide() {
        goToSlide(currentSlide + 1);
    }
    
    function startSlider() {
        if (slideInterval) clearInterval(slideInterval);
        slideInterval = setInterval(nextSlide, 5000);
    }
    
    // Add dot click handlers
    dots.forEach((dot, index) => {
        dot.addEventListener('click', function() {
            goToSlide(index);
            startSlider(); // Reset timer
        });
    });
    
    // Start slider
    goToSlide(0);
    startSlider();
    
    // Pause on hover
    const hero = document.querySelector('.hero');
    if (hero) {
        hero.addEventListener('mouseenter', () => {
            if (slideInterval) clearInterval(slideInterval);
        });
        hero.addEventListener('mouseleave', startSlider);
    }
})();

// ============================================
// 3. Typewriter Effect
// ============================================
(function() {
    const element = document.querySelector('.typewriter-text');
    if (!element) return;
    
    const texts = [
        'Excellence in Education Since 1985',
        'Where Champions Are Made',
        'Your Child\'s Future Starts Here',
        '80% UACE Pass Rate'
    ];
    
    let textIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    let currentText = '';
    
    function type() {
        const fullText = texts[textIndex];
        
        if (isDeleting) {
            currentText = fullText.substring(0, charIndex - 1);
            charIndex--;
        } else {
            currentText = fullText.substring(0, charIndex + 1);
            charIndex++;
        }
        
        // Remove existing cursor span and set text
        const cursorSpan = element.querySelector('.cursor');
        element.textContent = currentText;
        if (cursorSpan) {
            element.appendChild(cursorSpan);
        }
        
        // Add cursor back
        if (!element.querySelector('.cursor')) {
            const cursor = document.createElement('span');
            cursor.className = 'cursor';
            cursor.textContent = '|';
            element.appendChild(cursor);
        }
        
        // Speed control
        let speed = isDeleting ? 30 : 50;
        
        if (!isDeleting && charIndex === fullText.length) {
            speed = 2000; // Pause at end
            isDeleting = true;
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            textIndex = (textIndex + 1) % texts.length;
            speed = 500;
        }
        
        setTimeout(type, speed);
    }
    
    // Start typing after a short delay
    setTimeout(type, 800);
})();

// ============================================
// 4. Smooth Scroll for Anchor Links
// ============================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// ============================================
// 5. Form Validation Helper
// ============================================
function validateForm(form) {
    const inputs = form.querySelectorAll('[required]');
    let valid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#dc3545';
            valid = false;
        } else {
            input.style.borderColor = '';
        }
    });
    
    return valid;
}

// ============================================
// 6. Load News via AJAX (for homepage)
// ============================================
async function loadLatestNews() {
    const container = document.getElementById('news-feed');
    if (!container) return;
    
    try {
        const response = await fetch('api/news.php?limit=3');
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();
        
        container.innerHTML = '';
        
        data.forEach(item => {
            const card = document.createElement('div');
            card.className = 'card';
            
            const imgHtml = item.featured_image 
                ? `<img src="${item.featured_image}" alt="${item.title}" class="card-img" loading="lazy">`
                : `<div class="card-img-placeholder"></div>`;
            
            card.innerHTML = `
                ${imgHtml}
                <div class="card-content">
                    <span class="badge" style="background:${item.cat_color || '#2d7a4a'}; color:#fff;">
                        ${item.cat_name || 'News'}
                    </span>
                    <h3><a href="article.php?slug=${item.slug}">${item.title}</a></h3>
                    <p>${item.excerpt || truncateText(item.body, 120)}</p>
                    <div class="card-meta">
                        <span class="date"><i class="fas fa-calendar-alt"></i> ${formatDate(item.published_at || item.created_at)}</span>
                        <a href="article.php?slug=${item.slug}" class="read-more">Read More →</a>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });
    } catch (error) {
        console.error('Error loading news:', error);
        container.innerHTML = '<p class="text-center" style="padding:40px;color:#666;">Unable to load news at this time.</p>';
    }
}

// Helper: truncate text
function truncateText(text, length = 120) {
    if (!text) return '';
    if (text.length <= length) return text;
    return text.substring(0, length) + '...';
}

// Helper: format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

// ============================================
// 7. Initialize
// ============================================
// Load news on homepage
if (document.getElementById('news-feed')) {
    loadLatestNews();
}

// Auto-dismiss alerts after 5 seconds
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);