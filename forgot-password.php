<?php
// forgot-password.php - Halaman Lupa Password
session_start();

require_once 'config/koneksi.php';

 $error = '';
 $success = '';
 $step = 'email'; // email or reset

// Cek apakah ada token di URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $conn = getDB();
    $token_esc = $conn->real_escape_string($token);
    
    // Cek token valid (simulasi - dalam produksi perlu tabel password_resets)
    // Untuk sekarang, kita skip validasi token
    $step = 'reset';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'request') {
        // Step 1: Request reset
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        
        if (empty($email)) {
            $error = 'Email harus diisi';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid';
        } else {
            $conn = getDB();
            $email_esc = $conn->real_escape_string($email);
            
            // Cek apakah email ada
            $sql = "SELECT id, name FROM users WHERE email = '{$email_esc}' LIMIT 1";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                // Dalam produksi: Kirim email dengan link reset
                // Untuk simulasi: Tampilkan pesan sukses
                $success = 'Link reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam.';
            } else {
                // Tetap tampilkan sukses untuk keamanan (tidak memberitahu email tidak ada)
                $success = 'Jika email terdaftar, link reset password telah dikirim ke email Anda.';
            }
        }
    } elseif ($action === 'reset') {
        // Step 2: Reset password
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        if (empty($new_password)) {
            $error = 'Password baru harus diisi';
        } elseif (strlen($new_password) < 6) {
            $error = 'Password minimal 6 karakter';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Konfirmasi password tidak cocok';
        } else {
            // Dalam produksi: Update password berdasarkan token
            // Untuk simulasi: Anggap berhasil
            $success = 'Password berhasil diubah. Silakan login dengan password baru.';
            $step = 'done';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Sodakoh Pohon</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        },
                        earth: {
                            50: '#faf7f2',
                            500: '#b8946b',
                            700: '#7f6042',
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
                <a href="index.php" class="flex items-center space-x-3">
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
                </a>

                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-primary-600 font-medium transition">Beranda</a>
                    <a href="login.php" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="pt-20 min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-3xl shadow-card p-8" data-aos="fade-up">
                <?php if ($step === 'email' && !$success): ?>
                <!-- Step 1: Request Email -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 mx-auto bg-primary-100 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-key text-primary-600 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Lupa Password?</h2>
                    <p class="text-gray-500">Masukkan email akun Anda dan kami akan mengirimkan link untuk reset password</p>
                </div>
                
                <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <input type="hidden" name="action" value="request">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <div class="relative">
                                <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="email" 
                                       name="email"
                                       placeholder="nama@email.com"
                                       required
                                       class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold py-3 px-6 rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Kirim Link Reset
                        </button>
                    </div>
                </form>
                
                <?php elseif ($step === 'reset' && !$success): ?>
                <!-- Step 2: Reset Password -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 mx-auto bg-primary-100 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-lock text-primary-600 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Buat Password Baru</h2>
                    <p class="text-gray-500">Masukkan password baru untuk akun Anda</p>
                </div>
                
                <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <input type="hidden" name="action" value="reset">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                            <div class="relative">
                                <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="password" 
                                       name="new_password"
                                       id="new_password"
                                       placeholder="Minimal 6 karakter"
                                       required
                                       class="w-full pl-11 pr-12 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                                <button type="button" onclick="togglePassword('new_password', 'icon1')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="icon1"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                            <div class="relative">
                                <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="password" 
                                       name="confirm_password"
                                       id="confirm_password"
                                       placeholder="Ulangi password"
                                       required
                                       class="w-full pl-11 pr-12 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                                <button type="button" onclick="togglePassword('confirm_password', 'icon2')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="icon2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold py-3 px-6 rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                            <i class="fas fa-check mr-2"></i>
                            Simpan Password Baru
                        </button>
                    </div>
                </form>
                
                <?php else: ?>
                <!-- Success -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-green-100 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Berhasil!</h2>
                    <p class="text-gray-500 mb-6"><?php echo $success; ?></p>
                    
                    <a href="login.php" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Login Sekarang
                    </a>
                </div>
                <?php endif; ?>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600">
                        Ingat password? 
                        <a href="login.php" class="text-primary-600 hover:text-primary-700 font-semibold">Masuk di sini</a>
                    </p>
                </div>
            </div>
            
            <!-- Help -->
            <div class="mt-6 text-center text-sm text-gray-500">
                <p>Butuh bantuan? Hubungi <a href="mailto:support@sodakohpohon.id" class="text-primary-600 hover:underline">support@sodakohpohon.id</a></p>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword(inputId, iconId) {
            const password = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>