<?php
// admin/campaign.php - Manajemen Campaign
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
        'planted_trees' => 890,
        'status' => 'active',
        'created_at' => '2026-01-15',
        'deadline' => '2026-03-30',
        'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 2,
        'title' => 'Reboisasi Lereng Merapi',
        'location' => 'Magelang, Jawa Tengah',
        'tree_type' => 'Sengon & Mahoni',
        'price_per_tree' => 12000,
        'target_trees' => 4000,
        'current_trees' => 2300,
        'planted_trees' => 1650,
        'status' => 'active',
        'created_at' => '2026-01-20',
        'deadline' => '2026-03-15',
        'image' => 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 3,
        'title' => 'Penghijauan Hutan Lombok',
        'location' => 'Lombok, NTB',
        'tree_type' => 'Mahoni',
        'price_per_tree' => 15000,
        'target_trees' => 3000,
        'current_trees' => 780,
        'planted_trees' => 450,
        'status' => 'active',
        'created_at' => '2026-02-01',
        'deadline' => '2026-04-20',
        'image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 4,
        'title' => 'Hutan Pangan Kalimantan',
        'location' => 'Kutai, Kaltim',
        'tree_type' => 'Durian & Petai',
        'price_per_tree' => 25000,
        'target_trees' => 2000,
        'current_trees' => 450,
        'planted_trees' => 120,
        'status' => 'active',
        'created_at' => '2026-02-10',
        'deadline' => '2026-05-10',
        'image' => 'https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 5,
        'title' => 'Konservasi Hutan Papua',
        'location' => 'Jayapura, Papua',
        'tree_type' => 'Merbau',
        'price_per_tree' => 30000,
        'target_trees' => 1500,
        'current_trees' => 320,
        'planted_trees' => 100,
        'status' => 'pending',
        'created_at' => '2026-02-20',
        'deadline' => '2026-06-30',
        'image' => 'https://images.unsplash.com/photo-1425913397330-cf8af2ff40a1?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'
    ],
    [
        'id' => 6,
        'title' => 'Mangrove Pesisir Jakarta',
        'location' => 'Jakarta Utara',
        'tree_type' => 'Mangrove',
        'price_per_tree' => 10000,
        'target_trees' => 3500,
        'current_trees' => 1250,
        'planted_trees' => 840,
        'status' => 'active',
        'created_at' => '2026-01-25',
        'deadline' => '2026-03-25',
        'image' => 'https://images.unsplash.com/photo-1621451498295-af1ea68616ee?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'
    ]
];

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$campaign_id = isset($_GET['id']) ? $_GET['id'] : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Campaign - Sodakoh Pohon</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
        
        .campaign-card {
            transition: all 0.3s ease;
        }
        
        .campaign-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-completed {
            background-color: #e0e7ff;
            color: #3730a3;
        }
    </style>
