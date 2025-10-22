// Dashboard Charts
document.addEventListener('DOMContentLoaded', function() {
    // Get data from data attributes
    const dashboardData = document.getElementById('dashboard-data');
    if (!dashboardData) return;

    const stats = JSON.parse(dashboardData.dataset.stats);
    const programs = JSON.parse(dashboardData.dataset.programs);
    const yearLevels = JSON.parse(dashboardData.dataset.yearLevels);
    const violations = JSON.parse(dashboardData.dataset.violations);

    // Chart.js configuration for dark mode
    Chart.defaults.color = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
    Chart.defaults.borderColor = document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB';

    // Student & Employee Overview Chart
    const enrollmentCtx = document.getElementById('enrollmentChart')?.getContext('2d');
    if (enrollmentCtx) {
        new Chart(enrollmentCtx, {
            type: 'bar',
            data: {
                labels: ['Students', 'Employees'],
                datasets: [{
                    label: 'Count',
                    data: [stats.total_students, stats.total_employees],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)'
                    ],
                    borderColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)'
                    ],
                    borderWidth: 1
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
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Violation Status Chart
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

    // Program Distribution Chart
    const programCtx = document.getElementById('programChart')?.getContext('2d');
    if (programCtx) {
        new Chart(programCtx, {
            type: 'pie',
            data: {
                labels: programs.map(p => p.program),
                datasets: [{
                    data: programs.map(p => p.count),
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(251, 191, 36)',
                        'rgb(168, 85, 247)',
                        'rgb(239, 68, 68)',
                        'rgb(236, 72, 153)',
                        'rgb(14, 165, 233)',
                        'rgb(139, 92, 246)'
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

    // Year Level Distribution Chart
    const yearLevelCtx = document.getElementById('yearLevelChart')?.getContext('2d');
    if (yearLevelCtx) {
        new Chart(yearLevelCtx, {
            type: 'bar',
            data: {
                labels: yearLevels.map(y => y.year_level),
                datasets: [{
                    label: 'Students',
                    data: yearLevels.map(y => y.count),
                    backgroundColor: 'rgba(168, 85, 247, 0.8)',
                    borderColor: 'rgb(168, 85, 247)',
                    borderWidth: 1
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
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
