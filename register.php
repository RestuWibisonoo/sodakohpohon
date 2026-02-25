<?php
// register.php - Halaman Registrasi User/Donatur
session_start();

// Jika sudah login, redirect ke index
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

require_once 'config/koneksi.php';

 $error = '';
 $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
    $terms = isset($_POST['terms']) ? true : false;
    
    // Validasi
    if (empty($name)) {
        $error = 'Nama lengkap harus diisi';
    } elseif (empty($email)) {
        $error = 'Email harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } elseif (empty($password)) {
        $error = 'Password harus diisi';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } elseif ($password !== $password_confirm) {
        $error = 'Konfirmasi password tidak cocok';
    } elseif (!$terms) {
        $error = 'Anda harus menyetujui syarat dan ketentuan';
    } else {
        $conn = getDB();
        $email_esc = $conn->real_escape_string($email);
        
        // Cek apakah email sudah terdaftar
        $sql = "SELECT id FROM users WHERE email = '{$email_esc}' LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $error = 'Email sudah terdaftar. Silakan login atau gunakan email lain.';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user baru
            $name_esc = $conn->real_escape_string($name);
            $phone_esc = $conn->real_escape_string($phone);
            
            $sql = "INSERT INTO users (name, email, password, phone, role, created_at) 
                    VALUES ('{$name_esc}', '{$email_esc}', '{$hashed_password}', '{$phone_esc}', 'user', NOW())";
            
            if ($conn->query($sql)) {
                $success = 'Registrasi berhasil! Silakan login untuk melanjutkan.';
            } else {
                $error = 'Terjadi kesalahan. Silakan coba lagi.';
            }
        }
    }
}
?>
<?php include 'includes/header-simple.php'; ?>

    <!-- Register Content -->
    <div class="pt-20 min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-3xl shadow-card p-8" data-aos="fade-up">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 mx-auto bg-primary-100 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-user-plus text-primary-600 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Daftar Akun Baru</h2>
                    <p class="text-gray-500">Bergabunglah sebagai donatur untuk mendapatkan manfaat lebih</p>
                </div>
                
                <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $success; ?>
                    <a href="login.php" class="ml-2 font-semibold underline">Login di sini</a>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" 
                                       name="name"
                                       placeholder="Masukkan nama lengkap"
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                       required
                                       class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="email" 
                                       name="email"
                                       placeholder="nama@email.com"
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                       required
                                       class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor WhatsApp
                            </label>
                            <div class="relative">
                                <i class="fab fa-whatsapp absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="tel" 
                                       name="phone"
                                       placeholder="08123456789"
                                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                       class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="password" 
                                       name="password"
                                       id="password"
                                       placeholder="Minimal 6 karakter"
                                       required
                                       class="w-full pl-11 pr-12 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                                <button type="button" onclick="togglePassword('password', 'toggleIcon1')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="toggleIcon1"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="password" 
                                       name="password_confirm"
                                       id="password_confirm"
                                       placeholder="Ulangi password"
                                       required
                                       class="w-full pl-11 pr-12 py-3 border border-gray-200 rounded-xl focus:border-primary-600 focus:ring-2 focus:ring-primary-100 outline-none transition">
                                <button type="button" onclick="togglePassword('password_confirm', 'toggleIcon2')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="toggleIcon2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <input type="checkbox" 
                                   name="terms" 
                                   id="terms"
                                   required
                                   class="mt-1 w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <label for="terms" class="ml-3 text-sm text-gray-600">
                                Saya setuju dengan <a href="#" class="text-primary-600 hover:underline">Syarat dan Ketentuan</a> serta <a href="#" class="text-primary-600 hover:underline">Kebijakan Privasi</a>
                            </label>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold py-3 px-6 rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                            <i class="fas fa-user-plus mr-2"></i>
                            Daftar Sekarang
                        </button>
                    </div>
                </form>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600">
                        Sudah punya akun? 
                        <a href="login.php" class="text-primary-600 hover:text-primary-700 font-semibold">Masuk di sini</a>
                    </p>
                </div>
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