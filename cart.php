<?php
// cart.php
session_start();

// Simulasi data keranjang (nanti dari database)
$cart_items = [
    [
        'id' => 1,
        'campaign_id' => 1,
        'campaign_title' => 'Restorasi Mangrove Demak',
        'tree_type' => 'Mangrove Rhizophora',
        'price_per_tree' => 10000,
        'quantity' => 5,
        'subtotal' => 50000,
        'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80',
        'location' => 'Demak, Jawa Tengah'
    ]
];

$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['subtotal'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Donasi - Sodakoh Pohon</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
            background-color: #faf7f2;
        }
        
        .cart-item {
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            box-shadow: 0 20px 35px -8px rgba(0,0,0,0.05);
        }
        
        .quantity-control {
            transition: all 0.2s ease;
        }
        
        .quantity-control:hover {
            background-color: #10b981;
            color: white;
            border-color: #10b981;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-effect border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="absolute inset-0 bg-primary-600 rounded-xl blur-sm opacity-60"></div>
                        <div class="relative bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl p-2.5">
                            <i class="fas fa-tree text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-extrabold">
                            <span class="text-primary-700">Sodakoh</span>
                            <span class="text-earth-700">Pohon</span>
                        </span>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-600 hover:text-primary-600 font-medium transition">Beranda</a>
                    <a href="index.php#campaigns" class="text-gray-600 hover:text-primary-600 font-medium transition">Campaign</a>
                    <a href="laporan.php" class="text-gray-600 hover:text-primary-600 font-medium transition">Laporan</a>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="cart.php" class="relative p-2 text-primary-600">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="absolute -top-1 -right-1 bg-primary-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                            <?php echo count($cart_items); ?>
                        </span>
                    </a>
                    <a href="index.php#campaigns" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                        <i class="fas fa-plus-circle mr-2"></i>Tambah Donasi
                    </a>
                </div>
            </div>
        </div>
    </nav>

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
            <a href="index.php#campaigns" 
               class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-2xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                <i class="fas fa-tree mr-2"></i>
                Lihat Campaign
            </a>
        </div>
        <?php else: ?>
        
        <!-- Cart Content -->
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                <?php foreach ($cart_items as $index => $item): ?>
                <div class="cart-item bg-white rounded-2xl shadow-card p-6">
                    <div class="flex flex-col sm:flex-row gap-6">
                        <!-- Image -->
                        <div class="sm:w-32 h-32 rounded-xl overflow-hidden">
                            <img src="<?php echo $item['image']; ?>" 
                                 alt="<?php echo $item['campaign_title']; ?>"
                                 class="w-full h-full object-cover">
                        </div>
                        
                        <!-- Details -->
                        <div class="flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-1">
                                        <?php echo $item['campaign_title']; ?>
                                    </h3>
                                    <div class="flex items-center gap-3 text-sm text-gray-500 mb-2">
                                        <span><i class="fas fa-leaf mr-1 text-primary-600"></i><?php echo $item['tree_type']; ?></span>
                                        <span><i class="fas fa-map-marker-alt mr-1 text-primary-600"></i><?php echo $item['location']; ?></span>
                                    </div>
                                    <div class="text-primary-700 font-bold">
                                        Rp <?php echo number_format($item['price_per_tree']); ?> / pohon
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-sm text-gray-500 mb-1">Subtotal</div>
                                    <div class="text-2xl font-bold text-primary-700">
                                        Rp <?php echo number_format($item['subtotal']); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quantity Control -->
                            <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-medium text-gray-700">Jumlah pohon:</span>
                                    <div class="flex items-center border border-gray-200 rounded-xl">
                                        <button onclick="updateQuantity(<?php echo $item['id']; ?>, 'decrease')" 
                                                class="quantity-control px-4 py-2 text-gray-600 hover:bg-primary-600 hover:text-white hover:border-primary-600 rounded-l-xl transition">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span class="px-6 py-2 font-semibold text-gray-900 border-x border-gray-200">
                                            <?php echo $item['quantity']; ?>
                                        </span>
                                        <button onclick="updateQuantity(<?php echo $item['id']; ?>, 'increase')" 
                                                class="quantity-control px-4 py-2 text-gray-600 hover:bg-primary-600 hover:text-white hover:border-primary-600 rounded-r-xl transition">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <button onclick="removeItem(<?php echo $item['id']; ?>)" 
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
                    <a href="index.php#campaigns" class="inline-flex items-center text-primary-700 font-semibold hover:text-primary-800 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Lanjutkan Memilih Campaign
                    </a>
                </div>
            </div>
            
            <!-- Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-card p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Ringkasan Donasi</h2>
                    
                    <div class="space-y-4 mb-6">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">
                                <?php echo $item['campaign_title']; ?> (<?php echo $item['quantity']; ?> pohon)
                            </span>
                            <span class="font-semibold text-gray-900">
                                Rp <?php echo number_format($item['subtotal']); ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700 font-medium">Total Donasi</span>
                                <span class="text-2xl font-extrabold text-primary-700">
                                    Rp <?php echo number_format($total_amount); ?>
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                *Belum termasuk biaya admin
                            </p>
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
                        <div class="flex items-center justify-center gap-4">
                            <div class="text-center">
                                <i class="fas fa-shield-alt text-2xl text-primary-600 mb-2"></i>
                                <p class="text-xs text-gray-500">Donasi Aman</p>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-clock text-2xl text-primary-600 mb-2"></i>
                                <p class="text-xs text-gray-500">24/7 Support</p>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-leaf text-2xl text-primary-600 mb-2"></i>
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
        function updateQuantity(itemId, action) {
            // Implementasi update quantity via AJAX
            alert('Update quantity - Item ID: ' + itemId + ', Action: ' + action);
            location.reload();
        }
        
        function removeItem(itemId) {
            if (confirm('Hapus donasi ini dari keranjang?')) {
                // Implementasi remove item via AJAX
                alert('Item ' + itemId + ' dihapus');
                location.reload();
            }
        }
        
        function clearCart() {
            if (confirm('Yakin ingin mengosongkan keranjang?')) {
                // Implementasi clear cart via AJAX
                alert('Keranjang dikosongkan');
                location.reload();
            }
        }
    </script>
</body>
</html>