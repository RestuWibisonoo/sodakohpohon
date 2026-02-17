<?php
// campaign.php - Halaman Daftar Campaign Publik
session_start();

// Simulasi data campaign
 $campaigns = [
    [
        'id' => 1,
        'title' => 'Restorasi Mangrove Demak',
        'location' => 'Demak, Jawa Tengah',
        'tree_type' => 'Mangrove Rhizophora',
        'price_per_tree' => 10000,
        'target_trees' => 5000,
        'current_trees' => 1450,
        'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'description' => 'Program penanaman mangrove untuk membangun sabuk hijau yang melindungi garis pantai dari abrasi.',
        'donors' => 245,
        'days_left' => 30,
        'category' => 'Mangrove'
    ],
    [
        'id' => 2,
        'title' => 'Reboisasi Lereng Merapi',
        'location' => 'Magelang, Jawa Tengah',
        'tree_type' => 'Sengon & Mahoni',
        'price_per_tree' => 12000,
        'target_trees' => 4000,
        'current_trees' => 2300,
        'image' => 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'description' => 'Penanaman sengon dan mahoni di lereng Merapi untuk mencegah erosi dan longsor.',
        'donors' => 312,
        'days_left' => 15,
        'category' => 'Reboisasi'
    ],
    [
        'id' => 3,
        'title' => 'Penghijauan Hutan Lombok',
        'location' => 'Lombok, NTB',
        'tree_type' => 'Mahoni',
        'price_per_tree' => 15000,
        'target_trees' => 3000,
        'current_trees' => 780,
        'image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'description' => 'Penanaman pohon di kawasan hutan yang terbakar untuk memulihkan ekosistem.',
        'donors' => 156,
        'days_left' => 45,
        'category' => 'Reboisasi'
    ],
    [
        'id' => 4,
        'title' => 'Hutan Pangan Kalimantan',
        'location' => 'Kutai, Kaltim',
        'tree_type' => 'Durian & Petai',
        'price_per_tree' => 25000,
        'target_trees' => 2000,
        'current_trees' => 450,
        'image' => 'https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'description' => 'Program hutan pangan untuk memberdayakan masyarakat adat dan melestarikan hutan.',
        'donors' => 89,
        'days_left' => 60,
        'category' => 'Hutan Pangan'
    ],
    [
        'id' => 5,
        'title' => 'Konservasi Hutan Papua',
        'location' => 'Jayapura, Papua',
        'tree_type' => 'Merbau',
        'price_per_tree' => 30000,
        'target_trees' => 1500,
        'current_trees' => 320,
        'image' => 'https://images.unsplash.com/photo-1425913397330-cf8af2ff40a1?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'description' => 'Konservasi hutan tropis Papua untuk melindungi keanekaragaman hayati.',
        'donors' => 67,
        'days_left' => 90,
        'category' => 'Konservasi'
    ],
    [
        'id' => 6,
        'title' => 'Mangrove Pesisir Jakarta',
        'location' => 'Jakarta Utara',
        'tree_type' => 'Mangrove',
        'price_per_tree' => 10000,
        'target_trees' => 3500,
        'current_trees' => 1250,
        'image' => 'https://images.unsplash.com/photo-1621451498295-af1ea68616ee?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'description' => 'Penanaman mangrove di kawasan pesisir Jakarta Utara untuk perlindungan banjir rob.',
        'donors' => 198,
        'days_left' => 25,
        'category' => 'Mangrove'
    ],
    [
        'id' => 7,
        'title' => 'Hutan Kota Surabaya',
        'location' => 'Surabaya, Jawa Timur',
        'tree_type' => 'Trembesi',
        'price_per_tree' => 8500,
        'target_trees' => 2500,
        'current_trees' => 890,
        'image' => 'https://images.unsplash.com/photo-1502082553048-f009c37129b9?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'description' => 'Penghijauan kawasan perkotaan Surabaya untuk mengurangi polusi udara.',
        'donors' => 145,
        'days_left' => 40,
        'category' => 'Hutan Kota'
    ],
    [
        'id' => 8,
        'title' => 'Restorasi Danau Toba',
        'location' => 'Samosir, Sumatera Utara',
        'tree_type' => 'Pinus',
        'price_per_tree' => 18000,
        'target_trees' => 4000,
        'current_trees' => 1100,
        'image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'description' => 'Restorasi kawasan tangkapan air Danau Toba dengan penanaman pohon pinus.',
        'donors' => 178,
        'days_left' => 55,
        'category' => 'Konservasi'
    ],
    [
        'id' => 9,
        'title' => 'Green School Bali',
        'location' => 'Badung, Bali',
        'tree_type' => 'Bambu',
        'price_per_tree' => 15000,
        'target_trees' => 2000,
        'current_trees' => 650,
        'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'description' => 'Program penanaman bambu untuk mendukung pendidikan lingkungan di Bali.',
        'donors' => 92,
        'days_left' => 35,
        'category' => 'Hutan Kota'
    ]
];

