<?php
// users/certificate.php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Digital - Sodakoh Pohon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; background-color: #faf7f2; } </style>
</head>
<body class="antialiased">

    <?php include '../includes/header.php'; ?>

    <div class="max-w-4xl mx-auto px-4 py-10">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Sertifikat Sedekah</h1>
            <span class="text-sm text-gray-500">Total Sertifikat: 0</span>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-10 text-center">
            <img src="https://placehold.co/100x100/e5e7eb/9ca3af?text=Cert" alt="Empty State" class="w-20 h-20 mx-auto mb-4 rounded-full">
            <h3 class="text-lg font-medium text-gray-900">Belum ada sertifikat</h3>
            <p class="text-gray-500 mt-2 max-w-md mx-auto">
                Sertifikat digital akan otomatis muncul di sini setelah donasi Anda diverifikasi dan pohon berhasil ditanam.
            </p>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>