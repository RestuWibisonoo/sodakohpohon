<?php
// login.php - Halaman Login User/Donatur
session_start();

// Jika sudah login, redirect ke index
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

require_once 'config/koneksi.php';

 $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (!empty($email) && !empty($password)) {
        $conn = getDB();
        $email_esc = $conn->real_escape_string($email);
        
        $sql = "SELECT * FROM users WHERE email = '{$email_esc}' LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                // Update last login
                $conn->query("UPDATE users SET last_login = NOW() WHERE id = {$user['id']}");
                
                // Redirect to previous page or home
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = 'Password yang Anda masukkan salah';
            }
        } else {
            $error = 'Email tidak ditemukan';
        }
    } else {
        $error = 'Harap isi email dan password';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sodakoh Pohon</title>
    
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
                    <a href="register.php" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                        <i class="fas fa-user-plus mr-2"></i>Daftar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Login Content -->
    <div class="pt-20 min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-3xl shadow-card p-8" data-aos="fade-up">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 mx-auto bg-primary-100 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-user text-primary-600 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Masuk ke Akun</h2>
                    <p class="text-gray-500">Donatur yang login dapat melihat riwayat donasi</p>
                </div>
                
                <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
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
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <div class="relative">
                                <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="password" 
                                       name="password"
                                       id="password"
                                       placeholder="Masukkan password"
                                       required
                                       class="w-full pl-11 pr-12 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                                <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                            </label>
                            <a href="forgot-password.php" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                Lupa password?
                            </a>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold py-3 px-6 rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Masuk
                        </button>
                    </div>
                </form>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600">
                        Belum punya akun? 
                        <a href="register.php" class="text-primary-600 hover:text-primary-700 font-semibold">Daftar sekarang</a>
                    </p>
                </div>
                
                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">atau</span>
                    </div>
                </div>
                
                <!-- Guest Donation -->
                <a href="index.php#campaigns" 
                   class="w-full flex items-center justify-center px-4 py-3 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-primary-600 hover:text-primary-600 transition">
                    <i class="fas fa-heart mr-2"></i>
                    Donasi Tanpa Login
                </a>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            
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