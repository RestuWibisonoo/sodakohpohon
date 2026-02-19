<?php
// admin/donations.php - Data Donasi
session_start();
require_once '../config/koneksi.php';
require_once '../models/Donation.php';
require_once '../models/Campaign.php';

// Cek autentikasi
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$donationModel = new Donation();
$campaignModel = new Campaign();

// Filter by status
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$campaign_filter = isset($_GET['campaign']) ? $_GET['campaign'] : 'all';

// Ambil data donasi dari database
$status_param = ($status_filter != 'all') ? $status_filter : null;
$campaign_param = ($campaign_filter != 'all') ? $campaign_filter : null;
$raw_donations = $donationModel->getAll($status_param, $campaign_param);

// Map DB fields ke template keys
$donations = [];
foreach ($raw_donations as $d) {
    $donations[] = [
        'id' => $d['donation_number'],
        'db_id' => $d['id'],
        'donor_name' => $d['donor_name'],
        'donor_email' => $d['donor_email'] ?? '-',
        'campaign' => $d['campaign_title'] ?? '-',
        'trees' => $d['trees_count'],
        'amount' => $d['amount'],
        'status' => $d['status'],
        'payment_method' => $d['payment_method'] ?? '-',
        'date' => $d['created_at'],
        'anonymous' => $d['anonymous'] ? true : false,
        'message' => $d['message'] ?? '',
        'donor_phone' => $d['donor_phone'] ?? '-',
    ];
}

// Calculate totals
$total_donations = count($donations);
$total_amount = array_sum(array_column($donations, 'amount'));
$total_trees = array_sum(array_column($donations, 'trees'));
$paid_donations = count(array_filter($donations, fn($d) => $d['status'] == 'paid'));
$pending_donations = count(array_filter($donations, fn($d) => $d['status'] == 'pending'));
$failed_donations = count(array_filter($donations, fn($d) => in_array($d['status'], ['failed', 'cancelled'])));

// Ambil daftar campaign untuk filter dropdown
$all_campaigns = $campaignModel->getAll();

