/**
 * Main JavaScript
 * SMK INFOKOM BOGOR
 */

document.addEventListener('DOMContentLoaded', function() {

    // ===== MOBILE NAVIGATION =====
    const hamburger = document.getElementById('hamburgerBtn');
    const mobileNav = document.getElementById('mobileNav');
    const navOverlay = document.getElementById('navOverlay');

    function toggleMobileNav() {
        hamburger.classList.toggle('active');
        mobileNav.classList.toggle('open');
        navOverlay.classList.toggle('active');
        document.body.style.overflow = mobileNav.classList.contains('open') ? 'hidden' : '';
    }

    if (hamburger) {
        hamburger.addEventListener('click', toggleMobileNav);
    }

    if (navOverlay) {
        navOverlay.addEventListener('click', toggleMobileNav);
    }

    // Close mobile nav on link click
    document.querySelectorAll('.mobile-nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (mobileNav.classList.contains('open')) {
                toggleMobileNav();
            }
        });
    });

    // ===== STICKY NAVBAR SHADOW =====
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // ===== HERO SLIDER =====
    initSlider();

    // ===== COUNTER ANIMATION =====
    initCounters();

    // ===== LIGHTBOX =====
    initLightbox();

    // ===== FORM VALIDATION HELPER =====
    initFormValidation();

    // ===== AUTO DISMISS FLASH =====
    const flashMsg = document.getElementById('flashMessage');
    if (flashMsg) {
        setTimeout(() => {
            flashMsg.style.opacity = '0';
            flashMsg.style.transition = 'opacity 0.5s ease';
            setTimeout(() => flashMsg.remove(), 500);
        }, 5000);
    }
});

// ===== SLIDER =====
function initSlider() {
    const slides = document.querySelectorAll('.hero-slide');
    if (slides.length === 0) return;

    let current = 0;
    const dots = document.querySelectorAll('.slider-dot');
    const prevBtn = document.querySelector('.slider-arrow.prev');
    const nextBtn = document.querySelector('.slider-arrow.next');
    let autoSlideInterval;

    function goToSlide(index) {
        slides[current].classList.remove('active');
        if (dots[current]) dots[current].classList.remove('active');
        current = (index + slides.length) % slides.length;
        slides[current].classList.add('active');
        if (dots[current]) dots[current].classList.add('active');
    }

    function nextSlide() {
        goToSlide(current + 1);
    }

    function prevSlide() {
        goToSlide(current - 1);
    }

    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, 5000);
    }

    function stopAutoSlide() {
        clearInterval(autoSlideInterval);
    }

    // Dot clicks
    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            stopAutoSlide();
            goToSlide(i);
            startAutoSlide();
        });
    });

    // Arrow clicks
    if (prevBtn) prevBtn.addEventListener('click', () => {
        stopAutoSlide();
        prevSlide();
        startAutoSlide();
    });

    if (nextBtn) nextBtn.addEventListener('click', () => {
        stopAutoSlide();
        nextSlide();
        startAutoSlide();
    });

    // Touch swipe
    let touchStartX = 0;
    const sliderEl = document.querySelector('.hero-slider');
    if (sliderEl) {
        sliderEl.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
        }, { passive: true });

        sliderEl.addEventListener('touchend', (e) => {
            const diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) {
                stopAutoSlide();
                if (diff > 0) nextSlide();
                else prevSlide();
                startAutoSlide();
            }
        }, { passive: true });
    }

    startAutoSlide();
}

// ===== COUNTER ANIMATION =====
function initCounters() {
    const counters = document.querySelectorAll('.stat-number[data-target]');
    if (counters.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
}

function animateCounter(el) {
    const target = parseInt(el.dataset.target) || 0;
    const suffix = el.dataset.suffix || '';
    const duration = 2000;
    const start = performance.now();

    function update(now) {
        const elapsed = now - start;
        const progress = Math.min(elapsed / duration, 1);
        // Ease out cubic
        const eased = 1 - Math.pow(1 - progress, 3);
        const current = Math.floor(eased * target);
        el.textContent = current.toLocaleString('id-ID') + suffix;

        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            el.textContent = target.toLocaleString('id-ID') + suffix;
        }
    }

    requestAnimationFrame(update);
}

// ===== LIGHTBOX =====
function initLightbox() {
    const galleryItems = document.querySelectorAll('.gallery-item[data-src]');
    if (galleryItems.length === 0) return;

    // Create lightbox element
    const lightbox = document.createElement('div');
    lightbox.id = 'lightbox';
    lightbox.innerHTML = `
        <div class="lightbox-overlay"></div>
        <div class="lightbox-content">
            <button class="lightbox-close" aria-label="Close">&times;</button>
            <img src="" alt="">
            <p class="lightbox-caption"></p>
        </div>
    `;
    document.body.appendChild(lightbox);

    const overlay = lightbox.querySelector('.lightbox-overlay');
    const closeBtn = lightbox.querySelector('.lightbox-close');
    const img = lightbox.querySelector('img');
    const caption = lightbox.querySelector('.lightbox-caption');

    function openLightbox(src, title) {
        img.src = src;
        caption.textContent = title || '';
        lightbox.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.remove('open');
        document.body.style.overflow = '';
        img.src = '';
    }

    galleryItems.forEach(item => {
        item.addEventListener('click', () => {
            openLightbox(item.dataset.src, item.dataset.title || '');
        });
    });

    overlay.addEventListener('click', closeLightbox);
    closeBtn.addEventListener('click', closeLightbox);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeLightbox();
    });
}

// ===== FORM VALIDATION =====
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;

            requiredFields.forEach(field => {
                field.classList.remove('field-error');
                if (!field.value.trim()) {
                    field.classList.add('field-error');
                    valid = false;
                }
            });

            // Email validation
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                field.classList.remove('field-error');
                if (field.value && !isValidEmail(field.value)) {
                    field.classList.add('field-error');
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();
                const firstError = form.querySelector('.field-error');
                if (firstError) firstError.focus();
            }
        });
    });
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// ===== SCROLL ANIMATIONS =====
const animateOnScroll = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-in');
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.fade-up, .fade-in').forEach(el => {
    animateOnScroll.observe(el);
});