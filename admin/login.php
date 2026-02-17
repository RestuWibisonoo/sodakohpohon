<?php
// admin/login.php - Halaman Login Admin
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

require_once '../config/koneksi.php';

 $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (!empty($email) && !empty($password)) {
        $conn = getDB();
        $email_esc = $conn->real_escape_string($email);
        
        $sql = "SELECT * FROM users WHERE email = '{$email_esc}' AND role = 'admin' LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password (using password_verify for hashed passwords)
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['name'];
                $_SESSION['admin_email'] = $user['email'];
                
                // Update last login
                $conn->query("UPDATE users SET last_login = NOW() WHERE id = {$user['id']}");
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Password yang Anda masukkan salah';
            }
        } else {
            $error = 'Email tidak ditemukan atau bukan admin';
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
    <title>Login Admin - Sodakoh Pohon</title>
    
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
            background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center space-x-3">
                    <div class="bg-white rounded-xl p-2.5 shadow-lg">
                        <i class="fas fa-tree text-primary-600 text-2xl"></i>
                    </div>
                    <div class="text-left">
                        <span class="text-2xl font-extrabold text-white">
                            Sodakoh<span class="text-primary-100">Pohon</span>
                        </span>
                        <span class="block text-xs text-white/70">Administrator Panel</span>
                    </div>
                </div>
            </div>
            
            <!-- Login Card -->
            <div class="bg-white rounded-3xl shadow-2xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 text-center mb-2">Selamat Datang</h2>
                <p class="text-gray-500 text-center mb-8">Silakan masuk ke akun admin Anda</p>
                
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
                                       placeholder="admin@sodakohpohon.id"
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
                                <input type="checkbox" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                            </label>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold py-3 px-6 rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Masuk
                        </button>
                    </div>
                </form>
                
                <div class="mt-6 text-center">
                    <a href="../index.php" class="text-sm text-gray-500 hover:text-primary-600 transition">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali ke Website
                    </a>
                </div>
            </div>
            
            <!-- Info -->
            <div class="text-center mt-6 text-white/70 text-sm">
                <p>© 2026 Sodakoh Pohon. All rights reserved.</p>
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