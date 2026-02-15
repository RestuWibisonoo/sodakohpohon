<?php
// controllers/donationController.php
// Controller untuk menangani semua request terkait donasi

require_once dirname(__DIR__) . '/config/koneksi.php';
require_once dirname(__DIR__) . '/models/Donation.php';
require_once dirname(__DIR__) . '/models/Campaign.php';
require_once dirname(__DIR__) . '/models/Cart.php';

class DonationController {
    private $donation;
    private $campaign;
    private $cart;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->donation = new Donation();
        $this->campaign = new Campaign();
        $this->cart = new Cart();
    }
    
    /**
     * Menampilkan halaman checkout
     */
    public function checkout() {
        // Cek apakah keranjang kosong
        if ($this->cart->isEmpty()) {
            $this->redirectWithMessage('index.php#campaigns', 'Keranjang donasi masih kosong', 'error');
            return;
        }
        
        $cart_summary = $this->cart->getSummary();
        
        include dirname(__DIR__) . '/checkout.php';
    }
    
    /**
     * Proses donasi
     */
    public function processDonation() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
            exit;
        }
        
        // Validasi keranjang
        if ($this->cart->isEmpty()) {
            echo json_encode([
                'success' => false,
                'message' => 'Keranjang donasi kosong'
            ]);
            exit;
        }
        
        // Validasi input
        $donor_name = isset($_POST['donor_name']) ? trim($_POST['donor_name']) : '';
        $donor_email = isset($_POST['donor_email']) ? trim($_POST['donor_email']) : '';
        $donor_phone = isset($_POST['donor_phone']) ? trim($_POST['donor_phone']) : '';
        $anonymous = isset($_POST['anonymous']) ? (bool)$_POST['anonymous'] : false;
        $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';
        $terms = isset($_POST['terms']) ? (bool)$_POST['terms'] : false;
        
        // Validasi terms
        if (!$terms) {
            echo json_encode([
                'success' => false,
                'message' => 'Anda harus menyetujui syarat dan ketentuan'
            ]);
            exit;
        }
        
        // Validasi nama untuk non-anonim
        if (!$anonymous && empty($donor_name)) {
            echo json_encode([
                'success' => false,
                'message' => 'Nama donatur harus diisi'
            ]);
            exit;
        }
        
        // Validasi email
        if (!filter_var($donor_email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false,
                'message' => 'Format email tidak valid'
            ]);
            exit;
        }
        
        // Validasi metode pembayaran
        if (empty($payment_method)) {
            echo json_encode([
                'success' => false,
                'message' => 'Pilih metode pembayaran'
            ]);
            exit;
        }
        
        // Proses donasi
        $donation_id = $this->cart->convertToDonation([
            'donor_name' => $donor_name,
            'donor_email' => $donor_email,
            'donor_phone' => $donor_phone,
            'anonymous' => $anonymous,
            'payment_method' => $payment_method,
            'message' => $message
        ]);
        
        if ($donation_id) {
            // Ambil data donasi untuk response
            $donation_data = $this->donation->getById($donation_id);
            
            echo json_encode([
                'success' => true,
                'message' => 'Donasi berhasil diproses',
                'donation_id' => $donation_id,
                'donation_number' => $donation_data['donation_number'],
                'redirect_url' => 'success.php?id=' . $donation_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal memproses donasi. Silakan coba lagi.'
            ]);
        }
        exit;
    }
    
    /**
     * Menampilkan halaman sukses donasi
     */
    public function success() {
        $donation_id = isset($_GET['id']) ? $_GET['id'] : 0;
        
        if ($donation_id <= 0) {
            $this->redirectWithMessage('index.php', 'Donasi tidak ditemukan', 'error');
            return;
        }
        
        $donation = $this->donation->getById($donation_id);
        
        if (!$donation) {
            $this->redirectWithMessage('index.php', 'Donasi tidak ditemukan', 'error');
            return;
        }
        
        include dirname(__DIR__) . '/success.php';
    }
    
    /**
     * API: Konfirmasi pembayaran (callback dari payment gateway)
     */
    public function paymentCallback() {
        header('Content-Type: application/json');
        
        // Ini akan diintegrasikan dengan payment gateway
        // Contoh: Midtrans, Xendit, etc.
        
        $donation_id = isset($_POST['donation_id']) ? $_POST['donation_id'] : 0;
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        
        if ($status === 'success' || $status === 'paid') {
            $result = $this->donation->confirmDonation($donation_id);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Pembayaran berhasil dikonfirmasi'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal mengkonfirmasi pembayaran'
                ]);
            }
        }
        
        exit;
    }
    
    /**
     * API: Cek status donasi
     */
    public function checkStatus() {
        header('Content-Type: application/json');
        
        $donation_number = isset($_GET['donation_number']) ? $_GET['donation_number'] : '';
        
        if (empty($donation_number)) {
            echo json_encode([
                'success' => false,
                'message' => 'Nomor donasi tidak valid'
            ]);
            exit;
        }
        
        $donation = $this->donation->getByDonationNumber($donation_number);
        
        if ($donation) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'donation_number' => $donation['donation_number'],
                    'status' => $donation['status'],
                    'donor_name' => $donation['donor_name'],
                    'campaign' => $donation['campaign_title'],
                    'trees' => $donation['trees_count'],
                    'amount' => $donation['amount'],
                    'date' => $donation['created_at']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Donasi tidak ditemukan'
            ]);
        }
        
        exit;
    }
    
    /**
     * API: Get donasi by email
     */
    public function getByEmail() {
        header('Content-Type: application/json');
        
        $email = isset($_GET['email']) ? $_GET['email'] : '';
        
        if (empty($email)) {
            echo json_encode([
                'success' => false,
                'message' => 'Email tidak valid'
            ]);
            exit;
        }
        
        $donations = $this->donation->getByEmail($email);
        
        echo json_encode([
            'success' => true,
            'data' => $donations,
            'total' => count($donations)
        ]);
        exit;
    }
    
    /**
     * Download sertifikat donasi
     */
    public function downloadCertificate() {
        $donation_id = isset($_GET['id']) ? $_GET['id'] : 0;
        
        if ($donation_id <= 0) {
            $this->redirectWithMessage('index.php', 'Donasi tidak ditemukan', 'error');
            return;
        }
        
        $donation = $this->donation->getById($donation_id);
        
        if (!$donation || $donation['status'] !== 'paid') {
            $this->redirectWithMessage('index.php', 'Sertifikat hanya tersedia untuk donasi sukses', 'error');
            return;
        }
        
        // Generate PDF sertifikat
        $this->generateCertificatePDF($donation);
    }
    
    /**
     * Generate PDF sertifikat
     */
    private function generateCertificatePDF($donation) {
        // Ini akan diimplementasikan dengan library PDF seperti Dompdf, TCPDF, dll
        // Untuk sekarang, redirect ke halaman success dengan parameter download
        
        header('Location: success.php?id=' . $donation['id'] . '&download=certificate');
        exit;
    }
    
    /**
     * Redirect dengan flash message
     */
    private function redirectWithMessage($url, $message, $type = 'success') {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
        header('Location: ' . $url);
        exit;
    }
}

// Router untuk donation controller
$action = isset($_GET['action']) ? $_GET['action'] : 'checkout';
$controller = new DonationController();

switch ($action) {
    case 'process':
        $controller->processDonation();
        break;
        
    case 'success':
        $controller->success();
        break;
        
    case 'callback':
        $controller->paymentCallback();
        break;
        
    case 'check_status':
        $controller->checkStatus();
        break;
        
    case 'get_by_email':
        $controller->getByEmail();
        break;
        
    case 'download_certificate':
        $controller->downloadCertificate();
        break;
        
    default:
        $controller->checkout();
        break;
}
?>