<?php
// controllers/campaignController.php
// Controller untuk menangani semua request terkait campaign

require_once dirname(__DIR__) . '/config/koneksi.php';
require_once dirname(__DIR__) . '/models/Campaign.php';
require_once dirname(__DIR__) . '/models/Donation.php';
require_once dirname(__DIR__) . '/models/Cart.php';

class CampaignController {
    private $campaign;
    private $donation;
    private $cart;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->campaign = new Campaign();
        $this->donation = new Donation();
        $this->cart = new Cart();
    }
    
    /**
     * Menampilkan halaman daftar campaign
     */
    public function index() {
        $campaigns = $this->campaign->getActiveCampaigns();
        
        // Get stats untuk ditampilkan
        $total_trees_collected = $this->campaign->getTotalTreesCollected();
        $total_trees_planted = $this->campaign->getTotalTreesPlanted();
        $stats = $this->donation->getStats();
        
        include dirname(__DIR__) . '/index.php';
    }
    
    /**
     * Menampilkan detail campaign
     */
    public function detail($id) {
        $campaign = $this->campaign->getById($id);
        
        if (!$campaign) {
            $this->redirectWithMessage('index.php', 'Campaign tidak ditemukan', 'error');
            return;
        }
        
        // Get related campaigns
        $related_campaigns = $this->campaign->getActiveCampaigns(3);
        
        include dirname(__DIR__) . '/campaign-detail.php';
    }
    
    /**
     * API: Get campaign data for AJAX
     */
    public function getCampaignData($id) {
        header('Content-Type: application/json');
        
        $campaign = $this->campaign->getById($id);
        
        if ($campaign) {
            echo json_encode([
                'success' => true,
                'data' => $campaign
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Campaign tidak ditemukan'
            ]);
        }
        exit;
    }
    
    /**
     * API: Get all campaigns for AJAX
     */
    public function getAllCampaigns() {
        header('Content-Type: application/json');
        
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : null;
        
        $campaigns = $this->campaign->getAll($status, $limit);
        
        echo json_encode([
            'success' => true,
            'data' => $campaigns,
            'total' => count($campaigns)
        ]);
        exit;
    }
    
    /**
     * API: Get campaign stats for AJAX
     */
    public function getStats() {
        header('Content-Type: application/json');
        
        $stats = $this->campaign->getStats();
        $donation_stats = $this->donation->getStats();
        
        $merged_stats = array_merge($stats, $donation_stats);
        
        echo json_encode([
            'success' => true,
            'data' => $merged_stats
        ]);
        exit;
    }
    
    /**
     * API: Get chart data for laporan.php
     */
    public function getChartData() {
        header('Content-Type: application/json');
        
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        
        $monthly_donations = $this->donation->getMonthlyDonations($year);
        $campaign_distribution = $this->donation->getDonationsByCampaign();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'monthly_donations' => $monthly_donations,
                'campaign_distribution' => $campaign_distribution,
                'year' => $year
            ]
        ]);
        exit;
    }
    
    /**
     * Menambahkan item ke keranjang
     */
    public function addToCart() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
            exit;
        }
        
        $campaign_id = isset($_POST['campaign_id']) ? (int)$_POST['campaign_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        if ($campaign_id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Campaign tidak valid'
            ]);
            exit;
        }
        
        if ($quantity <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Jumlah pohon harus minimal 1'
            ]);
            exit;
        }
        
        $result = $this->cart->addItem($campaign_id, $quantity);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Berhasil ditambahkan ke keranjang',
                'cart' => $this->cart->getSummary()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menambahkan ke keranjang'
            ]);
        }
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

// Router untuk campaign controller
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$controller = new CampaignController();

switch ($action) {
    case 'detail':
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $controller->detail($id);
        break;
        
    case 'get_data':
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $controller->getCampaignData($id);
        break;
        
    case 'get_all':
        $controller->getAllCampaigns();
        break;
        
    case 'get_stats':
        $controller->getStats();
        break;
        
    case 'get_chart_data':
        $controller->getChartData();
        break;
        
    case 'add_to_cart':
        $controller->addToCart();
        break;
        
    default:
        $controller->index();
        break;
}
?>