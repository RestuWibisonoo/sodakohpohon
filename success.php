<?php
// success.php
require_once 'config/koneksi.php';
require_once 'models/Donation.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Baca donation_id dari URL
$donation_id = isset($_GET['donation_id']) ? (int)$_GET['donation_id'] : 0;

$donationModel = new Donation();
$donation      = null;

if ($donation_id > 0) {
    $donation = $donationModel->getById($donation_id);
}

// Fallback ke flash session jika ID tidak ada/tidak ketemu
if (!$donation && isset($_SESSION['last_donation'])) {
    $donation = $_SESSION['last_donation'];
}

// Jika benar-benar tidak ada, tampilkan halaman sukses generik
if (!$donation) {
    $donation = [
        'donation_number'    => '-',
        'campaign_title'     => '-',
        'trees_count'        => 0,
        'amount'             => 0,
        'donor_name'         => 'Donatur',
        'created_at'         => date('Y-m-d H:i:s'),
        'certificate_number' => '-',
        'status'             => 'pending',
    ];
}

$trees_count        = $donation['trees_count'] ?? 0;
$campaign_title     = $donation['campaign_title'] ?? '-';
$donor_name         = $donation['donor_name'] ?? 'Donatur';
$amount             = $donation['amount'] ?? 0;
$donation_number    = $donation['donation_number'] ?? '-';
$certificate_number = $donation['certificate_number'] ?? '-';
$created_date       = isset($donation['created_at'])
    ? date('d F Y', strtotime($donation['created_at']))
    : date('d F Y');
