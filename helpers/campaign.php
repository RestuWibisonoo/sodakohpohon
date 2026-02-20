<?php
/**
 * helpers/campaign.php
 * Helper functions untuk mengambil dan memproses data campaign
 */

require_once dirname(__DIR__) . '/config/koneksi.php';

/**
 * Format gambar campaign
 * Mengubah path lokal menjadi URL yang benar
 * 
 * @param string $image Path gambar
 * @return string URL gambar yang benar
 */
function formatCampaignImage($image)
{
    // Jika sudah URL lengkap (external URL), return as is
    if (filter_var($image, FILTER_VALIDATE_URL)) {
        return $image;
    }

    // Jika null atau kosong, pakai placeholder
    if (empty($image)) {
        return 'https://via.placeholder.com/600x400?text=Campaign+Image';
    }

    // Normalize path: convert backslash to forward slash
    $image = str_replace('\\', '/', $image);
    
    // Remove 'uploads/campaigns/' prefix if already exists to prevent duplication
    $image = preg_replace('#^uploads/campaigns/#i', '', $image);
    
    // Return full path with forward slashes
    $local_path = 'uploads/campaigns/' . $image;
    
    // Add simple cache buster using time (force refresh every hour)
    $cache_busted = $local_path . '?t=' . (intval(time() / 3600));
    
    return $cache_busted;
}

/**
 * Hitung sisa hari dari deadline
 * 
 * @param string $deadline Tanggal deadline (format YYYY-MM-DD)
 * @return int Jumlah hari tersisa
 */
function calculateDaysLeft($deadline)
{
    if (empty($deadline)) {
        return 0;
    }

    $now = time();
    $deadline_time = strtotime($deadline);
    $diff = $deadline_time - $now;
    $days_left = floor($diff / (60 * 60 * 24));

    return ($days_left < 0) ? 0 : $days_left;
}

/**
 * Process campaign row dari database ke array campaign
 * 
 * @param array $row Data campaign dari database
 * @return array Data campaign yang sudah diproses
 */
function processCampaignRow($row)
{
    return [
        'id'            => $row['id'],
        'title'         => $row['title'],
        'location'      => $row['location'],
        'tree_type'     => $row['tree_type'],
        'price_per_tree'=> (int)$row['price_per_tree'],
        'target_trees'  => (int)$row['target_trees'],
        'current_trees' => (int)$row['current_trees'],
        'image'         => formatCampaignImage($row['image']),
        'description'   => $row['description'],
        'donors'        => (int)($row['donors_count'] ?? 0),
        'days_left'     => calculateDaysLeft($row['deadline']),
        'category'      => $row['tree_type']
    ];
}

/**
 * Ambil statistik campaign dari database
 * 
 * @return array Array berisi total_trees, total_planted, total_donors, total_locations
 */
function getCampaignStats()
{
    $db = getDB();

    $stat_query = "SELECT 
                    COALESCE(SUM(c.current_trees), 0) as total_trees,
                    COALESCE(SUM(c.planted_trees), 0) as total_planted,
                    COUNT(DISTINCT d.donor_email) as total_donors,
                    COUNT(DISTINCT c.location) as total_locations
                   FROM campaigns c
                   LEFT JOIN donations d ON c.id = d.campaign_id AND d.status = 'paid'
                   WHERE c.status = 'active'";

    $stat_result = mysqli_query($db, $stat_query);
    if (!$stat_result) {
        error_log('getCampaignStats query error: ' . mysqli_error($db));
        return ['total_trees' => 0, 'total_planted' => 0, 'total_donors' => 0, 'total_locations' => 0];
    }
    
    $stats = mysqli_fetch_assoc($stat_result);

    return [
        'total_trees'     => (int)($stats['total_trees'] ?? 0),
        'total_planted'   => (int)($stats['total_planted'] ?? 0),
        'total_donors'    => (int)($stats['total_donors'] ?? 0),
        'total_locations' => (int)($stats['total_locations'] ?? 0)
    ];
}

/**
 * Ambil list campaign dengan filter dan sort
 * 
 * @param string $category_filter Filter category (tree_type) atau 'all' untuk semua
 * @param string $sort_by Cara sorting: 'popular', 'deadline', 'progress'
 * @return array Array berisi list campaign
 */
