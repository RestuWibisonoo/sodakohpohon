<?php
// campaign-detail.php
require_once 'config/koneksi.php';
require_once 'models/Campaign.php';

// Validasi ID dari URL
$campaign_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($campaign_id <= 0) {
    header('Location: campaign.php');
    exit;
}

$campaignModel = new Campaign();
$campaign = $campaignModel->getById($campaign_id);

// Jika campaign tidak ditemukan, redirect ke halaman campaign
if (!$campaign) {
    header('Location: campaign.php');
    exit;
}

// Hitung nilai turunan
$progress        = $campaign['progress'] ?? 0;
$remaining_trees = $campaign['remaining_trees'] ?? 0;
$days_left       = $campaign['days_left'] ?? 0;
$benefits        = $campaign['benefits'] ?? [];  // array string
$gallery         = $campaign['gallery'] ?? [];   // array row dari campaign_gallery

// Gabungkan juga foto dari planting_gallery milik planting campaign ini
$conn = getDB();
$pg_sql = "SELECT pg.image_url, pg.caption 
           FROM planting_gallery pg 
           JOIN plantings p ON pg.planting_id = p.id 
           WHERE p.campaign_id = {$campaign_id} 
           ORDER BY pg.created_at DESC 
           LIMIT 20";
$pg_res = $conn->query($pg_sql);
if ($pg_res) {
    while ($row = $pg_res->fetch_assoc()) {
        $gallery[] = ['image_url' => $row['image_url'], 'caption' => $row['caption']];
    }
}

// Ambil campaign lain (aktif, bukan campaign ini) untuk sidebar
$other_campaigns = $campaignModel->getActiveCampaigns(4);
$other_campaigns = array_filter($other_campaigns, fn($c) => $c['id'] != $campaign_id);
$other_campaigns = array_slice(array_values($other_campaigns), 0, 3);

// Helper: resolusi path gambar ke URL
function campaignImageUrl($path) {
    if (empty($path)) return 'assets/images/campaign-default.png';
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
    return $path; // path relatif dari root publik
}

