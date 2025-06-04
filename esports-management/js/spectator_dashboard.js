document.addEventListener('DOMContentLoaded', function() {
    const loader = document.getElementById('loader');
    
    setTimeout(() => {
        loader.style.opacity = '0';
        setTimeout(() => {
            loader.style.display = 'none';
        }, 500);
    }, 2000);
    
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    const themeIcon = themeToggle.querySelector('i');
    
    const isDarkTheme = localStorage.getItem('darkTheme') === 'true';
    
    if (isDarkTheme) {
        body.classList.add('dark-theme');
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
    }
    
    themeToggle.addEventListener('click', function() {
        body.classList.toggle('dark-theme');
        
        if (body.classList.contains('dark-theme')) {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
            localStorage.setItem('darkTheme', 'true');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
            localStorage.setItem('darkTheme', 'false');
        }
    });
    
    const navLinks = document.querySelectorAll('.sidebar-nav a');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navLinks.forEach(l => l.parentElement.classList.remove('active'));
            
            this.parentElement.classList.add('active');
        });
    });
    
    const remindButtons = document.querySelectorAll('.remind-btn');
    
    remindButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = this.querySelector('i');
            
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                this.innerHTML = `<i class="fas fa-bell"></i> Reminded`;
                this.style.backgroundColor = '#00b894';
                this.style.color = 'white';
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                this.innerHTML = `<i class="far fa-bell"></i> Remind`;
                this.style.backgroundColor = '#fdcb6e';
                this.style.color = '#1e272e';
            }
        });
    });
    
    function updateStats() {
        const statValues = document.querySelectorAll('.stat-value');
        
        statValues.forEach(stat => {
            if (!isNaN(parseInt(stat.textContent))) {
                let currentValue = parseInt(stat.textContent);
                
                let change = Math.floor(Math.random() * 3) - 1;
                
                currentValue = Math.max(1, currentValue + change);
                
                stat.style.transform = 'scale(1.1)';
                stat.style.color = change > 0 ? '#00b894' : change < 0 ? '#d63031' : 'inherit';
                
                setTimeout(() => {
                    stat.textContent = currentValue;
                    
                    setTimeout(() => {
                        stat.style.transform = 'scale(1)';
                        stat.style.color = 'inherit';
                    }, 300);
                }, 300);
            }
        });
    }
    
    setInterval(updateStats, 30000);
});