$current_page = 'donations';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Donasi - Sodakoh Pohon</title>

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

        .status-badge {
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }

        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-cancelled {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .table-header {
            background-color: #f9fafb;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
        }

        .table-row {
            transition: all 0.2s ease;
        }

        .table-row:hover {
            background-color: #f9fafb;
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
                    <h1 class="text-2xl font-bold text-gray-900">Data Donasi</h1>

                    <div class="flex items-center space-x-4">
                        <button onclick="exportData()"
                            class="bg-white border-2 border-primary-600 text-primary-700 px-5 py-2.5 rounded-xl hover:bg-primary-50 transition flex items-center">
                            <i class="fas fa-file-export mr-2"></i>
                            Export CSV
                        </button>
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
                <!-- Stats Summary -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-hand-holding-heart text-primary-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-gray-500">Total</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Total Donasi</p>
                        <p class="text-2xl font-extrabold text-gray-900">
                            <?php echo number_format($total_donations); ?>
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-emerald-600 text-xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Total Nominal</p>
                        <p class="text-2xl font-extrabold text-gray-900">
                            Rp
                            <?php echo number_format($total_amount); ?>
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600">
                                <?php echo $paid_donations; ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Donasi Sukses</p>
                        <p class="text-2xl font-extrabold text-gray-900">
                            <?php echo number_format($paid_donations); ?>
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-yellow-600">
                                <?php echo $pending_donations; ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Menunggu</p>
                        <p class="text-2xl font-extrabold text-gray-900">
                            <?php echo number_format($pending_donations); ?>
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-tree text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Total Pohon</p>
                        <p class="text-2xl font-extrabold text-gray-900">
                            <?php echo number_format($total_trees); ?>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">dari donasi</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-1 relative">
                            <i
                                class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" placeholder="Cari donatur atau ID donasi..." id="searchInput"
                                class="pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none w-full">
                        </div>

                        <div class="flex items-center gap-3">
                            <select id="statusFilter"
                                class="px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none">
                                <option value="all" <?php echo $status_filter=='all' ? 'selected' : '' ; ?>>Semua Status
                                </option>
                                <option value="paid" <?php echo $status_filter=='paid' ? 'selected' : '' ; ?>>Sukses
                                </option>
                                <option value="pending" <?php echo $status_filter=='pending' ? 'selected' : '' ; ?>
                                    >Pending</option>
                                <option value="failed" <?php echo $status_filter=='failed' ? 'selected' : '' ; ?>>Gagal
                                </option>
                                <option value="cancelled" <?php echo $status_filter=='cancelled' ? 'selected' : '' ; ?>
                                    >Dibatalkan</option>
                            </select>

                            <select id="campaignFilter"
                                class="px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none">
                                <option value="all">Semua Campaign</option>
                                <?php foreach ($all_campaigns as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo $campaign_filter==$c['id']
                                    ? 'selected' : '' ; ?>>
                                    <?php echo htmlspecialchars($c['title']); ?>
                                </option>
                                <?php
endforeach; ?>
                            </select>

                            <button onclick="resetFilters()"
                                class="px-4 py-3 text-gray-600 hover:text-gray-900 border border-gray-200 rounded-xl hover:bg-gray-50 transition"
                                title="Reset Filter">
                                <i class="fas fa-undo-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Donations Table -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="table-header">
                                <tr>
                                    <th class="px-6 py-4 text-left">ID Donasi</th>
                                    <th class="px-6 py-4 text-left">Donatur</th>
                                    <th class="px-6 py-4 text-left">Campaign</th>
                                    <th class="px-6 py-4 text-left">Jumlah Pohon</th>
                                    <th class="px-6 py-4 text-left">Nominal</th>
                                    <th class="px-6 py-4 text-left">Metode</th>
                                    <th class="px-6 py-4 text-left">Status</th>
                                    <th class="px-6 py-4 text-left">Tanggal</th>
                                    <th class="px-6 py-4 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($donations)): ?>
                                <tr>
                                    <td colspan="9" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-inbox text-gray-300 text-3xl"></i>
                                            </div>
                                            <p class="text-gray-500 text-lg font-medium mb-1">Belum ada donasi</p>
                                            <p class="text-gray-400 text-sm">Data donasi akan muncul di sini saat ada
                                                donatur yang berdonasi</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php
else: ?>
                                <?php foreach ($donations as $donation): ?>
                                <tr class="table-row donation-row"
                                    data-search="<?php echo htmlspecialchars(strtolower($donation['donor_name'] . ' ' . $donation['id'] . ' ' . $donation['donor_email'])); ?>">
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($donation['id']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-primary-100 to-primary-200 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-bold text-primary-700">
                                                    <?php echo $donation['anonymous'] ? 'A' : htmlspecialchars(substr($donation['donor_name'], 0, 1)); ?>
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">
                                                    <?php echo $donation['anonymous'] ? 'Anonymous' : htmlspecialchars($donation['donor_name']); ?>
                                                </p>
                                                <?php if (!$donation['anonymous']): ?>
                                                <p class="text-xs text-gray-500">
                                                    <?php echo htmlspecialchars($donation['donor_email']); ?>
                                                </p>
                                                <?php
        endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?php echo htmlspecialchars($donation['campaign']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-gray-900">
                                            <?php echo $donation['trees']; ?> pohon
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-gray-900">
                                            Rp
                                            <?php echo number_format($donation['amount']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?php echo htmlspecialchars($donation['payment_method']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php
        $status = $donation['status'];
        $statusClass = 'status-pending';
        $statusIcon = 'clock';
        $statusLabel = 'Pending';
        if ($status == 'paid') {
            $statusClass = 'status-paid';
            $statusIcon = 'check-circle';
            $statusLabel = 'Sukses';
        }
        elseif ($status == 'failed') {
            $statusClass = 'status-failed';
            $statusIcon = 'times-circle';
            $statusLabel = 'Gagal';
        }
        elseif ($status == 'cancelled') {
            $statusClass = 'status-cancelled';
            $statusIcon = 'ban';
            $statusLabel = 'Dibatalkan';
        }
?>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <i class="fas fa-<?php echo $statusIcon; ?> mr-1"></i>
                                            <?php echo $statusLabel; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php echo date('d/m/Y H:i', strtotime($donation['date'])); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <button onclick="viewDetail(<?php echo $donation['db_id']; ?>)"
                                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($donation['status'] == 'pending'): ?>
                                            <button onclick="confirmPayment(<?php echo $donation['db_id']; ?>)"
                                                class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition"
                                                title="Konfirmasi Pembayaran">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                            <button onclick="cancelDonation(<?php echo $donation['db_id']; ?>)"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition"
                                                title="Batalkan Donasi">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                            <?php
        endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
    endforeach; ?>
                                <?php
endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                        <span class="text-sm text-gray-600">
                            Menampilkan
                            <?php echo count($donations); ?> donasi
                        </span>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Donation data for detail view
        const donationsData = <?php echo json_encode($donations); ?>;

        // View donation detail — uses db_id
        function viewDetail(dbId) {
            const donation = donationsData.find(d => d.db_id == dbId);

            if (donation) {
                const statusMap = {
                    'paid': '<span class="status-badge status-paid"><i class="fas fa-check-circle mr-1"></i>Sukses</span>',
                    'pending': '<span class="status-badge status-pending"><i class="fas fa-clock mr-1"></i>Pending</span>',
                    'failed': '<span class="status-badge status-failed"><i class="fas fa-times-circle mr-1"></i>Gagal</span>',
                    'cancelled': '<span class="status-badge status-cancelled"><i class="fas fa-ban mr-1"></i>Dibatalkan</span>'
                };

                Swal.fire({
                    title: 'Detail Donasi',
                    html: `
                        <div class="text-left space-y-3">
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <p class="text-gray-500">ID Donasi:</p>
                                <p class="font-mono font-medium">${donation.id}</p>
                                <p class="text-gray-500">Donatur:</p>
                                <p class="font-medium">${donation.anonymous ? 'Anonymous' : donation.donor_name}</p>
                                <p class="text-gray-500">Email:</p>
                                <p>${donation.donor_email}</p>
                                <p class="text-gray-500">Telepon:</p>
                                <p>${donation.donor_phone}</p>
                                <p class="text-gray-500">Campaign:</p>
                                <p>${donation.campaign}</p>
                                <p class="text-gray-500">Jumlah Pohon:</p>
                                <p class="font-semibold">${donation.trees} pohon</p>
                                <p class="text-gray-500">Total Donasi:</p>
                                <p class="font-bold text-primary-700">Rp ${Number(donation.amount).toLocaleString('id-ID')}</p>
                                <p class="text-gray-500">Metode:</p>
                                <p>${donation.payment_method}</p>
                                <p class="text-gray-500">Status:</p>
                                <div>${statusMap[donation.status] || donation.status}</div>
                                <p class="text-gray-500">Tanggal:</p>
                                <p>${donation.date}</p>
                            </div>
                            ${donation.message ? '<div class="mt-3 p-3 bg-gray-50 rounded-lg"><p class="text-xs text-gray-500 mb-1">Pesan:</p><p class="text-sm">' + donation.message + '</p></div>' : ''}
                        </div>
                    `,
                    confirmButtonColor: '#059669',
                    confirmButtonText: 'Tutup',
                    width: '480px'
                });
            }
        }

        // Confirm payment — uses db_id (integer)
        function confirmPayment(dbId) {
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                text: 'Konfirmasi donasi ini sebagai sukses?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Konfirmasi',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', dbId);

                    fetch('../controllers/adminController.php?action=confirm_donation', {
                        method: 'POST',
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Sukses!',
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

        // Cancel donation — uses db_id (integer)
        function cancelDonation(dbId) {
            Swal.fire({
                title: 'Batalkan Donasi?',
                text: 'Donasi ini akan dibatalkan. Tindakan ini tidak bisa dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Kembali'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', dbId);

                    fetch('../controllers/adminController.php?action=cancel_donation', {
                        method: 'POST',
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Dibatalkan!',
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

        // Export data
        function exportData() {
            Swal.fire({
                title: 'Export Data Donasi',
                text: 'Data akan diexport dalam format CSV',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Export',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../controllers/adminController.php?action=export_donations';
                }
            });
        }

        // Apply filters (server-side via URL params)
        function applyFilters() {
            const status = document.getElementById('statusFilter').value;
            const campaign = document.getElementById('campaignFilter').value;
            let url = 'donations.php?';
            if (status !== 'all') url += 'status=' + status + '&';
            if (campaign !== 'all') url += 'campaign=' + campaign + '&';
            window.location.href = url;
        }

        // Filter event listeners
        document.getElementById('statusFilter').addEventListener('change', applyFilters);
        document.getElementById('campaignFilter').addEventListener('change', applyFilters);

        // Reset filters
        function resetFilters() {
            window.location.href = 'donations.php';
        }

        // Client-side search (filter table rows)
        document.getElementById('searchInput').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            const rows = document.querySelectorAll('.donation-row');

            rows.forEach(row => {
                const searchData = row.dataset.search || '';
                row.style.display = searchData.includes(term) ? '' : 'none';
            });
        });
    </script>
</body>

</html>