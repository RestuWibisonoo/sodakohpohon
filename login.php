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
<?php include 'includes/header-simple.php'; ?>

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