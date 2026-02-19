<?php
// controllers/adminController.php
// Controller untuk menangani semua AJAX request admin

require_once dirname(__DIR__) . '/config/koneksi.php';
require_once dirname(__DIR__) . '/models/Campaign.php';
require_once dirname(__DIR__) . '/models/Donation.php';

// Cek autentikasi admin
if (!isAdminLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$campaign = new Campaign();
$donation = new Donation();

switch ($action) {
    // ============ CAMPAIGN ============
    case 'store_campaign':
        storeCampaign($campaign);
        break;

    case 'update_campaign':
        updateCampaign($campaign);
        break;

    case 'delete_campaign':
        deleteCampaign($campaign);
        break;

    // ============ DONATION ============
    case 'confirm_donation':
        confirmDonation($donation);
        break;

    case 'cancel_donation':
        cancelDonation($donation);
        break;

    case 'export_donations':
        exportDonations($donation);
        break;

    // ============ PLANTING ============
    case 'store_planting':
        storePlanting($campaign);
        break;

    case 'update_planting':
        updatePlanting($campaign);
        break;

    case 'delete_planting':
        deletePlanting();
        break;

    // ============ API ============
    case 'get_stats':
        getStats($campaign, $donation);
        break;

    case 'get_chart_data':
        getChartData($donation);
        break;

    default:
        jsonResponse(false, 'Action tidak valid');
        break;
}

// ============================================================
// HELPER
// ============================================================
function jsonResponse($success, $message, $data = [])
{
    header('Content-Type: application/json');
    $response = ['success' => $success, 'message' => $message];
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    echo json_encode($response);
    exit;
}

// ============================================================
// CAMPAIGN FUNCTIONS
// ============================================================
function storeCampaign($campaign)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(false, 'Method tidak diizinkan');
    }

    // Validasi
    $errors = validateCampaign($_POST);
    if (!empty($errors)) {
        jsonResponse(false, 'Validasi gagal', ['errors' => $errors]);
    }

    try {
        // Handle upload gambar
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_path = $campaign->uploadImage($_FILES['image']);
        }

        $data = $_POST;
        if ($image_path) {
            $data['image'] = $image_path;
        }

        $campaign_id = $campaign->create($data);

        if ($campaign_id) {
            // Simpan benefits jika ada
            if (isset($_POST['benefits']) && is_array($_POST['benefits'])) {
                foreach ($_POST['benefits'] as $benefit) {
                    if (!empty($benefit)) {
                        $campaign->addBenefit($campaign_id, $benefit);
                    }
                }
            }
            jsonResponse(true, 'Campaign berhasil dibuat', ['campaign_id' => $campaign_id, 'redirect' => 'campaign.php']);
        }
        else {
            jsonResponse(false, 'Gagal membuat campaign');
        }
    }
    catch (Exception $e) {
        jsonResponse(false, 'Gagal menyimpan campaign: ' . $e->getMessage());
    }
}

function updateCampaign($campaign)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(false, 'Method tidak diizinkan');
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        jsonResponse(false, 'ID campaign tidak valid');
    }

    $errors = validateCampaign($_POST);
    if (!empty($errors)) {
        jsonResponse(false, 'Validasi gagal', ['errors' => $errors]);
    }

    // Handle upload gambar
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_path = $campaign->uploadImage($_FILES['image']);
    }

    // Only pass valid DB columns to update
    $allowed_fields = ['title', 'description', 'long_description', 'location', 'tree_type',
        'price_per_tree', 'target_trees', 'deadline', 'partner', 'map_url', 'status', 'category'];
    $data = [];
    foreach ($allowed_fields as $field) {
        if (isset($_POST[$field])) {
            $data[$field] = $_POST[$field];
        }
    }

    if ($image_path) {
        $data['image'] = $image_path;
    }

    $result = $campaign->update($id, $data);

    if ($result) {
        // Update benefits jika ada
        if (isset($_POST['benefits']) && is_array($_POST['benefits'])) {
            $conn = getDB();
            $conn->query("DELETE FROM campaign_benefits WHERE campaign_id = {$id}");
            foreach ($_POST['benefits'] as $benefit) {
                if (!empty($benefit)) {
                    $campaign->addBenefit($id, $benefit);
                }
            }
        }
        jsonResponse(true, 'Campaign berhasil diperbarui', ['redirect' => 'campaign.php']);
    }
    else {
        jsonResponse(false, 'Gagal memperbarui campaign');
    }
}
   

