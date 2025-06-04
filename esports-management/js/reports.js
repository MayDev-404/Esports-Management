function generateReport() {
    const reportType = document.getElementById('reportType').value;
    if (!reportType) {
        alert("Please select a report type.");
        return;
    }

    document.getElementById('reportResults').innerHTML = '<div class="loading"></div>';
    document.getElementById('performanceChart').style.display = 'none';

    fetch('reports_data.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `reportType=${reportType}`
    })
    .then(res => res.text())
    .then(html => {
        document.getElementById('reportResults').innerHTML = html;
        
        setTimeout(() => {
            const table = document.querySelector('#reportResults table');
            if (table) table.classList.add('table-loaded');
        }, 100);

        if (reportType === 'player') {
            const labels = [];
            const data = [];
            const colors = [];

            document.querySelectorAll('#reportResults tbody tr').forEach(row => {
                const username = row.children[1].innerText;
                const winRate = parseFloat(row.children[5].innerText);
                
                labels.push(username);
                data.push(winRate);
                
                if (winRate >= 70) {
                    colors.push('rgba(0, 255, 143, 0.8)');
                } else if (winRate >= 45) {
                    colors.push('rgba(0, 242, 255, 0.8)');
                } else {
                    colors.push('rgba(255, 56, 96, 0.8)');
                }
                
                row.children[5].setAttribute('data-value', `${winRate}%`);
            });

            drawChart(labels, data, colors);
        } else {
            document.getElementById('performanceChart').style.display = 'none';
        }
        
        if (reportType === 'tournament' || reportType === 'match') {
            colorizeStatusCells();
        }
    })
    .catch(error => {
        document.getElementById('reportResults').innerHTML = `
            <div class="error-message">
                <p>Error loading report: ${error.message}</p>
            </div>
        `;
    });
}

function colorizeStatusCells() {
    document.querySelectorAll('#reportResults tbody tr').forEach(row => {
        const statusCell = row.children[row.children.length - 1];
        const status = statusCell.innerText.toLowerCase();
        
        if (status.includes('win') || status.includes('victory') || status.includes('completed')) {
            statusCell.style.color = 'var(--success)';
        } else if (status.includes('loss') || status.includes('defeat') || status.includes('cancelled')) {
            statusCell.style.color = 'var(--danger)';
        } else if (status.includes('upcoming') || status.includes('scheduled') || status.includes('pending')) {
            statusCell.style.color = 'var(--warning)';
        }
    });
}

function drawChart(labels, data, colors) {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    document.getElementById('performanceChart').style.display = 'block';

    if (window.myChart) window.myChart.destroy();
    
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(0, 242, 255, 0.6)');
    gradient.addColorStop(1, 'rgba(88, 64, 255, 0.1)');

    window.myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Win Rate (%)',
                data: data,
                backgroundColor: colors || gradient,
                borderColor: 'rgba(0, 242, 255, 1)',
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: 'rgba(255, 0, 228, 0.7)',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: '#ffffff',
                        font: {
                            family: "'Rajdhani', sans-serif",
                            size: 14
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'PLAYER PERFORMANCE ANALYSIS',
                    color: '#00f2ff',
                    font: {
                        family: "'Orbitron', sans-serif",
                        size: 16,
                        weight: 'bold'
                    },
                    padding: {
                        bottom: 20
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: '#a0a0c0',
                        font: {
                            family: "'Rajdhani', sans-serif"
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: '#a0a0c0',
                        font: {
                            family: "'Rajdhani', sans-serif"
                        }
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeOutQuart'
            }
        }
    });
    
    setTimeout(() => {
        document.getElementById('performanceChart').classList.add('chart-loaded');
    }, 100);
}

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('keydown', function(e) {
        if (e.altKey && e.key === 'g') {
            generateReport();
        }
    });
    
    document.querySelector('.report-container').classList.add('fade-in');
});