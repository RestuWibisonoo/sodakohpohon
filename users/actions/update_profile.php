<?php
// users/actions/update_profile.php
// Handle profile update untuk nama dan nomor telepon

session_start();

// Proteksi: Jika belum login, tendang ke login.php
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = 'Anda harus login untuk mengubah profil';
    $_SESSION['flash_type'] = 'error';
    header('Location: ../login.php');
    exit;
}

require_once dirname(dirname(__DIR__)) . '/config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /users/profile.php');
    exit;
}

// Ambil dan sanitasi input
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

// Validasi
$errors = [];

if (empty($name)) {
    $errors[] = 'Nama tidak boleh kosong';
}

if (strlen($name) < 3) {
    $errors[] = 'Nama minimal 3 karakter';
}

if (strlen($name) > 255) {
    $errors[] = 'Nama maksimal 255 karakter';
}

if (!empty($phone) && strlen($phone) > 20) {
    $errors[] = 'Nomor telepon maksimal 20 karakter';
}

// Jika ada error, kembalikan ke halaman profil dengan query parameter
if (!empty($errors)) {
    $error_msg = urlencode(implode(' | ', $errors));
    header('Location: /users/profile.php?status=error&message=' . $error_msg);
    exit;
}

try {
    $conn = getDB();
    $user_id = $_SESSION['user_id'];
    
    // Escape input untuk keamanan
    $name_esc = $conn->real_escape_string($name);
    $phone_esc = $conn->real_escape_string($phone);
    
    // Update ke database
    $sql = "UPDATE users SET name = '{$name_esc}', phone = '{$phone_esc}', updated_at = NOW() WHERE id = {$user_id}";
    
    if (!$conn->query($sql)) {
        throw new Exception('Gagal memperbarui profil: ' . $conn->error);
    }
    
    // Update session dengan data terbaru
    $_SESSION['user_name'] = $name;
    $_SESSION['user_phone'] = $phone;
    
    // Redirect dengan status success
    header('Location: /users/profile.php?status=success&message=' . urlencode('Profil berhasil diperbarui!'));
    exit;
    
} catch (Exception $e) {
    $error_msg = urlencode('Error: ' . $e->getMessage());
    header('Location: /users/profile.php?status=error&message=' . $error_msg);
    exit;
}
?>
