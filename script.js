document.addEventListener('DOMContentLoaded', function () {
    const contactForm = document.getElementById('contactForm');
    const messageOutput = document.getElementById('messageOutput');

    const revealItems = document.querySelectorAll('.reveal');
    const revealObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
            }
        });
    }, {
        threshold: 0.15
    });

    revealItems.forEach(function (item) {
        revealObserver.observe(item);
    });

    const statNumbers = document.querySelectorAll('.stat-number');
    const statObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                if (entry.target.dataset.started === 'true') {
                    return;
                }

                entry.target.dataset.started = 'true';
                const target = Number(entry.target.dataset.target);
                const suffix = entry.target.dataset.suffix || '';
                const duration = 1800;
                const startTime = performance.now();

                function updateCounter(time) {
                    const progress = Math.min((time - startTime) / duration, 1);
                    const easedProgress = 1 - Math.pow(1 - progress, 3);
                    const current = Math.floor(easedProgress * target);
                    entry.target.textContent = current + suffix;

                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    } else {
                        entry.target.textContent = target + suffix;
                        entry.target.classList.add('finished');
                    }
                }

                requestAnimationFrame(updateCounter);
                statObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.4
    });

    statNumbers.forEach(function (item) {
        statObserver.observe(item);
    });

    if (contactForm && messageOutput) {
        contactForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(contactForm);

            fetch('submit_contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    messageOutput.className = 'notice notice-success';
                    messageOutput.textContent = data.message;
                    contactForm.reset();
                } else {
                    messageOutput.className = 'notice notice-error';
                    messageOutput.textContent = data.message;
                }
            })
            .catch(() => {
                messageOutput.className = 'notice notice-error';
                messageOutput.textContent = 'Sorry, something went wrong. Please try again later.';
            });
        });
    }

    // Active nav link is set server-side (see includes/header.php), so no
    // client-side hash-tracking is needed here anymore.

    const sidebar = document.querySelector('.sidebar');
    const sidebarToggler = document.querySelector('.sidebar-toggler');
    let lastScrollY = window.pageYOffset;

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('open');
    }

    function openSidebar() {
        if (sidebar) sidebar.classList.add('open');
    }

    if (sidebarToggler) {
        sidebarToggler.addEventListener('click', function () {
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    window.addEventListener('scroll', function () {
        const currentScrollY = window.pageYOffset;
        if (currentScrollY > lastScrollY && sidebar) {
            closeSidebar();
        }
        lastScrollY = currentScrollY;
    });
});
