<?php
// cart.php
require_once 'config/koneksi.php';
require_once 'models/Cart.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect jika belum login
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
if (!$is_logged_in) {
    header('Location: login.php?redirect=cart.php');
    exit;
}

$cart     = new Cart();
$cart_items   = $cart->getItems();
$total_amount = $cart->getSubtotal();
?>
<?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <div class="pt-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Keranjang Donasi</h1>
                <p class="text-gray-600">Review donasi Anda sebelum melanjutkan ke pembayaran</p>
            </div>
            <div class="hidden md:flex items-center text-sm">
                <span class="flex items-center text-primary-700">
                    <i class="fas fa-check-circle mr-2"></i>
                    1. Keranjang
                </span>
                <i class="fas fa-chevron-right mx-3 text-gray-300"></i>
                <span class="flex items-center text-gray-400">
                    <i class="fas fa-circle mr-2"></i>
                    2. Checkout
                </span>
                <i class="fas fa-chevron-right mx-3 text-gray-300"></i>
                <span class="flex items-center text-gray-400">
                    <i class="fas fa-circle mr-2"></i>
                    3. Selesai
                </span>
            </div>
        </div>

        <?php if (empty($cart_items)): ?>
        <!-- Empty Cart -->
        <div class="bg-white rounded-3xl shadow-card p-12 text-center">
            <div class="w-24 h-24 mx-auto bg-primary-100 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-shopping-cart text-4xl text-primary-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-3">Keranjang Masih Kosong</h2>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                Anda belum memilih program penanaman pohon. Yuk, mulai sodakoh pohon sekarang!
            </p>
            <a href="campaign.php" 
               class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-2xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                <i class="fas fa-tree mr-2"></i>
                Lihat Campaign
            </a>
        </div>
        <?php else: ?>
        
        <!-- Cart Content -->
        <div class="grid lg:grid-cols-3 gap-8" id="cartContainer">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4" id="cartItemsList">
                <?php foreach ($cart_items as $item): ?>
                <div class="cart-item bg-white rounded-2xl shadow-card p-6" id="cart-item-<?php echo htmlspecialchars($item['id']); ?>">
                    <div class="flex flex-col sm:flex-row gap-6">
                        <!-- Image -->
                        <div class="sm:w-32 h-32 rounded-xl overflow-hidden flex-shrink-0">
                            <img src="<?php echo htmlspecialchars($item['image'] ?? 'assets/images/campaign-default.png'); ?>" 
                                 alt="<?php echo htmlspecialchars($item['campaign_title']); ?>"
                                 class="w-full h-full object-cover"
                                 onerror="this.src='assets/images/campaign-default.png'">
                        </div>
                        
                        <!-- Details -->
                        <div class="flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-1">
                                        <?php echo htmlspecialchars($item['campaign_title']); ?>
                                    </h3>
                                    <div class="flex items-center gap-3 text-sm text-gray-500 mb-2">
                                        <span><i class="fas fa-leaf mr-1 text-primary-600"></i><?php echo htmlspecialchars($item['tree_type']); ?></span>
                                        <span><i class="fas fa-map-marker-alt mr-1 text-primary-600"></i><?php echo htmlspecialchars($item['location']); ?></span>
                                    </div>
                                    <div class="text-primary-700 font-bold">
                                        Rp <?php echo number_format($item['price_per_tree']); ?> / pohon
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-sm text-gray-500 mb-1">Subtotal</div>
                                    <div class="text-2xl font-bold text-primary-700 item-subtotal" data-item-id="<?php echo htmlspecialchars($item['id']); ?>">
                                        Rp <?php echo number_format($item['subtotal']); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quantity Control -->
                            <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-medium text-gray-700">Jumlah pohon:</span>
                                    <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                                        <button onclick="updateQuantity('<?php echo htmlspecialchars($item['id']); ?>', 'decrease')" 
                                                class="px-4 py-2 text-gray-600 hover:bg-primary-600 hover:text-white transition">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span class="px-5 py-2 font-semibold text-gray-900 border-x border-gray-200 qty-display" 
                                              id="qty-<?php echo htmlspecialchars($item['id']); ?>">
                                            <?php echo $item['quantity']; ?>
                                        </span>
                                        <button onclick="updateQuantity('<?php echo htmlspecialchars($item['id']); ?>', 'increase')" 
                                                class="px-4 py-2 text-gray-600 hover:bg-primary-600 hover:text-white transition">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <button onclick="removeItem('<?php echo htmlspecialchars($item['id']); ?>')" 
                                        class="text-red-500 hover:text-red-700 transition text-sm font-medium">
                                    <i class="fas fa-trash-alt mr-1"></i>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Continue Donating -->
                <div class="bg-white rounded-2xl shadow-card p-6">
                    <a href="campaign.php" class="inline-flex items-center text-primary-700 font-semibold hover:text-primary-800 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Lanjutkan Memilih Campaign
                    </a>
                </div>
            </div>
            
            <!-- Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-card p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Ringkasan Donasi</h2>
                    
                    <div class="space-y-3 mb-6" id="summaryItems">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="flex justify-between text-sm summary-row" id="summary-<?php echo htmlspecialchars($item['id']); ?>">
                            <span class="text-gray-600">
                                <?php echo htmlspecialchars($item['campaign_title']); ?>
                                (<span class="summary-qty"><?php echo $item['quantity']; ?></span> pohon)
                            </span>
                            <span class="font-semibold text-gray-900 summary-subtotal">
                                Rp <?php echo number_format($item['subtotal']); ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700 font-medium">Total Donasi</span>
                                <span class="text-2xl font-extrabold text-primary-700" id="grandTotal">
                                    Rp <?php echo number_format($total_amount); ?>
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">*Belum termasuk biaya admin</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <a href="checkout.php" 
                           class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold py-4 px-6 rounded-2xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25 flex items-center justify-center">
                            <i class="fas fa-credit-card mr-2"></i>
                            Lanjut ke Checkout
                        </a>
                        
                        <button onclick="clearCart()" 
                                class="w-full bg-white border-2 border-gray-200 text-gray-700 font-semibold py-4 px-6 rounded-2xl hover:border-red-500 hover:text-red-500 transition flex items-center justify-center">
                            <i class="fas fa-trash-alt mr-2"></i>
                            Kosongkan Keranjang
                        </button>
                    </div>
                    
                    <!-- Trust Badges -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-center gap-6">
                            <div class="text-center">
                                <i class="fas fa-shield-alt text-2xl text-primary-600 mb-1"></i>
                                <p class="text-xs text-gray-500">Donasi Aman</p>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-clock text-2xl text-primary-600 mb-1"></i>
                                <p class="text-xs text-gray-500">24/7 Support</p>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-leaf text-2xl text-primary-600 mb-1"></i>
                                <p class="text-xs text-gray-500">Transparan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="flex items-center justify-center space-x-3 mb-4">
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
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
                    <p>&copy; <?php echo date('Y'); ?> Sodakoh Pohon. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const priceMap = {
            <?php foreach ($cart_items as $item): ?>
            '<?php echo htmlspecialchars($item['id']); ?>': <?php echo (float)$item['price_per_tree']; ?>,
            <?php endforeach; ?>
        };

        function callCart(action, data, cb) {
            const body = new URLSearchParams({ action, ...data });
            fetch('controllers/cartController.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: body.toString()
            })
            .then(r => r.json())
            .then(cb)
            .catch(() => showToast('Terjadi kesalahan koneksi', true));
        }

        function showToast(msg, isError = false) {
            const t = document.createElement('div');
            t.className = 'fixed bottom-6 right-6 ' +
                (isError ? 'bg-red-600' : 'bg-primary-600') +
                ' text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2 animate-bounce text-sm';
            t.innerHTML = '<i class="fas fa-' + (isError ? 'exclamation-circle' : 'check-circle') + '"></i> ' + msg;
            document.body.appendChild(t);
            setTimeout(() => t.remove(), 3000);
        }

        function refreshGrandTotal() {
            let total = 0;
            document.querySelectorAll('.item-subtotal').forEach(el => {
                const raw = el.getAttribute('data-raw');
                if (raw) total += parseFloat(raw);
            });
            document.getElementById('grandTotal').textContent =
                'Rp ' + total.toLocaleString('id-ID');
        }

        function updateQuantity(itemId, action) {
            const qtyEl = document.getElementById('qty-' + itemId);
            let qty = parseInt(qtyEl.textContent.trim());
            if (action === 'decrease') qty = Math.max(1, qty - 1);
            else qty = qty + 1;

            callCart('update_quantity', { item_id: itemId, quantity: qty }, data => {
                if (data.success) {
                    qtyEl.textContent = qty;
                    // Update summary qty
                    const summaryRow = document.getElementById('summary-' + itemId);
                    if (summaryRow) summaryRow.querySelector('.summary-qty').textContent = qty;
                    // Update subtotal
                    const price = priceMap[itemId] || 0;
                    const subtotal = qty * price;
                    const subtotalEl = document.querySelector('.item-subtotal[data-item-id="' + itemId + '"]');
                    if (subtotalEl) {
                        subtotalEl.setAttribute('data-raw', subtotal);
                        subtotalEl.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
                    }
                    if (summaryRow) {
                        summaryRow.querySelector('.summary-subtotal').textContent =
                            'Rp ' + subtotal.toLocaleString('id-ID');
                    }
                    refreshGrandTotal();
                    updateCartBadge(data.cart_count);
                } else {
                    showToast(data.message || 'Gagal update jumlah', true);
                }
            });
        }

        function removeItem(itemId) {
            if (!confirm('Hapus donasi ini dari keranjang?')) return;
            callCart('remove_item', { item_id: itemId }, data => {
                if (data.success) {
                    const el = document.getElementById('cart-item-' + itemId);
                    if (el) el.remove();
                    const s = document.getElementById('summary-' + itemId);
                    if (s) s.remove();
                    updateCartBadge(data.cart_count);
                    if (data.cart_count === 0) location.reload();
                    else refreshGrandTotal();
                } else {
                    showToast(data.message || 'Gagal menghapus item', true);
                }
            });
        }

        function clearCart() {
            if (!confirm('Yakin ingin mengosongkan keranjang?')) return;
            callCart('clear_cart', {}, data => {
                if (data.success) {
                    updateCartBadge(0);
                    location.reload();
                } else {
                    showToast('Gagal mengosongkan keranjang', true);
                }
            });
        }

        function updateCartBadge(count) {
            const badge = document.querySelector('a[href="cart.php"] span');
            if (badge) badge.textContent = count;
        }

        // Set data-raw for initial subtotals
        document.querySelectorAll('.item-subtotal').forEach(el => {
            const itemId = el.getAttribute('data-item-id');
            const price = priceMap[itemId] || 0;
            const qty = parseInt((document.getElementById('qty-' + itemId) || {}).textContent || '0');
            el.setAttribute('data-raw', qty * price);
        });
    </script>
</body>
</html>