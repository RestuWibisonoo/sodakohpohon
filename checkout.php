<?php
// checkout.php
session_start();

// Simulasi data keranjang
$cart_items = [
    [
        'id' => 1,
        'campaign_title' => 'Restorasi Mangrove Demak',
        'quantity' => 5,
        'price_per_tree' => 10000,
        'subtotal' => 50000
    ]
];

$total_amount = 50000;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Donasi - Sodakoh Pohon</title>
    
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
        
        .payment-method-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .payment-method-card:hover {
            border-color: #10b981;
            background-color: #ecfdf5;
        }
        
        .payment-method-card.active {
            border-color: #10b981;
            background-color: #ecfdf5;
            border-width: 2px;
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
                    <span class="text-2xl font-extrabold">
                        <span class="text-primary-700">Sodakoh</span>
                        <span class="text-earth-700">Pohon</span>
                    </span>
                </div>
                
                <div class="flex items-center text-sm">
                    <span class="flex items-center text-primary-700">
                        <i class="fas fa-check-circle mr-2"></i>
                        Keranjang
                    </span>
                    <i class="fas fa-chevron-right mx-3 text-gray-300"></i>
                    <span class="flex items-center text-primary-700">
                        <i class="fas fa-circle mr-2"></i>
                        Checkout
                    </span>
                    <i class="fas fa-chevron-right mx-3 text-gray-300"></i>
                    <span class="flex items-center text-gray-400">
                        <i class="fas fa-circle mr-2"></i>
                        Selesai
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-20 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-8">
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Checkout Donasi</h1>
            <p class="text-gray-600">Lengkapi data diri untuk melanjutkan pembayaran</p>
        </div>
        
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Form Column -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-card p-6 space-y-6">
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
                                <input type="text" 
                                       placeholder="Masukkan nama lengkap"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       placeholder="Masukkan email aktif"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                                <p class="text-xs text-gray-500 mt-2">
                                    Konfirmasi donasi dan e-sertifikat akan dikirim ke email ini
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor WhatsApp
                                </label>
                                <input type="tel" 
                                       placeholder="Contoh: 08123456789"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="anonymous" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
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
                            Doa & Ucapan (Opsional)
                        </h2>
                        
                        <div>
                            <textarea rows="3" 
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
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div class="payment-method-card active border-2 border-primary-600 bg-primary-50 rounded-xl p-4 text-center">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" class="h-6 mx-auto mb-2">
                                <span class="text-xs font-semibold text-primary-700">BCA Virtual Account</span>
                            </div>
                            <div class="payment-method-card border-2 border-gray-200 rounded-xl p-4 text-center hover:border-primary-600">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/9/94/Bank_Mandiri_logo.svg" alt="Mandiri" class="h-6 mx-auto mb-2">
                                <span class="text-xs font-semibold text-gray-700">Mandiri VA</span>
                            </div>
                            <div class="payment-method-card border-2 border-gray-200 rounded-xl p-4 text-center hover:border-primary-600">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/6/68/Bank_BRI_logo.svg" alt="BRI" class="h-6 mx-auto mb-2">
                                <span class="text-xs font-semibold text-gray-700">BRI VA</span>
                            </div>
                            <div class="payment-method-card border-2 border-gray-200 rounded-xl p-4 text-center hover:border-primary-600">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/8/83/BNI_logo.svg" alt="BNI" class="h-6 mx-auto mb-2">
                                <span class="text-xs font-semibold text-gray-700">BNI VA</span>
                            </div>
                            <div class="payment-method-card border-2 border-gray-200 rounded-xl p-4 text-center hover:border-primary-600">
                                <i class="fas fa-wallet text-2xl text-gray-600 mb-2"></i>
                                <span class="text-xs font-semibold text-gray-700">OVO</span>
                            </div>
                            <div class="payment-method-card border-2 border-gray-200 rounded-xl p-4 text-center hover:border-primary-600">
                                <i class="fas fa-mobile-alt text-2xl text-gray-600 mb-2"></i>
                                <span class="text-xs font-semibold text-gray-700">GoPay</span>
                            </div>
                            <div class="payment-method-card border-2 border-gray-200 rounded-xl p-4 text-center hover:border-primary-600">
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
                </div>
            </div>
            
            <!-- Summary Column -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-card p-6 sticky top-24">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Donasi</h2>
                    
                    <?php foreach ($cart_items as $item): ?>
                    <div class="flex justify-between text-sm mb-3">
                        <div>
                            <span class="font-medium text-gray-900"><?php echo $item['campaign_title']; ?></span>
                            <span class="text-gray-500 block text-xs"><?php echo $item['quantity']; ?> pohon x Rp <?php echo number_format($item['price_per_tree']); ?></span>
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
                    </div>
                    
                    <button onclick="processPayment()" 
                            class="w-full mt-6 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold py-4 px-6 rounded-2xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25 flex items-center justify-center">
                        <i class="fas fa-lock mr-2"></i>
                        Bayar Sekarang
                    </button>
                    
                    <p class="text-xs text-gray-500 text-center mt-4">
                        <i class="fas fa-shield-alt mr-1 text-primary-600"></i>
                        Pembayaran aman & terenkripsi
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function processPayment() {
            // Validasi form
            if (!document.getElementById('terms').checked) {
                alert('Harap setuju dengan syarat dan ketentuan');
                return;
            }
            
            // Simulasi proses pembayaran
            alert('Memproses pembayaran...\nTotal donasi: Rp <?php echo number_format($total_amount); ?>\nAnda akan diarahkan ke halaman pembayaran.');
            
            // Redirect ke halaman sukses (simulasi)
            window.location.href = 'success.php';
        }
        
        // Payment method selection
        document.querySelectorAll('.payment-method-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.payment-method-card').forEach(c => {
                    c.classList.remove('active', 'border-primary-600', 'bg-primary-50');
                    c.classList.add('border-gray-200');
                });
                this.classList.add('active', 'border-primary-600', 'bg-primary-50');
                this.classList.remove('border-gray-200');
            });
        });
    </script>
</body>
</html>