<?php
// admin/donations.php - Data Donasi
session_start();

// Simulasi data donasi
$donations = [
    [
        'id' => 'DON-20260228-001',
        'donor_name' => 'Ahmad Fauzi',
        'donor_email' => 'ahmad.fauzi@email.com',
        'campaign' => 'Restorasi Mangrove Demak',
        'trees' => 10,
        'amount' => 100000,
        'status' => 'paid',
        'payment_method' => 'BCA Virtual Account',
        'date' => '2026-02-28 14:23:45',
        'anonymous' => false
    ],
    [
        'id' => 'DON-20260228-002',
        'donor_name' => 'Sarah Putri',
        'donor_email' => 'sarah.putri@email.com',
        'campaign' => 'Reboisasi Lereng Merapi',
        'trees' => 5,
        'amount' => 60000,
        'status' => 'paid',
        'payment_method' => 'GoPay',
        'date' => '2026-02-28 11:15:22',
        'anonymous' => false
    ],
    [
        'id' => 'DON-20260228-003',
        'donor_name' => 'Anonymous',
        'donor_email' => '-',
        'campaign' => 'Mangrove Pesisir Jakarta',
        'trees' => 3,
        'amount' => 30000,
        'status' => 'paid',
        'payment_method' => 'OVO',
        'date' => '2026-02-28 09:45:10',
        'anonymous' => true
    ],
    [
        'id' => 'DON-20260227-001',
        'donor_name' => 'Dewi Lestari',
        'donor_email' => 'dewi.lestari@email.com',
        'campaign' => 'Penghijauan Hutan Lombok',
        'trees' => 7,
        'amount' => 105000,
        'status' => 'paid',
        'payment_method' => 'Mandiri VA',
        'date' => '2026-02-27 16:30:50',
        'anonymous' => false
    ],
    [
        'id' => 'DON-20260227-002',
        'donor_name' => 'Budi Santoso',
        'donor_email' => 'budi.santoso@email.com',
        'campaign' => 'Restorasi Mangrove Demak',
        'trees' => 2,
        'amount' => 20000,
        'status' => 'paid',
        'payment_method' => 'BRI VA',
        'date' => '2026-02-27 13:12:30',
        'anonymous' => false
    ],
    [
        'id' => 'DON-20260226-001',
        'donor_name' => 'Rina Wijaya',
        'donor_email' => 'rina.wijaya@email.com',
        'campaign' => 'Hutan Pangan Kalimantan',
        'trees' => 4,
        'amount' => 100000,
        'status' => 'pending',
        'payment_method' => 'Transfer Bank',
        'date' => '2026-02-26 10:20:15',
        'anonymous' => false
    ],
    [
        'id' => 'DON-20260225-001',
        'donor_name' => 'Anonymous',
        'donor_email' => '-',
        'campaign' => 'Konservasi Hutan Papua',
        'trees' => 2,
        'amount' => 60000,
        'status' => 'paid',
        'payment_method' => 'BNI VA',
        'date' => '2026-02-25 08:45:33',
        'anonymous' => true
    ],
    [
        'id' => 'DON-20260224-001',
        'donor_name' => 'Hendra Kusuma',
        'donor_email' => 'hendra.k@email.com',
        'campaign' => 'Reboisasi Lereng Merapi',
        'trees' => 8,
        'amount' => 96000,
        'status' => 'paid',
        'payment_method' => 'Kartu Kredit',
        'date' => '2026-02-24 19:50:22',
        'anonymous' => false
    ]
];

// Filter by status
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$campaign_filter = isset($_GET['campaign']) ? $_GET['campaign'] : 'all';

