<?php
// laporan.php - Halaman Transparansi dan Laporan Publik
session_start();

// Simulasi data statistik (nanti dari database)
$total_donations = 187650000; // Total donasi dalam rupiah
$total_trees_collected = 15234;
$total_trees_planted = 8750;
$total_donors = 3241;
$total_campaigns = 12;
$total_locations = 23;

// Data donasi per bulan (simulasi)
$monthly_donations = [
    'Jan' => 12500000,
    'Feb' => 15200000,
    'Mar' => 18400000,
    'Apr' => 22300000,
    'May' => 19800000,
    'Jun' => 25600000,
    'Jul' => 27800000,
    'Aug' => 31200000,
    'Sep' => 29500000,
    'Oct' => 32400000,
    'Nov' => 35600000,
    'Dec' => 38900000
];

// Data pohon per campaign
$campaign_trees = [
    ['name' => 'Restorasi Mangrove Demak', 'collected' => 1450, 'planted' => 890, 'target' => 5000],
    ['name' => 'Reboisasi Lereng Merapi', 'collected' => 2300, 'planted' => 1650, 'target' => 4000],
    ['name' => 'Penghijauan Hutan Lombok', 'collected' => 780, 'planted' => 450, 'target' => 3000],
    ['name' => 'Hutan Pangan Kalimantan', 'collected' => 450, 'planted' => 120, 'target' => 2000],
    ['name' => 'Mangrove Pesisir Jakarta', 'collected' => 1250, 'planted' => 840, 'target' => 3500],
    ['name' => 'Konservasi Hutan Papua', 'collected' => 320, 'planted' => 100, 'target' => 1500]
];

