<?php
// models/Cart.php
// Model untuk mengelola keranjang donasi

require_once dirname(__DIR__) . '/config/koneksi.php';
require_once 'Campaign.php';

class Cart {
    private $db;
    private $conn;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
        
        // Inisialisasi session cart jika belum ada
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [
                'id' => null,
                'items' => [],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
    }
    
    /**
     * Menambahkan item ke keranjang
     */
    public function addItem($campaign_id, $quantity) {
        $campaign_id = (int)$campaign_id;
        $quantity = (int)$quantity;
        
        if ($quantity < 1) {
            return false;
        }
        
        // Ambil data campaign
        $campaign = new Campaign();
        $campaign_data = $campaign->getById($campaign_id);
        
        if (!$campaign_data) {
            return false;
        }
        
        // Hitung subtotal
        $subtotal = $quantity * $campaign_data['price_per_tree'];
        
        // Cek apakah keranjang sudah memiliki item
        if (empty($_SESSION['cart']['items'])) {
            // Keranjang kosong, tambah item
            $_SESSION['cart']['items'] = [
                [
                    'id' => uniqid(),
                    'campaign_id' => $campaign_id,
                    'campaign_title' => $campaign_data['title'],
                    'tree_type' => $campaign_data['tree_type'],
                    'location' => $campaign_data['location'],
                    'price_per_tree' => $campaign_data['price_per_tree'],
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                    'image' => $campaign_data['image'],
                    'added_at' => date('Y-m-d H:i:s')
                ]
            ];
        } else {
            // Cek apakah campaign sudah ada di keranjang
            $existing_item_key = null;
            foreach ($_SESSION['cart']['items'] as $key => $item) {
                if ($item['campaign_id'] == $campaign_id) {
                    $existing_item_key = $key;
                    break;
                }
            }
            
            if ($existing_item_key !== null) {
                // Update quantity jika campaign sudah ada
                $_SESSION['cart']['items'][$existing_item_key]['quantity'] = $quantity;
                $_SESSION['cart']['items'][$existing_item_key]['subtotal'] = $quantity * $campaign_data['price_per_tree'];
                $_SESSION['cart']['items'][$existing_item_key]['updated_at'] = date('Y-m-d H:i:s');
            } else {
                // Tambah item baru (akan menggantikan yang lama sesuai aturan)
                $_SESSION['cart']['items'] = [
                    [
                        'id' => uniqid(),
                        'campaign_id' => $campaign_id,
                        'campaign_title' => $campaign_data['title'],
                        'tree_type' => $campaign_data['tree_type'],
                        'location' => $campaign_data['location'],
                        'price_per_tree' => $campaign_data['price_per_tree'],
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                        'image' => $campaign_data['image'],
                        'added_at' => date('Y-m-d H:i:s')
                    ]
                ];
            }
        }
        
        $_SESSION['cart']['updated_at'] = date('Y-m-d H:i:s');
        
        return true;
    }
    
