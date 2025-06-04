document.addEventListener('DOMContentLoaded', function() {
    const performanceCtx = document.getElementById('teamPerformanceChart');
    
    if (performanceCtx) {
        let winsValue = 0;
        let lossesValue = 0;
        let drawsValue = 0;
        
        const winsElement = document.querySelector('.stat-item:nth-child(2) .stat-value');
        const lossesElement = document.querySelector('.stat-item:nth-child(3) .stat-value');
        const drawsElement = document.querySelector('.stat-item:nth-child(4) .stat-value');
        
        if (winsElement) winsValue = parseInt(winsElement.textContent) || 0;
        if (lossesElement) lossesValue = parseInt(lossesElement.textContent) || 0;
        if (drawsElement) drawsValue = parseInt(drawsElement.textContent) || 0;
        
        const totalMatches = winsValue + lossesValue + drawsValue;
        const winRate = totalMatches > 0 ? (winsValue / totalMatches) * 100 : 50;
        
        const matchCoordination = Math.min(100, Math.max(30, winRate + Math.floor(Math.random() * 20)));
        const strategy = Math.min(100, Math.max(30, winRate + Math.floor(Math.random() * 15)));
        const teamSynergy = Math.min(100, Math.max(40, winRate + Math.floor(Math.random() * 25)));
        const individualSkill = Math.min(100, Math.max(50, winRate + Math.floor(Math.random() * 30)));
        
        const teamPerformanceChart = new Chart(performanceCtx, {
            type: 'radar',
            data: {
                labels: ['Win Rate', 'Match Coordination', 'Strategy', 'Team Synergy', 'Individual Skill'],
                datasets: [{
                    label: 'Team Performance',
                    data: [winRate, matchCoordination, strategy, teamSynergy, individualSkill],
                    backgroundColor: 'rgba(108, 92, 231, 0.2)',
                    borderColor: '#6c5ce7',
                    borderWidth: 2,
                    pointBackgroundColor: '#6c5ce7',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#6c5ce7'
                },
                {
                    label: 'League Average',
                    data: [50, 60, 55, 65, 70],
                    backgroundColor: 'rgba(0, 206, 201, 0.2)',
                    borderColor: '#00cec9',
                    borderWidth: 2,
                    pointBackgroundColor: '#00cec9',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#00cec9'
                }]
            },
            options: {
                scales: {
                    r: {
                        angleLines: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        pointLabels: {
                            color: '#b2bec3',
                            font: {
                                size: 12
                            }
                        },
                        ticks: {
                            backdropColor: 'transparent',
                            color: 'rgba(255, 255, 255, 0.5)',
                            showLabelBackdrop: false,
                            z: 1
                        },
                        min: 0,
                        max: 100
                    }
                },
                elements: {
                    line: {
                        tension: 0.1
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#b2bec3',
                            boxWidth: 12,
                            padding: 20,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(26, 36, 47, 0.9)',
                        titleColor: '#ffffff',
                        bodyColor: '#b2bec3',
                        bodyFont: {
                            size: 12
                        },
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        padding: 12,
                        displayColors: false
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
    
    const scheduleMatchBtn = document.getElementById('scheduleMatchBtn');
    if (scheduleMatchBtn) {
        scheduleMatchBtn.addEventListener('click', function() {
            window.location.href = 'manager/view_schedules.php?action=schedule';
        });
    }
    
    const addPlayerBtn = document.getElementById('addPlayerBtn');
    if (addPlayerBtn) {
        addPlayerBtn.addEventListener('click', function() {
            window.location.href = 'manager/manage_your_team.php?action=add';
        });
    }
    
    const cards = document.querySelectorAll('.dashboard-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 15px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.08)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08)';
        });
    });
    
    function updateTime() {
        const now = new Date();
        const options = { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' };
        const dateElement = document.querySelector('.current-date');
        if (dateElement) {
            dateElement.textContent = now.toLocaleDateString('en-US', options);
        }
    }
    
    updateTime();
    setInterval(updateTime, 60000);
});