document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const loader = document.querySelector('.dashboard-loader');
        loader.style.opacity = '0';
        setTimeout(() => {
            loader.style.display = 'none';
        }, 600);
    }, 2000);

    initAnimations();
    
    initCounters();
    
    setupThemeToggle();
});

function initAnimations() {
    const fadeElements = document.querySelectorAll('.fade-in');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('visible');
                }, 150 * Array.from(fadeElements).indexOf(entry.target));
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    fadeElements.forEach(element => {
        observer.observe(element);
    });
}

function initCounters() {
    const counters = document.querySelectorAll('.counter');
    
    counters.forEach(counter => {
        const target = parseInt(counter.innerText.replace(/,/g, ''));
        counter.innerText = '0';
        
        const duration = 2000; 
        const steps = 50; 
        const stepTime = duration / steps;
        const increment = target / steps;
        
        let current = 0;
        let step = 0;
        
        const updateCounter = () => {
            step++;
            current = Math.ceil(increment * step);
            
            if (current > target) current = target;
            
            counter.innerText = current.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            
            if (current < target) {
                setTimeout(updateCounter, stepTime);
            }
        };
        
        setTimeout(updateCounter, 500);
    });
}

function setupThemeToggle() {
    const themeToggle = document.getElementById('mode-toggle');
    const body = document.body;
    const moonIcon = '<i class="fas fa-moon"></i>';
    const sunIcon = '<i class="fas fa-sun"></i>';
    
    const savedTheme = localStorage.getItem('esports-dashboard-theme');
    
    if (savedTheme === 'light') {
        enableLightMode();
    }
    
    themeToggle.addEventListener('click', function() {
        this.classList.add('clicked');
        
        setTimeout(() => {
            this.classList.remove('clicked');
        }, 300);
        
        if (body.classList.contains('light-mode')) {
            disableLightMode();
        } else {
            enableLightMode();
        }
    });
    
    function enableLightMode() {
        body.classList.add('light-mode');
        themeToggle.innerHTML = moonIcon;
        localStorage.setItem('esports-dashboard-theme', 'light');
    }
    
    function disableLightMode() {
        body.classList.remove('light-mode');
        themeToggle.innerHTML = sunIcon;
        localStorage.setItem('esports-dashboard-theme', 'dark');
    }
}

document.addEventListener('click', function(e) {
    if (e.target.closest('.logout-card')) {
        const confirmed = confirm('Are you sure you want to log out?');
        if (!confirmed) {
            e.preventDefault();
        }
    }
});

class DashboardNotifications {
    constructor() {
        this.container = null;
        this.init();
    }
    
    init() {
        this.container = document.createElement('div');
        this.container.className = 'notification-container';
        this.container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            max-width: 350px;
            z-index: 9999;
        `;
        document.body.appendChild(this.container);
    }
    
    show(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            background-color: ${this.getBackgroundColor(type)};
            color: white;
            padding: 15px 20px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 0;
            transform: translateX(20px);
            transition: opacity 0.3s, transform 0.3s;
        `;
        
        notification.innerHTML = `
            <div>${this.getIcon(type)}</div>
            <div>${message}</div>
        `;
        
        this.container.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(20px)';
            
            setTimeout(() => {
                this.container.removeChild(notification);
            }, 300);
        }, duration);
    }
    
    getIcon(type) {
        switch(type) {
            case 'success': return '<i class="fas fa-check-circle"></i>';
            case 'error': return '<i class="fas fa-exclamation-circle"></i>';
            case 'warning': return '<i class="fas fa-exclamation-triangle"></i>';
            default: return '<i class="fas fa-info-circle"></i>';
        }
    }
    
    getBackgroundColor(type) {
        switch(type) {
            case 'success': return '#00e676';
            case 'error': return '#ff1744';
            case 'warning': return '#ffa000';
            default: return '#00b8d4';
        }
    }
}

const notifications = new DashboardNotifications();

document.querySelectorAll('.nav-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.querySelector('.card-icon').style.transform = 'scale(1.1) rotate(5deg)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.querySelector('.card-icon').style.transform = 'scale(1) rotate(0)';
    });
});

function setupTableRowActions() {
    document.querySelectorAll('.data-table .action-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const action = this.dataset.action;
            const itemId = this.closest('tr').dataset.id;
            
            if (action === 'edit') {
                editItem(itemId);
            } else if (action === 'delete') {
                deleteItem(itemId);
            } else if (action === 'view') {
                viewItem(itemId);
            }
        });
    });
}

function editItem(id) {
    notifications.show(`Editing item #${id}`, 'info');
}

function deleteItem(id) {
    const confirmed = confirm(`Are you sure you want to delete item #${id}?`);
    if (confirmed) {
        notifications.show(`Item #${id} deleted successfully`, 'success');
    }
}

function viewItem(id) {
    notifications.show(`Viewing details for item #${id}`, 'info');
}

function trackActivity(action) {
    console.log(`User activity: ${action}`);
}

document.addEventListener('DOMContentLoaded', function() {
    trackActivity('Admin dashboard loaded');
    
    document.querySelectorAll('.nav-card').forEach(card => {
        card.addEventListener('click', function() {
            const title = this.querySelector('h3').textContent;
            trackActivity(`Navigated to ${title}`);
        });
    });
});