    /**
     * Mengupdate quantity item di keranjang
     */
    public function updateQuantity($item_id, $quantity) {
        $quantity = (int)$quantity;
        
        if ($quantity < 1) {
            return $this->removeItem($item_id);
        }
        
        foreach ($_SESSION['cart']['items'] as $key => $item) {
            if ($item['id'] == $item_id) {
                $_SESSION['cart']['items'][$key]['quantity'] = $quantity;
                $_SESSION['cart']['items'][$key]['subtotal'] = $quantity * $item['price_per_tree'];
                $_SESSION['cart']['items'][$key]['updated_at'] = date('Y-m-d H:i:s');
                $_SESSION['cart']['updated_at'] = date('Y-m-d H:i:s');
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Menghapus item dari keranjang
     */
    public function removeItem($item_id) {
        foreach ($_SESSION['cart']['items'] as $key => $item) {
            if ($item['id'] == $item_id) {
                unset($_SESSION['cart']['items'][$key]);
                $_SESSION['cart']['items'] = array_values($_SESSION['cart']['items']);
                $_SESSION['cart']['updated_at'] = date('Y-m-d H:i:s');
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Mendapatkan semua item di keranjang
     */
    public function getItems() {
        return isset($_SESSION['cart']['items']) ? $_SESSION['cart']['items'] : [];
    }
    
    /**
     * Mendapatkan total item di keranjang
     */
    public function getTotalItems() {
        $total = 0;
        foreach ($this->getItems() as $item) {
            $total += $item['quantity'];
        }
        return $total;
    }
    
    /**
     * Mendapatkan subtotal keranjang
     */
    public function getSubtotal() {
        $subtotal = 0;
        foreach ($this->getItems() as $item) {
            $subtotal += $item['subtotal'];
        }
        return $subtotal;
    }
    
    /**
     * Mendapatkan total donasi (dengan biaya admin jika ada)
     */
    public function getTotal($admin_fee = 0) {
        return $this->getSubtotal() + $admin_fee;
    }
    
    /**
     * Mengecek apakah keranjang kosong
     */
    public function isEmpty() {
        return empty($_SESSION['cart']['items']);
    }
    
    /**
     * Mendapatkan data campaign dari item pertama
     * (Digunakan untuk checkout karena hanya boleh 1 campaign)
     */
    public function getPrimaryCampaign() {
        $items = $this->getItems();
        if (!empty($items)) {
            return $items[0];
        }
        return null;
    }
    
    /**
     * Membersihkan keranjang
     */
    public function clear() {
        $_SESSION['cart'] = [
            'id' => null,
            'items' => [],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return true;
    }
    
    /**
     * Menyimpan keranjang ke database (untuk user yang login)
     */
    public function saveToDatabase($user_id = null) {
        // Jika tidak ada user_id, simpan dengan session_id
        $session_id = session_id();
        $cart_data = json_encode($_SESSION['cart']['items']);
        $subtotal = $this->getSubtotal();
        $updated_at = date('Y-m-d H:i:s');
        
        // Cek apakah cart sudah ada
        $sql = "SELECT id FROM carts WHERE session_id = '{$session_id}'";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            // Update cart yang sudah ada
            $row = $result->fetch_assoc();
            $sql = "UPDATE carts 
                    SET cart_data = '{$cart_data}', 
                        subtotal = {$subtotal}, 
                        updated_at = '{$updated_at}' 
                    WHERE id = {$row['id']}";
        } else {
            // Buat cart baru
            $user_id_sql = $user_id ? $user_id : 'NULL';
            $sql = "INSERT INTO carts (session_id, user_id, cart_data, subtotal, created_at, updated_at) 
                    VALUES ('{$session_id}', {$user_id_sql}, '{$cart_data}', {$subtotal}, '{$updated_at}', '{$updated_at}')";
        }
        
        return $this->conn->query($sql);
    }
    
    /**
     * Memuat keranjang dari database
     */
    public function loadFromDatabase() {
        $session_id = session_id();
        
        $sql = "SELECT * FROM carts WHERE session_id = '{$session_id}' ORDER BY updated_at DESC LIMIT 1";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['cart']['items'] = json_decode($row['cart_data'], true);
            $_SESSION['cart']['updated_at'] = $row['updated_at'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Konversi keranjang menjadi donasi
     */
    public function convertToDonation($donor_data) {
        if ($this->isEmpty()) {
            return false;
        }
        
        $item = $this->getPrimaryCampaign();
        
        $donation = new Donation();
        
        $data = [
            'donor_name' => isset($donor_data['anonymous']) && $donor_data['anonymous'] ? 'Anonymous' : $donor_data['donor_name'],
            'donor_email' => $donor_data['donor_email'],
            'donor_phone' => isset($donor_data['donor_phone']) ? $donor_data['donor_phone'] : '',
            'anonymous' => isset($donor_data['anonymous']) ? $donor_data['anonymous'] : false,
            'campaign_id' => $item['campaign_id'],
            'trees_count' => $item['quantity'],
            'amount' => $item['subtotal'],
            'status' => 'pending',
            'payment_method' => $donor_data['payment_method'],
            'message' => isset($donor_data['message']) ? $donor_data['message'] : ''
        ];
        
        $donation_id = $donation->create($data);
        
        if ($donation_id) {
            // Kosongkan keranjang setelah sukses
            $this->clear();
            return $donation_id;
        }
        
        return false;
    }
    
    /**
     * Mendapatkan ringkasan keranjang
     */
    public function getSummary() {
        return [
            'items' => $this->getItems(),
            'total_items' => $this->getTotalItems(),
            'subtotal' => $this->getSubtotal(),
            'total' => $this->getTotal(),
            'is_empty' => $this->isEmpty(),
            'primary_campaign' => $this->getPrimaryCampaign()
        ];
    }
}
?>