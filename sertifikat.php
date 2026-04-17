<?php
/**
 * sertifikat.php — Halaman sertifikat donasi Sodakoh Pohon
 *
 * Mode:
 *   ?no=CERT-XXX            → tampilkan halaman HTML
 *   ?no=CERT-XXX&img=1      → output PNG inline (untuk <img src>)
 *   ?no=CERT-XXX&download=1 → download PNG
 */
require_once 'config/koneksi.php';

$cert_no  = trim($_GET['no'] ?? '');
$img_mode = isset($_GET['img']);
$download = isset($_GET['download']);

if (empty($cert_no)) {
    http_response_code(404);
    die('Nomor sertifikat tidak ditemukan.');
}

$cert_no_safe = getDB()->real_escape_string($cert_no);

$cert = db_get_row(
    "SELECT c.*, d.donation_number, d.amount
     FROM certificates c
     JOIN donations d ON d.id = c.donation_id
     WHERE c.certificate_number = '{$cert_no_safe}'"
);

if (!$cert) {
    http_response_code(404);
    die('Sertifikat tidak ditemukan. Pastikan nomor sertifikat sudah benar.');
}

/* ─────────────────────────────────────────────────────────────────────────────
   Fungsi: render template PNG + tulis teks nama & tanggal via GD
───────────────────────────────────────────────────────────────────────────── */
function render_certificate(array $cert, bool $force_download = false): void
{
    $tpl = __DIR__ . '/assets/images/sertifikat.png';
    if (!file_exists($tpl)) {
        http_response_code(500);
        die('Template sertifikat tidak ditemukan di: ' . $tpl);
    }

    $img = imagecreatefrompng($tpl);
    if (!$img) {
        http_response_code(500);
        die('Gagal memuat template PNG.');
    }

    imagealphablending($img, true);
    imagesavealpha($img, true);

    $W  = imagesx($img);
    $H  = imagesy($img);
    $CX = intdiv($W, 2);

    // Warna
    $c_dark  = imagecolorallocate($img, 25,  65,  45);   // hijau tua
    $c_green = imagecolorallocate($img,  5, 150, 105);   // primer
    $c_gray  = imagecolorallocate($img, 90,  90,  90);

    // Font TTF
    $fd   = __DIR__ . '/assets/fonts/';
    $fB   = $fd . 'OpenSans-Bold.ttf';
    $fR   = $fd . 'OpenSans-Regular.ttf';
    $fI   = $fd . 'OpenSans-Italic.ttf';
    $hasTTF = function_exists('imagettftext') && file_exists($fB);

    $name    = $cert['donor_name'];
    $camp    = $cert['campaign_name'];
    $trees   = (int) $cert['trees_count'];
    $tanggal = date('d F Y', strtotime($cert['issued_at']));
    $no      = $cert['certificate_number'];

    if ($hasTTF) {
        // ── Nama donatur ──────────────────────────────────────────────────────
        $sz   = max(22, min(38, (int)(650 / max(strlen($name), 1))));
        $bbox = imagettfbbox($sz, 0, $fB, $name);
        $tw   = $bbox[2] - $bbox[0];
        imagettftext($img, $sz, 0, $CX - intdiv($tw, 2), (int)($H * 0.445), $c_dark, $fB, $name);

        // ── Sub-teks 1: jumlah pohon ──────────────────────────────────────────
        $t1   = 'Telah menyedekahkan ' . number_format($trees) . ' pohon';
        $bb1  = imagettfbbox(14, 0, $fR, $t1);
        imagettftext($img, 14, 0, $CX - intdiv($bb1[2]-$bb1[0], 2), (int)($H * 0.54), $c_green, $fR, $t1);

        // ── Sub-teks 2: nama campaign ─────────────────────────────────────────
        $t2   = 'Campaign: ' . $camp;
        $bb2  = imagettfbbox(11, 0, $fI, $t2);
        imagettftext($img, 11, 0, $CX - intdiv($bb2[2]-$bb2[0], 2), (int)($H * 0.61), $c_gray, $fI, $t2);

        // ── Tanggal ───────────────────────────────────────────────────────────
        $bb3 = imagettfbbox(11, 0, $fR, $tanggal);
        imagettftext($img, 11, 0, $CX - intdiv($bb3[2]-$bb3[0], 2), (int)($H * 0.895), $c_gray, $fR, $tanggal);

        // ── Nomor sertifikat ──────────────────────────────────────────────────
        imagettftext($img, 8, 0, (int)($W * 0.06), (int)($H * 0.965), $c_gray, $fR, 'No: ' . $no);

    } else {
        // ── Fallback GD built-in (tanpa font TTF) ─────────────────────────────
        $f  = 5;
        $tw = strlen($name) * imagefontwidth($f);
        imagestring($img, $f, $CX - intdiv($tw, 2), (int)($H * 0.42), $name, $c_dark);

        $sub = $tanggal . '  |  ' . $trees . ' pohon';
        imagestring($img, 3, $CX - intdiv(strlen($sub) * imagefontwidth(3), 2), (int)($H * 0.56), $sub, $c_green);
        imagestring($img, 3, $CX - intdiv(strlen($camp) * imagefontwidth(3), 2), (int)($H * 0.62), $camp, $c_gray);
        imagestring($img, 2, 20, (int)($H * 0.94), 'No: ' . $no, $c_gray);
    }

    // ── Output ────────────────────────────────────────────────────────────────
    $fname = 'sertifikat-' . preg_replace('/[^A-Za-z0-9\-]/', '', $no) . '.png';
    header('Content-Type: image/png');
    if ($force_download) {
        header('Content-Disposition: attachment; filename="' . $fname . '"');
    }
    imagepng($img, null, 6); // kompresi 6 dari 9
    imagedestroy($img);
}

