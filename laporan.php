<?php
// laporan.php - Halaman Transparansi dan Laporan Publik
require_once 'config/koneksi.php';

$conn = getDB();

// ─── STATISTIK OVERVIEW ──────────────────────────────────────────────────────

// Total donasi (amount) dari donation yang paid
$row = db_get_row("SELECT COALESCE(SUM(amount), 0) as total FROM donations WHERE status = 'paid'");
$total_donations = (float) ($row['total'] ?? 0);

// Total pohon terkumpul (trees_count dari donasi paid)
$row = db_get_row("SELECT COALESCE(SUM(trees_count), 0) as total FROM donations WHERE status = 'paid'");
$total_trees_collected = (int) ($row['total'] ?? 0);

// Total pohon tertanam (dari tabel plantings)
$row = db_get_row("SELECT COALESCE(SUM(trees_planted), 0) as total FROM plantings");
$total_trees_planted = (int) ($row['total'] ?? 0);

// Total donatur unik
$row = db_get_row("SELECT COUNT(DISTINCT donor_email) as total FROM donations WHERE status = 'paid' AND donor_email IS NOT NULL AND donor_email != ''");
$total_donors = (int) ($row['total'] ?? 0);

// Total campaign aktif
$row = db_get_row("SELECT COUNT(*) as total FROM campaigns WHERE status = 'active'");
$total_campaigns = (int) ($row['total'] ?? 0);

// Total lokasi unik dari plantings
$row = db_get_row("SELECT COUNT(DISTINCT location) as total FROM plantings");
$total_locations = (int) ($row['total'] ?? 0);

// ─── TREND DONASI PER BULAN (12 BULAN TERAKHIR) ──────────────────────────────

$monthly_raw = db_query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as ym,
        DATE_FORMAT(created_at, '%b') as month_label,
        COALESCE(SUM(amount), 0) as total
    FROM donations
    WHERE status = 'paid'
      AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY ym ASC
");

// Buat array 12 bulan terakhir sebagai kerangka (termasuk bulan yang nihil)
$monthly_donations = [];
$monthly_labels = [];
for ($i = 11; $i >= 0; $i--) {
    $key = date('Y-m', strtotime("-$i months"));
    $label = date('M', strtotime("-$i months"));
    $monthly_donations[$key] = 0;
    $monthly_labels[$key] = $label;
}
foreach ($monthly_raw as $r) {
    if (isset($monthly_donations[$r['ym']])) {
        $monthly_donations[$r['ym']] = (float) $r['total'];
    }
}

// ─── DATA POHON PER CAMPAIGN ──────────────────────────────────────────────────

