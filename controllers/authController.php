<?php
// controllers/authController.php
// Controller untuk menangani autentikasi user

require_once dirname(__DIR__) . '/config/koneksi.php';

class AuthController {
    
    /**
     * Login User/Donatur
     */
    public function loginUser() {
        // Login logic sudah dihandle di login.php
        // Jika dipanggil via AJAX, bisa handle di sini
        redirect('login.php');
    }
    
    /**
     * Login Admin
     */
    public function loginAdmin() {
        // Login admin sudah dihandle di admin/login.php
        redirect('admin/login.php');
    }
    
    /**
     * Register User
     */
    public function register() {
        redirect('register.php');
    }
    
    /**
     * Forgot Password
     */
    public function forgotPassword() {
        redirect('forgot-password.php');
    }
    
    /**
     * Reset Password
     */
    public function resetPassword() {
        redirect('forgot-password.php');
    }
    
    /**
     * Logout User/Admin
     */
    public function logout() {
        // Unset semua session
        $_SESSION = array();
        
        // Destroy session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
        
        // Redirect ke home
        setFlashMessage('success', 'Anda telah berhasil logout');
        redirect('index.php');
    }
}
?>