// Helper: konversi Google Maps share URL → embed src
// Ekstrak koordinat dari pola @lat,lng lalu buat URL embed
function mapEmbedUrl($map_url) {
    if (empty($map_url)) {
        // Default: Indonesia tengah
        return 'https://www.google.com/maps?q=-2.5,118.0&z=5&output=embed';
    }
    // Coba ekstrak @lat,lng dari URL (format umum Google Maps)
    if (preg_match('/@(-?[\d.]+),(-?[\d.]+)/', $map_url, $m)) {
        $lat = $m[1];
        $lng = $m[2];
        return "https://www.google.com/maps?q={$lat},{$lng}&z=15&output=embed";
    }
    // Fallback: Indonesia tengah
    return 'https://www.google.com/maps?q=-2.5,118.0&z=5&output=embed';
}
?>
<?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <div class="pt-20">
        <!-- Hero Section Campaign -->
        <div class="relative h-[400px] lg:h-[500px] overflow-hidden">
            <img src="<?php echo htmlspecialchars(campaignImageUrl($campaign['image'])); ?>" 
                 alt="<?php echo htmlspecialchars($campaign['title']); ?>"
                 class="absolute inset-0 w-full h-full object-cover"
                 loading="eager">
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
                            <?php echo number_format($campaign['donors_count'] ?? 0); ?> donatur
                            <span class="mx-3">•</span>
                            <i class="fas fa-clock mr-2"></i>
                            <?php echo $days_left > 0 ? $days_left . ' hari tersisa' : 'Telah berakhir'; ?>
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
                            <?php if (!empty($benefits)): ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <?php foreach ($benefits as $benefit): ?>
                                <div class="flex items-center text-gray-700">
                                    <div class="w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-check text-primary-600 text-xs"></i>
                                    </div>
                                    <span><?php echo htmlspecialchars($benefit); ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <p class="text-gray-500 text-sm">Belum ada data manfaat untuk campaign ini.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Gallery -->
                    <div class="bg-white rounded-2xl shadow-card p-6" data-aos="fade-up">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-gray-900">Dokumentasi</h2>
                            <span class="text-sm text-primary-600 font-semibold"><?php echo count($gallery); ?> foto</span>
                        </div>
                        
                        <?php if (!empty($gallery)): ?>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            <?php foreach ($gallery as $index => $img): ?>
                            <?php
                                // Normalisasi: bisa array dengan key image_url, atau langsung string
                                $imgUrl = is_array($img) ? ($img['image_url'] ?? '') : $img;
                            ?>
                            <div class="gallery-item rounded-xl overflow-hidden aspect-square">
                                <img src="<?php echo htmlspecialchars($imgUrl); ?>" 
                                     alt="Dokumentasi <?php echo $index + 1; ?>"
                                     class="w-full h-full object-cover"
                                     loading="lazy"
                                     onerror="this.parentElement.classList.add('hidden')">
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-8 text-gray-400">
                            <i class="fas fa-images text-4xl mb-3"></i>
                            <p>Belum ada foto dokumentasi</p>
                        </div>
                        <?php endif; ?>
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
                                src="<?php echo htmlspecialchars(mapEmbedUrl($campaign['map_url'] ?? '')); ?>"
                                allowfullscreen
                                loading="lazy">
                            </iframe>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-map-pin text-primary-600 mr-2"></i>
                                <?php echo htmlspecialchars($campaign['location']); ?>
                            </div>
                            <?php if (!empty($campaign['map_url'])): ?>
                            <a href="<?php echo htmlspecialchars($campaign['map_url']); ?>" target="_blank" rel="noopener noreferrer"
                               class="text-primary-600 hover:text-primary-700 font-semibold text-sm">
                                Buka Google Maps
                                <i class="fas fa-external-link-alt ml-1"></i>
                            </a>
                            <?php endif; ?>
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
                                    <button onclick="setQuantity(1, this)" class="quick-select-btn flex-1 py-2 border border-gray-200 rounded-xl text-sm hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">1</button>
                                    <button onclick="setQuantity(3, this)" class="quick-select-btn flex-1 py-2 border border-gray-200 rounded-xl text-sm hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">3</button>
                                    <button onclick="setQuantity(5, this)" class="quick-select-btn flex-1 py-2 border border-gray-200 rounded-xl text-sm hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">5</button>
                                    <button onclick="setQuantity(10, this)" class="quick-select-btn flex-1 py-2 border border-gray-200 rounded-xl text-sm hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">10</button>
                                    <button onclick="setQuantity(20, this)" class="quick-select-btn flex-1 py-2 border border-gray-200 rounded-xl text-sm hover:bg-primary-600 hover:text-white hover:border-primary-600 transition">20</button>
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
                        
                        <!-- Campaign Lainnya (dinamis dari DB) -->
                        <div class="bg-white rounded-2xl shadow-card p-6 mt-6">
                            <h3 class="font-bold text-gray-900 mb-4">Campaign Lainnya</h3>
                            <?php if (!empty($other_campaigns)): ?>
                            <div class="space-y-3">
                                <?php foreach ($other_campaigns as $oc): ?>
                                <a href="campaign-detail.php?id=<?php echo $oc['id']; ?>" class="flex gap-3 hover:bg-gray-50 p-2 rounded-xl transition">
                                    <img src="<?php echo htmlspecialchars(campaignImageUrl($oc['image'])); ?>" 
                                         alt="<?php echo htmlspecialchars($oc['title']); ?>" 
                                         class="w-16 h-16 object-cover rounded-lg flex-shrink-0"
                                         onerror="this.src='assets/images/campaign-default.png'">
                                    <div class="min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-sm line-clamp-2"><?php echo htmlspecialchars($oc['title']); ?></h4>
                                        <p class="text-xs text-gray-500">Rp <?php echo number_format($oc['price_per_tree']); ?>/pohon</p>
                                        <p class="text-xs text-primary-600 mt-1"><?php echo number_format($oc['current_trees']); ?> pohon terkumpul</p>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <p class="text-sm text-gray-400">Tidak ada campaign lain saat ini.</p>
                            <?php endif; ?>
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
        const campaignId   = <?php echo (int)$campaign['id']; ?>;
        const isLoggedIn   = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
        const loginRedirect = 'login.php?redirect=' + encodeURIComponent('campaign-detail.php?id=<?php echo (int)$campaign['id']; ?>');

        const treeCountEl  = document.getElementById('treeCount');
        const totalPriceEl = document.getElementById('totalPrice');

        /* ── Helpers ──────────────────────────────────────────────── */
        function updateTotal() {
            const count = parseInt(treeCountEl.innerText);
            totalPriceEl.innerText = (count * pricePerTree).toLocaleString('id-ID');
        }

        function showToast(msg, isError = false) {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-6 right-6 ' +
                (isError ? 'bg-red-600' : 'bg-primary-600') +
                ' text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2 animate-bounce';
            toast.innerHTML = '<i class="fas fa-' + (isError ? 'exclamation-circle' : 'check-circle') + '"></i> ' + msg;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        /* ── Quick-Select (Fix 1) ─────────────────────────────────── */
        function setQuantity(count, btn) {
            treeCountEl.innerText = count;
            updateTotal();
            // Reset semua tombol
            document.querySelectorAll('.quick-select-btn').forEach(b => {
                b.classList.remove('bg-primary-600', 'text-white', 'border-primary-600');
                b.classList.add('border-gray-200');
                b.classList.remove('text-gray-700'); // bersihkan sisa
            });
            // Aktifkan tombol yang dipilih
            if (btn) {
                btn.classList.add('bg-primary-600', 'text-white', 'border-primary-600');
                btn.classList.remove('border-gray-200');
            }
        }

        // +/− buttons → hapus active state dari quick-select
        document.getElementById('increaseBtn').addEventListener('click', function() {
            let count = parseInt(treeCountEl.innerText);
            treeCountEl.innerText = ++count;
            updateTotal();
            document.querySelectorAll('.quick-select-btn').forEach(b => {
                b.classList.remove('bg-primary-600', 'text-white', 'border-primary-600');
                b.classList.add('border-gray-200');
            });
        });

        document.getElementById('decreaseBtn').addEventListener('click', function() {
            let count = parseInt(treeCountEl.innerText);
            if (count > 1) {
                treeCountEl.innerText = --count;
                updateTotal();
                document.querySelectorAll('.quick-select-btn').forEach(b => {
                    b.classList.remove('bg-primary-600', 'text-white', 'border-primary-600');
                    b.classList.add('border-gray-200');
                });
            }
        });

        /* ── Add to Cart (Fix 2 & 3) ─────────────────────────────── */
        function addToCart(cid, qty, redirectAfter) {
            if (!isLoggedIn) {
                window.location.href = loginRedirect;
                return;
            }
            fetch('controllers/campaignController.php?action=add_to_cart', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'campaign_id=' + cid + '&quantity=' + qty
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (redirectAfter) {
                        // Donasi Langsung: langsung ke checkout
                        window.location.href = 'checkout.php';
                    } else {
                        showToast('Berhasil ditambahkan ke keranjang!');
                    }
                } else {
                    showToast(data.message || 'Gagal menambahkan ke keranjang', true);
                }
            })
            .catch(() => showToast('Terjadi kesalahan koneksi', true));
        }

        // Tombol "Tambah ke Keranjang"
        document.getElementById('addToCartBtn').onclick = function() {
            addToCart(campaignId, parseInt(treeCountEl.innerText), false);
        };

        // Tombol "Donasi Langsung"
        function donateNow() {
            addToCart(campaignId, parseInt(treeCountEl.innerText), true);
        }

        /* ── Init active state tombol "5" ─────────────────────────── */
        (function() {
            const btns = document.querySelectorAll('.quick-select-btn');
            btns.forEach(b => {
                if (b.innerText.trim() === '5') {
                    b.classList.add('bg-primary-600', 'text-white', 'border-primary-600');
                    b.classList.remove('border-gray-200');
                }
            });
        })();
    </script>
</body>
</html>