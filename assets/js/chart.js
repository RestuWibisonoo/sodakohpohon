// assets/js/chart.js
// Konfigurasi dan utilitas untuk Chart.js di Sodakoh Pohon

/**
 * Inisialisasi chart untuk halaman laporan publik
 */
function initReportCharts(monthlyData, campaignData) {
    // Chart Trend Donasi
    if (document.getElementById('donationTrendChart')) {
        const ctx = document.getElementById('donationTrendChart').getContext('2d');
        
        // Format data dari PHP
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const amounts = monthlyData.map(item => item.total_amount);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Donasi',
                    data: amounts,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#059669',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Chart Distribusi Campaign (Donut)
    if (document.getElementById('campaignDistributionChart')) {
        const ctx = document.getElementById('campaignDistributionChart').getContext('2d');
        
        const labels = campaignData.map(item => item.campaign_name);
        const values = campaignData.map(item => parseInt(item.total_trees));
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: [
                        '#10b981',
                        '#34d399',
                        '#6ee7b7',
                        '#a7f3d0',
                        '#d1fae5',
                        '#ecfdf5'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
    
    // Chart Perbandingan Campaign
    if (document.getElementById('campaignComparisonChart')) {
        const ctx = document.getElementById('campaignComparisonChart').getContext('2d');
        
        const labels = campaignData.map(item => {
            // Shorten long names
            let name = item.campaign_name;
            if (name.length > 20) {
                name = name.substring(0, 20) + '...';
            }
            return name;
        });
        const collected = campaignData.map(item => parseInt(item.total_trees));
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pohon Terkumpul',
                    data: collected,
                    backgroundColor: '#10b981',
                    borderRadius: 8
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
                        title: {
                            display: true,
                            text: 'Jumlah Pohon'
                        }
                    }
                }
            }
        });
    }
}

/**
 * Inisialisasi chart untuk dashboard admin
 */
function initAdminCharts(monthlyData) {
    // Chart Donasi Bulanan
    if (document.getElementById('adminDonationChart')) {
        const ctx = document.getElementById('adminDonationChart').getContext('2d');
        
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const amounts = monthlyData.map(item => item.total_amount);
        const donations = monthlyData.map(item => item.total_donations);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Nominal Donasi',
                        data: amounts,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        yAxisID: 'y',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Jumlah Donasi',
                        data: donations,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 2,
                        yAxisID: 'y1',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.dataset.label.includes('Nominal')) {
                                    label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                } else {
                                    label += context.parsed.y + ' donasi';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            callback: function(value) {
                                return value + ' donasi';
                            }
                        }
                    }
                }
            }
        });
    }
}

/**
 * Format angka ke format Rupiah
 */
function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

/**
 * Format tanggal ke format Indonesia
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Export chart sebagai gambar
 */
function exportChartAsImage(chartId, filename) {
    const canvas = document.getElementById(chartId);
    if (canvas) {
        const link = document.createElement('a');
        link.download = filename || 'chart.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }
}

// Export functions untuk digunakan di halaman lain
window.SodakohCharts = {
    initReportCharts,
    initAdminCharts,
    formatRupiah,
    formatDate,
    exportChartAsImage
};