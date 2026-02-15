<?php
// admin/index.php
require_once '../controllers/adminController.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sodakoh Pohon</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
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
            background-color: #f3f4f6;
        }
        
        .sidebar-link {
            transition: all 0.2s ease;
        }
        
        .sidebar-link:hover {
            background-color: rgba(5, 150, 105, 0.1);
            color: #059669;
        }
        
        .sidebar-link.active {
            background-color: #059669;
            color: white;
        }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
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
        }
    </style>
</head>
<body>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-72 bg-white shadow-xl flex flex-col fixed h-full overflow-y-auto">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl p-2.5">
                        <i class="fas fa-tree text-white text-2xl"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-extrabold">
                            <span class="text-primary-700">Sodakoh</span>
                            <span class="text-gray-800">Pohon</span>
                        </span>
                        <span class="text-xs text-gray-500">Administrator Panel</span>
                    </div>
                </div>
            </div>
            
            <!-- Admin Profile -->
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-white">
                <div class="flex items-center">
                    <div class="relative">
                        <img src="https://ui-avatars.com/api/?name=Admin+Sodakoh&background=059669&color=fff&size=64" 
                             alt="Admin" 
                             class="w-12 h-12 rounded-xl border-2 border-white shadow-lg">
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                    </div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-900">Admin Sodakoh</p>
                        <p class="text-xs text-gray-500">admin@sodakohpohon.id</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 p-4">
                <div class="mb-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2">Main Menu</p>
                    <ul class="space-y-1">
                        <li>
                            <a href="index.php" class="sidebar-link active flex items-center px-4 py-3 rounded-xl text-white bg-primary-600">
                                <i class="fas fa-dashboard w-6"></i>
                                <span class="ml-3 font-medium">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="campaign.php" class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50">
                                <i class="fas fa-tree w-6 text-gray-500"></i>
                                <span class="ml-3 font-medium">Campaign</span>
                            </a>
                        </li>
                        <li>
                            <a href="donations.php" class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50">
                                <i class="fas fa-hand-holding-heart w-6 text-gray-500"></i>
                                <span class="ml-3 font-medium">Donasi</span>
                            </a>
                        </li>
                        <li>
                            <a href="planted.php" class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50">
                                <i class="fas fa-seedling w-6 text-gray-500"></i>
                                <span class="ml-3 font-medium">Penanaman</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="mb-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2">Laporan</p>
                    <ul class="space-y-1">
                        <li>
                            <a href="#" class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50">
                                <i class="fas fa-chart-line w-6 text-gray-500"></i>
                                <span class="ml-3 font-medium">Statistik</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50">
                                <i class="fas fa-file-export w-6 text-gray-500"></i>
                                <span class="ml-3 font-medium">Ekspor Data</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="mb-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2">Pengaturan</p>
                    <ul class="space-y-1">
                        <li>
                            <a href="#" class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50">
                                <i class="fas fa-user-cog w-6 text-gray-500"></i>
                                <span class="ml-3 font-medium">Profil</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50">
                                <i class="fas fa-sign-out-alt w-6 text-gray-500"></i>
                                <span class="ml-3 font-medium">Keluar</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Version Info -->
            <div class="p-4 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    Sodakoh Pohon v1.0.0<br>
                    &copy; 2026 All rights reserved
                </p>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-72 overflow-y-auto">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="flex justify-between items-center px-8 py-4">
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-500 hover:text-primary-600 transition">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        
                        <!-- Date -->
                        <div class="flex items-center text-sm text-gray-600 bg-gray-100 rounded-xl px-4 py-2">
                            <i class="fas fa-calendar-alt mr-2 text-primary-600"></i>
                            <?php echo date('d F Y'); ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="px-8 py-6">
                <!-- Welcome Banner -->
                <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-6 mb-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold mb-2">Selamat datang kembali, Admin! 👋</h2>
                            <p class="text-white/90">Berikut ringkasan aktivitas Sodakoh Pohon hari ini.</p>
                        </div>
                        <div class="hidden md:block">
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl px-6 py-3">
                                <p class="text-sm text-white/80">Donasi Hari Ini</p>
                                <p class="text-2xl font-bold">Rp <?php echo number_format($today_donations); ?></p>
                                <p class="text-xs text-white/70">dari <?php echo $today_donors; ?> donatur</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm hover:shadow-md">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-tree text-primary-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full">
                                +12.5%
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Total Pohon Terkumpul</p>
                        <p class="text-3xl font-extrabold text-gray-900"><?php echo number_format($total_trees_collected); ?></p>
                        <p class="text-xs text-gray-400 mt-2">sejak awal program</p>
                    </div>
                    
                    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm hover:shadow-md">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-earth-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-seedling text-earth-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-3 py-1 rounded-full">
                                <?php echo $pending_planting; ?> pending
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Pohon Tertanam</p>
                        <p class="text-3xl font-extrabold text-gray-900"><?php echo number_format($total_trees_planted); ?></p>
                        <p class="text-xs text-gray-400 mt-2">realisasi penanaman</p>
                    </div>
                    
                    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm hover:shadow-md">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                                +28
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Total Donatur</p>
                        <p class="text-3xl font-extrabold text-gray-900"><?php echo number_format($total_donors); ?></p>
                        <p class="text-xs text-gray-400 mt-2">orang telah berdonasi</p>
                    </div>
                    
                    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm hover:shadow-md">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-credit-card text-purple-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-3 py-1 rounded-full">
                                +15%
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Total Donasi</p>
                        <p class="text-3xl font-extrabold text-gray-900">Rp <?php echo number_format($total_donations_amount / 1000000, 1); ?>M</p>
                        <p class="text-xs text-gray-400 mt-2">terkumpul</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-plus-circle text-primary-600 mr-2"></i>
                            Aksi Cepat
                        </h3>
                        <div class="space-y-3">
                            <a href="campaign.php?action=create" 
                               class="flex items-center justify-between w-full p-3 bg-primary-50 hover:bg-primary-100 rounded-xl transition">
                                <span class="flex items-center">
                                    <i class="fas fa-plus text-primary-600 mr-3"></i>
                                    <span class="font-medium text-gray-700">Buat Campaign Baru</span>
                                </span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </a>
                            <a href="planted.php?action=update" 
                               class="flex items-center justify-between w-full p-3 bg-earth-50 hover:bg-earth-100 rounded-xl transition">
                                <span class="flex items-center">
                                    <i class="fas fa-seedling text-earth-600 mr-3"></i>
                                    <span class="font-medium text-gray-700">Update Penanaman</span>
                                </span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </a>
                            <a href="donations.php?action=export" 
                               class="flex items-center justify-between w-full p-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition">
                                <span class="flex items-center">
                                    <i class="fas fa-file-export text-blue-600 mr-3"></i>
                                    <span class="font-medium text-gray-700">Ekspor Laporan</span>
                                </span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Campaign Progress -->
                    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-900">Progress Campaign Aktif</h3>
                            <a href="campaign.php" class="text-sm text-primary-600 hover:text-primary-700 font-semibold">
                                Lihat Semua
                                <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        <div class="space-y-4">
                            <?php foreach ($campaigns as $campaign): 
                                $progress = ($campaign['collected'] / $campaign['target']) * 100;
                                $planted_percent = ($campaign['planted'] / $campaign['collected']) * 100;
                                if ($campaign['collected'] == 0) $planted_percent = 0;
                            ?>
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-medium text-gray-900"><?php echo $campaign['name']; ?></span>
                                    <span class="text-sm text-gray-600">
                                        <?php echo number_format($campaign['collected']); ?>/<?php echo number_format($campaign['target']); ?>
                                    </span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex-1">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs font-semibold text-primary-700"><?php echo round($progress); ?>%</span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-seedling mr-1 text-earth-600"></i>
                                        Tertanam: <?php echo number_format($campaign['planted']); ?> (<?php echo round($planted_percent); ?>%)
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1 text-orange-500"></i>
                                        Deadline: <?php echo date('d/m/Y', strtotime($campaign['deadline'])); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Donations & Plantings -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Donations -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-900 flex items-center">
                                <i class="fas fa-hand-holding-heart text-primary-600 mr-2"></i>
                                Donasi Terbaru
                            </h3>
                            <a href="donations.php" class="text-sm text-primary-600 hover:text-primary-700 font-semibold">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="space-y-4">
                            <?php foreach ($recent_donations as $donation): ?>
                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-primary-100 to-primary-200 rounded-full flex items-center justify-center mr-3">
                                        <span class="font-bold text-primary-700">
                                            <?php echo substr($donation['donor'], 0, 1); ?>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900"><?php echo $donation['donor']; ?></p>
                                        <p class="text-xs text-gray-500">
                                            <?php echo $donation['campaign']; ?> • <?php echo $donation['trees']; ?> pohon
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">Rp <?php echo number_format($donation['amount']); ?></p>
                                    <p class="text-xs text-gray-400"><?php echo date('H:i', strtotime($donation['date'])); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Recent Plantings -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-900 flex items-center">
                                <i class="fas fa-seedling text-earth-600 mr-2"></i>
                                Penanaman Terbaru
                            </h3>
                            <a href="planted.php" class="text-sm text-primary-600 hover:text-primary-700 font-semibold">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="space-y-4">
                            <?php foreach ($recent_plantings as $planting): ?>
                            <div class="flex items-start p-3 hover:bg-gray-50 rounded-xl transition">
                                <div class="w-10 h-10 bg-earth-100 rounded-xl flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-tree text-earth-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900"><?php echo $planting['campaign']; ?></p>
                                            <p class="text-xs text-gray-500 mb-1">
                                                <i class="fas fa-map-marker-alt mr-1 text-primary-600"></i>
                                                <?php echo $planting['location']; ?>
                                            </p>
                                        </div>
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                            <?php echo date('d/m', strtotime($planting['date'])); ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center text-sm mt-1">
                                        <span class="text-gray-700 mr-4">
                                            <i class="fas fa-tree mr-1 text-primary-600"></i>
                                            <?php echo number_format($planting['trees']); ?> pohon
                                        </span>
                                        <span class="text-gray-700">
                                            <i class="fas fa-users mr-1 text-primary-600"></i>
                                            <?php echo $planting['volunteers']; ?> relawan
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Initialize any charts if needed
        window.addEventListener('load', function() {
            // Add any chart initialization here
        });
    </script>
</body>
</html>