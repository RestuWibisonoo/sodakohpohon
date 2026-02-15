<?php
// success.php
session_start();

// Simulasi data donasi yang berhasil
$donation = [
    'id' => 'SP-' . date('Ymd') . '-001',
    'campaign_title' => 'Restorasi Mangrove Demak',
    'trees_count' => 5,
    'total_amount' => 50000,
    'donor_name' => 'Anonymous',
    'date' => date('d F Y'),
    'certificate_number' => 'SP-CERT-' . date('Ymd') . '-001'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih - Sodakoh Pohon</title>
    
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
        
        @keyframes checkmark {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .checkmark-animation {
            animation: checkmark 0.6s ease-out forwards;
        }
        
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #10b981;
            animation: confetti 5s ease-in-out infinite;
        }
        
        @keyframes confetti {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="antialiased">
    <!-- Confetti Effect -->
    <div id="confetti-container" class="fixed inset-0 pointer-events-none z-50"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-40 glass-effect border-b border-gray-200/50">
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
                
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                        <i class="fas fa-tree mr-2"></i>
                        Donasi Lagi
                    </a>
                </div>
            </div>
        </div>
    </nav>

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
                    <div class="w-24 h-24 mx-auto bg-gradient-to-r from-primary-600 to-primary-700 rounded-full flex items-center justify-center checkmark-animation shadow-xl shadow-primary-600/30">
                        <i class="fas fa-check text-white text-4xl"></i>
                    </div>
                    <div class="absolute -top-2 -right-2 w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center animate-bounce">
                        <i class="fas fa-leaf text-white"></i>
                    </div>
                </div>
                
                <!-- Thank You Message -->
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">
                    Terima Kasih!
                </h1>
                <p class="text-lg text-gray-600 mb-8">
                    Anda telah berhasil menyedekahkan <span class="font-bold text-primary-700 text-2xl"><?php echo $donation['trees_count']; ?> pohon</span> 
                    melalui program <span class="font-semibold"><?php echo $donation['campaign_title']; ?></span>
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
                            <span class="font-semibold text-gray-900"><?php echo $donation['id']; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Program</span>
                            <span class="font-semibold text-gray-900"><?php echo $donation['campaign_title']; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah Pohon</span>
                            <span class="font-semibold text-primary-700"><?php echo $donation['trees_count']; ?> pohon</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Donasi</span>
                            <span class="font-bold text-gray-900">Rp <?php echo number_format($donation['total_amount']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal</span>
                            <span class="text-gray-700"><?php echo $donation['date']; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status</span>
                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">
                                <i class="fas fa-check-circle mr-1"></i>
                                BERHASIL
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Certificate Card -->
                <div class="border-2 border-primary-200 rounded-2xl p-6 mb-8 bg-gradient-to-br from-white to-primary-50">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-certificate text-3xl text-primary-700"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <h4 class="font-bold text-gray-900">Sertifikat Donasi</h4>
                            <p class="text-sm text-gray-600 mb-2">
                                Nomor: <?php echo $donation['certificate_number']; ?>
                            </p>
                            <button onclick="downloadCertificate()" 
                                    class="inline-flex items-center text-primary-700 font-semibold text-sm hover:text-primary-800 transition">
                                <i class="fas fa-download mr-2"></i>
                                Unduh Sertifikat
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="campaign-detail.php?id=1" 
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
                        <button onclick="shareTelegram()" class="w-12 h-12 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition">
                            <i class="fab fa-telegram-plane text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Tracking Information -->
            <div class="bg-white rounded-2xl shadow-card p-6 mt-6 text-center">
                <div class="flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-map-marker-alt text-primary-600"></i>
                    <span class="text-gray-700">Lokasi penanaman:</span>
                    <span class="font-semibold text-gray-900">Demak, Jawa Tengah</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Anda akan menerima update perkembangan penanaman melalui email dalam 7-14 hari ke depan
                </p>
            </div>
        </div>
    </div>

    <script>
        // Create confetti effect
        function createConfetti() {
            const container = document.getElementById('confetti-container');
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.background = `hsl(${Math.random() * 360}, 70%, 50%)`;
                confetti.style.width = Math.random() * 10 + 5 + 'px';
                confetti.style.height = Math.random() * 10 + 5 + 'px';
                confetti.style.animationDelay = Math.random() * 2 + 's';
                confetti.style.animationDuration = Math.random() * 3 + 3 + 's';
                container.appendChild(confetti);
            }
        }
        
        // Start confetti on page load
        window.addEventListener('load', createConfetti);
        
        function downloadCertificate() {
            // Simulasi download sertifikat
            alert('Sertifikat donasi sedang diunduh...\nNomor: <?php echo $donation['certificate_number']; ?>');
        }
        
        function shareWhatsApp() {
            const text = encodeURIComponent('Saya baru saja menyedekahkan <?php echo $donation['trees_count']; ?> pohon melalui Sodakoh Pohon. Yuk, ikut berkontribusi untuk lingkungan! 🌳');
            window.open(`https://wa.me/?text=${text}`, '_blank');
        }
        
        function shareFacebook() {
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
        }
        
        function shareTwitter() {
            const text = encodeURIComponent('Saya baru saja menyedekahkan <?php echo $donation['trees_count']; ?> pohon melalui Sodakoh Pohon');
            const url = encodeURIComponent(window.location.href);
            window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
        }
        
        function shareTelegram() {
            const text = encodeURIComponent('Saya baru saja menyedekahkan <?php echo $donation['trees_count']; ?> pohon melalui Sodakoh Pohon');
            const url = encodeURIComponent(window.location.href);
            window.open(`https://t.me/share/url?url=${url}&text=${text}`, '_blank');
        }
    </script>
</body>
</html>