function getCampaigns($category_filter = 'all', $sort_by = 'popular')
{
    $db = getDB();

    // Sanitize inputs
    $category_filter = is_string($category_filter) ? trim($category_filter) : 'all';
    $sort_by = is_string($sort_by) ? trim($sort_by) : 'popular';

    // Base query - only fetch active campaigns with latest data
    $query = "SELECT 
                c.id, c.title, c.description, c.location, c.tree_type, 
                c.price_per_tree, c.target_trees, c.current_trees, 
                c.image, c.deadline, c.status, c.updated_at,
                COUNT(DISTINCT d.donor_email) as donors_count
               FROM campaigns c
               LEFT JOIN donations d ON c.id = d.campaign_id AND d.status = 'paid'
               WHERE c.status = 'active'";

    // Add category filter
    if ($category_filter != 'all') {
        $category_filter = mysqli_real_escape_string($db, $category_filter);
        $query .= " AND c.tree_type = '{$category_filter}'";
    }

    $query .= " GROUP BY c.id ";

    // Add sort
    switch ($sort_by) {
        case 'deadline':
            $query .= "ORDER BY c.deadline ASC";
            break;
        case 'progress':
            $query .= "ORDER BY (c.current_trees / c.target_trees) DESC";
            break;
        default: // popular
            $query .= "ORDER BY donors_count DESC, c.updated_at DESC";
            break;
    }

    $result = mysqli_query($db, $query);
    
    if (!$result) {
        error_log('getCampaigns query error: ' . mysqli_error($db));
        return [];
    }
    
    $campaigns = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $campaigns[] = processCampaignRow($row);
        }
    }

    return $campaigns;
}

/**
 * Ambil list campaign untuk home page (tanpa filter, hanya terbaru)
 * 
 * @param int $limit Jumlah campaign yang diambil
 * @return array Array berisi list campaign
 */
function getCampaignsForHome($limit = 3)
{
    $db = getDB();

    $limit = (int)$limit;
    $query = "SELECT 
                c.id, c.title, c.description, c.location, c.tree_type, 
                c.price_per_tree, c.target_trees, c.current_trees, 
                c.image, c.deadline, c.status,
                COUNT(DISTINCT d.donor_email) as donors_count
               FROM campaigns c
               LEFT JOIN donations d ON c.id = d.campaign_id AND d.status = 'paid'
               WHERE c.status = 'active'
               GROUP BY c.id
               ORDER BY c.id DESC
               LIMIT {$limit}";

    $result = mysqli_query($db, $query);
    $campaigns = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $campaigns[] = processCampaignRow($row);
        }
    }

    return $campaigns;
}

/**
 * Ambil semua kategori campaign yang tersedia
 * 
 * @return array Array berisi list kategori (tree_type unik)
 */
function getCampaignCategories()
{
    $db = getDB();

    $cat_query = "SELECT DISTINCT tree_type FROM campaigns WHERE status = 'active' ORDER BY tree_type ASC";
    $cat_result = mysqli_query($db, $cat_query);
    $categories = [];

    if ($cat_result) {
        while ($cat_row = mysqli_fetch_assoc($cat_result)) {
            $categories[] = $cat_row['tree_type'];
        }
    }

    return $categories;
}

/**
 * Ambil detail campaign berdasarkan ID
 * 
 * @param int $id Campaign ID
 * @return array|null Data campaign atau null jika tidak ditemukan
 */
function getCampaignById($id)
{
    $db = getDB();
    $id = (int)$id;

    $query = "SELECT 
                c.id, c.title, c.description, c.long_description, c.location, c.tree_type, 
                c.price_per_tree, c.target_trees, c.current_trees, c.planted_trees,
                c.image, c.deadline, c.status, c.created_at, c.partner, c.map_url,
                COUNT(DISTINCT d.donor_email) as donors_count
               FROM campaigns c
               LEFT JOIN donations d ON c.id = d.campaign_id AND d.status = 'paid'
               WHERE c.id = {$id} AND c.status = 'active'
               GROUP BY c.id
               LIMIT 1";

    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return processCampaignRow($row);
    }

    return null;
}
?>
