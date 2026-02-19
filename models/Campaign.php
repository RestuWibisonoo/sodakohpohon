<?php
// models/Campaign.php
// Model untuk mengelola data campaign penanaman pohon

require_once dirname(__DIR__) . '/config/koneksi.php';

class Campaign
{
    private $db;
    private $conn;

    // Properties
    public $id;
    public $title;
    public $description;
    public $long_description;
    public $location;
    public $tree_type;
    public $price_per_tree;
    public $target_trees;
    public $current_trees;
    public $planted_trees;
    public $image;
    public $gallery;
    public $status;
    public $partner;
    public $created_at;
    public $deadline;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Mendapatkan semua campaign
     */
    public function getAll($status = null, $limit = null)
    {
        $sql = "SELECT * FROM campaigns WHERE 1=1";

        if ($status) {
            $status_esc = $this->conn->real_escape_string($status);
            $sql .= " AND status = '{$status_esc}'";
        }

        $sql .= " ORDER BY created_at DESC";

        if ($limit) {
            $limit_esc = (int)$limit;
            $sql .= " LIMIT {$limit_esc}";
        }

        $result = $this->conn->query($sql);

        $campaigns = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['progress'] = $this->calculateProgress($row['current_trees'], $row['target_trees']);
                $row['days_left'] = $this->calculateDaysLeft($row['deadline']);
                $row['remaining_trees'] = $row['target_trees'] - $row['current_trees'];
                $campaigns[] = $row;
            }
        }

        return $campaigns;
    }

    /**
     * Mendapatkan campaign by ID
     */
    public function getById($id)
    {
        $id_esc = $this->conn->real_escape_string($id);
        $sql = "SELECT * FROM campaigns WHERE id = '{$id_esc}' LIMIT 1";

        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $campaign = $result->fetch_assoc();
            $campaign['progress'] = $this->calculateProgress($campaign['current_trees'], $campaign['target_trees']);
            $campaign['days_left'] = $this->calculateDaysLeft($campaign['deadline']);
            $campaign['remaining_trees'] = $campaign['target_trees'] - $campaign['current_trees'];

            // Get gallery images
            $campaign['gallery'] = $this->getGallery($id);

            // Get benefits
            $campaign['benefits'] = $this->getBenefits($id);

            return $campaign;
        }

        return null;
    }

    /**
     * Mendapatkan campaign aktif
     */
    public function getActiveCampaigns($limit = null)
    {
        return $this->getAll('active', $limit);
    }

    /**
     * Mendapatkan campaign unggulan
     */
    public function getFeaturedCampaigns($limit = 3)
    {
        $sql = "SELECT * FROM campaigns 
                WHERE status = 'active' 
                ORDER BY current_trees DESC 
                LIMIT " . (int)$limit;

        $result = $this->conn->query($sql);

        $campaigns = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['progress'] = $this->calculateProgress($row['current_trees'], $row['target_trees']);
                $row['days_left'] = $this->calculateDaysLeft($row['deadline']);
                $campaigns[] = $row;
            }
        }

        return $campaigns;
    }

    /**
     * Membuat campaign baru
     */
    public function create($data)
    {
        $title = $this->conn->real_escape_string($data['title']);
        $description = $this->conn->real_escape_string($data['description']);
        $long_description = isset($data['long_description']) ? $this->conn->real_escape_string($data['long_description']) : '';
        $location = $this->conn->real_escape_string($data['location']);
        $tree_type = $this->conn->real_escape_string($data['tree_type']);
        $price_per_tree = (float)$data['price_per_tree'];
        $target_trees = (int)$data['target_trees'];
        $current_trees = isset($data['current_trees']) ? (int)$data['current_trees'] : 0;
        $planted_trees = isset($data['planted_trees']) ? (int)$data['planted_trees'] : 0;
        $image = isset($data['image']) ? $this->conn->real_escape_string($data['image']) : '';
        $status = isset($data['status']) ? $this->conn->real_escape_string($data['status']) : 'active';
        $partner = isset($data['partner']) ? $this->conn->real_escape_string($data['partner']) : '';
        $map_url = isset($data['map_url']) ? $this->conn->real_escape_string($data['map_url']) : '';
        $deadline = $this->conn->real_escape_string($data['deadline']);
        $created_at = date('Y-m-d H:i:s');

        $sql = "INSERT INTO campaigns (
                    title, description, long_description, location, tree_type, 
                    price_per_tree, target_trees, current_trees, planted_trees, 
                    image, map_url, status, partner, deadline, created_at
                ) VALUES (
                    '{$title}', '{$description}', '{$long_description}', '{$location}', '{$tree_type}',
                    {$price_per_tree}, {$target_trees}, {$current_trees}, {$planted_trees},
                    '{$image}', '{$map_url}', '{$status}', '{$partner}', '{$deadline}', '{$created_at}'
                )";

        if ($this->conn->query($sql)) {
            return $this->conn->insert_id;
        }

        return false;
    }

    /**
     * Update campaign
     */
    public function update($id, $data)
    {
        $sets = [];

        foreach ($data as $key => $value) {
            $key_esc = $this->conn->real_escape_string($key);
            $value_esc = $this->conn->real_escape_string($value);
            $sets[] = "{$key_esc} = '{$value_esc}'";
        }

        $set_string = implode(", ", $sets);
        $id_esc = $this->conn->real_escape_string($id);

        $sql = "UPDATE campaigns SET {$set_string} WHERE id = '{$id_esc}'";

        return $this->conn->query($sql);
    }

    /**
     * Update jumlah pohon terkumpul
     */
    public function updateCurrentTrees($id, $trees)
    {
        $id_esc = $this->conn->real_escape_string($id);
        $trees_esc = (int)$trees;

        $sql = "UPDATE campaigns 
                SET current_trees = current_trees + {$trees_esc} 
                WHERE id = '{$id_esc}'";

        return $this->conn->query($sql);
    }

    /**
     * Update jumlah pohon tertanam
     */
    public function updatePlantedTrees($id, $trees)
    {
        $id_esc = $this->conn->real_escape_string($id);
        $trees_esc = (int)$trees;

        $sql = "UPDATE campaigns 
                SET planted_trees = planted_trees + {$trees_esc} 
                WHERE id = '{$id_esc}'";

        return $this->conn->query($sql);
    }

    /**
     * Hapus campaign
     */
    public function delete($id)
    {
        $id_esc = $this->conn->real_escape_string($id);

        // Hapus gallery terkait
        $this->conn->query("DELETE FROM campaign_gallery WHERE campaign_id = '{$id_esc}'");

        // Hapus campaign
        $sql = "DELETE FROM campaigns WHERE id = '{$id_esc}'";

        return $this->conn->query($sql);
    }

    /**
     * Mendapatkan statistik campaign
     */
    public function getStats()
    {
        $stats = [];

        // Total campaign
        $result = $this->conn->query("SELECT COUNT(*) as total FROM campaigns");
        $stats['total_campaigns'] = $result->fetch_assoc()['total'];

        // Total pohon terkumpul
        $result = $this->conn->query("SELECT SUM(current_trees) as total FROM campaigns");
        $stats['total_trees_collected'] = $result->fetch_assoc()['total'] ?? 0;

        // Total pohon tertanam
        $result = $this->conn->query("SELECT SUM(planted_trees) as total FROM campaigns");
        $stats['total_trees_planted'] = $result->fetch_assoc()['total'] ?? 0;

        // Total target pohon
        $result = $this->conn->query("SELECT SUM(target_trees) as total FROM campaigns");
        $stats['total_target_trees'] = $result->fetch_assoc()['total'] ?? 0;

        // Campaign aktif
        $result = $this->conn->query("SELECT COUNT(*) as total FROM campaigns WHERE status = 'active'");
        $stats['active_campaigns'] = $result->fetch_assoc()['total'];

        // Campaign selesai
        $result = $this->conn->query("SELECT COUNT(*) as total FROM campaigns WHERE status = 'completed'");
        $stats['completed_campaigns'] = $result->fetch_assoc()['total'];

        // Total donasi
        $result = $this->conn->query("SELECT SUM(amount) as total FROM donations WHERE status = 'paid'");
        $stats['total_donations'] = $result->fetch_assoc()['total'] ?? 0;

        return $stats;
    }

    /**
     * Mendapatkan total pohon terkumpul (global)
     */
    public function getTotalTreesCollected()
    {
        $result = $this->conn->query("SELECT SUM(current_trees) as total FROM campaigns");
        $data = $result->fetch_assoc();
        return $data['total'] ?? 0;
    }

    /**
     * Mendapatkan total pohon tertanam (global)
     */
    public function getTotalTreesPlanted()
    {
        $result = $this->conn->query("SELECT SUM(planted_trees) as total FROM campaigns");
        $data = $result->fetch_assoc();
        return $data['total'] ?? 0;
    }

    /**
     * Mendapatkan gallery campaign
     */
    public function getGallery($campaign_id)
    {
        $id_esc = $this->conn->real_escape_string($campaign_id);
        $sql = "SELECT * FROM campaign_gallery WHERE campaign_id = '{$id_esc}' ORDER BY created_at DESC";

        $result = $this->conn->query($sql);

        $gallery = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $gallery[] = $row;
            }
        }

        return $gallery;
    }

    /**
     * Menambah foto ke gallery
     */
    public function addToGallery($campaign_id, $image_url, $caption = '')
    {
        $campaign_id_esc = $this->conn->real_escape_string($campaign_id);
        $image_url_esc = $this->conn->real_escape_string($image_url);
        $caption_esc = $this->conn->real_escape_string($caption);
        $created_at = date('Y-m-d H:i:s');

        $sql = "INSERT INTO campaign_gallery (campaign_id, image_url, caption, created_at) 
                VALUES ('{$campaign_id_esc}', '{$image_url_esc}', '{$caption_esc}', '{$created_at}')";

        return $this->conn->query($sql);
    }

    /**
     * Mendapatkan benefits campaign
     */
    public function getBenefits($campaign_id)
    {
        $id_esc = $this->conn->real_escape_string($campaign_id);
        $sql = "SELECT * FROM campaign_benefits WHERE campaign_id = '{$id_esc}' ORDER BY id ASC";

        $result = $this->conn->query($sql);

        $benefits = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $benefits[] = $row['benefit'];
            }
        }

        return $benefits;
    }

    /**
     * Menambah benefit campaign
     */
    public function addBenefit($campaign_id, $benefit)
    {
        $campaign_id_esc = $this->conn->real_escape_string($campaign_id);
        $benefit_esc = $this->conn->real_escape_string($benefit);

        $sql = "INSERT INTO campaign_benefits (campaign_id, benefit) 
                VALUES ('{$campaign_id_esc}', '{$benefit_esc}')";

        return $this->conn->query($sql);
    }

    /**
     * Menghitung progress campaign
     */
    private function calculateProgress($current, $target)
    {
        if ($target <= 0)
            return 0;
        return round(($current / $target) * 100, 1);
    }

    /**
     * Menghitung sisa hari
     */
    private function calculateDaysLeft($deadline)
    {
        $deadline_date = new DateTime($deadline);
        $now = new DateTime();
        $diff = $now->diff($deadline_date);

        if ($now > $deadline_date) {
            return 0;
        }

        return $diff->days;
    }

    /**
     * Upload gambar campaign
     */
    public function uploadImage($file)
    {
        $target_dir = UPLOAD_PATH . 'campaigns/';

        // Buat folder jika belum ada
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;

        // Cek apakah file gambar valid
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            return false;
        }

        // Cek ukuran file (max 5MB)
        if ($file['size'] > 5000000) {
            return false;
        }

        // Cek format file
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($file_extension, $allowed_types)) {
            return false;
        }

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return 'uploads/campaigns/' . $file_name;
        }

        return false;
    }
}
?>