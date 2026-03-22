<?php
// controllers/cartController.php
// AJAX handler untuk operasi keranjang (update qty, remove item, clear)

require_once dirname(__DIR__) . '/config/koneksi.php';
require_once dirname(__DIR__) . '/models/Cart.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Harus login
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
if (!$is_logged_in) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu', 'require_login' => true]);
    exit;
}

$action = isset($_POST['action']) ? trim($_POST['action']) : '';
$cart   = new Cart();

switch ($action) {
    // ─── Update quantity ──────────────────────────────────────────────────
    case 'update_quantity':
        $item_id  = isset($_POST['item_id'])  ? trim($_POST['item_id'])    : '';
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity']    : 0;

        if (empty($item_id) || $quantity < 1) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
            exit;
        }

        $result = $cart->updateQuantity($item_id, $quantity);
        echo json_encode([
            'success'    => $result,
            'message'    => $result ? 'Jumlah diperbarui' : 'Gagal memperbarui jumlah',
            'cart_count' => $cart->getTotalItems(),
        ]);
        break;

    // ─── Remove item ──────────────────────────────────────────────────────
    case 'remove_item':
        $item_id = isset($_POST['item_id']) ? trim($_POST['item_id']) : '';

        if (empty($item_id)) {
            echo json_encode(['success' => false, 'message' => 'Item ID tidak valid']);
            exit;
        }

        $result = $cart->removeItem($item_id);
        echo json_encode([
            'success'    => $result,
            'message'    => $result ? 'Item dihapus' : 'Item tidak ditemukan',
            'cart_count' => $cart->getTotalItems(),
        ]);
        break;

    // ─── Clear cart ───────────────────────────────────────────────────────
    case 'clear_cart':
        $result = $cart->clear();
        echo json_encode([
            'success'    => $result,
            'message'    => $result ? 'Keranjang dikosongkan' : 'Gagal mengosongkan keranjang',
            'cart_count' => 0,
        ]);
        break;

    // ─── Get cart summary (untuk badge header) ────────────────────────────
    case 'get_summary':
        echo json_encode([
            'success'    => true,
            'cart_count' => $cart->getTotalItems(),
            'subtotal'   => $cart->getSubtotal(),
            'is_empty'   => $cart->isEmpty(),
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak dikenali']);
        break;
}
exit;
