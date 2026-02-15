<?php
// models/Donation.php
// Model untuk mengelola data donasi

require_once dirname(__DIR__) . '/config/koneksi.php';
require_once 'Campaign.php';

class Donation {
    private $db;
    private $conn;
    
    // Properties
    public $id;
    public $donation_number;
    public $donor_name;
    public $donor_email;
    public $donor_phone;
    public $anonymous;
    public $campaign_id;
    public $trees_count;
    public $amount;
    public $status;
    public $payment_method;
    public $payment_proof;
    public $message;
    public $certificate_number;
    public $created_at;
    public $updated_at;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Membuat donasi baru
     */
    public function create($data) {
        // Generate nomor donasi unik
        $donation_number = $this->generateDonationNumber();
        
        $donor_name = isset($data['donor_name']) ? $this->conn->real_escape_string($data['donor_name']) : 'Anonymous';
        $donor_email = isset($data['donor_email']) ? $this->conn->real_escape_string($data['donor_email']) : '';
        $donor_phone = isset($data['donor_phone']) ? $this->conn->real_escape_string($data['donor_phone']) : '';
        $anonymous = isset($data['anonymous']) && $data['anonymous'] ? 1 : 0;
        $campaign_id = (int)$data['campaign_id'];
        $trees_count = (int)$data['trees_count'];
        $amount = (float)$data['amount'];
        $status = isset($data['status']) ? $this->conn->real_escape_string($data['status']) : 'pending';
        $payment_method = isset($data['payment_method']) ? $this->conn->real_escape_string($data['payment_method']) : '';
        $message = isset($data['message']) ? $this->conn->real_escape_string($data['message']) : '';
        $certificate_number = $this->generateCertificateNumber();
        $created_at = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO donations (
                    donation_number, donor_name, donor_email, donor_phone, 
                    anonymous, campaign_id, trees_count, amount, status, 
                    payment_method, message, certificate_number, created_at
                ) VALUES (
                    '{$donation_number}', '{$donor_name}', '{$donor_email}', '{$donor_phone}',
                    {$anonymous}, {$campaign_id}, {$trees_count}, {$amount}, '{$status}',
                    '{$payment_method}', '{$message}', '{$certificate_number}', '{$created_at}'
                )";
        
        if ($this->conn->query($sql)) {
            $donation_id = $this->conn->insert_id;
            
            // Jika status langsung paid, update campaign
            if ($status === 'paid') {
                $this->confirmDonation($donation_id);
            }
            
            return $donation_id;
        }
        
        return false;
    }
    
    /**
     * Mendapatkan donasi by ID
     */
    public function getById($id) {
        $id_esc = $this->conn->real_escape_string($id);
        $sql = "SELECT d.*, c.title as campaign_title, c.tree_type, c.location 
                FROM donations d
                LEFT JOIN campaigns c ON d.campaign_id = c.id
                WHERE d.id = '{$id_esc}' LIMIT 1";
        
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Mendapatkan donasi by nomor donasi
     */
    public function getByDonationNumber($donation_number) {
        $number_esc = $this->conn->real_escape_string($donation_number);
        $sql = "SELECT d.*, c.title as campaign_title 
                FROM donations d
                LEFT JOIN campaigns c ON d.campaign_id = c.id
                WHERE d.donation_number = '{$number_esc}' LIMIT 1";
        
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Mendapatkan donasi by email donatur
     */
    public function getByEmail($email, $limit = null) {
        $email_esc = $this->conn->real_escape_string($email);
        $sql = "SELECT d.*, c.title as campaign_title 
                FROM donations d
                LEFT JOIN campaigns c ON d.campaign_id = c.id
                WHERE d.donor_email = '{$email_esc}' 
                ORDER BY d.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $result = $this->conn->query($sql);
        
        $donations = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $donations[] = $row;
            }
        }
        
        return $donations;
    }
    
