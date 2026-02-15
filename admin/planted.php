<?php
// admin/planted.php - Update Penanaman
session_start();

// Simulasi data penanaman
$plantings = [
    [
        'id' => 1,
        'campaign_id' => 1,
        'campaign_name' => 'Restorasi Mangrove Demak',
        'location' => 'Demak, Jawa Tengah',
        'trees_planted' => 350,
        'planting_date' => '2026-02-15',
        'volunteers' => 45,
        'coordinator' => 'Kelompok Tani Hutan',
        'description' => 'Penanaman mangrove tahap ke-3 bersama masyarakat setempat',
        'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'status' => 'completed'
    ],
    [
        'id' => 2,
        'campaign_id' => 2,
        'campaign_name' => 'Reboisasi Lereng Merapi',
        'location' => 'Magelang, Jawa Tengah',
        'trees_planted' => 500,
        'planting_date' => '2026-02-10',
        'volunteers' => 78,
        'coordinator' => 'Komunitas Pecinta Alam',
        'description' => 'Penanaman sengon dan mahoni di lereng Merapi',
        'image' => 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'status' => 'completed'
    ],
    [
        'id' => 3,
        'campaign_id' => 6,
        'campaign_name' => 'Mangrove Pesisir Jakarta',
        'location' => 'Jakarta Utara',
        'trees_planted' => 280,
        'planting_date' => '2026-02-05',
        'volunteers' => 52,
        'coordinator' => 'Forum Komunitas Hijau',
        'description' => 'Penanaman mangrove di kawasan Muara Angke',
        'image' => 'https://images.unsplash.com/photo-1621451498295-af1ea68616ee?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'status' => 'completed'
    ],
    [
        'id' => 4,
        'campaign_id' => 3,
        'campaign_name' => 'Penghijauan Hutan Lombok',
        'location' => 'Lombok, NTB',
        'trees_planted' => 450,
        'planting_date' => '2026-01-28',
        'volunteers' => 63,
        'coordinator' => 'Green Lombok Foundation',
        'description' => 'Penanaman 450 pohon di kawasan hutan yang terbakar',
        'image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
        'status' => 'completed'
    ],
    [
        'id' => 5,
        'campaign_id' => 1,
        'campaign_name' => 'Restorasi Mangrove Demak',
        'location' => 'Demak, Jawa Tengah',
        'trees_planted' => 200,
        'planting_date' => '2026-03-05',
        'volunteers' => 30,
        'coordinator' => 'Kelompok Tani Hutan',
        'description' => 'Penanaman mangrove tahap ke-4 - persiapan',
        'image' => null,
        'status' => 'scheduled'
    ]
];

