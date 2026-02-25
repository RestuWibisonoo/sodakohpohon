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
<?php include 'includes/header-simple.php'; ?>

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