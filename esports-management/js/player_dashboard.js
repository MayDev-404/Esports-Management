document.addEventListener('DOMContentLoaded', function() {
    const toggleMenu = document.querySelector('.toggle-menu');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (toggleMenu) {
        toggleMenu.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    document.addEventListener('click', function(e) {
        const isClickInsideSidebar = sidebar.contains(e.target);
        const isClickOnToggleMenu = toggleMenu.contains(e.target);
        
        if (!isClickInsideSidebar && !isClickOnToggleMenu && window.innerWidth < 992) {
            sidebar.classList.remove('active');
        }
    });
    
    const notificationIcon = document.querySelector('.notifications');
    
    if (notificationIcon) {
        notificationIcon.addEventListener('click', function() {
            console.log('Notifications clicked');
        });
    }
    
    const userDropdown = document.querySelector('.user-dropdown');
    
    if (userDropdown) {
        userDropdown.addEventListener('click', function() {
            console.log('User dropdown clicked');
        });
    }
    
    const currentPage = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-menu ul li a');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage || 
            (currentPage.includes('index.php') && link.getAttribute('href') === '#')) {
            link.parentElement.classList.add('active');
        }
    });
    
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.animationDelay = `${0.1 * (index + 1)}s`;
    });
    
    const matchCards = document.querySelectorAll('.match-card');
    matchCards.forEach(card => {
        const dateText = card.querySelector('.date').textContent;
        const matchDate = new Date(dateText);
        const today = new Date();
        
        if (matchDate.toDateString() === today.toDateString()) {
            card.classList.add('match-today');
        }
    });
    
    function adjustSidebarHeight() {
        const windowHeight = window.innerHeight;
        sidebar.style.height = `${windowHeight}px`;
    }
    
    window.addEventListener('resize', adjustSidebarHeight);
    adjustSidebarHeight();
});