/**
 * EduCRM Frontend JavaScript
 */

// AOS Initialize
AOS.init({
    duration: 800,
    easing: 'ease-out',
    once: true,
    offset: 50
});

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('mainNav');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            // Close mobile menu
            const navCollapse = document.querySelector('.navbar-collapse');
            if (navCollapse.classList.contains('show')) {
                navCollapse.classList.remove('show');
            }
        }
    });
});

// Counter animation on scroll
const observerOptions = { threshold: 0.5 };
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counters = entry.target.querySelectorAll('.stat-item strong');
            counters.forEach(counter => {
                const text = counter.textContent;
                const num = parseInt(text.replace(/[^0-9]/g, ''));
                const suffix = text.replace(/[0-9]/g, '');
                animateValue(counter, 0, num, 1500, suffix);
            });
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

const statsSection = document.querySelector('.hero-stats');
if (statsSection) observer.observe(statsSection);

function animateValue(el, start, end, duration, suffix) {
    const step = (end - start) / (duration / 16);
    let current = start;
    const timer = setInterval(() => {
        current += step;
        if (current >= end) {
            el.textContent = end + suffix;
            clearInterval(timer);
        } else {
            el.textContent = Math.floor(current) + suffix;
        }
    }, 16);
}
