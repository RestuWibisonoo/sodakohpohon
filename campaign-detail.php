<?php
// campaign-detail.php
// Simulasi data campaign berdasarkan ID (nanti akan diambil dari database)
$campaign_id = isset($_GET['id']) ? $_GET['id'] : 1;

// Simulasi data campaign
$campaign = [
    'id' => 1,
    'title' => 'Restorasi Mangrove Demak',
    'location' => 'Demak, Jawa Tengah',
    'tree_type' => 'Mangrove Rhizophora',
    'price_per_tree' => 10000,
    'current_trees' => 1450,
    'target_trees' => 5000,
    'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
    'description' => 'Kawasan pesisir Demak mengalami abrasi yang cukup parah dalam beberapa tahun terakhir. Program penanaman mangrove ini bertujuan untuk membangun sabuk hijau yang melindungi garis pantai dari abrasi, sekaligus mengembalikan ekosistem mangrove yang menjadi habitat berbagai biota laut.',
    'long_description' => 'Mangrove memiliki peran vital dalam ekosistem pesisir. Akarnya yang kokoh mampu menahan abrasi dan tsunami, menjadi tempat pemijahan ikan dan udang, serta menyerap karbon lebih banyak dibanding hutan tropis. Program ini merupakan kolaborasi dengan Kelompok Tani Hutan Mangrove Demak dan Dinas Kelautan dan Perikanan setempat. Setiap donasi akan digunakan untuk pembelian bibit, penanaman, dan perawatan selama 3 bulan pertama.',
    'benefits' => [
        'Melindungi garis pantai dari abrasi',
        'Menciptakan habitat baru bagi biota laut',
        'Menyerap karbon hingga 4x lebih banyak',
        'Memberdayakan masyarakat lokal'
    ],
    'gallery' => [
        'https://images.unsplash.com/photo-1621451498295-af1ea68616ee?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1624535168245-0f9d5d773c2e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1627548941779-2c635951b9ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'
    ],
    'donors' => 245,
    'days_left' => 30,
    'partner' => 'Kelompok Tani Hutan Mangrove Demak',
    'map_url' => 'https://maps.google.com/?q=-6.8945,110.6364'
];