$campaign_trees_raw = db_query("
    SELECT 
        c.title as name,
        c.target_trees as target,
        c.current_trees as collected,
        COALESCE(SUM(p.trees_planted), 0) as planted
    FROM campaigns c
    LEFT JOIN plantings p ON p.campaign_id = c.id
    GROUP BY c.id
    ORDER BY c.created_at DESC
");

$campaign_trees = [];
foreach ($campaign_trees_raw as $row) {
    $campaign_trees[] = [
        'name' => $row['name'],
        'target' => (int) $row['target'],
        'collected' => (int) $row['collected'],
        'planted' => (int) $row['planted'],
    ];
}

// ─── DOKUMENTASI PENANAMAN (dari tabel plantings + campaigns) ─────────────────

$planting_documentations_raw = db_query("
    SELECT 
        p.id,
        p.planting_date as date,
        c.title as campaign,
        p.location,
        p.trees_planted,
        p.volunteers,
        p.image,
        p.description,
        p.status
    FROM plantings p
    JOIN campaigns c ON c.id = p.campaign_id
    ORDER BY p.planting_date DESC
");

$planting_documentations = [];
foreach ($planting_documentations_raw as $row) {
    $planting_documentations[] = [
        'id' => $row['id'],
        'date' => $row['date'],
        'campaign' => $row['campaign'],
        'location' => $row['location'],
        'trees_planted' => (int) $row['trees_planted'],
        'volunteers' => (int) $row['volunteers'],
        'image' => $row['image'],
        'description' => $row['description'],
        'status' => $row['status'],
    ];
}

// ─── LAPORAN KEUANGAN ─────────────────────────────────────────────────────────

$finance = db_get_row("
    SELECT 
        COALESCE(SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END), 0) as total_paid,
        COALESCE(SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END), 0) as total_pending
    FROM donations
");
$finance_total_paid = (float) ($finance['total_paid'] ?? 0);
$finance_total_pending = (float) ($finance['total_pending'] ?? 0);
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="pt-32 pb-16 bg-gradient-to-b from-primary-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
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
            <div class="stat-card bg-white rounded-2xl p-6 shadow-card">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-tree text-primary-600 text-xl"></i>
                </div>
                <p class="text-sm text-gray-500 mb-1">Total Pohon Terkumpul</p>
                <p class="text-2xl md:text-3xl font-extrabold text-gray-900">
                    <?php echo number_format($total_trees_collected); ?>
                </p>
                <p class="text-xs text-gray-400 mt-2">sepanjang masa</p>
            </div>

            <div class="stat-card bg-white rounded-2xl p-6 shadow-card">
                <div class="w-12 h-12 bg-earth-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-seedling text-earth-600 text-xl"></i>
                </div>
                <p class="text-sm text-gray-500 mb-1">Pohon Tertanam</p>
                <p class="text-2xl md:text-3xl font-extrabold text-gray-900">
                    <?php echo number_format($total_trees_planted); ?>
                </p>
                <p class="text-xs text-gray-400 mt-2">realisasi penanaman</p>
            </div>

            <div class="stat-card bg-white rounded-2xl p-6 shadow-card">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <p class="text-sm text-gray-500 mb-1">Total Donatur</p>
                <p class="text-2xl md:text-3xl font-extrabold text-gray-900"><?php echo number_format($total_donors); ?>
                </p>
                <p class="text-xs text-gray-400 mt-2">orang telah berdonasi</p>
            </div>

            <div class="stat-card bg-white rounded-2xl p-6 shadow-card">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-map-marker-alt text-purple-600 text-xl"></i>
                </div>
                <p class="text-sm text-gray-500 mb-1">Lokasi Tanam</p>
                <p class="text-2xl md:text-3xl font-extrabold text-gray-900"><?php echo $total_locations; ?></p>
                <p class="text-xs text-gray-400 mt-2">tersebar di Indonesia</p>
            </div>

            <div class="stat-card bg-white rounded-2xl p-6 shadow-card">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-hand-holding-heart text-yellow-600 text-xl"></i>
                </div>
                <p class="text-sm text-gray-500 mb-1">Total Donasi</p>
                <p class="text-2xl md:text-3xl font-extrabold text-gray-900">Rp
                    <?php echo number_format($total_donations / 1000000, 1); ?>M
                </p>
                <p class="text-xs text-gray-400 mt-2">terkumpul</p>
            </div>
        </div>
    </div>
</section>

<!-- Charts Section -->
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Tabs -->
        <div class="border-b border-gray-200 mb-8">
            <div class="flex space-x-8 overflow-x-auto">
                <button onclick="showTab('overview')"
                    class="tab-btn active px-1 py-4 text-sm font-medium border-b-2 border-primary-600 text-primary-700 whitespace-nowrap">
                    <i class="fas fa-chart-pie mr-2"></i>Overview
                </button>
                <button onclick="showTab('donations')"
                    class="tab-btn px-1 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">
                    <i class="fas fa-chart-line mr-2"></i>Trend Donasi
                </button>
                <button onclick="showTab('campaigns')"
                    class="tab-btn px-1 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">
                    <i class="fas fa-chart-bar mr-2"></i>Per Campaign
                </button>
                <button onclick="showTab('impact')"
                    class="tab-btn px-1 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">
                    <i class="fas fa-leaf mr-2"></i>Dampak Lingkungan
                </button>
            </div>
        </div>

        <!-- Tab Content -->
        <div id="overview-tab" class="tab-content block">
            <div class="grid lg:grid-cols-2 gap-8">
                <!-- Donut Chart - Distribusi Campaign -->
                <div class="bg-white rounded-2xl shadow-card p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Distribusi Pohon per Campaign</h3>
                    <div class="h-80 relative">
                        <canvas id="campaignDistributionChart"></canvas>
                    </div>
                    <div class="mt-4 text-sm text-gray-500 text-center">
                        Total <?php echo number_format($total_trees_collected); ?> pohon dari
                        <?php echo $total_campaigns; ?> campaign aktif
                    </div>
                </div>

                <!-- Progress Overview -->
                <div class="bg-white rounded-2xl shadow-card p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Progress Realisasi Penanaman</h3>
                    <div class="space-y-6">
                        <?php foreach (array_slice($campaign_trees, 0, 4) as $campaign):
                            $progress = $campaign['collected'] > 0 ? ($campaign['planted'] / $campaign['collected']) * 100 : 0;
                            $progress = min($progress, 100);
                            ?>
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-900"><?php echo $campaign['name']; ?></span>
                                    <span class="text-sm text-gray-600">
                                        <?php echo number_format($campaign['planted']); ?>/<?php echo number_format($campaign['collected']); ?>
                                        pohon
                                    </span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-xs text-gray-500">Tertanam</span>
                                    <span class="text-xs font-semibold text-primary-700"><?php echo round($progress); ?>%
                                        terealisasi</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Rata-rata realisasi</span>
                            <?php
                            $total_col = array_sum(array_column($campaign_trees, 'collected'));
                            $total_plt = array_sum(array_column($campaign_trees, 'planted'));
                            $avg_real = $total_col > 0 ? round(($total_plt / $total_col) * 100, 1) : 0;
                            ?>
                            <span class="font-bold text-primary-700"><?php echo $avg_real; ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="donations-tab" class="tab-content hidden">
            <div class="bg-white rounded-2xl shadow-card p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Trend Donasi 12 Bulan Terakhir</h3>
                    <div class="flex items-center space-x-2 mt-2 md:mt-0">
                        <span class="text-sm text-gray-500">Total: </span>
                        <span class="font-bold text-primary-700">Rp
                            <?php echo number_format($total_donations); ?></span>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="donationTrendChart"></canvas>
                </div>
                <?php
                $monthly_vals = array_values($monthly_donations);
                $monthly_nonzero = array_filter($monthly_vals, fn($v) => $v > 0);
                $max_monthly = !empty($monthly_nonzero) ? max($monthly_nonzero) : 0;
                $avg_monthly = !empty($monthly_nonzero) ? array_sum($monthly_nonzero) / count($monthly_nonzero) : 0;
                $avg_per_donor = $total_donors > 0 ? $total_donations / $total_donors : 0;
                // Bulan dengan donasi tertinggi
                $max_key = !empty($monthly_nonzero) ? array_search($max_monthly, $monthly_donations) : null;
                $max_month_label = $max_key ? date('F Y', strtotime($max_key . '-01')) : '-';
                $total_12m = array_sum($monthly_nonzero);
                ?>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-sm text-gray-500 mb-1">Donasi Tertinggi/Bln</p>
                        <p class="font-bold text-gray-900">Rp
                            <?php echo number_format($max_monthly / 1000000, 1, ',', '.'); ?> Jt
                        </p>
                        <p class="text-xs text-gray-400"><?php echo $max_month_label; ?></p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500 mb-1">Rata-rata/Bulan</p>
                        <p class="font-bold text-gray-900">Rp
                            <?php echo number_format($avg_monthly / 1000000, 1, ',', '.'); ?> Jt
                        </p>
                        <p class="text-xs text-gray-400">12 bulan terakhir</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500 mb-1">Total 12 Bulan</p>
                        <p class="font-bold text-green-600">Rp
                            <?php echo number_format($total_12m / 1000000, 1, ',', '.'); ?> Jt
                        </p>
                        <p class="text-xs text-gray-400">donasi terverifikasi</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500 mb-1">Rata-rata/Donatur</p>
                        <p class="font-bold text-gray-900">Rp <?php echo number_format($avg_per_donor); ?></p>
                        <p class="text-xs text-gray-400">per donatur unik</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="campaigns-tab" class="tab-content hidden">
            <div class="bg-white rounded-2xl shadow-card p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Perbandingan Pohon per Campaign</h3>
                <div class="h-80">
                    <canvas id="campaignComparisonChart"></canvas>
                </div>
                <div class="mt-8 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Campaign</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Target</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Terkumpul</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tertanam</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Progress</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($campaign_trees as $campaign):
                                $collected_percent = ($campaign['collected'] / $campaign['target']) * 100;
                                ?>
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo $campaign['name']; ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        <?php echo number_format($campaign['target']); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        <?php echo number_format($campaign['collected']); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        <?php echo number_format($campaign['planted']); ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <span
                                                class="text-sm font-semibold text-primary-700 mr-2"><?php echo round($collected_percent); ?>%</span>
                                            <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-primary-600 h-1.5 rounded-full"
                                                    style="width: <?php echo $collected_percent; ?>%"></div>
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
                <div class="bg-white rounded-2xl shadow-card p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Dampak Lingkungan</h3>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between p-4 bg-primary-50 rounded-xl">
                            <div class="flex items-center">
                                <div
                                    class="w-12 h-12 bg-primary-200 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-cloud-sun text-primary-700 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Penyerapan CO₂</p>
                                    <p class="text-2xl font-bold text-gray-900">218.7 ton</p>
                                </div>
                            </div>
                            <span class="text-xs text-primary-700 bg-primary-100 px-3 py-1 rounded-full">+45% dari
                                target</span>
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
                            <span class="text-xs text-yellow-700 bg-yellow-100 px-3 py-1 rounded-full">petani &
                                relawan</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-card p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">SDGs Contribution</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span
                                    class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white text-sm mr-3">13</span>
                                <span class="text-gray-700">Penanganan Perubahan Iklim</span>
                            </div>
                            <span class="font-bold text-gray-900">218.7t CO₂</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span
                                    class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm mr-3">14</span>
                                <span class="text-gray-700">Ekosistem Laut</span>
                            </div>
                            <span class="font-bold text-gray-900">3.2km²</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span
                                    class="w-8 h-8 bg-green-800 rounded-full flex items-center justify-center text-white text-sm mr-3">15</span>
                                <span class="text-gray-700">Ekosistem Daratan</span>
                            </div>
                            <span class="font-bold text-gray-900">12.4km²</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span
                                    class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center text-white text-sm mr-3">11</span>
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
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Dokumentasi Penanaman</h2>
            <p class="text-lg text-gray-600">
                Bukti nyata penanaman pohon dari donasi Anda. Kami selalu update dokumentasi setiap kali melakukan
                penanaman.
            </p>
        </div>

        <?php if (empty($planting_documentations)): ?>
            <div class="col-span-4 text-center py-16 text-gray-400">
                <i class="fas fa-seedling text-5xl mb-4 block"></i>
                <p>Belum ada data dokumentasi penanaman.</p>
            </div>
        <?php else: ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($planting_documentations as $doc):
                    // Tentukan URL gambar: bisa upload lokal atau URL eksternal
                    $imgSrc = '';
                    if (!empty($doc['image'])) {
                        // Jika sudah berupa URL lengkap (http/https)
                        if (strpos($doc['image'], 'http') === 0) {
                            $imgSrc = $doc['image'];
                        } else {
                            $imgSrc = BASE_URL . '/' . ltrim($doc['image'], '/');
                        }
                    } else {
                        $imgSrc = 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=600&q=80';
                    }
                    // Label status
                    $statusMap = [
                        'completed' => ['label' => 'Selesai', 'cls' => 'bg-green-100 text-green-700'],
                        'scheduled' => ['label' => 'Terjadwal', 'cls' => 'bg-blue-100 text-blue-700'],
                        'cancelled' => ['label' => 'Dibatalkan', 'cls' => 'bg-red-100 text-red-700']
                    ];
                    $statusInfo = $statusMap[$doc['status']] ?? ['label' => $doc['status'], 'cls' => 'bg-gray-100 text-gray-600'];
                    ?>
                    <div class="documentation-card bg-white rounded-2xl shadow-card overflow-hidden group">
                        <div class="relative h-48 overflow-hidden">
                            <img src="<?php echo htmlspecialchars($imgSrc); ?>"
                                alt="<?php echo htmlspecialchars($doc['campaign']); ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                onerror="this.src='https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=600&q=80'">
                            <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm rounded-lg px-3 py-1">
                                <span
                                    class="text-xs font-bold text-primary-700"><?php echo date('d M Y', strtotime($doc['date'])); ?></span>
                            </div>
                            <div class="absolute top-3 right-3">
                                <span class="text-xs font-semibold px-2 py-1 rounded-full <?php echo $statusInfo['cls']; ?>">
                                    <?php echo $statusInfo['label']; ?>
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 mb-1 line-clamp-1">
                                <?php echo htmlspecialchars($doc['campaign']); ?>
                            </h3>
                            <p class="text-xs text-gray-500 mb-2">
                                <i class="fas fa-map-marker-alt mr-1 text-primary-600"></i>
                                <?php echo htmlspecialchars($doc['location']); ?>
                            </p>
                            <?php if (!empty($doc['description'])): ?>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                    <?php echo htmlspecialchars($doc['description']); ?>
                                </p>
                            <?php endif; ?>
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
        <?php endif; ?>

        <div class="text-center mt-10">
            <button
                class="inline-flex items-center px-6 py-3 border-2 border-primary-600 text-primary-700 font-semibold rounded-xl hover:bg-primary-50 transition">
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
        <div class="bg-white rounded-2xl shadow-card p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Laporan Keuangan</h2>
                    <p class="text-gray-600">Transparansi penggunaan dana donasi</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <button
                        class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition text-sm">
                        <i class="fas fa-download mr-2"></i>
                        Unduh Laporan (PDF)
                    </button>
                </div>
            </div>

            <?php
            $finance_pending = $finance_total_pending;
            $finance_paid = $finance_total_paid;
            $finance_pct = $finance_paid > 0 ? round(($finance_paid / ($finance_paid + $finance_pending)) * 100, 1) : 0;
            ?>
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-50 rounded-xl p-5">
                    <p class="text-sm text-gray-500 mb-1">Total Dana Terkumpul</p>
                    <p class="text-2xl font-bold text-gray-900">Rp
                        <?php echo number_format(($finance_paid + $finance_pending) / 1000000, 2, ',', '.'); ?> Juta
                    </p>
                    <p class="text-xs text-gray-400 mt-2">Per <?php echo date('d M Y'); ?></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-5">
                    <p class="text-sm text-gray-500 mb-1">Dana Terverifikasi (Paid)</p>
                    <p class="text-2xl font-bold text-gray-900">Rp
                        <?php echo number_format($finance_paid / 1000000, 2, ',', '.'); ?> Juta
                    </p>
                    <p class="text-xs text-green-600 mt-2"><?php echo $finance_pct; ?>% dari total terkumpul</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-5">
                    <p class="text-sm text-gray-500 mb-1">Dana Menunggu Verifikasi</p>
                    <p class="text-2xl font-bold text-gray-900">Rp
                        <?php echo number_format($finance_pending / 1000000, 2, ',', '.'); ?> Juta
                    </p>
                    <p class="text-xs text-gray-400 mt-2">status pending</p>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h4 class="font-semibold text-gray-900 mb-2">Ringkasan Donasi per Campaign</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-gray-500 uppercase border-b">
                                <th class="pb-2 pr-4">Campaign</th>
                                <th class="pb-2 pr-4 text-right">Donasi Paid</th>
                                <th class="pb-2 text-right">Jumlah Donatur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $fin_rows = db_query("
                            SELECT c.title, 
                                   COALESCE(SUM(CASE WHEN d.status='paid' THEN d.amount ELSE 0 END),0) as total_paid,
                                   COUNT(CASE WHEN d.status='paid' THEN 1 END) as donors
                            FROM campaigns c
                            LEFT JOIN donations d ON d.campaign_id = c.id
                            GROUP BY c.id
                            ORDER BY total_paid DESC
                        ");
                            foreach ($fin_rows as $fr): ?>
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 pr-4 font-medium text-gray-800">
                                        <?php echo htmlspecialchars($fr['title']); ?>
                                    </td>
                                    <td class="py-2 pr-4 text-right text-gray-700">Rp
                                        <?php echo number_format($fr['total_paid']); ?>
                                    </td>
                                    <td class="py-2 text-right text-gray-500"><?php echo $fr['donors']; ?> orang</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- AOS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<!-- Fallback: jika AOS belum trigger, konten tetap terlihat -->
<style>
    [data-aos] {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
    }
</style>
<script>
    if (typeof AOS !== 'undefined') {
        // Reset fallback jika AOS berhasil load
        document.querySelectorAll('style').forEach(s => {
            if (s.textContent.includes('[data-aos]')) s.remove();
        });
        AOS.init({
            duration: 800,
            once: true
        });
    }

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
    window.addEventListener('load', function () {
        // ─── Data dari PHP untuk semua chart ───────────────────────────────────
        const campaignNames = <?php echo json_encode(array_column($campaign_trees, 'name')); ?>;
        const campaignCollected = <?php echo json_encode(array_column($campaign_trees, 'collected')); ?>;
        const campaignPlanted = <?php echo json_encode(array_column($campaign_trees, 'planted')); ?>;
        const monthLabels = <?php echo json_encode(array_values($monthly_labels)); ?>;
        const monthlyData = <?php echo json_encode(array_values($monthly_donations)); ?>;

        // Warna gradasi hijau untuk chart
        const greenPalette = ['#059669', '#10b981', '#34d399', '#6ee7b7', '#a7f3d0', '#d1fae5', '#ecfdf5', '#f0fdf4', '#bbf7d0', '#86efac', '#4ade80', '#22c55e'];

        // ─── Campaign Distribution Chart (Donut) ───────────────────────────────
        const ctx1 = document.getElementById('campaignDistributionChart').getContext('2d');
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: campaignNames,
                datasets: [{
                    data: campaignCollected,
                    backgroundColor: greenPalette.slice(0, campaignNames.length),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, padding: 15 }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed.toLocaleString('id-ID')} pohon`
                        }
                    }
                }
            }
        });

        // ─── Donation Trend Chart (Line) ───────────────────────────────────────
        const ctx2 = document.getElementById('donationTrendChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Donasi (Rupiah)',
                    data: monthlyData,
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
                            label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => 'Rp ' + (value / 1000000).toFixed(1) + 'jt'
                        }
                    }
                }
            }
        });

        // ─── Campaign Comparison Chart (Bar) ───────────────────────────────────
        const ctx3 = document.getElementById('campaignComparisonChart').getContext('2d');
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: campaignNames,
                datasets: [
                    {
                        label: 'Terkumpul (pohon)',
                        data: campaignCollected,
                        backgroundColor: '#10b981',
                        borderRadius: 8
                    },
                    {
                        label: 'Tertanam (pohon)',
                        data: campaignPlanted,
                        backgroundColor: '#f59e0b',
                        borderRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Jumlah Pohon' }
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
    window.addEventListener('scroll', function () {
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