    /**
     * Mendapatkan semua donasi
     */
    public function getAll($status = null, $campaign_id = null, $limit = null) {
        $sql = "SELECT d.*, c.title as campaign_title 
                FROM donations d
                LEFT JOIN campaigns c ON d.campaign_id = c.id
                WHERE 1=1";
        
        if ($status) {
            $status_esc = $this->conn->real_escape_string($status);
            $sql .= " AND d.status = '{$status_esc}'";
        }
        
        if ($campaign_id) {
            $campaign_id_esc = (int)$campaign_id;
            $sql .= " AND d.campaign_id = {$campaign_id_esc}";
        }
        
        $sql .= " ORDER BY d.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $result = $this->conn->query($sql);
        
        $donations = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $donations[] = $row;
            }
        }
        
        return $donations;
    }
    
    /**
     * Konfirmasi donasi (update status ke paid)
     */
    public function confirmDonation($id) {
        $id_esc = $this->conn->real_escape_string($id);
        $updated_at = date('Y-m-d H:i:s');
        
        // Update status donasi
        $sql = "UPDATE donations 
                SET status = 'paid', updated_at = '{$updated_at}' 
                WHERE id = '{$id_esc}' AND status = 'pending'";
        
        if ($this->conn->query($sql)) {
            if ($this->conn->affected_rows > 0) {
                // Ambil data donasi
                $donation = $this->getById($id);
                
                if ($donation) {
                    // Update current_trees di campaign
                    $campaign = new Campaign();
                    $campaign->updateCurrentTrees($donation['campaign_id'], $donation['trees_count']);
                    
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Batalkan donasi
     */
    public function cancelDonation($id) {
        $id_esc = $this->conn->real_escape_string($id);
        $updated_at = date('Y-m-d H:i:s');
        
        $sql = "UPDATE donations 
                SET status = 'cancelled', updated_at = '{$updated_at}' 
                WHERE id = '{$id_esc}' AND status = 'pending'";
        
        return $this->conn->query($sql);
    }
    
    /**
     * Hapus donasi
     */
    public function delete($id) {
        $id_esc = $this->conn->real_escape_string($id);
        $sql = "DELETE FROM donations WHERE id = '{$id_esc}'";
        
        return $this->conn->query($sql);
    }
    
    /**
     * Mendapatkan statistik donasi
     */
    public function getStats() {
        $stats = [];
        
        // Total donasi sukses
        $result = $this->conn->query("SELECT COUNT(*) as total, SUM(amount) as total_amount 
                                     FROM donations WHERE status = 'paid'");
        $row = $result->fetch_assoc();
        $stats['total_donations'] = $row['total'] ?? 0;
        $stats['total_amount'] = $row['total_amount'] ?? 0;
        
        // Total pohon dari donasi
        $result = $this->conn->query("SELECT SUM(trees_count) as total FROM donations WHERE status = 'paid'");
        $stats['total_trees'] = $result->fetch_assoc()['total'] ?? 0;
        
        // Total donatur unik
        $result = $this->conn->query("SELECT COUNT(DISTINCT donor_email) as total FROM donations WHERE status = 'paid' AND donor_email != ''");
        $stats['total_donors'] = $result->fetch_assoc()['total'] ?? 0;
        
        // Donasi hari ini
        $today = date('Y-m-d');
        $result = $this->conn->query("SELECT COUNT(*) as total, SUM(amount) as total_amount 
                                     FROM donations WHERE DATE(created_at) = '{$today}' AND status = 'paid'");
        $row = $result->fetch_assoc();
        $stats['today_donations'] = $row['total'] ?? 0;
        $stats['today_amount'] = $row['total_amount'] ?? 0;
        
        // Donasi pending
        $result = $this->conn->query("SELECT COUNT(*) as total FROM donations WHERE status = 'pending'");
        $stats['pending_donations'] = $result->fetch_assoc()['total'] ?? 0;
        
        return $stats;
    }
    
    /**
     * Mendapatkan data donasi per bulan untuk grafik
     */
    public function getMonthlyDonations($year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $sql = "SELECT 
                    MONTH(created_at) as month,
                    COUNT(*) as total_donations,
                    SUM(amount) as total_amount,
                    SUM(trees_count) as total_trees
                FROM donations 
                WHERE status = 'paid' 
                    AND YEAR(created_at) = {$year}
                GROUP BY MONTH(created_at)
                ORDER BY MONTH(created_at) ASC";
        
        $result = $this->conn->query($sql);
        
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = [
                'month' => $i,
                'total_donations' => 0,
                'total_amount' => 0,
                'total_trees' => 0
            ];
        }
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $months[$row['month']] = $row;
            }
        }
        
        return array_values($months);
    }
    
    /**
     * Mendapatkan donasi per campaign
     */
    public function getDonationsByCampaign() {
        $sql = "SELECT 
                    c.id,
                    c.title as campaign_name,
                    COUNT(d.id) as total_donations,
                    SUM(d.trees_count) as total_trees,
                    SUM(d.amount) as total_amount
                FROM campaigns c
                LEFT JOIN donations d ON c.id = d.campaign_id AND d.status = 'paid'
                GROUP BY c.id
                ORDER BY total_amount DESC";
        
        $result = $this->conn->query($sql);
        
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        
        return $data;
    }
    
    /**
     * Generate nomor donasi unik
     * Format: SP-YYYYMMDD-XXXX
     */
    private function generateDonationNumber() {
        $date = date('Ymd');
        $prefix = "SP-{$date}-";
        
        $sql = "SELECT COUNT(*) as total FROM donations WHERE donation_number LIKE '{$prefix}%'";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        
        $sequence = str_pad($row['total'] + 1, 4, '0', STR_PAD_LEFT);
        
        return $prefix . $sequence;
    }
    
    /**
     * Generate nomor sertifikat
     */
    private function generateCertificateNumber() {
        $date = date('Ymd');
        $prefix = "SP-CERT-{$date}-";
        
        $sql = "SELECT COUNT(*) as total FROM donations WHERE certificate_number LIKE '{$prefix}%'";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        
        $sequence = str_pad($row['total'] + 1, 4, '0', STR_PAD_LEFT);
        
        return $prefix . $sequence;
    }
    
    /**
     * Upload bukti pembayaran
     */
    public function uploadPaymentProof($file) {
        $target_dir = UPLOAD_PATH . 'payments/';
        
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;
        
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            return false;
        }
        
        if ($file['size'] > 5000000) {
            return false;
        }
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array($file_extension, $allowed_types)) {
            return false;
        }
        
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return 'uploads/payments/' . $file_name;
        }
        
        return false;
    }
}
?>