function deleteCampaign($campaign)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(false, 'Method tidak diizinkan');
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        jsonResponse(false, 'ID campaign tidak valid');
    }

    $result = $campaign->delete($id);

    if ($result) {
        jsonResponse(true, 'Campaign berhasil dihapus');
    }
    else {
        jsonResponse(false, 'Gagal menghapus campaign');
    }
}

function validateCampaign($data)
{
    $errors = [];
    if (empty($data['title']))
        $errors['title'] = 'Nama campaign harus diisi';
    if (empty($data['location']))
        $errors['location'] = 'Lokasi harus diisi';
    if (empty($data['tree_type']))
        $errors['tree_type'] = 'Jenis pohon harus diisi';
    if (empty($data['price_per_tree']) || $data['price_per_tree'] <= 0)
        $errors['price_per_tree'] = 'Harga per pohon harus valid';
    if (empty($data['target_trees']) || $data['target_trees'] <= 0)
        $errors['target_trees'] = 'Target pohon harus valid';
    if (empty($data['deadline']))
        $errors['deadline'] = 'Deadline harus diisi';
    if (empty($data['description']))
        $errors['description'] = 'Deskripsi harus diisi';
    return $errors;
}

// ============================================================
// DONATION FUNCTIONS
// ============================================================
function confirmDonation($donation)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(false, 'Method tidak diizinkan');
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        jsonResponse(false, 'ID donasi tidak valid');
    }

    $result = $donation->confirmDonation($id);

    if ($result) {
        jsonResponse(true, 'Donasi berhasil dikonfirmasi');
    }
    else {
        jsonResponse(false, 'Gagal mengkonfirmasi donasi');
    }
}

function cancelDonation($donation)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(false, 'Method tidak diizinkan');
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        jsonResponse(false, 'ID donasi tidak valid');
    }

    $result = $donation->cancelDonation($id);

    if ($result) {
        jsonResponse(true, 'Donasi berhasil dibatalkan');
    }
    else {
        jsonResponse(false, 'Gagal membatalkan donasi');
    }
}

function exportDonations($donation)
{
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $campaign_id = isset($_GET['campaign']) ? $_GET['campaign'] : null;

    $donations = $donation->getAll($status, $campaign_id);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="donasi_sodakoh_pohon_' . date('Ymd') . '.csv"');

    $output = fopen('php://output', 'w');
    // BOM for Excel UTF-8
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($output, [
        'No. Donasi', 'Tanggal', 'Donatur', 'Email', 'Campaign',
        'Jumlah Pohon', 'Nominal', 'Status', 'Metode Pembayaran'
    ]);

    foreach ($donations as $d) {
        fputcsv($output, [
            $d['donation_number'],
            date('d/m/Y H:i', strtotime($d['created_at'])),
            $d['donor_name'],
            $d['donor_email'],
            $d['campaign_title'] ?? '-',
            $d['trees_count'],
            $d['amount'],
            $d['status'],
            $d['payment_method']
        ]);
    }
    fclose($output);
    exit;
}