// Data dokumentasi penanaman
$planting_documentations = [
    [
        'date' => '2026-02-15',
        'campaign' => 'Restorasi Mangrove Demak',
        'location' => 'Demak, Jawa Tengah',
        'trees_planted' => 350,
        'volunteers' => 45,
        'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'description' => 'Penanaman mangrove tahap ke-3 bersama Kelompok Tani Hutan'
    ],
    [
        'date' => '2026-02-10',
        'campaign' => 'Reboisasi Lereng Merapi',
        'location' => 'Magelang, Jawa Tengah',
        'trees_planted' => 500,
        'volunteers' => 78,
        'image' => 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'description' => 'Penanaman sengon dan mahoni di lereng Merapi'
    ],
    [
        'date' => '2026-02-05',
        'campaign' => 'Mangrove Pesisir Jakarta',
        'location' => 'Jakarta Utara',
        'trees_planted' => 280,
        'volunteers' => 52,
        'image' => 'https://images.unsplash.com/photo-1621451498295-af1ea68616ee?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'description' => 'Penanaman mangrove di kawasan Muara Angke'
    ],
    [
        'date' => '2026-01-28',
        'campaign' => 'Penghijauan Hutan Lombok',
        'location' => 'Lombok, NTB',
        'trees_planted' => 450,
        'volunteers' => 63,
        'image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'description' => 'Penanaman 450 pohon di kawasan hutan yang terbakar'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transparansi - Sodakoh Pohon</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        },
                        earth: {
                            50: '#faf7f2',
                            100: '#f0e9df',
                            200: '#e2d4c2',
                            300: '#d4bfa5',
                            400: '#c6a988',
                            500: '#b8946b',
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #faf7f2;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 35px -8px rgba(5, 150, 105, 0.15);
        }
        
        .progress-bar {
            height: 8px;
            border-radius: 100px;
            background-color: #e5e7eb;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            border-radius: 100px;
            transition: width 1s ease;
        }
        
        .tab-active {
            border-bottom: 3px solid #059669;
            color: #059669;
            font-weight: 600;
        }
        
        .documentation-card {
            transition: all 0.3s ease;
        }
        
        .documentation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 35px -8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-effect border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="absolute inset-0 bg-primary-600 rounded-xl blur-sm opacity-60"></div>
                        <div class="relative bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl p-2.5">
                            <i class="fas fa-tree text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-extrabold">
                            <span class="text-primary-700">Sodakoh</span>
                            <span class="text-earth-700">Pohon</span>
                        </span>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-600 hover:text-primary-600 font-medium transition px-1">Beranda</a>
                    <a href="index.php#campaigns" class="text-gray-600 hover:text-primary-600 font-medium transition px-1">Campaign</a>
                    <a href="laporan.php" class="text-primary-700 font-semibold border-b-2 border-primary-600 pb-1 px-1">Laporan</a>
                    <a href="#about" class="text-gray-600 hover:text-primary-600 font-medium transition px-1">Tentang</a>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="cart.php" class="relative p-2 text-gray-600 hover:text-primary-600 transition">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="absolute -top-1 -right-1 bg-primary-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">0</span>
                    </a>
                    <a href="index.php#campaigns" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                        <i class="fas fa-hand-holding-heart mr-2"></i>Donasi
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-16 bg-gradient-to-b from-primary-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto" data-aos="fade-up">
                <div class="inline-flex items-center bg-primary-100 rounded-full px-4 py-2 mb-6">
                    <i class="fas fa-chart-line text-primary-700 mr-2"></i>
                    <span class="text-sm font-semibold text-primary-800">Transparansi Publik</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6">
                    Laporan <span class="gradient-text">Transparansi</span> Sodakoh Pohon
                </h1>
                <p class="text-xl text-gray-600 leading-relaxed">
                    Kami percaya bahwa kepercayaan dibangun di atas transparansi. 
                    Setiap donasi, setiap pohon, dan setiap penanaman dapat dilacak secara real-time.
                </p>
            </div>
        </div>
    </section>

    <!-- Stats Overview -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 md:gap-6">
                <div class="stat-card bg-white rounded-2xl p-6 shadow-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-tree text-primary-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Total Pohon Terkumpul</p>
                    <p class="text-2xl md:text-3xl font-extrabold text-gray-900"><?php echo number_format($total_trees_collected); ?></p>
                    <p class="text-xs text-gray-400 mt-2">sepanjang masa</p>
                </div>
                
                <div class="stat-card bg-white rounded-2xl p-6 shadow-card" data-aos="fade-up" data-aos-delay="150">
                    <div class="w-12 h-12 bg-earth-100 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-seedling text-earth-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Pohon Tertanam</p>
                    <p class="text-2xl md:text-3xl font-extrabold text-gray-900"><?php echo number_format($total_trees_planted); ?></p>
                    <p class="text-xs text-gray-400 mt-2">realisasi penanaman</p>
                </div>
                
                <div class="stat-card bg-white rounded-2xl p-6 shadow-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Total Donatur</p>
                    <p class="text-2xl md:text-3xl font-extrabold text-gray-900"><?php echo number_format($total_donors); ?></p>
                    <p class="text-xs text-gray-400 mt-2">orang telah berdonasi</p>
                </div>
                
                <div class="stat-card bg-white rounded-2xl p-6 shadow-card" data-aos="fade-up" data-aos-delay="250">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-map-marker-alt text-purple-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Lokasi Tanam</p>
                    <p class="text-2xl md:text-3xl font-extrabold text-gray-900"><?php echo $total_locations; ?></p>
                    <p class="text-xs text-gray-400 mt-2">tersebar di Indonesia</p>
                </div>
                
                <div class="stat-card bg-white rounded-2xl p-6 shadow-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-hand-holding-heart text-yellow-600 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Total Donasi</p>
                    <p class="text-2xl md:text-3xl font-extrabold text-gray-900">Rp <?php echo number_format($total_donations / 1000000, 1); ?>M</p>
                    <p class="text-xs text-gray-400 mt-2">terkumpul</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Charts Section -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-8" data-aos="fade-up">
                <div class="flex space-x-8 overflow-x-auto">
                    <button onclick="showTab('overview')" class="tab-btn active px-1 py-4 text-sm font-medium border-b-2 border-primary-600 text-primary-700 whitespace-nowrap">
                        <i class="fas fa-chart-pie mr-2"></i>Overview
                    </button>
                    <button onclick="showTab('donations')" class="tab-btn px-1 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">
                        <i class="fas fa-chart-line mr-2"></i>Trend Donasi
                    </button>
                    <button onclick="showTab('campaigns')" class="tab-btn px-1 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">
                        <i class="fas fa-chart-bar mr-2"></i>Per Campaign
                    </button>
                    <button onclick="showTab('impact')" class="tab-btn px-1 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">
                        <i class="fas fa-leaf mr-2"></i>Dampak Lingkungan
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div id="overview-tab" class="tab-content block">
                <div class="grid lg:grid-cols-2 gap-8">
                    <!-- Donut Chart - Distribusi Campaign -->
                    <div class="bg-white rounded-2xl shadow-card p-6" data-aos="fade-right">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Distribusi Pohon per Campaign</h3>
                        <div class="h-80 relative">
                            <canvas id="campaignDistributionChart"></canvas>
                        </div>
                        <div class="mt-4 text-sm text-gray-500 text-center">
                            Total <?php echo number_format($total_trees_collected); ?> pohon dari 6 campaign aktif
                        </div>
                    </div>
                    
                    <!-- Progress Overview -->
                    <div class="bg-white rounded-2xl shadow-card p-6" data-aos="fade-left">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Progress Realisasi Penanaman</h3>
                        <div class="space-y-6">
                            <?php foreach (array_slice($campaign_trees, 0, 4) as $campaign): 
                                $progress = ($campaign['planted'] / $campaign['collected']) * 100;
                                if ($campaign['collected'] == 0) $progress = 0;
                            ?>
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-900"><?php echo $campaign['name']; ?></span>
                                    <span class="text-sm text-gray-600">
                                        <?php echo number_format($campaign['planted']); ?>/<?php echo number_format($campaign['collected']); ?> pohon
                                    </span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-xs text-gray-500">Tertanam</span>
                                    <span class="text-xs font-semibold text-primary-700"><?php echo round($progress); ?>% terealisasi</span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Rata-rata realisasi</span>
                                <span class="font-bold text-primary-700">57.8%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="donations-tab" class="tab-content hidden">
                <div class="bg-white rounded-2xl shadow-card p-6" data-aos="fade-up">
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Trend Donasi 12 Bulan Terakhir</h3>
                        <div class="flex items-center space-x-2 mt-2 md:mt-0">
                            <span class="text-sm text-gray-500">Total: </span>
                            <span class="font-bold text-primary-700">Rp <?php echo number_format($total_donations); ?></span>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="donationTrendChart"></canvas>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 pt-6 border-t border-gray-200">
                        <div class="text-center">
                            <p class="text-sm text-gray-500 mb-1">Donasi Tertinggi</p>
                            <p class="font-bold text-gray-900">Rp 38.9 Jt</p>
                            <p class="text-xs text-gray-400">Desember 2025</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500 mb-1">Rata-rata/Bulan</p>
                            <p class="font-bold text-gray-900">Rp 24.3 Jt</p>
                            <p class="text-xs text-gray-400">sepanjang tahun</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500 mb-1">Pertumbuhan YoY</p>
                            <p class="font-bold text-green-600">+45.2%</p>
                            <p class="text-xs text-gray-400">dibanding 2025</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500 mb-1">Rata-rata Donasi</p>
                            <p class="font-bold text-gray-900">Rp 57.900</p>
                            <p class="text-xs text-gray-400">per donatur</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="campaigns-tab" class="tab-content hidden">
                <div class="bg-white rounded-2xl shadow-card p-6" data-aos="fade-up">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Perbandingan Pohon per Campaign</h3>
                    <div class="h-80">
                        <canvas id="campaignComparisonChart"></canvas>
                    </div>
                    <div class="mt-8 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terkumpul</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tertanam</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($campaign_trees as $campaign): 
                                    $collected_percent = ($campaign['collected'] / $campaign['target']) * 100;
                                ?>
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo $campaign['name']; ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-600"><?php echo number_format($campaign['target']); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-600"><?php echo number_format($campaign['collected']); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-600"><?php echo number_format($campaign['planted']); ?></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <span class="text-sm font-semibold text-primary-700 mr-2"><?php echo round($collected_percent); ?>%</span>
                                            <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-primary-600 h-1.5 rounded-full" style="width: <?php echo $collected_percent; ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="impact-tab" class="tab-content hidden">
                <div class="grid lg:grid-cols-2 gap-8">
                    <div class="bg-white rounded-2xl shadow-card p-6" data-aos="fade-right">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Dampak Lingkungan</h3>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between p-4 bg-primary-50 rounded-xl">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-primary-200 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-cloud-sun text-primary-700 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Penyerapan CO₂</p>
                                        <p class="text-2xl font-bold text-gray-900">218.7 ton</p>
                                    </div>
                                </div>
                                <span class="text-xs text-primary-700 bg-primary-100 px-3 py-1 rounded-full">+45% dari target</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-water text-blue-700 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Air Terserap</p>
                                        <p class="text-2xl font-bold text-gray-900">1.2M liter</p>
                                    </div>
                                </div>
                                <span class="text-xs text-blue-700 bg-blue-100 px-3 py-1 rounded-full">per tahun</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-earth-50 rounded-xl">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-earth-200 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-paw text-earth-700 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Habitat Terlindungi</p>
                                        <p class="text-2xl font-bold text-gray-900">156 ha</p>
                                    </div>
                                </div>
                                <span class="text-xs text-earth-700 bg-earth-100 px-3 py-1 rounded-full">luas area</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-xl">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-yellow-200 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-users text-yellow-700 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Masyarakat Terlibat</p>
                                        <p class="text-2xl font-bold text-gray-900">1,245 orang</p>
                                    </div>
                                </div>
                                <span class="text-xs text-yellow-700 bg-yellow-100 px-3 py-1 rounded-full">petani & relawan</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-card p-6" data-aos="fade-left">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">SDGs Contribution</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white text-sm mr-3">13</span>
                                    <span class="text-gray-700">Penanganan Perubahan Iklim</span>
                                </div>
                                <span class="font-bold text-gray-900">218.7t CO₂</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm mr-3">14</span>
                                    <span class="text-gray-700">Ekosistem Laut</span>
                                </div>
                                <span class="font-bold text-gray-900">3.2km²</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="w-8 h-8 bg-green-800 rounded-full flex items-center justify-center text-white text-sm mr-3">15</span>
                                    <span class="text-gray-700">Ekosistem Daratan</span>
                                </div>
                                <span class="font-bold text-gray-900">12.4km²</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center text-white text-sm mr-3">11</span>
                                    <span class="text-gray-700">Kota Berkelanjutan</span>
                                </div>
                                <span class="font-bold text-gray-900">5 program</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dokumentasi Penanaman -->
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Dokumentasi Penanaman</h2>
                <p class="text-lg text-gray-600">
                    Bukti nyata penanaman pohon dari donasi Anda. Kami selalu update dokumentasi setiap kali melakukan penanaman.
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($planting_documentations as $doc): ?>
                <div class="documentation-card bg-white rounded-2xl shadow-card overflow-hidden group" data-aos="fade-up">
                    <div class="relative h-48 overflow-hidden">
                        <img src="<?php echo $doc['image']; ?>" 
                             alt="<?php echo $doc['campaign']; ?>"
                             class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm rounded-lg px-3 py-1">
                            <span class="text-xs font-bold text-primary-700"><?php echo date('d M Y', strtotime($doc['date'])); ?></span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-1 line-clamp-1"><?php echo $doc['campaign']; ?></h3>
                        <p class="text-xs text-gray-500 mb-2">
                            <i class="fas fa-map-marker-alt mr-1 text-primary-600"></i>
                            <?php echo $doc['location']; ?>
                        </p>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                            <?php echo $doc['description']; ?>
                        </p>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-tree mr-1 text-primary-600"></i>
                                <?php echo number_format($doc['trees_planted']); ?> pohon
                            </span>
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-users mr-1 text-primary-600"></i>
                                <?php echo $doc['volunteers']; ?> relawan
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-10" data-aos="fade-up">
                <button class="inline-flex items-center px-6 py-3 border-2 border-primary-600 text-primary-700 font-semibold rounded-xl hover:bg-primary-50 transition">
                    <i class="fas fa-images mr-2"></i>
                    Lihat Seluruh Dokumentasi
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Laporan Keuangan -->
    <section class="py-12 bg-gray-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-card p-8" data-aos="fade-up">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Laporan Keuangan</h2>
                        <p class="text-gray-600">Transparansi penggunaan dana donasi</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <button class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition text-sm">
                            <i class="fas fa-download mr-2"></i>
                            Unduh Laporan (PDF)
                        </button>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gray-50 rounded-xl p-5">
                        <p class="text-sm text-gray-500 mb-1">Total Dana Terkumpul</p>
                        <p class="text-2xl font-bold text-gray-900">Rp 187.65 Juta</p>
                        <p class="text-xs text-gray-400 mt-2">Per 28 Feb 2026</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-5">
                        <p class="text-sm text-gray-500 mb-1">Dana Tersalurkan</p>
                        <p class="text-2xl font-bold text-gray-900">Rp 142.32 Juta</p>
                        <p class="text-xs text-green-600 mt-2">75.8% tersalurkan</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-5">
                        <p class="text-sm text-gray-500 mb-1">Sisa Dana Operasional</p>
                        <p class="text-2xl font-bold text-gray-900">Rp 45.33 Juta</p>
                        <p class="text-xs text-gray-400 mt-2">untuk penanaman berikutnya</p>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Rincian Penggunaan Dana</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Pembelian bibit pohon</span>
                            <span class="font-semibold text-gray-900">Rp 78.45 Juta</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Biaya penanaman & perawatan</span>
                            <span class="font-semibold text-gray-900">Rp 42.87 Juta</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Transportasi & logistik</span>
                            <span class="font-semibold text-gray-900">Rp 12.34 Juta</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Biaya operasional</span>
                            <span class="font-semibold text-gray-900">Rp 8.66 Juta</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="flex items-center justify-center space-x-3 mb-4">
                    <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl p-2.5">
                        <i class="fas fa-tree text-white text-xl"></i>
                    </div>
                    <span class="text-xl font-extrabold">
                        <span class="text-primary-500">Sodakoh</span>
                        <span class="text-white">Pohon</span>
                    </span>
                </div>
                <p class="text-gray-400 text-sm max-w-md mx-auto">
                    Transparansi adalah komitmen kami. Setiap rupiah donasi dapat dipertanggungjawabkan.
                </p>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
                    <p>&copy; <?php echo date('Y'); ?> Sodakoh Pohon. Laporan diperbaharui setiap bulan.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });

        // Tab functionality
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            // Update active state on buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active', 'border-primary-600', 'text-primary-700');
                btn.classList.add('text-gray-500');
            });
            
            event.target.classList.add('active', 'border-primary-600', 'text-primary-700');
        }

        // Initialize charts when page loads
        window.addEventListener('load', function() {
            // Campaign Distribution Chart (Donut)
            const ctx1 = document.getElementById('campaignDistributionChart').getContext('2d');
            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: ['Mangrove Demak', 'Lereng Merapi', 'Hutan Lombok', 'Hutan Pangan', 'Mangrove Jakarta', 'Hutan Papua'],
                    datasets: [{
                        data: [1450, 2300, 780, 450, 1250, 320],
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
                    }
                }
            });

            // Donation Trend Chart
            const ctx2 = document.getElementById('donationTrendChart').getContext('2d');
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Donasi 2025',
                        data: [12500000, 15200000, 18400000, 22300000, 19800000, 25600000, 27800000, 31200000, 29500000, 32400000, 35600000, 38900000],
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
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000000) + 'jt';
                                }
                            }
                        }
                    }
                }
            });

            // Campaign Comparison Chart
            const ctx3 = document.getElementById('campaignComparisonChart').getContext('2d');
            new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: ['Mangrove Demak', 'Lereng Merapi', 'Hutan Lombok', 'Hutan Pangan', 'Mangrove Jakarta', 'Hutan Papua'],
                    datasets: [
                        {
                            label: 'Terkumpul',
                            data: [1450, 2300, 780, 450, 1250, 320],
                            backgroundColor: '#10b981',
                            borderRadius: 8
                        },
                        {
                            label: 'Tertanam',
                            data: [890, 1650, 450, 120, 840, 100],
                            backgroundColor: '#f59e0b',
                            borderRadius: 8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
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
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('bg-white/95', 'backdrop-blur-md', 'shadow-lg');
                nav.classList.remove('glass-effect');
            } else {
                nav.classList.remove('bg-white/95', 'backdrop-blur-md', 'shadow-lg');
                nav.classList.add('glass-effect');
            }
        });
    </script>
</body>
</html>