?>
<?php include 'includes/header.php'; ?>

    <!-- Confetti Effect -->
    <div id="confetti-container" class="fixed inset-0 pointer-events-none z-50"></div>

    <!-- Success Content -->
    <div class="pt-20 min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto py-12">
            <!-- Success Card -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 text-center relative overflow-hidden">
                <!-- Decorative Background -->
                <div class="absolute top-0 left-0 w-32 h-32 bg-primary-100 rounded-full -translate-x-16 -translate-y-16 opacity-50"></div>
                <div class="absolute bottom-0 right-0 w-32 h-32 bg-primary-100 rounded-full translate-x-16 translate-y-16 opacity-50"></div>
                
                <!-- Success Icon -->
                <div class="relative mb-8">
                    <div class="w-24 h-24 mx-auto bg-gradient-to-r from-primary-600 to-primary-700 rounded-full flex items-center justify-center shadow-xl shadow-primary-600/30" style="animation: checkmark 0.5s ease-out">
                        <i class="fas fa-check text-white text-4xl"></i>
                    </div>
                    <div class="absolute -top-2 -right-2 w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center animate-bounce">
                        <i class="fas fa-leaf text-white"></i>
                    </div>
                </div>
                
                <!-- Thank You Message -->
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">
                    Terima Kasih, <?php echo htmlspecialchars($donor_name); ?>!
                </h1>
                <p class="text-lg text-gray-600 mb-8">
                    Anda telah berhasil menyedekahkan 
                    <span class="font-bold text-primary-700 text-2xl"><?php echo number_format($trees_count); ?> pohon</span>
                    <?php if ($campaign_title !== '-'): ?>
                    melalui program <span class="font-semibold"><?php echo htmlspecialchars($campaign_title); ?></span>
                    <?php endif; ?>
                </p>
                
                <!-- Impact Statement -->
                <div class="bg-gradient-to-r from-primary-50 to-earth-50 rounded-2xl p-6 mb-8">
                    <p class="text-gray-700 italic">
                        "Setiap pohon yang Anda tanam akan tumbuh dan memberikan manfaat untuk generasi mendatang. 
                        Ini adalah sedekah jariyah yang terus mengalir."
                    </p>
                </div>
                
                <!-- Donation Details -->
                <div class="bg-gray-50 rounded-2xl p-6 mb-8 text-left">
                    <h3 class="font-bold text-gray-900 mb-4">Detail Donasi</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nomor Donasi</span>
                            <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($donation_number); ?></span>
                        </div>
                        <?php if ($campaign_title !== '-'): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Program</span>
                            <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($campaign_title); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah Pohon</span>
                            <span class="font-semibold text-primary-700"><?php echo number_format($trees_count); ?> pohon</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Donasi</span>
                            <span class="font-bold text-gray-900">Rp <?php echo number_format($amount); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal</span>
                            <span class="text-gray-700"><?php echo $created_date; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status</span>
                            <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold">
                                <i class="fas fa-clock mr-1"></i>
                                MENUNGGU PEMBAYARAN
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Info pembayaran simulasi -->
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-8 text-left">
                    <div class="flex gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5 flex-shrink-0"></i>
                        <div>
                            <p class="font-semibold text-blue-800 mb-1">Donasi telah tercatat!</p>
                            <p class="text-sm text-blue-700">
                                Pesanan Anda berhasil disimpan dengan nomor <strong><?php echo htmlspecialchars($donation_number); ?></strong>. 
                                Konfirmasi akan dikirim ke email Anda setelah pembayaran selesai diverifikasi oleh admin.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Certificate Card -->
                <?php if ($certificate_number !== '-'): ?>
                <div class="border-2 border-primary-200 rounded-2xl p-6 mb-8 bg-gradient-to-br from-white to-primary-50">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-certificate text-3xl text-primary-700"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <h4 class="font-bold text-gray-900">Sertifikat Donasi Disiapkan</h4>
                            <p class="text-sm text-gray-600 mb-2">
                                Nomor: <?php echo htmlspecialchars($certificate_number); ?>
                            </p>
                            <p class="text-xs text-gray-500">Akan tersedia setelah pembayaran dikonfirmasi</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="campaign.php" 
                       class="inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-2xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                        <i class="fas fa-tree mr-2"></i>
                        Donasi Lagi
                    </a>
                    <a href="laporan.php" 
                       class="inline-flex items-center justify-center px-6 py-4 bg-white border-2 border-primary-600 text-primary-700 font-bold rounded-2xl hover:bg-primary-50 transition">
                        <i class="fas fa-chart-line mr-2"></i>
                        Lihat Dampak
                    </a>
                </div>
                
                <!-- Share -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-4">Bagikan kebaikan ini</p>
                    <div class="flex justify-center space-x-4">
                        <button onclick="shareWhatsApp()" class="w-12 h-12 bg-green-500 text-white rounded-xl hover:bg-green-600 transition">
                            <i class="fab fa-whatsapp text-xl"></i>
                        </button>
                        <button onclick="shareFacebook()" class="w-12 h-12 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </button>
                        <button onclick="shareTwitter()" class="w-12 h-12 bg-blue-400 text-white rounded-xl hover:bg-blue-500 transition">
                            <i class="fab fa-twitter text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Info tracking -->
            <div class="bg-white rounded-2xl shadow-card p-6 mt-6 text-center">
                <div class="flex items-center justify-center gap-2 text-sm mb-2">
                    <i class="fas fa-envelope text-primary-600"></i>
                    <span class="text-gray-700">Konfirmasi donasi akan dikirim ke email Anda</span>
                </div>
                <p class="text-xs text-gray-500">
                    Update perkembangan penanaman akan dikirimkan dalam 7-14 hari ke depan
                </p>
            </div>
        </div>
    </div>

    <script>
        // Confetti
        (function createConfetti() {
            const container = document.getElementById('confetti-container');
            for (let i = 0; i < 60; i++) {
                const c = document.createElement('div');
                c.style.cssText = `
                    position:absolute;
                    left:${Math.random()*100}%;
                    top:-10px;
                    width:${Math.random()*8+4}px;
                    height:${Math.random()*8+4}px;
                    background:hsl(${Math.random()*360},70%,50%);
                    border-radius:${Math.random()>0.5?'50%':'2px'};
                    animation:fall ${Math.random()*3+3}s ${Math.random()*2}s linear forwards;
                `;
                container.appendChild(c);
            }
        })();

        function shareWhatsApp() {
            const text = encodeURIComponent('Saya baru saja menyedekahkan <?php echo $trees_count; ?> pohon melalui Sodakoh Pohon. Yuk, ikut berkontribusi! 🌳');
            window.open('https://wa.me/?text=' + text, '_blank');
        }
        function shareFacebook() {
            window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(location.href), '_blank');
        }
        function shareTwitter() {
            const text = encodeURIComponent('Saya baru saja menyedekahkan <?php echo $trees_count; ?> pohon melalui Sodakoh Pohon 🌳');
            window.open('https://twitter.com/intent/tweet?text=' + text, '_blank');
        }
    </script>
    <style>
        @keyframes fall {
            to { transform: translateY(105vh) rotate(360deg); opacity: 0; }
        }
    </style>
</body>
</html>