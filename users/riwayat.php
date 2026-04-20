<?php
// users/history.php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

// Dummy Data untuk contoh tampilan
$donations = [
    ['date' => '2026-04-15', 'campaign' => 'Penanaman Pohon Mangrove', 'amount' => 50000, 'status' => 'Berhasil'],
    ['date' => '2026-03-10', 'campaign' => 'Hutan Kota Jakarta', 'amount' => 100000, 'status' => 'Berhasil'],
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Donasi - Sodakoh Pohon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; background-color: #faf7f2; } </style>
</head>
<body class="antialiased">

    <?php include '../includes/header.php'; ?>

    <div class="max-w-5xl mx-auto px-4 py-10">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Histori Sedekah</h1>
        
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <?php if (empty($donations)): ?>
                <div class="p-10 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                        <i class="fas fa-receipt text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Belum ada donasi</h3>
                    <p class="text-gray-500 mt-1">Mulai sedekah pohon pertama Anda sekarang.</p>
                    <a href="../campaign.php" class="inline-block mt-4 px-5 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">Lihat Campaign</a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs leading-normal">
                            <tr>
                                <th class="py-3 px-6 font-semibold">Tanggal</th>
                                <th class="py-3 px-6 font-semibold">Campaign</th>
                                <th class="py-3 px-6 font-semibold">Jumlah</th>
                                <th class="py-3 px-6 font-semibold">Status</th>
                                <th class="py-3 px-6 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm font-light">
                            <?php foreach ($donations as $d): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                    <td class="py-3 px-6 whitespace-nowrap"><?php echo date('d M Y', strtotime($d['date'])); ?></td>
                                    <td class="py-3 px-6 font-medium text-gray-900"><?php echo htmlspecialchars($d['campaign']); ?></td>
                                    <td class="py-3 px-6">Rp <?php echo number_format($d['amount'], 0, ',', '.'); ?></td>
                                    <td class="py-3 px-6">
                                        <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-semibold">
                                            <?php echo $d['status']; ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <a href="#" class="text-primary-600 hover:text-primary-800 text-xs font-semibold">Detail</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>