<?php
// auth.php - Router untuk autentikasi
session_start();

require_once 'controllers/authController.php';

 $controller = new AuthController();
 $action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        $controller->loginUser();
        break;
        
    case 'admin_login':
        $controller->loginAdmin();
        break;
        
    case 'register':
        $controller->register();
        break;
        
    case 'forgot_password':
        $controller->forgotPassword();
        break;
        
    case 'reset_password':
        $controller->resetPassword();
        break;
        
    case 'logout':
        $controller->logout();
        break;
        
    default:
        header('Location: index.php');
        exit;
}