// ============================================================
// PLANTING FUNCTIONS
// ============================================================
function storePlanting($campaign)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(false, 'Method tidak diizinkan');
    }

    $conn = getDB();

    $campaign_id = (int)$_POST['campaign_id'];
    $trees_planted = (int)$_POST['trees_planted'];
    $planting_date = $conn->real_escape_string($_POST['planting_date']);
    $location = $conn->real_escape_string($_POST['location']);
    $volunteers = (int)($_POST['volunteers'] ?? 0);
    $coordinator = $conn->real_escape_string($_POST['coordinator'] ?? '');
    $description = $conn->real_escape_string($_POST['description'] ?? '');

    // Handle upload gambar
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = UPLOAD_PATH . 'plantings/';
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $file_name = uniqid() . '.' . $ext;
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = 'uploads/plantings/' . $file_name;
        }
    }

    $now = date('Y-m-d H:i:s');
    $sql = "INSERT INTO plantings (campaign_id, trees_planted, planting_date, location, volunteers, coordinator, description, image, status, created_at) 
            VALUES ({$campaign_id}, {$trees_planted}, '{$planting_date}', '{$location}', {$volunteers}, '{$coordinator}', '{$description}', '{$image_path}', 'completed', '{$now}')";

    if ($conn->query($sql)) {
        // Update planted_trees di campaign
        $campaign->updatePlantedTrees($campaign_id, $trees_planted);
        jsonResponse(true, 'Data penanaman berhasil disimpan', ['redirect' => 'planted.php']);
    }
    else {
        jsonResponse(false, 'Gagal menyimpan data penanaman: ' . $conn->error);
    }
}

function updatePlanting($campaign)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(false, 'Method tidak diizinkan');
    }

    $conn = getDB();

    $id = (int)$_POST['id'];
    if ($id <= 0) {
        jsonResponse(false, 'ID penanaman tidak valid');
    }

    $campaign_id = (int)$_POST['campaign_id'];
    $trees_planted = (int)$_POST['trees_planted'];
    $planting_date = $conn->real_escape_string($_POST['planting_date']);
    $location = $conn->real_escape_string($_POST['location']);
    $volunteers = (int)($_POST['volunteers'] ?? 0);
    $coordinator = $conn->real_escape_string($_POST['coordinator'] ?? '');
    $description = $conn->real_escape_string($_POST['description'] ?? '');
    $now = date('Y-m-d H:i:s');

    // Handle upload gambar jika ada file baru
    $image_sql = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = UPLOAD_PATH . 'plantings/';
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $file_name = uniqid() . '.' . $ext;
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = 'uploads/plantings/' . $file_name;
            $image_sql = ", image = '{$image_path}'";
        }
    }

    $sql = "UPDATE plantings 
            SET campaign_id = {$campaign_id}, trees_planted = {$trees_planted}, 
                planting_date = '{$planting_date}', location = '{$location}', 
                volunteers = {$volunteers}, coordinator = '{$coordinator}', 
                description = '{$description}'{$image_sql}, updated_at = '{$now}' 
            WHERE id = {$id}";

    if ($conn->query($sql)) {
        jsonResponse(true, 'Data penanaman berhasil diupdate');
    }
    else {
        jsonResponse(false, 'Gagal mengupdate data penanaman: ' . $conn->error);
    }
}

function deletePlanting()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(false, 'Method tidak diizinkan');
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        jsonResponse(false, 'ID penanaman tidak valid');
    }

    $conn = getDB();
    $sql = "DELETE FROM plantings WHERE id = {$id}";

    if ($conn->query($sql)) {
        jsonResponse(true, 'Data penanaman berhasil dihapus');
    }
    else {
        jsonResponse(false, 'Gagal menghapus data penanaman');
    }
}

// ============================================================
// API / STATS FUNCTIONS
// ============================================================
function getStats($campaign, $donation)
{
    $campaign_stats = $campaign->getStats();
    $donation_stats = $donation->getStats();

    jsonResponse(true, 'OK', ['data' => array_merge($campaign_stats, $donation_stats)]);
}

function getChartData($donation)
{
    $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
    $monthly_donations = $donation->getMonthlyDonations($year);

    jsonResponse(true, 'OK', ['data' => ['monthly_donations' => $monthly_donations]]);
}
?>  $monthly_donations = $donation->getMonthlyDonations($year);

    jsonResponse(true, 'OK', ['data' => ['monthly_donations' => $monthly_donations]]);
}
?>