$progress = ($campaign['current_trees'] / $campaign['target_trees']) * 100;
$remaining_trees = $campaign['target_trees'] - $campaign['current_trees'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $campaign['title']; ?> - Sodakoh Pohon</title>
    
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
            height: 10px;
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
        
        .quantity-btn {
            transition: all 0.2s ease;
        }
        
        .quantity-btn:hover {
            background-color: #10b981;
            color: white;
            border-color: #10b981;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .gallery-item {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .gallery-item:hover {
            transform: scale(1.05);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        
        .active-gallery {
            border: 3px solid #10b981;
            transform: scale(1.05);
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
                    <a href="index.php#campaigns" class="text-gray-600 hover:text-primary-600 font-medium transition px-1">Campaign</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-primary-600 font-medium transition px-1">Cara Kerja</a>
                    <a href="laporan.php" class="text-gray-600 hover:text-primary-600 font-medium transition px-1">Laporan</a>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-4">
                    <a href="cart.php" class="relative p-2 text-gray-600 hover:text-primary-600 transition">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="absolute -top-1 -right-1 bg-primary-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">0</span>
                    </a>
                    <a href="#campaigns" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                        <i class="fas fa-hand-holding-heart mr-2"></i>Donasi
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-20">
        <!-- Hero Section Campaign -->
        <div class="relative h-[400px] lg:h-[500px] overflow-hidden">
            <img src="<?php echo $campaign['image']; ?>" 
                 alt="<?php echo $campaign['title']; ?>"
                 class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            
            <!-- Breadcrumb -->
            <div class="absolute top-24 left-0 right-0 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-white/80 text-sm flex items-center space-x-2">
                    <a href="index.php" class="hover:text-white transition">Beranda</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <a href="index.php#campaigns" class="hover:text-white transition">Campaign</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span class="text-white"><?php echo $campaign['title']; ?></span>
                </div>
            </div>
            
            <!-- Campaign Title -->
            <div class="absolute bottom-0 left-0 right-0 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                    <div data-aos="fade-right">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-3 py-1 bg-primary-600 text-white text-xs font-bold rounded-full">
                                <i class="fas fa-tree mr-1"></i><?php echo $campaign['tree_type']; ?>
                            </span>
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-xs font-bold rounded-full">
                                <i class="fas fa-map-marker-alt mr-1"></i><?php echo $campaign['location']; ?>
                            </span>
                        </div>
                        <h1 class="text-3xl lg:text-5xl font-bold text-white mb-3">
                            <?php echo $campaign['title']; ?>
                        </h1>
                        <div class="flex items-center text-white/80 text-sm">
                            <i class="fas fa-users mr-2"></i>
                            <?php echo number_format($campaign['donors']); ?> donatur
                            <span class="mx-3">•</span>
                            <i class="fas fa-clock mr-2"></i>
                            <?php echo $campaign['days_left']; ?> hari tersisa
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="flex gap-4" data-aos="fade-left">
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 text-white">
                            <div class="text-sm opacity-90">Terkumpul</div>
                            <div class="text-2xl font-bold"><?php echo number_format($campaign['current_trees']); ?></div>
                            <div class="text-xs opacity-75">pohon</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 text-white">
                            <div class="text-sm opacity-90">Target</div>
                            <div class="text-2xl font-bold"><?php echo number_format($campaign['target_trees']); ?></div>
                            <div class="text-xs opacity-75">pohon</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Left Column - Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Progress Card -->
                    <div class="bg-white rounded-2xl shadow-card p-6" data-aos="fade-up">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Perkembangan Penanaman</h2>
                        
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="font-semibold text-gray-900">Progress Terkumpul</span>
                                <span class="text-primary-700 font-bold"><?php echo round($progress, 1); ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                            <div class="flex justify-between mt-3 text-sm">
                                <span class="text-gray-600">
                                    <span class="font-bold text-gray-900"><?php echo number_format($campaign['current_trees']); ?></span> pohon terkumpul
                                </span>
                                <span class="text-gray-600">
                                    Butuh <span class="font-bold text-primary-700"><?php echo number_format($remaining_trees); ?></span> pohon lagi
                                </span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                            <div>
                                <div class="text-sm text-gray-500 mb-1">Harga per pohon</div>
                                <div class="text-2xl font-bold text-primary-700">
                                    Rp <?php echo number_format($campaign['price_per_tree']); ?>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 mb-1">Mitra Penanaman</div>
                                <div class="font-semibold text-gray-900"><?php echo $campaign['partner']; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="bg-white rounded-2xl shadow-card p-6" data-aos="fade-up">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Tentang Program</h2>
                        <div class="prose max-w-none">
                            <p class="text-gray-600 mb-4 text-lg leading-relaxed">
                                <?php echo $campaign['description']; ?>
                            </p>
                            <p class="text-gray-600 leading-relaxed">
                                <?php echo $campaign['long_description']; ?>
                            </p>
                        </div>
                        
                        <!-- Benefits -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h3 class="font-semibold text-gray-900 mb-4">Manfaat Program:</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <?php foreach ($campaign['benefits'] as $benefit): ?>
                                <div class="flex items-center text-gray-700">
                                    <div class="w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-check text-primary-600 text-xs"></i>
                                    </div>
                                    <span><?php echo $benefit; ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gallery -->
                    <div class="bg-white rounded-2xl shadow-card p-6" data-aos="fade-up">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-gray-900">Dokumentasi</h2>
                            <span class="text-sm text-primary-600 font-semibold"><?php echo count($campaign['gallery']); ?> foto</span>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-3">
                            <?php foreach ($campaign['gallery'] as $index => $image): ?>
                            <div class="gallery-item rounded-xl overflow-hidden aspect-square">
                                <img src="<?php echo $image; ?>" 
                                     alt="Dokumentasi <?php echo $index + 1; ?>"
                                     class="w-full h-full object-cover">
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <button class="text-primary-600 font-semibold hover:text-primary-700 transition">
                                <i class="fas fa-images mr-2"></i>
                                Lihat semua dokumentasi
                            </button>
                        </div>
                    </div>
                    
                    <!-- Location -->
                    <div class="bg-white rounded-2xl shadow-card p-6" data-aos="fade-up">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Lokasi Penanaman</h2>
                        <div class="aspect-video bg-gray-200 rounded-xl mb-4 overflow-hidden">
                            <iframe 
                                width="100%" 
                                height="100%" 
                                frameborder="0" 
                                style="border:0"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.827279!3d-6.17511!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5d2e764b12d%3A0x3d2ad6e1e0e9bcc8!2sJakarta!5e0!3m2!1sen!2sid!4v1234567890"
                                allowfullscreen>
                            </iframe>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-map-pin text-primary-600 mr-2"></i>
                                <?php echo $campaign['location']; ?>
                            </div>
                            <a href="<?php echo $campaign['map_url']; ?>" target="_blank" 
                               class="text-primary-600 hover:text-primary-700 font-semibold text-sm">
                                Buka Google Maps
                                <i class="fas fa-external-link-alt ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Donation Form -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24" data-aos="fade-left">
                        <div class="bg-white rounded-2xl shadow-card p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">
                                Pilih Jumlah Pohon
                            </h2>
                            
                            <!-- Tree Counter -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Berapa pohon yang ingin disedekahkan?
                                </label>
                                
                                <div class="flex items-center justify-between bg-gray-50 rounded-2xl p-4">
                                    <button id="decreaseBtn" 
                                            class="quantity-btn w-12 h-12 flex items-center justify-center border-2 border-gray-300 rounded-xl text-gray-600 hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">
                                        <i class="fas fa-minus text-xl"></i>
                                    </button>
                                    
                                    <div class="text-center">
                                        <span id="treeCount" class="text-4xl font-bold text-primary-700">5</span>
                                        <span class="text-gray-600 ml-2">pohon</span>
                                    </div>
                                    
                                    <button id="increaseBtn" 
                                            class="quantity-btn w-12 h-12 flex items-center justify-center border-2 border-gray-300 rounded-xl text-gray-600 hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">
                                        <i class="fas fa-plus text-xl"></i>
                                    </button>
                                </div>
                                
                                <!-- Quick Select -->
                                <div class="flex gap-2 mt-3">
                                    <button onclick="setQuantity(1)" class="flex-1 py-2 border border-gray-200 rounded-xl text-sm hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">1</button>
                                    <button onclick="setQuantity(3)" class="flex-1 py-2 border border-gray-200 rounded-xl text-sm hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">3</button>
                                    <button onclick="setQuantity(5)" class="flex-1 py-2 border border-primary-600 bg-primary-600 text-white rounded-xl text-sm">5</button>
                                    <button onclick="setQuantity(10)" class="flex-1 py-2 border border-gray-200 rounded-xl text-sm hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">10</button>
                                    <button onclick="setQuantity(20)" class="flex-1 py-2 border border-gray-200 rounded-xl text-sm hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">20</button>
                                </div>
                            </div>
                            
                            <!-- Total Price -->
                            <div class="bg-gradient-to-r from-primary-50 to-earth-50 rounded-2xl p-5 mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-700">Total donasi:</span>
                                    <span class="text-sm text-gray-500">(<?php echo number_format($campaign['price_per_tree']); ?> / pohon)</span>
                                </div>
                                <div class="flex items-end justify-between">
                                    <span class="text-3xl font-extrabold text-primary-700">
                                        Rp <span id="totalPrice"><?php echo number_format(5 * $campaign['price_per_tree']); ?></span>
                                    </span>
                                    <span class="text-gray-500 text-sm mb-1">sekali donasi</span>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                <button id="addToCartBtn" 
                                        onclick="addToCart(<?php echo $campaign['id']; ?>, document.getElementById('treeCount').innerText)"
                                        class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold py-4 px-6 rounded-2xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                                    <i class="fas fa-cart-plus mr-2"></i>
                                    Tambah ke Keranjang
                                </button>
                                
                                <button onclick="donateNow()"
                                        class="w-full bg-white border-2 border-primary-600 text-primary-700 font-bold py-4 px-6 rounded-2xl hover:bg-primary-50 transition">
                                    <i class="fas fa-hand-holding-heart mr-2"></i>
                                    Donasi Langsung
                                </button>
                            </div>
                            
                            <!-- Payment Methods -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <p class="text-sm text-gray-500 text-center mb-4">
                                    Metode pembayaran yang tersedia:
                                </p>
                                <div class="flex justify-center gap-4">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" class="h-8 opacity-50 grayscale hover:opacity-100 hover:grayscale-0 transition">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/9/94/Bank_Mandiri_logo.svg" alt="Mandiri" class="h-8 opacity-50 grayscale hover:opacity-100 hover:grayscale-0 transition">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/6/68/Bank_BRI_logo.svg" alt="BRI" class="h-8 opacity-50 grayscale hover:opacity-100 hover:grayscale-0 transition">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/8/83/BNI_logo.svg" alt="BNI" class="h-8 opacity-50 grayscale hover:opacity-100 hover:grayscale-0 transition">
                                </div>
                            </div>
                            
                            <!-- Trust Badge -->
                            <div class="mt-6 text-center">
                                <div class="inline-flex items-center text-xs text-gray-500">
                                    <i class="fas fa-shield-alt text-primary-600 mr-2"></i>
                                    Donasi aman dan terpercaya
                                </div>
                            </div>
                        </div>
                        
                        <!-- Similar Campaigns -->
                        <div class="bg-white rounded-2xl shadow-card p-6 mt-6">
                            <h3 class="font-bold text-gray-900 mb-4">Campaign Lainnya</h3>
                            <div class="space-y-3">
                                <a href="#" class="flex gap-3 hover:bg-gray-50 p-2 rounded-xl transition">
                                    <img src="https://images.unsplash.com/photo-1472214103451-9374bd1c798e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" 
                                         alt="Campaign" 
                                         class="w-16 h-16 object-cover rounded-lg">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 text-sm">Reboisasi Lereng Merapi</h4>
                                        <p class="text-xs text-gray-500">12.000/pohon</p>
                                        <p class="text-xs text-primary-600 mt-1">2300 pohon terkumpul</p>
                                    </div>
                                </a>
                                <a href="#" class="flex gap-3 hover:bg-gray-50 p-2 rounded-xl transition">
                                    <img src="https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" 
                                         alt="Campaign" 
                                         class="w-16 h-16 object-cover rounded-lg">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 text-sm">Penghijauan Hutan Lombok</h4>
                                        <p class="text-xs text-gray-500">15.000/pohon</p>
                                        <p class="text-xs text-primary-600 mt-1">780 pohon terkumpul</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
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
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Jelajahi</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="index.php" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="index.php#campaigns" class="hover:text-white transition">Campaign</a></li>
                        <li><a href="laporan.php" class="hover:text-white transition">Laporan</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li>hello@sodakohpohon.id</li>
                        <li>+62 21 1234 5678</li>
                        <li>Jakarta, Indonesia</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Ikuti Kami</h4>
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary-600 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary-600 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary-600 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; <?php echo date('Y'); ?> Sodakoh Pohon. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });

        const pricePerTree = <?php echo $campaign['price_per_tree']; ?>;
        const treeCountEl = document.getElementById('treeCount');
        const totalPriceEl = document.getElementById('totalPrice');
        
        function updateTotal() {
            const count = parseInt(treeCountEl.innerText);
            const total = count * pricePerTree;
            totalPriceEl.innerText = total.toLocaleString('id-ID');
        }
        
        function setQuantity(count) {
            treeCountEl.innerText = count;
            updateTotal();
            
            // Update active state on quick select buttons
            document.querySelectorAll('.quick-select-btn').forEach(btn => {
                btn.classList.remove('bg-primary-600', 'text-white', 'border-primary-600');
                btn.classList.add('border-gray-200');
            });
            event.target.classList.add('bg-primary-600', 'text-white', 'border-primary-600');
        }
        
        document.getElementById('increaseBtn').addEventListener('click', function() {
            let count = parseInt(treeCountEl.innerText);
            count++;
            treeCountEl.innerText = count;
            updateTotal();
        });
        
        document.getElementById('decreaseBtn').addEventListener('click', function() {
            let count = parseInt(treeCountEl.innerText);
            if (count > 1) {
                count--;
                treeCountEl.innerText = count;
                updateTotal();
            }
        });
        
        function addToCart(campaignId, quantity) {
            // Simulasi add to cart
            alert('Berhasil ditambahkan ke keranjang!\nCampaign: <?php echo $campaign['title']; ?>\nJumlah: ' + quantity + ' pohon\nTotal: Rp ' + (quantity * pricePerTree).toLocaleString('id-ID'));
            window.location.href = 'cart.php';
        }
        
        function donateNow() {
            const quantity = parseInt(treeCountEl.innerText);
            addToCart(<?php echo $campaign['id']; ?>, quantity);
        }
        
        // Set initial active state
        document.querySelectorAll('.quick-select-btn').forEach(btn => {
            if (btn.innerText === '5') {
                btn.classList.add('bg-primary-600', 'text-white', 'border-primary-600');
            }
        });
    </script>
</body>
</html>