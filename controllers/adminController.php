<?php
// controllers/adminController.php
// Controller untuk menangani semua request admin

require_once dirname(__DIR__) . '/config/koneksi.php';
require_once dirname(__DIR__) . '/models/Campaign.php';
require_once dirname(__DIR__) . '/models/Donation.php';
require_once dirname(__DIR__) . '/models/Cart.php';

class AdminController {
    private $campaign;
    private $donation;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Cek login admin
        $this->checkAuth();
        
        $this->campaign = new Campaign();
        $this->donation = new Donation();
    }
    
    /**
     * Cek autentikasi admin
     */
    private function checkAuth() {
        $allowed_pages = ['login', 'do_login'];
        $current_action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';
        
        if (!isset($_SESSION['admin_logged_in']) && !in_array($current_action, $allowed_pages)) {
            header('Location: login.php');
            exit;
        }
    }
    
    /**
     * Halaman login admin
     */
    public function login() {
        include dirname(__DIR__) . '/admin/login.php';
    }
    
    /**
     * Proses login admin
     */
    public function doLogin() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
            exit;
        }
        
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        // Validasi sederhana (nanti diganti dengan database)
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = 1;
            $_SESSION['admin_name'] = 'Admin Sodakoh';
            $_SESSION['admin_email'] = 'admin@sodakohpohon.id';
            
            echo json_encode([
                'success' => true,
                'message' => 'Login berhasil',
                'redirect' => 'index.php'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Username atau password salah'
            ]);
        }
        exit;
    }
    
    /**
     * Logout admin
     */
    public function logout() {
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_email']);
        
        header('Location: login.php');
        exit;
    }
    
    /**
     * Dashboard admin
     */
    public function dashboard() {
        $campaign_stats = $this->campaign->getStats();
        $donation_stats = $this->donation->getStats();
        
        $stats = array_merge($campaign_stats, $donation_stats);
        
        // Get recent donations
        $recent_donations = $this->donation->getAll('paid', null, 5);
        
        // Get active campaigns
        $active_campaigns = $this->campaign->getActiveCampaigns(4);
        
        include dirname(__DIR__) . '/admin/index.php';
    }
    
    /**
     * Manajemen campaign
     */
    public function campaign() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        
        switch ($action) {
            case 'create':
                $this->createCampaign();
                break;
                
            case 'edit':
                $this->editCampaign();
                break;
                
            case 'delete':
                $this->deleteCampaign();
                break;
                
            case 'store':
                $this->storeCampaign();
                break;
                
            case 'update':
                $this->updateCampaign();
                break;
                
            default:
                $this->listCampaigns();
                break;
        }
    }
    
    /**
     * List semua campaign
     */
    private function listCampaigns() {
        $campaigns = $this->campaign->getAll();
        include dirname(__DIR__) . '/admin/campaign.php';
    }
    
    /**
     * Form create campaign
     */
    private function createCampaign() {
        include dirname(__DIR__) . '/admin/campaign.php';
    }
    
    /**
     * Form edit campaign
     */
    private function editCampaign() {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $campaign = $this->campaign->getById($id);
        
        if (!$campaign) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Campaign tidak ditemukan'
            ];
            header('Location: campaign.php');
            exit;
        }
        
        include dirname(__DIR__) . '/admin/campaign.php';
    }
    
    /**
     * Proses simpan campaign baru
     */
    private function storeCampaign() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
            exit;
        }
        
        // Validasi input
        $errors = $this->validateCampaign($_POST);
        
        if (!empty($errors)) {
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            exit;
        }
        
        // Handle upload gambar
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_path = $this->campaign->uploadImage($_FILES['image']);
        }
        
        $data = $_POST;
        if ($image_path) {
            $data['image'] = $image_path;
        }
        
        $campaign_id = $this->campaign->create($data);
        
        if ($campaign_id) {
            // Simpan benefits jika ada
            if (isset($_POST['benefits']) && is_array($_POST['benefits'])) {
                foreach ($_POST['benefits'] as $benefit) {
                    if (!empty($benefit)) {
                        $this->campaign->addBenefit($campaign_id, $benefit);
                    }
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Campaign berhasil dibuat',
                'campaign_id' => $campaign_id,
                'redirect' => 'campaign.php'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal membuat campaign'
            ]);
        }
        exit;
    }
    
    /**
     * Proses update campaign
     */
    private function updateCampaign() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
            exit;
        }
        
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        
        if ($id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID campaign tidak valid'
            ]);
            exit;
        }
        
        // Validasi input
        $errors = $this->validateCampaign($_POST);
        
        if (!empty($errors)) {
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            exit;
        }
        
        // Handle upload gambar
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_path = $this->campaign->uploadImage($_FILES['image']);
            if ($image_path) {
                $_POST['image'] = $image_path;
            }
        }
        
        $result = $this->campaign->update($id, $_POST);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Campaign berhasil diperbarui',
                'redirect' => 'campaign.php'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal memperbarui campaign'
            ]);
        }
        exit;
    }
    
    /**
     * Hapus campaign
     */
    private function deleteCampaign() {
        header('Content-Type: application/json');
        
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        
        if ($id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID campaign tidak valid'
            ]);
            exit;
        }
        
        $result = $this->campaign->delete($id);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Campaign berhasil dihapus'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menghapus campaign'
            ]);
        }
        exit;
    }
    
    /**
     * Validasi input campaign
     */
    private function validateCampaign($data) {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors['title'] = 'Nama campaign harus diisi';
        }
        
        if (empty($data['location'])) {
            $errors['location'] = 'Lokasi harus diisi';
        }
        
        if (empty($data['tree_type'])) {
            $errors['tree_type'] = 'Jenis pohon harus diisi';
        }
        
        if (empty($data['price_per_tree']) || $data['price_per_tree'] <= 0) {
            $errors['price_per_tree'] = 'Harga per pohon harus diisi dan lebih dari 0';
        }
        
        if (empty($data['target_trees']) || $data['target_trees'] <= 0) {
            $errors['target_trees'] = 'Target pohon harus diisi dan lebih dari 0';
        }
        
        if (empty($data['deadline'])) {
            $errors['deadline'] = 'Deadline harus diisi';
        }
        
        if (empty($data['description'])) {
            $errors['description'] = 'Deskripsi harus diisi';
        }
        
        return $errors;
    }
    
    /**
     * Manajemen donasi
     */
    public function donations() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        
        switch ($action) {
            case 'detail':
                $this->donationDetail();
                break;
                
            case 'confirm':
                $this->confirmDonation();
                break;
                
            case 'cancel':
                $this->cancelDonation();
                break;
                
            case 'export':
                $this->exportDonations();
                break;
                
            default:
                $this->listDonations();
                break;
        }
    }
    
    /**
     * List semua donasi
     */
    private function listDonations() {
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $campaign_id = isset($_GET['campaign']) ? $_GET['campaign'] : null;
        
        $donations = $this->donation->getAll($status, $campaign_id);
        $stats = $this->donation->getStats();
        
        include dirname(__DIR__) . '/admin/donations.php';
    }
    
    /**
     * Detail donasi
     */
    private function donationDetail() {
        header('Content-Type: application/json');
        
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $donation = $this->donation->getById($id);
        
        if ($donation) {
            echo json_encode([
                'success' => true,
                'data' => $donation
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
     * Konfirmasi donasi
     */
    private function confirmDonation() {
        header('Content-Type: application/json');
        
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        
        if ($id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID donasi tidak valid'
            ]);
            exit;
        }
        
        $result = $this->donation->confirmDonation($id);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Donasi berhasil dikonfirmasi'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal mengkonfirmasi donasi'
            ]);
        }
        exit;
    }
    
    /**
     * Batalkan donasi
     */
    private function cancelDonation() {
        header('Content-Type: application/json');
        
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        
        if ($id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID donasi tidak valid'
            ]);
            exit;
        }
        
        $result = $this->donation->cancelDonation($id);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Donasi berhasil dibatalkan'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal membatalkan donasi'
            ]);
        }
        exit;
    }
    
    /**
     * Export donasi ke CSV
     */
    private function exportDonations() {
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $campaign_id = isset($_GET['campaign']) ? $_GET['campaign'] : null;
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
        
        $donations = $this->donation->getAll($status, $campaign_id);
        
        // Set header untuk download CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="donasi_sodakoh_pohon_' . date('Ymd') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Header CSV
        fputcsv($output, [
            'No. Donasi',
            'Tanggal',
            'Donatur',
            'Email',
            'Campaign',
            'Jumlah Pohon',
            'Nominal',
            'Status',
            'Metode Pembayaran'
        ]);
        
        // Data
        foreach ($donations as $donation) {
            fputcsv($output, [
                $donation['donation_number'],
                date('d/m/Y H:i', strtotime($donation['created_at'])),
                $donation['donor_name'],
                $donation['donor_email'],
                $donation['campaign_title'],
                $donation['trees_count'],
                $donation['amount'],
                $donation['status'],
                $donation['payment_method']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Manajemen penanaman
     */
    public function planted() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        
        switch ($action) {
            case 'create':
                $this->createPlanting();
                break;
                
            case 'store':
                $this->storePlanting();
                break;
                
            case 'delete':
                $this->deletePlanting();
                break;
                
            default:
                $this->listPlantings();
                break;
        }
    }
    
    /**
     * List semua penanaman
     */
    private function listPlantings() {
        // Ambil data penanaman dari database
        $conn = getDB();
        $sql = "SELECT p.*, c.title as campaign_name 
                FROM plantings p 
                LEFT JOIN campaigns c ON p.campaign_id = c.id 
                ORDER BY p.planting_date DESC";
        $result = $conn->query($sql);
        
        $plantings = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $plantings[] = $row;
            }
        }
        
        include dirname(__DIR__) . '/admin/planted.php';
    }
    
    /**
     * Form create penanaman
     */
    private function createPlanting() {
        $campaigns = $this->campaign->getActiveCampaigns();
        include dirname(__DIR__) . '/admin/planted.php';
    }
    
    /**
     * Proses simpan penanaman
     */
    private function storePlanting() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
            exit;
        }
        
        $conn = getDB();
        
        $campaign_id = (int)$_POST['campaign_id'];
        $trees_planted = (int)$_POST['trees_planted'];
        $planting_date = $conn->real_escape_string($_POST['planting_date']);
        $location = $conn->real_escape_string($_POST['location']);
        $volunteers = (int)$_POST['volunteers'];
        $coordinator = $conn->real_escape_string($_POST['coordinator']);
        $description = $conn->real_escape_string($_POST['description']);
        $created_at = date('Y-m-d H:i:s');
        
        // Handle upload gambar
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = UPLOAD_PATH . 'plantings/';
            
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $file_name = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'uploads/plantings/' . $file_name;
            }
        }
        
        $sql = "INSERT INTO plantings (campaign_id, trees_planted, planting_date, location, volunteers, coordinator, description, image, created_at) 
                VALUES ({$campaign_id}, {$trees_planted}, '{$planting_date}', '{$location}', {$volunteers}, '{$coordinator}', '{$description}', '{$image_path}', '{$created_at}')";
        
        if ($conn->query($sql)) {
            // Update planted_trees di campaign
            $this->campaign->updatePlantedTrees($campaign_id, $trees_planted);
            
            echo json_encode([
                'success' => true,
                'message' => 'Data penanaman berhasil disimpan',
                'redirect' => 'planted.php'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menyimpan data penanaman'
            ]);
        }
        exit;
    }
    
    /**
     * Hapus data penanaman
     */
    private function deletePlanting() {
        header('Content-Type: application/json');
        
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        
        if ($id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID penanaman tidak valid'
            ]);
            exit;
        }
        
        $conn = getDB();
        $id_esc = $conn->real_escape_string($id);
        
        $sql = "DELETE FROM plantings WHERE id = '{$id_esc}'";
        
        if ($conn->query($sql)) {
            echo json_encode([
                'success' => true,
                'message' => 'Data penanaman berhasil dihapus'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menghapus data penanaman'
            ]);
        }
        exit;
    }
    
    /**
     * Get stats untuk dashboard
     */
    public function getStats() {
        header('Content-Type: application/json');
        
        $campaign_stats = $this->campaign->getStats();
        $donation_stats = $this->donation->getStats();
        
        echo json_encode([
            'success' => true,
            'data' => array_merge($campaign_stats, $donation_stats)
        ]);
        exit;
    }
    
    /**
     * Get chart data untuk admin
     */
    public function getChartData() {
        header('Content-Type: application/json');
        
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $monthly_donations = $this->donation->getMonthlyDonations($year);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'monthly_donations' => $monthly_donations
            ]
        ]);
        exit;
    }
}

// Router untuk admin controller
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';
$controller = new AdminController();

switch ($action) {
    // Auth
    case 'login':
        $controller->login();
        break;
        
    case 'do_login':
        $controller->doLogin();
        break;
        
    case 'logout':
        $controller->logout();
        break;
    
    // Dashboard
    case 'dashboard':
        $controller->dashboard();
        break;
    
    // Campaign
    case 'campaign':
        $controller->campaign();
        break;
    
    // Donations
    case 'donations':
        $controller->donations();
        break;
    
    // Plantings
    case 'planted':
        $controller->planted();
        break;
    
    // API
    case 'get_stats':
        $controller->getStats();
        break;
        
    case 'get_chart_data':
        $controller->getChartData();
        break;
    
    default:
        $controller->dashboard();
        break;
}
?>