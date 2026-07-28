// ============================================
// NAV TOGGLE (Mobile)
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            const isOpen = navMenu.classList.toggle('open');
            this.classList.toggle('active');
            this.setAttribute('aria-expanded', isOpen);
        });
    }
});

// ============================================
// STICKY NAV - SCROLL EFFECT
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const nav = document.getElementById('mainNav');
    
    if (!nav) return;
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });
});

// ============================================
// NEWS TICKER - WORKING
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    var slide = document.getElementById('tickerSlide');
    var link = document.getElementById('tickerLink');
    var container = document.getElementById('tickerContainer');
    
    // Check if elements exist
    if (!slide || !link || !container) {
        console.log('Ticker elements not found');
        return;
    }
    
    // Check if data exists
    if (typeof tickerNewsData === 'undefined') {
        console.log('Ticker data not found');
        return;
    }
    
    if (!tickerNewsData || tickerNewsData.length === 0) {
        console.log('No news items found');
        return;
    }
    
    console.log('Ticker loaded with ' + tickerNewsData.length + ' news items');
    
    // If only one news item, show it without animation
    if (tickerNewsData.length === 1) {
        link.textContent = tickerNewsData[0].title;
        link.href = 'article.php?slug=' + tickerNewsData[0].slug;
        return;
    }
    
    var currentIndex = 0;
    var timer = null;
    
    // Set first news item
    link.textContent = tickerNewsData[0].title;
    link.href = 'article.php?slug=' + tickerNewsData[0].slug;
    
    function showNextNews() {
        // Move to next index
        currentIndex = (currentIndex + 1) % tickerNewsData.length;
        var news = tickerNewsData[currentIndex];
        
        // Fade out
        slide.style.opacity = '0';
        slide.style.transition = 'opacity 0.3s ease';
        
        setTimeout(function() {
            // Change the content
            link.textContent = news.title;
            link.href = 'article.php?slug=' + news.slug;
            
            // Fade in
            slide.style.opacity = '1';
        }, 300);
    }
    
    function startTicker() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
        timer = setInterval(showNextNews, 4000);
        console.log('Ticker started');
    }
    
    // Pause on hover
    container.addEventListener('mouseenter', function() {
        if (timer) {
            clearInterval(timer);
            timer = null;
            console.log('Ticker paused');
        }
    });
    
    container.addEventListener('mouseleave', function() {
        if (!timer) {
            startTicker();
            console.log('Ticker resumed');
        }
    });
    
    // Start the ticker
    slide.style.opacity = '1';
    startTicker();
});

// ============================================
// HERO SLIDER
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    var currentSlide = 0;
    var slides = document.querySelectorAll('.hero-slider img');
    var dots = document.querySelectorAll('.hero-dots .dot');
    var slideInterval;
    
    if (slides.length === 0) return;
    
    function goToSlide(index) {
        slides.forEach(function(s) { s.classList.remove('active'); });
        dots.forEach(function(d) { d.classList.remove('active'); });
        
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
    
    dots.forEach(function(dot, index) {
        dot.addEventListener('click', function() {
            goToSlide(index);
            startSlider();
        });
    });
    
    goToSlide(0);
    startSlider();
    
    var hero = document.querySelector('.hero');
    if (hero) {
        hero.addEventListener('mouseenter', function() {
            if (slideInterval) clearInterval(slideInterval);
        });
        hero.addEventListener('mouseleave', startSlider);
    }
});

// ============================================
// TYPEWRITER EFFECT
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    var element = document.querySelector('.typewriter-text');
    if (!element) return;
    
    var texts = [
        'Excellence in Education Since 1985',
        'Where Champions Are Made',
        'Your Child\'s Future Starts Here',
        '80% UACE Pass Rate'
    ];
    
    var textIndex = 0;
    var charIndex = 0;
    var isDeleting = false;
    var currentText = '';
    
    function type() {
        var fullText = texts[textIndex];
        
        if (isDeleting) {
            currentText = fullText.substring(0, charIndex - 1);
            charIndex--;
        } else {
            currentText = fullText.substring(0, charIndex + 1);
            charIndex++;
        }
        
        var cursorSpan = element.querySelector('.cursor');
        element.textContent = currentText;
        if (cursorSpan) {
            element.appendChild(cursorSpan);
        }
        
        if (!element.querySelector('.cursor')) {
            var cursor = document.createElement('span');
            cursor.className = 'cursor';
            cursor.textContent = '|';
            element.appendChild(cursor);
        }
        
        var speed = isDeleting ? 30 : 50;
        
        if (!isDeleting && charIndex === fullText.length) {
            speed = 2000;
            isDeleting = true;
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            textIndex = (textIndex + 1) % texts.length;
            speed = 500;
        }
        
        setTimeout(type, speed);
    }
    
    setTimeout(type, 800);
});

console.log('All scripts loaded successfully!');