// Simulasi data campaign untuk dropdown
$campaigns = [
    ['id' => 1, 'name' => 'Restorasi Mangrove Demak', 'remaining' => 3550],
    ['id' => 2, 'name' => 'Reboisasi Lereng Merapi', 'remaining' => 1700],
    ['id' => 3, 'name' => 'Penghijauan Hutan Lombok', 'remaining' => 2220],
    ['id' => 4, 'name' => 'Hutan Pangan Kalimantan', 'remaining' => 1550],
    ['id' => 5, 'name' => 'Konservasi Hutan Papua', 'remaining' => 1180],
    ['id' => 6, 'name' => 'Mangrove Pesisir Jakarta', 'remaining' => 2250]
];

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Penanaman - Sodakoh Pohon</title>
    
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
        
        .planting-card {
            transition: all 0.3s ease;
        }
        
        .planting-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar (sama dengan sebelumnya) -->
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
                            <a href="planted.php" class="sidebar-link active flex items-center px-4 py-3 rounded-xl text-white bg-primary-600">
                                <i class="fas fa-seedling w-6"></i>
                                <span class="ml-3 font-medium">Penanaman</span>
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
                        <?php echo $action == 'create' ? 'Update Penanaman Baru' : 'Manajemen Penanaman'; ?>
                    </h1>
                    
                    <div class="flex items-center space-x-4">
                        <?php if($action == 'list'): ?>
                        <a href="?action=create" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-5 py-2.5 rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25 flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Update Penanaman
                        </a>
                        <?php endif; ?>
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
                <?php if ($action == 'create'): ?>
                <!-- Form Update Penanaman -->
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <form id="plantingForm" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Campaign <span class="text-red-500">*</span>
                                </label>
                                <select name="campaign_id" 
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none">
                                    <option value="">-- Pilih Campaign --</option>
                                    <?php foreach ($campaigns as $campaign): ?>
                                    <option value="<?php echo $campaign['id']; ?>">
                                        <?php echo $campaign['name']; ?> (Sisa: <?php echo number_format($campaign['remaining']); ?> pohon)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
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
                                    Jumlah Pohon Ditanam <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       name="trees_planted"
                                       placeholder="Contoh: 500"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Penanaman <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="planting_date"
                                       value="<?php echo date('Y-m-d'); ?>"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Relawan
                                </label>
                                <input type="number" 
                                       name="volunteers"
                                       placeholder="Contoh: 50"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Koordinator Lapangan
                                </label>
                                <input type="text" 
                                       name="coordinator"
                                       placeholder="Contoh: Kelompok Tani Hutan"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi Kegiatan
                            </label>
                            <textarea name="description" 
                                      rows="3"
                                      placeholder="Jelaskan kegiatan penanaman yang dilakukan..."
                                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Foto Dokumentasi
                            </label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-primary-600 mb-2"></i>
                                        <p class="mb-1 text-sm text-gray-600">
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
                                Simpan Update Penanaman
                            </button>
                            <a href="planted.php" 
                               class="bg-white border-2 border-gray-200 text-gray-700 font-bold py-3 px-8 rounded-xl hover:border-gray-300 transition">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
                
                <?php else: ?>
                <!-- List Penanaman -->
                
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-tree text-primary-600 text-xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Total Pohon Tertanam</p>
                        <p class="text-2xl font-extrabold text-gray-900">1,780</p>
                        <p class="text-xs text-gray-400 mt-2">dari 6 campaign</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Kegiatan Selesai</p>
                        <p class="text-2xl font-extrabold text-gray-900">4</p>
                        <p class="text-xs text-gray-400 mt-2">penanaman</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Dijadwalkan</p>
                        <p class="text-2xl font-extrabold text-gray-900">1</p>
                        <p class="text-xs text-gray-400 mt-2">penanaman</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Total Relawan</p>
                        <p class="text-2xl font-extrabold text-gray-900">268</p>
                        <p class="text-xs text-gray-400 mt-2">terlibat</p>
                    </div>
                </div>
                
                <!-- Filter -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" 
                                   placeholder="Cari campaign atau lokasi..." 
                                   class="pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none w-full">
                        </div>
                        
                        <select class="px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none">
                            <option>Semua Campaign</option>
                            <?php foreach ($campaigns as $campaign): ?>
                            <option><?php echo $campaign['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <select class="px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none">
                            <option>Semua Status</option>
                            <option>Selesai</option>
                            <option>Dijadwalkan</option>
                        </select>
                    </div>
                </div>
                
                <!-- Plantings Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($plantings as $planting): ?>
                    <div class="planting-card bg-white rounded-2xl shadow-sm overflow-hidden">
                        <?php if($planting['image']): ?>
                        <div class="h-48 overflow-hidden">
                            <img src="<?php echo $planting['image']; ?>" 
                                 alt="<?php echo $planting['campaign_name']; ?>"
                                 class="w-full h-full object-cover">
                        </div>
                        <?php else: ?>
                        <div class="h-48 bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center">
                            <i class="fas fa-seedling text-5xl text-primary-600"></i>
                        </div>
                        <?php endif; ?>
                        
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-gray-900"><?php echo $planting['campaign_name']; ?></h3>
                                <span class="status-badge <?php echo $planting['status'] == 'completed' ? 'status-paid' : 'status-pending'; ?>">
                                    <?php echo $planting['status'] == 'completed' ? 'Selesai' : 'Dijadwalkan'; ?>
                                </span>
                            </div>
                            
                            <div class="flex items-center text-xs text-gray-500 mb-3">
                                <i class="fas fa-map-marker-alt mr-1 text-primary-600"></i>
                                <?php echo $planting['location']; ?>
                                <span class="mx-2">•</span>
                                <i class="fas fa-calendar mr-1 text-primary-600"></i>
                                <?php echo date('d M Y', strtotime($planting['planting_date'])); ?>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                <?php echo $planting['description']; ?>
                            </p>
                            
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 mb-1">Pohon Ditanam</p>
                                    <p class="text-lg font-bold text-gray-900"><?php echo number_format($planting['trees_planted']); ?></p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 mb-1">Relawan</p>
                                    <p class="text-lg font-bold text-gray-900"><?php echo $planting['volunteers']; ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-user mr-1 text-primary-600"></i>
                                    <?php echo $planting['coordinator']; ?>
                                </span>
                                <div class="flex space-x-2">
                                    <button onclick="editPlanting(<?php echo $planting['id']; ?>)" 
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deletePlanting(<?php echo $planting['id']; ?>)" 
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <div class="mt-8 bg-white rounded-2xl shadow-sm px-6 py-4 flex items-center justify-between">
                    <span class="text-sm text-gray-600">
                        Menampilkan 1-<?php echo count($plantings); ?> dari <?php echo count($plantings); ?> kegiatan
                    </span>
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
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Form submission handler
        document.getElementById('plantingForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Sukses!',
                text: 'Update penanaman berhasil disimpan',
                icon: 'success',
                confirmButtonColor: '#059669',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'planted.php';
            });
        });
        
        // Edit planting function
        function editPlanting(id) {
            Swal.fire({
                title: 'Edit Penanaman',
                text: 'Fitur edit akan segera tersedia',
                icon: 'info',
                confirmButtonColor: '#059669',
                confirmButtonText: 'OK'
            });
        }
        
        // Delete planting function
        function deletePlanting(id) {
            Swal.fire({
                title: 'Hapus Kegiatan?',
                text: 'Data penanaman yang dihapus tidak dapat dikembalikan!',
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
                        text: 'Data penanaman berhasil dihapus.',
                        icon: 'success',
                        confirmButtonColor: '#059669',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'planted.php';
                    });
                }
            });
        }
    </script>
</body>
</html>