// Filter by category
 $category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
 $sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'popular';

if ($category_filter != 'all') {
    $campaigns = array_filter($campaigns, function($c) use ($category_filter) {
        return $c['category'] == $category_filter;
    });
}

// Sort campaigns
switch ($sort_by) {
    case 'newest':
        // Already in order
        break;
    case 'deadline':
        usort($campaigns, function($a, $b) {
            return $a['days_left'] - $b['days_left'];
        });
        break;
    case 'progress':
        usort($campaigns, function($a, $b) {
            $prog_a = ($a['current_trees'] / $a['target_trees']) * 100;
            $prog_b = ($b['current_trees'] / $b['target_trees']) * 100;
            return $prog_b - $prog_a;
        });
        break;
    default: // popular
        usort($campaigns, function($a, $b) {
            return $b['donors'] - $a['donors'];
        });
        break;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Campaign - Sodakoh Pohon</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
        
        .campaign-card {
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 35px -8px rgba(0,0,0,0.05), 0 10px 10px -5px rgba(0,0,0,0.02);
        }
        
        .campaign-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 45px -12px rgba(5, 150, 105, 0.15), 0 15px 20px -8px rgba(0,0,0,0.05);
        }
        
        .campaign-image {
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        
        .campaign-card:hover .campaign-image {
            transform: scale(1.08);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .badge-new {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        }
        
        .badge-urgent {
            background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
        }
        
        .badge-popular {
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .filter-btn.active {
            background-color: #059669;
            color: white;
        }
    </style>
</head>
<body class="antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-effect border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
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

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-600 hover:text-primary-600 font-medium transition px-1">Beranda</a>
                    <a href="campaign.php" class="text-primary-700 font-semibold border-b-2 border-primary-600 pb-1 px-1">Campaign</a>
                    <a href="laporan.php" class="text-gray-600 hover:text-primary-600 font-medium transition px-1">Laporan</a>
                </div>

                <!-- Action Buttons -->
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
    <section class="pt-32 pb-12 bg-gradient-to-b from-primary-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto" data-aos="fade-up">
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">
                    Semua Campaign Penanaman
                </h1>
                <p class="text-xl text-gray-600 mb-8">
                    Pilih program penanaman pohon sesuai dengan passion dan lokasi yang kamu inginkan
                </p>
                
                <!-- Stats -->
                <div class="flex justify-center gap-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-700"><?php echo count($campaigns); ?></div>
                        <div class="text-sm text-gray-500">Campaign Aktif</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-700"><?php echo number_format(array_sum(array_column($campaigns, 'current_trees'))); ?></div>
                        <div class="text-sm text-gray-500">Pohon Terkumpul</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-700"><?php echo number_format(array_sum(array_column($campaigns, 'donors'))); ?></div>
                        <div class="text-sm text-gray-500">Total Donatur</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter & Campaign Section -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8" data-aos="fade-up">
                <div class="flex items-center gap-3 overflow-x-auto pb-2">
                    <a href="?category=all&sort=<?php echo $sort_by; ?>" 
                       class="filter-btn px-5 py-2 rounded-full text-sm font-semibold transition whitespace-nowrap <?php echo $category_filter == 'all' ? 'active bg-primary-600 text-white' : 'bg-white text-gray-700 hover:bg-primary-50 hover:text-primary-700 border border-gray-200'; ?>">
                        Semua
                    </a>
                    <a href="?category=Mangrove&sort=<?php echo $sort_by; ?>" 
                       class="filter-btn px-5 py-2 rounded-full text-sm font-medium transition whitespace-nowrap <?php echo $category_filter == 'Mangrove' ? 'active bg-primary-600 text-white' : 'bg-white text-gray-700 hover:bg-primary-50 hover:text-primary-700 border border-gray-200'; ?>">
                        Mangrove
                    </a>
                    <a href="?category=Reboisasi&sort=<?php echo $sort_by; ?>" 
                       class="filter-btn px-5 py-2 rounded-full text-sm font-medium transition whitespace-nowrap <?php echo $category_filter == 'Reboisasi' ? 'active bg-primary-600 text-white' : 'bg-white text-gray-700 hover:bg-primary-50 hover:text-primary-700 border border-gray-200'; ?>">
                        Reboisasi
                    </a>
                    <a href="?category=Hutan Kota&sort=<?php echo $sort_by; ?>" 
                       class="filter-btn px-5 py-2 rounded-full text-sm font-medium transition whitespace-nowrap <?php echo $category_filter == 'Hutan Kota' ? 'active bg-primary-600 text-white' : 'bg-white text-gray-700 hover:bg-primary-50 hover:text-primary-700 border border-gray-200'; ?>">
                        Hutan Kota
                    </a>
                    <a href="?category=Hutan Pangan&sort=<?php echo $sort_by; ?>" 
                       class="filter-btn px-5 py-2 rounded-full text-sm font-medium transition whitespace-nowrap <?php echo $category_filter == 'Hutan Pangan' ? 'active bg-primary-600 text-white' : 'bg-white text-gray-700 hover:bg-primary-50 hover:text-primary-700 border border-gray-200'; ?>">
                        Hutan Pangan
                    </a>
                    <a href="?category=Konservasi&sort=<?php echo $sort_by; ?>" 
                       class="filter-btn px-5 py-2 rounded-full text-sm font-medium transition whitespace-nowrap <?php echo $category_filter == 'Konservasi' ? 'active bg-primary-600 text-white' : 'bg-white text-gray-700 hover:bg-primary-50 hover:text-primary-700 border border-gray-200'; ?>">
                        Konservasi
                    </a>
                </div>
                
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Urutkan:</span>
                    <select id="sortSelect" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-600">
                        <option value="popular" <?php echo $sort_by == 'popular' ? 'selected' : ''; ?>>Paling Populer</option>
                        <option value="newest" <?php echo $sort_by == 'newest' ? 'selected' : ''; ?>>Terbaru</option>
                        <option value="deadline" <?php echo $sort_by == 'deadline' ? 'selected' : ''; ?>>Deadline Terdekat</option>
                        <option value="progress" <?php echo $sort_by == 'progress' ? 'selected' : ''; ?>>Progress Tertinggi</option>
                    </select>
                </div>
            </div>

            <!-- Campaign Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($campaigns as $index => $campaign): 
                    $progress = ($campaign['current_trees'] / $campaign['target_trees']) * 100;
                    $progress = round($progress, 1);
                ?>
                <div class="campaign-card group" data-aos="fade-up" data-aos-delay="<?php echo ($index % 3) * 100; ?>">
                    <!-- Image Container -->
                    <div class="relative h-56 overflow-hidden">
                        <img src="<?php echo $campaign['image']; ?>" 
                             alt="<?php echo $campaign['title']; ?>"
                             class="campaign-image w-full h-full object-cover">
                        
                        <!-- Overlay Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        
                        <!-- Badges -->
                        <div class="absolute top-4 left-4 flex gap-2">
                            <?php if ($index < 2): ?>
                            <span class="badge-new px-3 py-1 rounded-full text-white text-xs font-bold">
                                <i class="fas fa-star mr-1"></i>BARU
                            </span>
                            <?php endif; ?>
                            <?php if ($campaign['days_left'] <= 20): ?>
                            <span class="badge-urgent px-3 py-1 rounded-full text-white text-xs font-bold">
                                <i class="fas fa-clock mr-1"></i>URGENT
                            </span>
                            <?php endif; ?>
                            <?php if ($campaign['donors'] > 200): ?>
                            <span class="badge-popular px-3 py-1 rounded-full text-white text-xs font-bold">
                                <i class="fas fa-fire mr-1"></i>POPULER
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Price Tag -->
                        <div class="absolute bottom-4 left-4">
                            <div class="bg-white/90 backdrop-blur-sm rounded-xl px-4 py-2">
                                <span class="text-xs text-gray-600 block">Mulai dari</span>
                                <span class="text-xl font-bold text-primary-700">Rp <?php echo number_format($campaign['price_per_tree']); ?></span>
                                <span class="text-xs text-gray-500">/pohon</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6">
                        <!-- Category -->
                        <div class="mb-2">
                            <span class="text-xs font-semibold text-primary-600 bg-primary-50 px-2 py-1 rounded-full">
                                <?php echo $campaign['category']; ?>
                            </span>
                        </div>
                        
                        <!-- Title & Location -->
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-primary-700 transition">
                                <?php echo $campaign['title']; ?>
                            </h3>
                            <div class="flex items-center text-gray-500 text-sm">
                                <i class="fas fa-map-marker-alt mr-2 text-primary-600"></i>
                                <?php echo $campaign['location']; ?>
                                <span class="mx-2">•</span>
                                <i class="fas fa-leaf mr-2 text-primary-600"></i>
                                <?php echo $campaign['tree_type']; ?>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            <?php echo $campaign['description']; ?>
                        </p>
                        
                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="font-semibold text-gray-900"><?php echo number_format($campaign['current_trees']); ?> pohon</span>
                                <span class="text-gray-500">Target <?php echo number_format($campaign['target_trees']); ?> pohon</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                            <div class="flex justify-between mt-2">
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-users mr-1"></i><?php echo $campaign['donors']; ?> donatur
                                </span>
                                <span class="text-xs font-semibold text-primary-700">
                                    <?php echo $progress; ?>% terkumpul
                                </span>
                            </div>
                        </div>
                        
                        <!-- Footer Stats -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center text-sm">
                                <i class="far fa-clock mr-2 text-gray-400"></i>
                                <span class="text-gray-600"><?php echo $campaign['days_left']; ?> hari lagi</span>
                            </div>
                            <a href="campaign-detail.php?id=<?php echo $campaign['id']; ?>" 
                               class="inline-flex items-center text-primary-700 font-semibold hover:text-primary-800 transition group">
                                Lihat Detail
                                <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                <div class="flex items-center space-x-2">
                    <button class="w-10 h-10 flex items-center justify-center border border-gray-200 rounded-lg text-gray-500 hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="w-10 h-10 flex items-center justify-center bg-primary-600 text-white rounded-lg">1</button>
                    <button class="w-10 h-10 flex items-center justify-center border border-gray-200 rounded-lg text-gray-700 hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">2</button>
                    <button class="w-10 h-10 flex items-center justify-center border border-gray-200 rounded-lg text-gray-700 hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">3</button>
                    <button class="w-10 h-10 flex items-center justify-center border border-gray-200 rounded-lg text-gray-500 hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">
                        <i class="fas fa-chevron-right"></i>
                    </button>
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
                <p class="text-gray-400 text-sm">
                    Sedekah dalam bentuk pohon untuk masa depan bumi yang lebih hijau.
                </p>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
                    <p>&copy; <?php echo date('Y'); ?> Sodakoh Pohon. All rights reserved.</p>
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
        
        // Sort functionality
        document.getElementById('sortSelect').addEventListener('change', function() {
            const sortValue = this.value;
            const currentCategory = '<?php echo $category_filter; ?>';
            window.location.href = '?category=' + currentCategory + '&sort=' + sortValue;
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