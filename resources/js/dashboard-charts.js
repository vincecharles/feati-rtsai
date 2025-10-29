// Dashboard Charts - Robust initialization
function initCharts() {
    // Wait for Chart.js to be available
    if (typeof Chart === 'undefined') {
        console.log('Waiting for Chart.js to load...');
        setTimeout(initCharts, 100);
        return;
    }

    console.log('✓ Chart.js loaded, initializing charts');
    
    // Get data from data attributes
    const dashboardData = document.getElementById('dashboard-data');
    if (!dashboardData) {
        console.error('❌ Dashboard data element not found');
        return;
    }

    try {
        const stats = JSON.parse(dashboardData.getAttribute('data-stats'));
        const programs = JSON.parse(dashboardData.getAttribute('data-programs'));
        const yearLevels = JSON.parse(dashboardData.getAttribute('data-year-levels'));
        const violations = JSON.parse(dashboardData.getAttribute('data-violations'));
        const severity = JSON.parse(dashboardData.getAttribute('data-severity') || '{}');
        const topTypes = JSON.parse(dashboardData.getAttribute('data-top-types') || '[]');
        const trends = JSON.parse(dashboardData.getAttribute('data-trends') || '[]');
        
        console.log('✓ Data parsed successfully');
        console.log('Severity:', severity);
        console.log('Top Types:', topTypes);
        console.log('Trends:', trends);
        
        // Now initialize the charts with the data
        initializeAllCharts(stats, programs, yearLevels, violations, severity, topTypes, trends);
    } catch (e) {
        console.error('❌ Error parsing dashboard data:', e);
        return;
    }
}

function initializeAllCharts(stats, programs, yearLevels, violations, severity, topTypes, trends) {
    console.log('Initializing all charts...');
    
    // Chart.js configuration for dark mode
    Chart.defaults.color = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
    Chart.defaults.borderColor = document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB';

    // Disciplinary Overview Chart (Violation Status)
    const violationCtx = document.getElementById('violationChart')?.getContext('2d');
    if (violationCtx) {
        new Chart(violationCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Resolved', 'Under Review'],
                datasets: [{
                    data: [
                        violations.pending,
                        violations.resolved,
                        violations.under_review
                    ],
                    backgroundColor: [
                        'rgb(251, 191, 36)',
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Violations Analytics - Trend Line Chart
    const violationTrendsCtx = document.getElementById('violationTrendsChart')?.getContext('2d');
    if (violationTrendsCtx) {
        console.log('Initializing Violations Analytics chart');
        console.log('Trends data:', trends);
        
        const trendLabels = trends.length > 0 ? trends.map(t => {
            const d = new Date(t.date);
            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }) : ['No Data'];
        const trendData = trends.length > 0 ? trends.map(t => t.count) : [0];
        
        console.log('Trend labels:', trendLabels);
        console.log('Trend data:', trendData);
        
        new Chart(violationTrendsCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Daily Violations',
                    data: trendData,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(239, 68, 68)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Violations by Severity Chart
    const violationSeverityCtx = document.getElementById('violationSeverityChart')?.getContext('2d');
    if (violationSeverityCtx) {
        new Chart(violationSeverityCtx, {
            type: 'bar',
            data: {
                labels: ['Minor', 'Moderate', 'Major', 'Critical'],
                datasets: [{
                    label: 'Count',
                    data: [
                        severity.minor || 0,
                        severity.moderate || 0,
                        severity.major || 0,
                        severity.critical || 0
                    ],
                    backgroundColor: [
                        'rgb(34, 197, 94)',
                        'rgb(251, 191, 36)',
                        'rgb(249, 115, 22)',
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                indexAxis: 'x',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Top Violation Types Chart
    const topViolationTypesCtx = document.getElementById('topViolationTypesChart')?.getContext('2d');
    if (topViolationTypesCtx) {
        console.log('Initializing Top Violation Types chart');
        console.log('Top types data:', topTypes);
        
        const typeLabels = topTypes.length > 0 ? topTypes.map(t => t.name) : ['No Data'];
        const typeData = topTypes.length > 0 ? topTypes.map(t => t.count) : [0];
        
        console.log('Type labels:', typeLabels);
        console.log('Type data:', typeData);
        
        new Chart(topViolationTypesCtx, {
            type: 'bar',
            data: {
                labels: typeLabels,
                datasets: [{
                    label: 'Reports',
                    data: typeData,
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(139, 92, 246)',
                        'rgb(236, 72, 153)',
                        'rgb(249, 115, 22)',
                        'rgb(239, 68, 68)'
                    ].slice(0, typeData.length),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                }
            }
        });
    }
    
    console.log('✓ All charts initialized successfully!');
}

// Initialize charts when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCharts);
} else {
    initCharts();
}

// Also try initialization after a short delay to ensure Chart.js is loaded
setTimeout(initCharts, 500);