/* ─────────────────────────────────────────────────────────────────────────────
   Route: img / download → output PNG langsung
───────────────────────────────────────────────────────────────────────────── */
if ($img_mode || $download) {
    // Pastikan tidak ada output sebelumnya (whitespace, BOM, dll.)
    while (ob_get_level()) ob_end_clean();
    render_certificate($cert, $download);
    exit;
}

/* ─────────────────────────────────────────────────────────────────────────────
   Route: halaman HTML
───────────────────────────────────────────────────────────────────────────── */
include 'includes/header.php';
?>

    <!-- Halaman Sertifikat -->
    <section class="pt-32 pb-20 min-h-screen" style="background: linear-gradient(135deg,#ecfdf5 0%,#f0fdf4 50%,#faf7f2 100%);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center bg-primary-100 rounded-full px-4 py-2 mb-4">
                    <i class="fas fa-award text-primary-700 mr-2"></i>
                    <span class="text-sm font-semibold text-primary-800">Sertifikat Donasi</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3">
                    Terima Kasih, <span class="gradient-text"><?= htmlspecialchars($cert['donor_name']) ?>!</span>
                </h1>
                <p class="text-gray-500 text-lg">
                    Anda telah berkontribusi menanam
                    <strong><?= number_format($cert['trees_count']) ?> pohon</strong>
                    di campaign <strong><?= htmlspecialchars($cert['campaign_name']) ?></strong>.
                </p>
            </div>

            <!-- Info Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
                    <i class="fas fa-hashtag text-primary-600 text-lg mb-2 block"></i>
                    <p class="text-xs text-gray-400 mb-1">No. Sertifikat</p>
                    <p class="font-bold text-gray-800 text-xs"><?= htmlspecialchars($cert['certificate_number']) ?></p>
                </div>
                <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
                    <i class="fas fa-tree text-green-600 text-lg mb-2 block"></i>
                    <p class="text-xs text-gray-400 mb-1">Jumlah Pohon</p>
                    <p class="font-bold text-gray-800"><?= number_format($cert['trees_count']) ?> pohon</p>
                </div>
                <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
                    <i class="fas fa-calendar-alt text-blue-600 text-lg mb-2 block"></i>
                    <p class="text-xs text-gray-400 mb-1">Tanggal Donasi</p>
                    <p class="font-bold text-gray-800"><?= date('d M Y', strtotime($cert['issued_at'])) ?></p>
                </div>
                <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
                    <i class="fas fa-shield-alt text-purple-600 text-lg mb-2 block"></i>
                    <p class="text-xs text-gray-400 mb-1">Diterbitkan</p>
                    <p class="font-bold text-gray-800 text-xs"><?= htmlspecialchars($cert['issued_by']) ?></p>
                </div>
            </div>

            <!-- Preview Sertifikat -->
            <div class="bg-white rounded-2xl shadow-card p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-eye mr-2 text-primary-600"></i>Preview Sertifikat
                </h2>
                <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-50 relative">
                    <!-- Skeleton loading -->
                    <div id="cert-skeleton" class="absolute inset-0 bg-gradient-to-r from-gray-100 via-gray-50 to-gray-100 animate-pulse flex items-center justify-center">
                        <i class="fas fa-certificate text-gray-300 text-5xl"></i>
                    </div>
                    <img
                        src="sertifikat.php?no=<?= urlencode($cert['certificate_number']) ?>&img=1"
                        alt="Sertifikat <?= htmlspecialchars($cert['donor_name']) ?>"
                        class="w-full h-auto block relative z-10"
                        onload="document.getElementById('cert-skeleton').style.display='none'"
                        onerror="document.getElementById('cert-skeleton').innerHTML='<p class=\'text-red-400 text-sm\'>Gagal memuat preview. <a href=\'?no=<?= urlencode($cert['certificate_number']) ?>&download=1\' class=\'underline\'>Download langsung</a></p>'"
                    >
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center mb-8">
                <a href="sertifikat.php?no=<?= urlencode($cert['certificate_number']) ?>&download=1"
                   class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                    <i class="fas fa-download mr-2"></i>Download PNG
                </a>
                <button onclick="shareCert()"
                        class="inline-flex items-center justify-center px-8 py-3 border-2 border-primary-600 text-primary-700 font-semibold rounded-xl hover:bg-primary-50 transition">
                    <i class="fas fa-share-alt mr-2"></i>Bagikan
                </button>
                <a href="campaign.php"
                   class="inline-flex items-center justify-center px-8 py-3 border-2 border-gray-300 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition">
                    <i class="fas fa-leaf mr-2"></i>Donasi Lagi
                </a>
            </div>

            <!-- Link verifikasi -->
            <div class="text-center">
                <p class="text-xs text-gray-400">
                    Verifikasi sertifikat ini di:
                    <a href="<?= BASE_URL ?>/sertifikat.php?no=<?= urlencode($cert['certificate_number']) ?>"
                       class="text-primary-600 hover:underline break-all ml-1">
                        <?= BASE_URL ?>/sertifikat.php?no=<?= urlencode($cert['certificate_number']) ?>
                    </a>
                </p>
            </div>

        </div>
    </section>

    <script>
        function shareCert() {
            const url = '<?= BASE_URL ?>/sertifikat.php?no=<?= urlencode($cert['certificate_number']) ?>';
            const text = 'Saya telah menyedekahkan <?= (int)$cert['trees_count'] ?> pohon melalui Sodakoh Pohon! 🌱';
            if (navigator.share) {
                navigator.share({ title: 'Sertifikat Donasi', text, url });
            } else {
                navigator.clipboard.writeText(url).then(() => alert('Link sertifikat disalin!'));
            }
        }
    </script>

<?php include 'includes/footer.php'; ?>
