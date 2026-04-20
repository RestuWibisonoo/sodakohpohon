<?php
// users/actions/change_password.php
// Handle perubahan password

session_start();

// Proteksi: Jika belum login
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = 'Anda harus login untuk mengubah password';
    $_SESSION['flash_type'] = 'error';
    header('Location: ../login.php');
    exit;
}

require_once dirname(dirname(__DIR__)) . '/config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./profile.php');
    exit;
}

// Ambil input
$old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

// Validasi
$errors = [];

if (empty($old_password)) {
    $errors[] = 'Password lama harus diisi';
}

if (empty($new_password)) {
    $errors[] = 'Password baru harus diisi';
}

if (strlen($new_password) < 6) {
    $errors[] = 'Password baru minimal 6 karakter';
}

if ($new_password !== $confirm_password) {
    $errors[] = 'Konfirmasi password tidak sesuai';
}

if ($old_password === $new_password) {
    $errors[] = 'Password baru tidak boleh sama dengan password lama';
}

// Jika ada error
if (!empty($errors)) {
    $error_msg = urlencode(implode(' | ', $errors));
    header('Location: /users/profile.php?status=error&message=' . $error_msg . '#change-password');
    exit;
}

try {
    $conn = getDB();
    $user_id = $_SESSION['user_id'];
    
    // Ambil password saat ini dari database
    $sql = "SELECT password FROM users WHERE id = {$user_id} LIMIT 1";
    $result = $conn->query($sql);
    
    if (!$result || $result->num_rows === 0) {
        throw new Exception('User tidak ditemukan');
    }
    
    $user = $result->fetch_assoc();
    
    // Verifikasi password lama
    if (!password_verify($old_password, $user['password'])) {
        $error_msg = urlencode('Password lama tidak sesuai');
        header('Location: /users/profile.php?status=error&message=' . $error_msg . '#change-password');
        exit;
    }
    
    // Hash password baru
    $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
    $new_password_hash_esc = $conn->real_escape_string($new_password_hash);
    
    // Update password di database
    $sql = "UPDATE users SET password = '{$new_password_hash_esc}', updated_at = NOW() WHERE id = {$user_id}";
    
    if (!$conn->query($sql)) {
        throw new Exception('Gagal memperbarui password: ' . $conn->error);
    }
    
    // Set flash message success
    $_SESSION['flash_message'] = 'Password berhasil diubah!';
    $success_msg = urlencode('Password berhasil diubah!');
    header('Location: /users/profile.php?status=success&message=' . $success_msg . '#change-password');
    exit;
    
} catch (Exception $e) {
    $error_msg = urlencode('Error: ' . $e->getMessage());
    header('Location: /users/profile.php?status=error&message=' . $error_msg . '#change-password');
    exit;
}
