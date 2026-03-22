<?php
// checkout.php
require_once 'config/koneksi.php';
require_once 'models/Cart.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect jika belum login
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
if (!$is_logged_in) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}

$cart = new Cart();

// Jika keranjang kosong, kembalikan ke cart
if ($cart->isEmpty()) {
    header('Location: cart.php');
    exit;
}

$cart_items   = $cart->getItems();
$total_amount = $cart->getSubtotal();

// Pre-fill dari session user jika ada
$user_name  = $_SESSION['user_name']  ?? '';
$user_email = $_SESSION['user_email'] ?? '';
$user_phone = $_SESSION['user_phone'] ?? '';
?>
<?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <div class="pt-20 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-8">
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Checkout Donasi</h1>
            <p class="text-gray-600">Lengkapi data diri untuk melanjutkan pembayaran</p>
        </div>
        
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Form Column -->
            <div class="lg:col-span-2">
                <form id="checkoutForm" class="bg-white rounded-2xl shadow-card p-6 space-y-6">

                    <!-- Identitas Donatur -->
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <span class="w-6 h-6 bg-primary-600 text-white rounded-lg flex items-center justify-center text-sm mr-3">1</span>
                            Identitas Donatur
                        </h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="donor_name" name="donor_name"
                                       value="<?php echo htmlspecialchars($user_name); ?>"
                                       placeholder="Masukkan nama lengkap"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="donor_email" name="donor_email"
                                       value="<?php echo htmlspecialchars($user_email); ?>"
                                       placeholder="Masukkan email aktif"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition" required>
                                <p class="text-xs text-gray-500 mt-2">
                                    Konfirmasi donasi dan e-sertifikat akan dikirim ke email ini
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor WhatsApp
                                </label>
                                <input type="tel" id="donor_phone" name="donor_phone"
                                       value="<?php echo htmlspecialchars($user_phone); ?>"
                                       placeholder="Contoh: 08123456789"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="anonymous" name="anonymous" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                <label for="anonymous" class="ml-3 text-sm text-gray-700">
                                    Donasi sebagai anonim (nama tidak ditampilkan di publik)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Doa & Ucapan -->
                    <div class="pt-6 border-t border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <span class="w-6 h-6 bg-primary-600 text-white rounded-lg flex items-center justify-center text-sm mr-3">2</span>
                            Doa &amp; Ucapan (Opsional)
                        </h2>
                        
                        <div>
                            <textarea rows="3" id="message" name="message"
                                      placeholder="Tulis doa atau ucapan untuk program ini..."
                                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition"></textarea>
                            <p class="text-xs text-gray-500 mt-2">
                                Ucapan Anda akan ditampilkan di halaman publik
                            </p>
                        </div>
                    </div>
                    
                    <!-- Metode Pembayaran -->
                    <div class="pt-6 border-t border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <span class="w-6 h-6 bg-primary-600 text-white rounded-lg flex items-center justify-center text-sm mr-3">3</span>
                            Metode Pembayaran
                        </h2>
                        
                        <input type="hidden" id="payment_method" name="payment_method" value="BCA Virtual Account">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div class="payment-method-card selected border-2 border-primary-600 bg-primary-50 rounded-xl p-4 text-center cursor-pointer" data-method="BCA Virtual Account">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" class="h-6 mx-auto mb-2">
                                <span class="text-xs font-semibold text-primary-700">BCA Virtual Account</span>
                            </div>
                            <div class="payment-method-card border-2 border-gray-200 rounded-xl p-4 text-center hover:border-primary-600 cursor-pointer" data-method="Mandiri VA">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/9/94/Bank_Mandiri_logo.svg" alt="Mandiri" class="h-6 mx-auto mb-2">
                                <span class="text-xs font-semibold text-gray-700">Mandiri VA</span>
                            </div>
                            <div class="payment-method-card border-2 border-gray-200 rounded-xl p-4 text-center hover:border-primary-600 cursor-pointer" data-method="BRI VA">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/6/68/Bank_BRI_logo.svg" alt="BRI" class="h-6 mx-auto mb-2">
                                <span class="text-xs font-semibold text-gray-700">BRI VA</span>
                            </div>
                            <div class="payment-method-card border-2 border-gray-200 rounded-xl p-4 text-center hover:border-primary-600 cursor-pointer" data-method="BNI VA">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/8/83/BNI_logo.svg" alt="BNI" class="h-6 mx-auto mb-2">
                                <span class="text-xs font-semibold text-gray-700">BNI VA</span>
                            </div>
                            <div class="payment-method-card border-2 border-gray-200 rounded-xl p-4 text-center hover:border-primary-600 cursor-pointer" data-method="OVO">
                                <i class="fas fa-wallet text-2xl text-gray-600 mb-2"></i>
                                <span class="text-xs font-semibold text-gray-700">OVO</span>
                            </div>
                            <div class="payment-method-card border-2 border-gray-200 rounded-xl p-4 text-center hover:border-primary-600 cursor-pointer" data-method="GoPay">
                                <i class="fas fa-mobile-alt text-2xl text-gray-600 mb-2"></i>
                                <span class="text-xs font-semibold text-gray-700">GoPay</span>
                            </div>
                            <div class="payment-method-card border-2 border-gray-200 rounded-xl p-4 text-center hover:border-primary-600 cursor-pointer" data-method="Kartu Kredit">
                                <i class="fas fa-credit-card text-2xl text-gray-600 mb-2"></i>
                                <span class="text-xs font-semibold text-gray-700">Kartu Kredit</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Terms & Conditions -->
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex items-start">
                            <input type="checkbox" id="terms" class="mt-1 w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <label for="terms" class="ml-3 text-sm text-gray-600">
                                Saya setuju dengan <a href="#" class="text-primary-700 font-semibold hover:underline">syarat dan ketentuan</a> yang berlaku serta menyatakan bahwa donasi ini sah dan tidak melanggar hukum.
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Summary Column -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-card p-6 sticky top-24">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Donasi</h2>
                    
                    <?php foreach ($cart_items as $item): ?>
                    <div class="flex justify-between text-sm mb-3">
                        <div>
                            <span class="font-medium text-gray-900"><?php echo htmlspecialchars($item['campaign_title']); ?></span>
                            <span class="text-gray-500 block text-xs"><?php echo $item['quantity']; ?> pohon × Rp <?php echo number_format($item['price_per_tree']); ?></span>
                        </div>
                        <span class="font-semibold text-gray-900">Rp <?php echo number_format($item['subtotal']); ?></span>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="border-t border-gray-200 mt-4 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 font-medium">Total Donasi</span>
                            <span class="text-2xl font-extrabold text-primary-700">
                                Rp <?php echo number_format($total_amount); ?>
                            </span>
                        </div>
                        <div class="flex justify-between text-sm mt-2">
                            <span class="text-gray-500">Biaya Admin</span>
                            <span class="text-gray-700 font-medium">Rp 0</span>
                        </div>
                        <div class="flex justify-between font-bold text-primary-700 text-lg mt-3 pt-3 border-t border-dashed border-gray-200">
                            <span>Total Bayar</span>
                            <span>Rp <?php echo number_format($total_amount); ?></span>
                        </div>
                    </div>
                    
                    <button onclick="processPayment()" 
                            class="w-full mt-6 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold py-4 px-6 rounded-2xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25 flex items-center justify-center">
                        <i class="fas fa-lock mr-2"></i>
                        Bayar Sekarang
                    </button>
                    
                    <p class="text-xs text-gray-500 text-center mt-4">
                        <i class="fas fa-shield-alt mr-1 text-primary-600"></i>
                        Pembayaran aman &amp; terenkripsi
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Payment method selection
        document.querySelectorAll('.payment-method-card').forEach(card => {
            card.addEventListener('click', function () {
                document.querySelectorAll('.payment-method-card').forEach(c => {
                    c.classList.remove('selected', 'border-primary-600', 'bg-primary-50');
                    c.classList.add('border-gray-200');
                    c.querySelectorAll('span').forEach(s => s.classList.remove('text-primary-700'));
                    c.querySelectorAll('span').forEach(s => s.classList.add('text-gray-700'));
                });
                this.classList.add('selected', 'border-primary-600', 'bg-primary-50');
                this.classList.remove('border-gray-200');
                this.querySelectorAll('span').forEach(s => {
                    s.classList.remove('text-gray-700');
                    s.classList.add('text-primary-700');
                });
                document.getElementById('payment_method').value = this.dataset.method;
            });
        });

        function processPayment() {
            if (!document.getElementById('terms').checked) {
                alert('Harap centang persetujuan syarat dan ketentuan terlebih dahulu.');
                return;
            }

            const donor_name   = document.getElementById('donor_name').value.trim();
            const donor_email  = document.getElementById('donor_email').value.trim();
            if (!donor_name || !donor_email) {
                alert('Nama dan email wajib diisi.');
                return;
            }

            const btn = document.querySelector('button[onclick="processPayment()"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

            const formData = new FormData(document.getElementById('checkoutForm'));
            formData.append('action', 'process_donation');
            formData.append('anonymous', document.getElementById('anonymous').checked ? '1' : '0');

            fetch('controllers/donationController.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'success.php?donation_id=' + (data.donation_id || '');
                } else {
                    alert(data.message || 'Gagal memproses donasi. Silakan coba lagi.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-lock mr-2"></i>Bayar Sekarang';
                }
            })
            .catch(() => {
                alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-lock mr-2"></i>Bayar Sekarang';
            });
        }
    </script>
</body>
</html>