// Calculate totals
$total_donations = count($donations);
$total_amount = array_sum(array_column($donations, 'amount'));
$total_trees = array_sum(array_column($donations, 'trees'));
$paid_donations = count(array_filter($donations, fn($d) => $d['status'] == 'paid'));
$pending_donations = count(array_filter($donations, fn($d) => $d['status'] == 'pending'));
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
                            <a href="donations.php" class="sidebar-link active flex items-center px-4 py-3 rounded-xl text-white bg-primary-600">
                                <i class="fas fa-hand-holding-heart w-6"></i>
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
                    <h1 class="text-2xl font-bold text-gray-900">Data Donasi</h1>
                    
                    <div class="flex items-center space-x-4">
                        <button onclick="exportData()" class="bg-white border-2 border-primary-600 text-primary-700 px-5 py-2.5 rounded-xl hover:bg-primary-50 transition flex items-center">
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
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-hand-holding-heart text-primary-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-gray-500">Total</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Total Donasi</p>
                        <p class="text-2xl font-extrabold text-gray-900"><?php echo number_format($total_donations); ?></p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600"><?php echo $paid_donations; ?></span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Donasi Sukses</p>
                        <p class="text-2xl font-extrabold text-gray-900"><?php echo number_format($paid_donations); ?></p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-yellow-600"><?php echo $pending_donations; ?></span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Menunggu</p>
                        <p class="text-2xl font-extrabold text-gray-900"><?php echo number_format($pending_donations); ?></p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-tree text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Total Pohon</p>
                        <p class="text-2xl font-extrabold text-gray-900"><?php echo number_format($total_trees); ?></p>
                        <p class="text-xs text-gray-400 mt-1">dari donasi</p>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" 
                                   placeholder="Cari donatur atau ID donasi..." 
                                   id="searchInput"
                                   class="pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none w-full">
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <select id="statusFilter" class="px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none">
                                <option value="all">Semua Status</option>
                                <option value="paid">Sukses</option>
                                <option value="pending">Pending</option>
                            </select>
                            
                            <select id="campaignFilter" class="px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none">
                                <option value="all">Semua Campaign</option>
                                <option value="Restorasi Mangrove Demak">Restorasi Mangrove Demak</option>
                                <option value="Reboisasi Lereng Merapi">Reboisasi Lereng Merapi</option>
                                <option value="Penghijauan Hutan Lombok">Penghijauan Hutan Lombok</option>
                                <option value="Mangrove Pesisir Jakarta">Mangrove Pesisir Jakarta</option>
                            </select>
                            
                            <button onclick="resetFilters()" class="px-4 py-3 text-gray-600 hover:text-gray-900 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
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
                                <?php foreach ($donations as $donation): ?>
                                <tr class="table-row">
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-sm font-medium text-gray-900">
                                            <?php echo $donation['id']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-primary-100 to-primary-200 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-bold text-primary-700">
                                                    <?php echo $donation['anonymous'] ? 'A' : substr($donation['donor_name'], 0, 1); ?>
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">
                                                    <?php echo $donation['anonymous'] ? 'Anonymous' : $donation['donor_name']; ?>
                                                </p>
                                                <?php if(!$donation['anonymous']): ?>
                                                <p class="text-xs text-gray-500"><?php echo $donation['donor_email']; ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?php echo $donation['campaign']; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-gray-900">
                                            <?php echo $donation['trees']; ?> pohon
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-gray-900">
                                            Rp <?php echo number_format($donation['amount']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?php echo $donation['payment_method']; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="status-badge <?php echo $donation['status'] == 'paid' ? 'status-paid' : 'status-pending'; ?>">
                                            <i class="fas fa-<?php echo $donation['status'] == 'paid' ? 'check-circle' : 'clock'; ?> mr-1"></i>
                                            <?php echo $donation['status'] == 'paid' ? 'Sukses' : 'Pending'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php echo date('d/m/Y H:i', strtotime($donation['date'])); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <button onclick="viewDetail('<?php echo $donation['id']; ?>')" 
                                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if($donation['status'] == 'pending'): ?>
                                            <button onclick="confirmPayment('<?php echo $donation['id']; ?>')" 
                                                    class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                        <span class="text-sm text-gray-600">
                            Menampilkan 1-<?php echo count($donations); ?> dari <?php echo count($donations); ?> donasi
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
                </div>
            </div>
        </main>
    </div>

    <script>
        // View donation detail
        function viewDetail(donationId) {
            Swal.fire({
                title: 'Detail Donasi',
                html: `
                    <div class="text-left">
                        <p class="mb-2"><strong>ID Donasi:</strong> ${donationId}</p>
                        <p class="mb-2"><strong>Donatur:</strong> Ahmad Fauzi</p>
                        <p class="mb-2"><strong>Campaign:</strong> Restorasi Mangrove Demak</p>
                        <p class="mb-2"><strong>Jumlah Pohon:</strong> 10 pohon</p>
                        <p class="mb-2"><strong>Total Donasi:</strong> Rp 100.000</p>
                        <p class="mb-2"><strong>Metode:</strong> BCA Virtual Account</p>
                        <p class="mb-2"><strong>Status:</strong> Sukses</p>
                        <p class="mb-2"><strong>Tanggal:</strong> 28/02/2026 14:23:45</p>
                    </div>
                `,
                confirmButtonColor: '#059669',
                confirmButtonText: 'Tutup'
            });
        }
        
        // Confirm payment
        function confirmPayment(donationId) {
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                text: `Konfirmasi donasi ${donationId} sebagai sukses?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Konfirmasi',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sukses!',
                        text: 'Donasi telah dikonfirmasi dan pohon akan ditambahkan ke campaign.',
                        icon: 'success',
                        confirmButtonColor: '#059669',
                        confirmButtonText: 'OK'
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
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data donasi berhasil diexport.',
                        icon: 'success',
                        confirmButtonColor: '#059669',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
        
        // Reset filters
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = 'all';
            document.getElementById('campaignFilter').value = 'all';
        }
    </script>
</body>
</html>