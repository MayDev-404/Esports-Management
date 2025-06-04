document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const headerOffset = 80;
            const elementPosition = target.offsetTop;
            const offsetPosition = elementPosition - headerOffset;
            
            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    });
});

const statsNumbers = document.querySelectorAll('.stat .number');
let hasAnimated = false;

function animateStats() {
    if (hasAnimated) return;
    
    statsNumbers.forEach(stat => {
        const target = parseInt(stat.getAttribute('data-target') || stat.textContent);
        let current = 0;
        const increment = Math.ceil(target / 50);
        const duration = 2000;
        const interval = duration / (target / increment);
        
        const counter = setInterval(() => {
            current += increment;
            if (current >= target) {
                stat.textContent = target + "+";
                clearInterval(counter);
            } else {
                stat.textContent = current;
            }
        }, interval);
    });
    
    hasAnimated = true;
}

function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

function checkStatsVisibility() {
    const heroSection = document.querySelector('.hero-stats');
    if (heroSection && isInViewport(heroSection)) {
        animateStats();
        window.removeEventListener('scroll', checkStatsVisibility);
    }
}

window.addEventListener('load', checkStatsVisibility);
window.addEventListener('scroll', checkStatsVisibility);

document.querySelectorAll('.feature-item').forEach(item => {
    item.addEventListener('mouseenter', () => {
        item.style.transform = 'translateY(-15px)';
    });

    item.addEventListener('mouseleave', () => {
        item.style.transform = 'translateY(0)';
    });
});

const hero = document.querySelector('.hero');
window.addEventListener('scroll', () => {
    if (hero) {
        const scrollPosition = window.pageYOffset;
        hero.style.backgroundPositionY = scrollPosition * 0.5 + 'px';
    }
});

const sections = document.querySelectorAll('section[id]');
window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        const sectionHeight = section.offsetHeight;
        if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
            current = section.getAttribute('id');
        }
    });

    document.querySelectorAll('nav a').forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('active');
        }
    });
});

const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.2
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-in');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

document.querySelectorAll('section').forEach(section => {
    section.classList.add('fade-in');
    observer.observe(section);
});

const gameIcons = document.querySelectorAll('.game-icon');
gameIcons.forEach(icon => {
    icon.addEventListener('mousemove', (e) => {
        const rect = icon.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        icon.style.background = `radial-gradient(circle at ${x}px ${y}px, rgba(40, 40, 40, 0.9), var(--card-bg))`;
    });
    
    icon.addEventListener('mouseleave', () => {
        icon.style.background = 'linear-gradient(135deg, var(--card-bg), rgba(40, 40, 40, 0.9))';
    });
});

const tagline = document.querySelector('.hero .tagline');
if (tagline) {
    const text = tagline.textContent;
    tagline.textContent = '';
    let i = 0;
    
    function typeWriter() {
        if (i < text.length) {
            tagline.textContent += text.charAt(i);
            i++;
            setTimeout(typeWriter, 50);
        }
    }
    
    setTimeout(typeWriter, 500);
}

const style = document.createElement('style');
style.textContent = `
    .fade-in {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.8s ease, transform 0.8s ease;
    }
    
    .animate-in {
        opacity: 1;
        transform: translateY(0);
    }
    
    nav a.active {
        color: var(--primary-color);
    }
    
    nav a.active::after {
        width: 70%;
    }
`;
document.head.appendChild(style);