<?php
// admin/planted.php - Update Penanaman
session_start();
require_once '../config/koneksi.php';
require_once '../models/Campaign.php';

// Cek autentikasi
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$campaignModel = new Campaign();
$conn = getDB();

// Ambil data penanaman dari database
$sql = "SELECT p.*, c.title as campaign_name 
        FROM plantings p 
        LEFT JOIN campaigns c ON p.campaign_id = c.id 
        ORDER BY p.planting_date DESC";
$result = $conn->query($sql);
$plantings = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $plantings[] = $row;
    }
}

// Ambil data campaign untuk dropdown
$all_campaigns = $campaignModel->getAll();
$campaigns = [];
foreach ($all_campaigns as $c) {
    $campaigns[] = [
        'id' => $c['id'],
        'name' => $c['title'],
        'remaining' => $c['target_trees'] - $c['current_trees']
    ];
}

// Hitung statistik dari data DB
$total_trees_planted = array_sum(array_column($plantings, 'trees_planted'));
$completed_plantings = count(array_filter($plantings, fn($p) => $p['status'] == 'completed'));
$scheduled_plantings = count(array_filter($plantings, fn($p) => $p['status'] == 'scheduled'));
$total_volunteers = array_sum(array_column($plantings, 'volunteers'));

// Helper function untuk resolve image path
function plantingImageUrl($path)
{
    if (empty($path))
        return '';
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://'))
        return $path;
    // Jika path sudah punya directory prefix (uploads/plantings/...)
    if (str_starts_with($path, 'uploads/'))
        return '../' . ltrim($path, '/');
    // Jika path hanya nama file (legacy data), asumsi di uploads/plantings/
    return '../uploads/plantings/' . $path;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Jika edit, cari data planting yang akan diedit
$edit_planting = null;
if ($action == 'edit' && $edit_id > 0) {
    foreach ($plantings as $p) {
        if ($p['id'] == $edit_id) {
            $edit_planting = $p;
            break;
        }
    }
    if (!$edit_planting)
        $action = 'list'; // fallback jika tidak ditemukan
}

$current_page = 'planted';
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
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }

        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-scheduled {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-cancelled {
            background-color: #f3f4f6;
            color: #6b7280;
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
                        <?php
if ($action == 'create')
    echo 'Tambah Penanaman Baru';
elseif ($action == 'edit')
    echo 'Edit Penanaman';
else
    echo 'Manajemen Penanaman';
?>
                    </h1>

                    <div class="flex items-center space-x-4">
                        <?php if ($action == 'list'): ?>
                        <a href="?action=create"
                            class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-5 py-2.5 rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25 flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Update Penanaman
                        </a>
                        <?php
endif; ?>
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
                <!-- Form Update Penanaman -->
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <form id="plantingForm" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <?php if ($edit_planting): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_planting['id']; ?>">
                        <?php
    endif; ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Campaign <span class="text-red-500">*</span>
                                </label>
                                <select name="campaign_id" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none">
                                    <option value="">-- Pilih Campaign --</option>
                                    <?php foreach ($campaigns as $campaign): ?>
                                    <option value="<?php echo $campaign['id']; ?>" <?php echo ($edit_planting &&
            $edit_planting['campaign_id'] == $campaign['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($campaign['name']); ?> (Sisa:
                                        <?php echo number_format($campaign['remaining']); ?> pohon)
                                    </option>
                                    <?php
    endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Lokasi Penanaman <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="location" placeholder="Contoh: Demak, Jawa Tengah" required
                                    value="<?php echo $edit_planting ? htmlspecialchars($edit_planting['location']) : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Pohon Ditanam <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="trees_planted" placeholder="Contoh: 500" required min="1"
                                    value="<?php echo $edit_planting ? $edit_planting['trees_planted'] : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Penanaman <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="planting_date" required
                                    value="<?php echo $edit_planting ? $edit_planting['planting_date'] : date('Y-m-d'); ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Relawan
                                </label>
                                <input type="number" name="volunteers" placeholder="Contoh: 50" min="0"
                                    value="<?php echo $edit_planting ? $edit_planting['volunteers'] : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Koordinator Lapangan
                                </label>
                                <input type="text" name="coordinator" placeholder="Contoh: Kelompok Tani Hutan"
                                    value="<?php echo $edit_planting ? htmlspecialchars($edit_planting['coordinator']) : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi Kegiatan
                            </label>
                            <textarea name="description" rows="3"
                                placeholder="Jelaskan kegiatan penanaman yang dilakukan..."
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition"><?php echo $edit_planting ? htmlspecialchars($edit_planting['description']) : ''; ?></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Foto Dokumentasi
                            </label>
                            <?php if ($edit_planting && $edit_planting['image']): ?>
                            <div class="mb-3">
                                <p class="text-xs text-gray-500 mb-2">Foto saat ini:</p>
                                <img src="<?php echo plantingImageUrl($edit_planting['image']); ?>" alt="Foto penanaman"
                                    class="h-32 rounded-lg object-cover">
                            </div>
                            <?php
    endif; ?>
                            <div class="flex items-center justify-center w-full">
                                <label id="uploadArea"
                                    class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div id="uploadPlaceholder"
                                        class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-primary-600 mb-2"></i>
                                        <p class="mb-1 text-sm text-gray-600">
                                            <span class="font-semibold">Klik untuk upload</span> atau drag & drop
                                        </p>
                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG (MAX. 5MB)</p>
                                    </div>
                                    <img id="imagePreview" src="" alt="Preview"
                                        class="hidden h-36 rounded-lg object-cover">
                                    <input type="file" name="image" class="hidden" accept="image/*" id="imageInput">
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-4">
                            <button type="submit" id="submitBtn"
                                class="bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold py-3 px-8 rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25 flex items-center">
                                <span id="submitSpinner" class="hidden mr-2">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                                <?php echo $edit_planting ? 'Update Penanaman' : 'Simpan Penanaman'; ?>
                            </button>
                            <a href="planted.php"
                                class="bg-white border-2 border-gray-200 text-gray-700 font-bold py-3 px-8 rounded-xl hover:border-gray-300 transition">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>

                <?php
else: ?>
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
                        <p class="text-2xl font-extrabold text-gray-900">
                            <?php echo number_format($total_trees_planted); ?>
                        </p>
                        <p class="text-xs text-gray-400 mt-2">dari
                            <?php echo count($plantings); ?> kegiatan
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600">
                                <?php echo $completed_plantings; ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Kegiatan Selesai</p>
                        <p class="text-2xl font-extrabold text-gray-900">
                            <?php echo number_format($completed_plantings); ?>
                        </p>
                        <p class="text-xs text-gray-400 mt-2">penanaman</p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-yellow-600">
                                <?php echo $scheduled_plantings; ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Dijadwalkan</p>
                        <p class="text-2xl font-extrabold text-gray-900">
                            <?php echo number_format($scheduled_plantings); ?>
                        </p>
                        <p class="text-xs text-gray-400 mt-2">penanaman</p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Total Relawan</p>
                        <p class="text-2xl font-extrabold text-gray-900">
                            <?php echo number_format($total_volunteers); ?>
                        </p>
                        <p class="text-xs text-gray-400 mt-2">terlibat</p>
                    </div>
                </div>

                <!-- Filter -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-1 relative">
                            <i
                                class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" placeholder="Cari campaign atau lokasi..." id="searchInput"
                                class="pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none w-full">
                        </div>

                        <select id="campaignFilter"
                            class="px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none">
                            <option value="all">Semua Campaign</option>
                            <?php foreach ($campaigns as $campaign): ?>
                            <option value="<?php echo $campaign['id']; ?>">
                                <?php echo htmlspecialchars($campaign['name']); ?>
                            </option>
                            <?php
    endforeach; ?>
                        </select>

                        <select id="statusFilter"
                            class="px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none">
                            <option value="all">Semua Status</option>
                            <option value="completed">Selesai</option>
                            <option value="scheduled">Dijadwalkan</option>
                        </select>

                        <button onclick="resetFilters()"
                            class="px-4 py-3 text-gray-600 hover:text-gray-900 border border-gray-200 rounded-xl hover:bg-gray-50 transition"
                            title="Reset Filter">
                            <i class="fas fa-undo-alt"></i>
                        </button>
                    </div>
                </div>

                <!-- Plantings Grid -->
                <?php if (empty($plantings)): ?>
                <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-seedling text-primary-400 text-3xl"></i>
                        </div>
                        <p class="text-gray-500 text-lg font-medium mb-1">Belum ada data penanaman</p>
                        <p class="text-gray-400 text-sm mb-4">Mulai tambahkan kegiatan penanaman pohon pertama</p>
                        <a href="?action=create"
                            class="bg-primary-600 text-white px-6 py-2.5 rounded-xl hover:bg-primary-700 transition">
                            <i class="fas fa-plus mr-2"></i>Tambah Penanaman
                        </a>
                    </div>
                </div>
                <?php
    else: ?>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6" id="plantingsGrid">
                    <?php foreach ($plantings as $planting): ?>
                    <div class="planting-card bg-white rounded-2xl shadow-sm overflow-hidden"
                        data-search="<?php echo htmlspecialchars(strtolower(($planting['campaign_name'] ?? '') . ' ' . $planting['location'])); ?>"
                        data-campaign="<?php echo $planting['campaign_id']; ?>"
                        data-status="<?php echo $planting['status']; ?>">
                        <?php
            $imgUrl = plantingImageUrl($planting['image']);
            if ($imgUrl):
?>
                        <div class="h-48 overflow-hidden">
                            <img src="<?php echo htmlspecialchars($imgUrl); ?>"
                                alt="<?php echo htmlspecialchars($planting['campaign_name'] ?? ''); ?>"
                                class="w-full h-full object-cover"
                                onerror="this.parentElement.innerHTML='<div class=\'h-48 bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center\'><i class=\'fas fa-seedling text-5xl text-primary-600\'></i></div>'">
                        </div>
                        <?php
            else: ?>
                        <div
                            class="h-48 bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center">
                            <i class="fas fa-seedling text-5xl text-primary-600"></i>
                        </div>
                        <?php
            endif; ?>

                        <div class="p-5">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-gray-900">
                                    <?php echo htmlspecialchars($planting['campaign_name'] ?? 'Campaign'); ?>
                                </h3>
                                <?php
            $st = $planting['status'];
            $stClass = 'status-scheduled';
            $stLabel = 'Dijadwalkan';
            if ($st == 'completed') {
                $stClass = 'status-completed';
                $stLabel = 'Selesai';
            }
            elseif ($st == 'cancelled') {
                $stClass = 'status-cancelled';
                $stLabel = 'Dibatalkan';
            }
?>
                                <span class="status-badge <?php echo $stClass; ?>">
                                    <?php echo $stLabel; ?>
                                </span>
                            </div>

                            <div class="flex items-center text-xs text-gray-500 mb-3">
                                <i class="fas fa-map-marker-alt mr-1 text-primary-600"></i>
                                <?php echo htmlspecialchars($planting['location']); ?>
                                <span class="mx-2">•</span>
                                <i class="fas fa-calendar mr-1 text-primary-600"></i>
                                <?php echo date('d M Y', strtotime($planting['planting_date'])); ?>
                            </div>

                            <?php if (!empty($planting['description'])): ?>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                <?php echo htmlspecialchars($planting['description']); ?>
                            </p>
                            <?php
            endif; ?>

                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 mb-1">Pohon Ditanam</p>
                                    <p class="text-lg font-bold text-gray-900">
                                        <?php echo number_format($planting['trees_planted']); ?>
                                    </p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 mb-1">Relawan</p>
                                    <p class="text-lg font-bold text-gray-900">
                                        <?php echo number_format($planting['volunteers']); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-user mr-1 text-primary-600"></i>
                                    <?php echo htmlspecialchars($planting['coordinator'] ?? '-'); ?>
                                </span>
                                <div class="flex space-x-2">
                                    <a href="?action=edit&id=<?php echo $planting['id']; ?>"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deletePlanting(<?php echo $planting['id']; ?>)"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
        endforeach; ?>
                </div>

                <!-- Info -->
                <div class="mt-6 bg-white rounded-2xl shadow-sm px-6 py-4 flex items-center justify-between">
                    <span class="text-sm text-gray-600">
                        Menampilkan
                        <?php echo count($plantings); ?> kegiatan penanaman
                    </span>
                </div>
                <?php
    endif; ?>

                <?php
endif; ?>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ============================================================
            // FORM SUBMISSION — POST to adminController.php
            // ============================================================
            document.getElementById('plantingForm')?.addEventListener('submit', function (e) {
                e.preventDefault();

                const form = this;
                const formData = new FormData(form);
                const isEdit = formData.has('id');
                const action = isEdit ? 'update_planting' : 'store_planting';
                const submitBtn = document.getElementById('submitBtn');
                const spinner = document.getElementById('submitSpinner');

                submitBtn.disabled = true;
                spinner.classList.remove('hidden');

                fetch('../controllers/adminController.php?action=' + action, {
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        submitBtn.disabled = false;
                        spinner.classList.add('hidden');

                        if (data.success) {
                            Swal.fire({
                                title: 'Sukses!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#059669',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = 'planted.php';
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        submitBtn.disabled = false;
                        spinner.classList.add('hidden');
                        Swal.fire('Error', 'Terjadi kesalahan koneksi', 'error');
                    });
            });

            // ============================================================
            // DELETE PLANTING
            // ============================================================
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
                        const formData = new FormData();
                        formData.append('id', id);

                        fetch('../controllers/adminController.php?action=delete_planting', {
                            method: 'POST',
                            body: formData
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Terhapus!',
                                        text: data.message,
                                        icon: 'success',
                                        confirmButtonColor: '#059669'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire('Error', data.message, 'error');
                                }
                            })
                            .catch(err => {
                                Swal.fire('Error', 'Terjadi kesalahan koneksi', 'error');
                            });
                    }
                });
            }

            // ============================================================
            // CLIENT-SIDE SEARCH & FILTER
            // ============================================================
            function applyClientFilters() {
                const search = (document.getElementById('searchInput')?.value || '').toLowerCase();
                const campaign = document.getElementById('campaignFilter')?.value || 'all';
                const status = document.getElementById('statusFilter')?.value || 'all';
                const cards = document.querySelectorAll('.planting-card');

                cards.forEach(card => {
                    const matchSearch = !search || (card.dataset.search || '').includes(search);
                    const matchCampaign = campaign === 'all' || card.dataset.campaign === campaign;
                    const matchStatus = status === 'all' || card.dataset.status === status;
                    card.style.display = (matchSearch && matchCampaign && matchStatus) ? '' : 'none';
                });
            }

            document.getElementById('searchInput')?.addEventListener('input', applyClientFilters);
            document.getElementById('campaignFilter')?.addEventListener('change', applyClientFilters);
            document.getElementById('statusFilter')?.addEventListener('change', applyClientFilters);

            function resetFilters() {
                const searchEl = document.getElementById('searchInput');
                const campEl = document.getElementById('campaignFilter');
                const statEl = document.getElementById('statusFilter');
                if (searchEl) searchEl.value = '';
                if (campEl) campEl.value = 'all';
                if (statEl) statEl.value = 'all';
                applyClientFilters();
            }

            // ============================================================
            // IMAGE PREVIEW
            // ============================================================
            document.getElementById('imageInput')?.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const preview = document.getElementById('imagePreview');
                        const placeholder = document.getElementById('uploadPlaceholder');
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        placeholder.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }); // end DOMContentLoaded
    </script>
</body>

</html>