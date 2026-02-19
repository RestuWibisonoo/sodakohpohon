<?php
// admin/campaign.php - Manajemen Campaign
session_start();
require_once '../config/koneksi.php';
require_once '../models/Campaign.php';

// Cek autentikasi
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$campaignModel = new Campaign();

// Helper: resolve image URL (handle local uploads vs external URLs)
function campaignImageUrl($path) {
    if (empty($path)) return 'https://via.placeholder.com/400x200?text=No+Image';
    // External URL (http/https) → return as-is
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
    // Local path like 'uploads/campaigns/...' → prepend '../'
    return '../' . ltrim($path, '/');
}

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$campaign_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data campaign dari database
$campaigns = $campaignModel->getAll();
$total_campaigns = count($campaigns);

// Statistik campaign
$campaign_stats = $campaignModel->getStats();
$active_count = $campaign_stats['active_campaigns'] ?? 0;
$completed_count = $campaign_stats['completed_campaigns'] ?? 0;

// Untuk edit, ambil data campaign spesifik
$edit_campaign = null;
if ($action == 'edit' && $campaign_id > 0) {
    $edit_campaign = $campaignModel->getById($campaign_id);
    if (!$edit_campaign) {
        header('Location: campaign.php');
        exit;
    }
}

$current_page = 'campaign';
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

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
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
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

        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
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
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 ml-72 overflow-y-auto">
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="flex justify-between items-center px-8 py-4">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <?php echo $action == 'create' ? 'Buat Campaign Baru' : ($action == 'edit' ? 'Edit Campaign' : 'Manajemen Campaign'); ?>
                    </h1>

                    <div class="flex items-center space-x-4">
                        <a href="?action=create"
                            class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-5 py-2.5 rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25 flex items-center">
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
                    <form id="campaignForm" enctype="multipart/form-data" class="space-y-6">
                        <?php if ($action == 'edit'): ?>
                        <input type="hidden" name="id" value="<?php echo $campaign_id; ?>">
                        <?php endif; ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Campaign <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" placeholder="Contoh: Restorasi Mangrove Demak"
                                    value="<?php echo $edit_campaign ? htmlspecialchars($edit_campaign['title']) : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition"
                                    required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Lokasi Penanaman <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="location" placeholder="Contoh: Demak, Jawa Tengah"
                                    value="<?php echo $edit_campaign ? htmlspecialchars($edit_campaign['location']) : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition"
                                    required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Pohon <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="tree_type" placeholder="Contoh: Mangrove Rhizophora"
                                    value="<?php echo $edit_campaign ? htmlspecialchars($edit_campaign['tree_type']) : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition"
                                    required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Kategori
                                </label>
                                <select name="category"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                                    <option value="Umum" <?php echo ($edit_campaign &&
                                        $edit_campaign['category']=='Umum' ) ? 'selected' : '' ; ?>>Umum</option>
                                    <option value="Mangrove" <?php echo ($edit_campaign &&
                                        $edit_campaign['category']=='Mangrove' ) ? 'selected' : '' ; ?>>Mangrove
                                    </option>
                                    <option value="Reboisasi" <?php echo ($edit_campaign &&
                                        $edit_campaign['category']=='Reboisasi' ) ? 'selected' : '' ; ?>>Reboisasi
                                    </option>
                                    <option value="Hutan Pangan" <?php echo ($edit_campaign &&
                                        $edit_campaign['category']=='Hutan Pangan' ) ? 'selected' : '' ; ?>>Hutan Pangan
                                    </option>
                                    <option value="Konservasi" <?php echo ($edit_campaign &&
                                        $edit_campaign['category']=='Konservasi' ) ? 'selected' : '' ; ?>>Konservasi
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Harga per Pohon (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="price_per_tree" placeholder="10000" min="1"
                                    value="<?php echo $edit_campaign ? (int)$edit_campaign['price_per_tree'] : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition"
                                    required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Target Jumlah Pohon <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="target_trees" placeholder="5000" min="1"
                                    value="<?php echo $edit_campaign ? (int)$edit_campaign['target_trees'] : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition"
                                    required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Deadline <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="deadline"
                                    value="<?php echo $edit_campaign ? $edit_campaign['deadline'] : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition"
                                    required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Mitra / Partner
                                </label>
                                <input type="text" name="partner" placeholder="Contoh: Kelompok Tani Hutan"
                                    value="<?php echo $edit_campaign ? htmlspecialchars($edit_campaign['partner'] ?? '') : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>

                            <?php if ($action == 'edit'): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Status
                                </label>
                                <select name="status"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                                    <option value="active" <?php echo ($edit_campaign['status']=='active' ) ? 'selected'
                                        : '' ; ?>>Aktif</option>
                                    <option value="pending" <?php echo ($edit_campaign['status']=='pending' )
                                        ? 'selected' : '' ; ?>>Menunggu</option>
                                    <option value="completed" <?php echo ($edit_campaign['status']=='completed' )
                                        ? 'selected' : '' ; ?>>Selesai</option>
                                    <option value="cancelled" <?php echo ($edit_campaign['status']=='cancelled' )
                                        ? 'selected' : '' ; ?>>Dibatalkan</option>
                                </select>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi Campaign <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" rows="4"
                                placeholder="Jelaskan tujuan dan manfaat dari campaign ini..."
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition"
                                required><?php echo $edit_campaign ? htmlspecialchars($edit_campaign['description']) : ''; ?></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Gambar Campaign
                            </label>
                            <?php if ($edit_campaign && !empty($edit_campaign['image'])): ?>
                            <div class="mb-3 flex items-center gap-3">
                                <img src="<?php echo htmlspecialchars(campaignImageUrl($edit_campaign['image'])); ?>"
                                    alt="Current image" class="w-24 h-24 object-cover rounded-lg border">
                                <span class="text-sm text-gray-500">Gambar saat ini. Upload gambar baru untuk
                                    mengganti.</span>
                            </div>
                            <?php endif; ?>
                            <div class="flex items-center justify-center w-full">
                                <label id="imageDropArea"
                                    class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6"
                                        id="imageUploadText">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-primary-600 mb-3"></i>
                                        <p class="mb-2 text-sm text-gray-600">
                                            <span class="font-semibold">Klik untuk upload</span> atau drag & drop
                                        </p>
                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG (MAX. 5MB)</p>
                                    </div>
                                    <input type="file" name="image" id="imageInput" class="hidden" accept="image/*">
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-4">
                            <button type="submit" id="submitBtn"
                                class="bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold py-3 px-8 rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                                <i class="fas fa-spinner fa-spin mr-2 hidden" id="submitSpinner"></i>
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
                <!-- Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Campaign</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    <?php echo $total_campaigns; ?>
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-bullhorn text-primary-600 text-lg"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Campaign Aktif</p>
                                <p class="text-2xl font-bold text-green-600">
                                    <?php echo $active_count; ?>
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-lg"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Campaign Selesai</p>
                                <p class="text-2xl font-bold text-blue-600">
                                    <?php echo $completed_count; ?>
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-flag-checkered text-blue-600 text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- List Campaign -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <!-- Filter & Search -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <i
                                        class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" id="searchCampaign" placeholder="Cari campaign..."
                                        class="pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none w-full md:w-64">
                                </div>
                                <select id="filterStatus"
                                    class="px-4 py-2.5 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none">
                                    <option value="">Semua Status</option>
                                    <option value="active">Aktif</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Selesai</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-500">Total:
                                    <?php echo $total_campaigns; ?> campaign
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Grid -->
                    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6" id="campaignGrid">
                        <?php if (empty($campaigns)): ?>
                        <div class="col-span-full text-center py-12">
                            <i class="fas fa-bullhorn text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-400 text-lg">Belum ada campaign</p>
                            <a href="?action=create"
                                class="inline-block mt-4 text-primary-600 hover:text-primary-700 font-semibold">
                                <i class="fas fa-plus-circle mr-1"></i> Buat Campaign Baru
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php foreach ($campaigns as $campaign):
        $progress = $campaign['target_trees'] > 0 ? ($campaign['current_trees'] / $campaign['target_trees']) * 100 : 0;
        $status_class = match($campaign['status']) {
            'active' => 'status-active',
            'pending' => 'status-pending',
            'completed' => 'status-completed',
            'cancelled' => 'status-cancelled',
            default => 'status-pending'
        };
        $status_text = match($campaign['status']) {
            'active' => 'Aktif',
            'pending' => 'Menunggu',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => '-'
        };
?>
                        <div class="campaign-card bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg"
                            data-title="<?php echo htmlspecialchars(strtolower($campaign['title'])); ?>"
                            data-status="<?php echo $campaign['status']; ?>">
                            <div class="relative h-48">
                                <img src="<?php echo htmlspecialchars(campaignImageUrl($campaign['image'] ?? '')); ?>"
                                    alt="<?php echo htmlspecialchars($campaign['title']); ?>"
                                    class="w-full h-full object-cover"
                                    onerror="this.src='https://via.placeholder.com/400x200?text=No+Image'">
                                <div class="absolute top-3 right-3">
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </div>
                                <?php if ($campaign['status'] == 'active' && !empty($campaign['deadline'])): ?>
                                <div class="absolute bottom-3 left-3">
                                    <span
                                        class="bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-lg text-sm font-semibold text-primary-700">
                                        <i class="fas fa-clock mr-1"></i>
                                        <?php
            $deadline = new DateTime($campaign['deadline']);
            $now = new DateTime();
            $diff = $now->diff($deadline);
            if ($deadline > $now) {
                echo $diff->days . ' hari lagi';
            } else {
                echo 'Lewat deadline';
            }
?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="p-5">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-bold text-gray-900 line-clamp-1">
                                        <?php echo htmlspecialchars($campaign['title']); ?>
                                    </h3>
                                    <span class="text-xs text-gray-500">
                                        ID: #
                                        <?php echo $campaign['id']; ?>
                                    </span>
                                </div>

                                <div class="flex items-center text-xs text-gray-500 mb-3">
                                    <i class="fas fa-map-marker-alt mr-1 text-primary-600"></i>
                                    <?php echo htmlspecialchars($campaign['location']); ?>
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-leaf mr-1 text-primary-600"></i>
                                    <?php echo htmlspecialchars($campaign['tree_type']); ?>
                                </div>

                                <div class="mb-3">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-600">Progress:</span>
                                        <span class="font-semibold text-primary-700">
                                            <?php echo round($progress); ?>%
                                        </span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo min($progress, 100); ?>%">
                                        </div>
                                    </div>
                                    <div class="flex justify-between text-xs mt-1">
                                        <span class="text-gray-500">
                                            <?php echo number_format($campaign['current_trees']); ?> pohon
                                        </span>
                                        <span class="text-gray-500">Target:
                                            <?php echo number_format($campaign['target_trees']); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2 mb-4 text-sm">
                                    <div class="bg-gray-50 rounded-lg p-2">
                                        <p class="text-xs text-gray-500">Harga/pohon</p>
                                        <p class="font-semibold text-gray-900">Rp
                                            <?php echo number_format($campaign['price_per_tree']); ?>
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-2">
                                        <p class="text-xs text-gray-500">Tertanam</p>
                                        <p class="font-semibold text-gray-900">
                                            <?php echo number_format($campaign['planted_trees']); ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div class="flex space-x-2">
                                        <a href="?action=edit&id=<?php echo $campaign['id']; ?>"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button
                                            onclick="deleteCampaign(<?php echo $campaign['id']; ?>, '<?php echo htmlspecialchars(addslashes($campaign['title'])); ?>')"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Hapus">
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
                        <span class="text-sm text-gray-600">
                            Menampilkan
                            <?php echo $total_campaigns; ?> campaign
                        </span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // ============================================================
        // FORM SUBMISSION — POST to adminController.php
        // ============================================================
        document.getElementById('campaignForm')?.addEventListener('submit', function (e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const isEdit = formData.has('id');
            const action = isEdit ? 'update_campaign' : 'store_campaign';
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('submitSpinner');

            // Disable button & show spinner
            submitBtn.disabled = true;
            spinner.classList.remove('hidden');

            fetch('../controllers/adminController.php?action=' + action, {
                method: 'POST',
                body: formData
            })
                .then(r => r.json())
                .then(data => {
                    submitBtn.disabled = false;
                    spinner.classList.add('hidden');

                    if (data.success) {
                        Swal.fire({
                            title: 'Sukses!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#059669'
                        }).then(() => {
                            window.location = data.redirect || 'campaign.php';
                        });
                    } else {
                        let errorMsg = data.message;
                        if (data.errors) {
                            errorMsg = Object.values(data.errors).join('\n');
                        }
                        Swal.fire({
                            title: 'Gagal!',
                            text: errorMsg,
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                })
                .catch(err => {
                    submitBtn.disabled = false;
                    spinner.classList.add('hidden');
                    console.error('Error:', err);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan koneksi. Silakan coba lagi.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                });
        });

        // ============================================================
        // DELETE CAMPAIGN — POST to adminController.php
        // ============================================================
        function deleteCampaign(id, title) {
            Swal.fire({
                title: 'Hapus Campaign?',
                html: 'Campaign <strong>"' + title + '"</strong> akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', id);

                    fetch('../controllers/adminController.php?action=delete_campaign', {
                        method: 'POST',
                        body: formData
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#059669'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan koneksi.',
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        });
                }
            });
        }

        // ============================================================
        // SEARCH & FILTER (client-side)
        // ============================================================
        const searchInput = document.getElementById('searchCampaign');
        const filterSelect = document.getElementById('filterStatus');

        function filterCampaigns() {
            const searchTerm = (searchInput?.value || '').toLowerCase();
            const statusFilter = filterSelect?.value || '';
            const cards = document.querySelectorAll('.campaign-card');

            cards.forEach(card => {
                const title = card.dataset.title || '';
                const status = card.dataset.status || '';
                const matchSearch = title.includes(searchTerm);
                const matchStatus = !statusFilter || status === statusFilter;
                card.style.display = (matchSearch && matchStatus) ? '' : 'none';
            });
        }

        searchInput?.addEventListener('input', filterCampaigns);
        filterSelect?.addEventListener('change', filterCampaigns);

        // ============================================================
        // IMAGE PREVIEW
        // ============================================================
        document.getElementById('imageInput')?.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({ title: 'Error', text: 'Ukuran file maksimal 5MB', icon: 'error' });
                    this.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (ev) {
                    const container = document.getElementById('imageUploadText');
                    container.innerHTML = '<img src="' + ev.target.result + '" class="h-40 object-contain rounded-lg">';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>