</head>
<body>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar (sama dengan index.php) -->
        <aside class="w-72 bg-white shadow-xl flex flex-col fixed h-full overflow-y-auto">
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
            
            <nav class="flex-1 p-4">
                <div class="mb-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2">Main Menu</p>
                    <ul class="space-y-1">
                        <li>
                            <a href="index.php" class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50">
                                <i class="fas fa-dashboard w-6 text-gray-500"></i>
                                <span class="ml-3 font-medium">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="campaign.php" class="sidebar-link active flex items-center px-4 py-3 rounded-xl text-white bg-primary-600">
                                <i class="fas fa-tree w-6"></i>
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
            </nav>
            
            <div class="p-4 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    Sodakoh Pohon v1.0.0<br>
                    &copy; 2026 All rights reserved
                </p>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-72 overflow-y-auto">
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="flex justify-between items-center px-8 py-4">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <?php echo $action == 'create' ? 'Buat Campaign Baru' : ($action == 'edit' ? 'Edit Campaign' : 'Manajemen Campaign'); ?>
                    </h1>
                    
                    <div class="flex items-center space-x-4">
                        <a href="?action=create" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-5 py-2.5 rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25 flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Buat Campaign
                        </a>
                        <button class="relative p-2 text-gray-500 hover:text-primary-600 transition">
                            <i class="fas fa-bell text-xl"></i>
                        </button>
                        <div class="flex items-center text-sm text-gray-600 bg-gray-100 rounded-xl px-4 py-2">
                            <i class="fas fa-calendar-alt mr-2 text-primary-600"></i>
                            <?php echo date('d F Y'); ?>
                        </div>
                    </div>
                </div>
            </header>

            <div class="px-8 py-6">
                <?php if ($action == 'create' || $action == 'edit'): ?>
                <!-- Form Create/Edit Campaign -->
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <form id="campaignForm" class="space-y-6">
                        <?php if ($action == 'edit'): ?>
                        <input type="hidden" name="id" value="<?php echo $campaign_id; ?>">
                        <?php endif; ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Campaign <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="title"
                                       placeholder="Contoh: Restorasi Mangrove Demak"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Lokasi Penanaman <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="location"
                                       placeholder="Contoh: Demak, Jawa Tengah"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Pohon <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="tree_type"
                                       placeholder="Contoh: Mangrove Rhizophora"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Harga per Pohon (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       name="price_per_tree"
                                       placeholder="10000"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Target Jumlah Pohon <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       name="target_trees"
                                       placeholder="5000"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Deadline <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="deadline"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi Campaign <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" 
                                      rows="4"
                                      placeholder="Jelaskan tujuan dan manfaat dari campaign ini..."
                                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Gambar Campaign
                            </label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-primary-600 mb-3"></i>
                                        <p class="mb-2 text-sm text-gray-600">
                                            <span class="font-semibold">Klik untuk upload</span> atau drag & drop
                                        </p>
                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG (MAX. 5MB)</p>
                                    </div>
                                    <input type="file" class="hidden" accept="image/*">
                                </label>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4 pt-4">
                            <button type="submit" 
                                    class="bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold py-3 px-8 rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                                <?php echo $action == 'create' ? 'Buat Campaign' : 'Simpan Perubahan'; ?>
                            </button>
                            <a href="campaign.php" 
                               class="bg-white border-2 border-gray-200 text-gray-700 font-bold py-3 px-8 rounded-xl hover:border-gray-300 transition">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
                
                <?php else: ?>
                <!-- List Campaign -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <!-- Filter & Search -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" 
                                           placeholder="Cari campaign..." 
                                           class="pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none w-full md:w-64">
                                </div>
                                <select class="px-4 py-2.5 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none">
                                    <option>Semua Status</option>
                                    <option>Aktif</option>
                                    <option>Pending</option>
                                    <option>Selesai</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-500">Total: <?php echo count($campaigns); ?> campaign</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campaign Grid -->
                    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                        <?php foreach ($campaigns as $campaign): 
                            $progress = ($campaign['current_trees'] / $campaign['target_trees']) * 100;
                            $status_class = $campaign['status'] == 'active' ? 'status-active' : ($campaign['status'] == 'pending' ? 'status-pending' : 'status-completed');
                            $status_text = $campaign['status'] == 'active' ? 'Aktif' : ($campaign['status'] == 'pending' ? 'Menunggu' : 'Selesai');
                        ?>
                        <div class="campaign-card bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg">
                            <div class="relative h-48">
                                <img src="<?php echo $campaign['image']; ?>" 
                                     alt="<?php echo $campaign['title']; ?>"
                                     class="w-full h-full object-cover">
                                <div class="absolute top-3 right-3">
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </div>
                                <?php if($campaign['status'] == 'active'): ?>
                                <div class="absolute bottom-3 left-3">
                                    <span class="bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-lg text-sm font-semibold text-primary-700">
                                        <i class="fas fa-clock mr-1"></i>
                                        <?php 
                                            $deadline = new DateTime($campaign['deadline']);
                                            $now = new DateTime();
                                            $diff = $now->diff($deadline);
                                            echo $diff->days . ' hari lagi';
                                        ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-bold text-gray-900 line-clamp-1"><?php echo $campaign['title']; ?></h3>
                                    <span class="text-xs text-gray-500">
                                        ID: #<?php echo $campaign['id']; ?>
                                    </span>
                                </div>
                                
                                <div class="flex items-center text-xs text-gray-500 mb-3">
                                    <i class="fas fa-map-marker-alt mr-1 text-primary-600"></i>
                                    <?php echo $campaign['location']; ?>
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-leaf mr-1 text-primary-600"></i>
                                    <?php echo $campaign['tree_type']; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-600">Progress:</span>
                                        <span class="font-semibold text-primary-700"><?php echo round($progress); ?>%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs mt-1">
                                        <span class="text-gray-500"><?php echo number_format($campaign['current_trees']); ?> pohon</span>
                                        <span class="text-gray-500">Target: <?php echo number_format($campaign['target_trees']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-2 mb-4 text-sm">
                                    <div class="bg-gray-50 rounded-lg p-2">
                                        <p class="text-xs text-gray-500">Harga/pohon</p>
                                        <p class="font-semibold text-gray-900">Rp <?php echo number_format($campaign['price_per_tree']); ?></p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-2">
                                        <p class="text-xs text-gray-500">Tertanam</p>
                                        <p class="font-semibold text-gray-900"><?php echo number_format($campaign['planted_trees']); ?></p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div class="flex space-x-2">
                                        <a href="?action=edit&id=<?php echo $campaign['id']; ?>" 
                                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteCampaign(<?php echo $campaign['id']; ?>)" 
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <a href="donations.php?campaign=<?php echo $campaign['id']; ?>" 
                                       class="text-xs text-primary-600 hover:text-primary-700 font-semibold">
                                        Lihat Donasi
                                        <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                        <span class="text-sm text-gray-600">Menampilkan 1-6 dari 6 campaign</span>
                        <div class="flex items-center space-x-2">
                            <button class="w-10 h-10 flex items-center justify-center border border-gray-200 rounded-lg text-gray-500 hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="w-10 h-10 flex items-center justify-center bg-primary-600 text-white rounded-lg">1</button>
                            <button class="w-10 h-10 flex items-center justify-center border border-gray-200 rounded-lg text-gray-500 hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Form submission handler
        document.getElementById('campaignForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Sukses!',
                text: '<?php echo $action == 'create' ? 'Campaign berhasil dibuat' : 'Campaign berhasil diperbarui'; ?>',
                icon: 'success',
                confirmButtonColor: '#059669',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'campaign.php';
            });
        });
        
        // Delete campaign function
        function deleteCampaign(id) {
            Swal.fire({
                title: 'Hapus Campaign?',
                text: 'Campaign yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Terhapus!',
                        text: 'Campaign berhasil dihapus.',
                        icon: 'success',
                        confirmButtonColor: '#059669',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'campaign.php';
                    });
                }
            });
        }
    </script>
</body>
</html>