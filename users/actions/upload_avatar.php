<?php
// users/actions/upload_avatar.php
// Handle upload dan resize avatar

session_start();

// Proteksi: Jika belum login
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Anda harus login']);
    exit;
}

require_once dirname(dirname(__DIR__)) . '/config/koneksi.php';

// Set response header
header('Content-Type: application/json');

// Validasi request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

// Validasi file upload
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'File tidak dikirimkan dengan benar']);
    exit;
}

$file = $_FILES['avatar'];
$errors = [];

// Validasi ukuran file (max 2MB)
$max_size = 2 * 1024 * 1024; // 2MB in bytes
if ($file['size'] > $max_size) {
    $errors[] = 'Ukuran file terlalu besar (maksimal 2MB)';
}

// Validasi MIME type
$allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    $errors[] = 'Format file tidak didukung (hanya JPG, PNG, WebP)';
}

// Validasi ekstensi file (double check)
$filename = basename($file['name']);
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($ext, $allowed_extensions)) {
    $errors[] = 'Ekstensi file tidak valid';
}

// Jika ada error
if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    $conn = getDB();
    $user_id = $_SESSION['user_id'];
    
    // Ambil nama file avatar lama untuk dihapus
    $sql = "SELECT avatar FROM users WHERE id = {$user_id}";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();
    $old_avatar = $user['avatar'] ?? null;
    
    // Generate nama file baru yang unik
    $new_filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
    $upload_dir = dirname(dirname(__DIR__)) . '/uploads/avatars/';
    $upload_path = $upload_dir . $new_filename;
    
    // Pastikan directory ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Load image dan resize
    $image = null;
    
    switch ($mime_type) {
        case 'image/jpeg':
            $image = @imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'image/png':
            $image = @imagecreatefrompng($file['tmp_name']);
            break;
        case 'image/webp':
            $image = @imagecreatefromwebp($file['tmp_name']);
            break;
    }
    
    if (!$image) {
        throw new Exception('Gagal membaca file gambar');
    }
    
    // Resize ke 200x200px dengan maintain aspect ratio
    $current_width = imagesx($image);
    $current_height = imagesy($image);
    $target_size = 200;
    
    // Hitung dimensi crop untuk membuat square
    $crop_size = min($current_width, $current_height);
    $crop_x = ($current_width - $crop_size) / 2;
    $crop_y = ($current_height - $crop_size) / 2;
    
    // Crop ke square dulu
    $cropped = imagecrop($image, [
        'x' => (int)$crop_x,
        'y' => (int)$crop_y,
        'width' => (int)$crop_size,
        'height' => (int)$crop_size
    ]);
    
    if (!$cropped) {
        throw new Exception('Gagal crop gambar');
    }
    
    // Buat image baru dengan ukuran target
    $resized = imagecreatetruecolor($target_size, $target_size);
    
    // Preserve transparency untuk PNG dan WebP
    if ($mime_type === 'image/png' || $mime_type === 'image/webp') {
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
        imagefilledrectangle($resized, 0, 0, $target_size, $target_size, $transparent);
    }
    
    // Resize gambar
    imagecopyresampled($resized, $cropped, 0, 0, 0, 0, $target_size, $target_size, $crop_size, $crop_size);
    
    // Save image
    $save_success = false;
    
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $save_success = imagejpeg($resized, $upload_path, 85);
            break;
        case 'png':
            $save_success = imagepng($resized, $upload_path, 6);
            break;
        case 'webp':
            $save_success = imagewebp($resized, $upload_path, 80);
            break;
    }
    
    imagedestroy($image);
    imagedestroy($cropped);
    imagedestroy($resized);
    
    if (!$save_success) {
        throw new Exception('Gagal menyimpan gambar');
    }
    
    // Hapus file avatar lama jika ada
    if (!empty($old_avatar) && $old_avatar !== null) {
        $old_path = dirname(dirname(__DIR__)) . '/' . $old_avatar;
        if (file_exists($old_path)) {
            @unlink($old_path);
        }
    }
    
    // Update database
    $avatar_relative_path = 'uploads/avatars/' . $new_filename;
    $avatar_path_esc = $conn->real_escape_string($avatar_relative_path);
    
    $sql = "UPDATE users SET avatar = '{$avatar_path_esc}', updated_at = NOW() WHERE id = {$user_id}";
    
    if (!$conn->query($sql)) {
        throw new Exception('Gagal mengupdate database: ' . $conn->error);
    }
    
    // Update session
    $_SESSION['user_avatar'] = $avatar_relative_path;
    
    echo json_encode([
        'success' => true,
        'message' => 'Avatar berhasil diupload',
        'avatar_url' => UPLOAD_URL . 'avatars/' . $new_filename
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

exit;
?>
