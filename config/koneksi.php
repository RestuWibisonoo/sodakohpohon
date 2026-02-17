<?php
// config/koneksi.php
// Konfigurasi koneksi database MySQL untuk Sodakoh Pohon

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sodakoh_pohon');

// Base URL configuration
define('BASE_URL', 'http://localhost/sodakohpohon');
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');

/**
 * Kelas Koneksi Database
 * Menggunakan pattern Singleton untuk koneksi yang efisien
 */
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            // Set charset ke UTF-8
            $this->conn->set_charset("utf8mb4");
            
            // Cek koneksi
            if ($this->conn->connect_error) {
                throw new Exception("Koneksi database gagal: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
    
    /**
     * Mendapatkan instance koneksi database
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    /**
     * Mendapatkan object koneksi mysqli
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Escape string untuk keamanan
     */
    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }
    
    /**
     * Menutup koneksi database
     */
    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

/**
 * Fungsi helper untuk mendapatkan koneksi database
 */
function getDB() {
    $db = Database::getInstance();
    return $db->getConnection();
}

/**
 * Fungsi helper untuk query dan return result dalam bentuk array
 */
function db_query($sql) {
    $conn = getDB();
    $result = $conn->query($sql);
    
    if ($result === false) {
        throw new Exception("Query error: " . $conn->error);
    }
    
    if ($result === true) {
        return true;
    }
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

/**
 * Fungsi helper untuk query dan return single row
 */
function db_get_row($sql) {
    $data = db_query($sql);
    return !empty($data) ? $data[0] : null;
}

/**
 * Fungsi helper untuk insert data
 */
function db_insert($table, $data) {
    $conn = getDB();
    
    $columns = implode(", ", array_keys($data));
    $values = "'" . implode("', '", array_map(function($value) use ($conn) {
        return $conn->real_escape_string($value);
    }, array_values($data))) . "'";
    
    $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
    
    if ($conn->query($sql)) {
        return $conn->insert_id;
    }
    
    return false;
}

/**
 * Fungsi helper untuk update data
 */
function db_update($table, $data, $where) {
    $conn = getDB();
    
    $set = [];
    foreach ($data as $key => $value) {
        $set[] = "{$key} = '" . $conn->real_escape_string($value) . "'";
    }
    $set_string = implode(", ", $set);
    
    $where_clause = [];
    foreach ($where as $key => $value) {
        $where_clause[] = "{$key} = '" . $conn->real_escape_string($value) . "'";
    }
    $where_string = implode(" AND ", $where_clause);
    
    $sql = "UPDATE {$table} SET {$set_string} WHERE {$where_string}";
    
    return $conn->query($sql);
}

/**
 * Fungsi helper untuk delete data
 */
function db_delete($table, $where) {
    $conn = getDB();
    
    $where_clause = [];
    foreach ($where as $key => $value) {
        $where_clause[] = "{$key} = '" . $conn->real_escape_string($value) . "'";
    }
    $where_string = implode(" AND ", $where_clause);
    
    $sql = "DELETE FROM {$table} WHERE {$where_string}";
    
    return $conn->query($sql);
}

/**
 * Buat folder uploads jika belum ada
 */
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0777, true);
}

/**
 * Session start untuk keperluan cart & login
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Fungsi helper untuk cek apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

/**
 * Fungsi helper untuk cek apakah admin sudah login
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Fungsi helper untuk mendapatkan data user yang login
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? ''
    ];
}

/**
 * Fungsi helper untuk mendapatkan data admin yang login
 */
function getCurrentAdmin() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['admin_id'] ?? null,
        'name' => $_SESSION['admin_name'] ?? '',
        'email' => $_SESSION['admin_email'] ?? ''
    ];
}

/**
 * Fungsi helper untuk redirect
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Fungsi helper untuk set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Fungsi helper untuk mendapatkan flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}
?>