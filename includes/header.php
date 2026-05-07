<?php
// Mulai session jika belum dimulai (sudah ada di koneksi.php, tapi aman ditambahkan)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ INCLUDE KONEKSI DATABASE (Singleton Pattern)
require_once dirname(__DIR__) . '/config/koneksi.php';

// Deteksi halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);
$is_home = ($current_page === 'index.php' || $current_page === 'header.php');
$is_campaign = ($current_page === 'campaign.php' || $current_page === 'campaign-detail.php');
$is_laporan = ($current_page === 'laporan.php');
$is_tentang = ($current_page === 'tentang.php');

// Deteksi status login user (pakai helper function dari koneksi.php)
$is_logged_in = isLoggedIn();

// 🔹 DEFAULT DATA USER (fallback)
$userData = [
    'name' => 'Pengguna',
    'email' => '',
    'avatar' => 'assets/images/default-avatar.png' // ✅ Ganti dengan path default Anda
];

// 🔹 AMBIL DATA USER DARI DATABASE JIKA SUDAH LOGIN
if ($is_logged_in && isset($_SESSION['user_id'])) {
    $userId = (int) $_SESSION['user_id'];

    // ✅ Gunakan getDB() untuk mendapatkan koneksi mysqli
    $conn = getDB();

    // ✅ Prepared Statement untuk keamanan (anti SQL Injection)
    $stmt = $conn->prepare("SELECT name, email, avatar FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        $userData['name'] = $user['name'] ?? $userData['name'];
        $userData['email'] = $user['email'] ?? $userData['email'];
        // Gunakan avatar dari DB jika ada & tidak kosong
        $userData['avatar'] = !empty($user['avatar']) ? $user['avatar'] : $userData['avatar'];
    }
}

// Hitung jumlah item di keranjang dari session
$_cart_count = 0;
if (isset($_SESSION['cart']['items']) && is_array($_SESSION['cart']['items'])) {
    foreach ($_SESSION['cart']['items'] as $_ci) {
        $_cart_count += (int) ($_ci['quantity'] ?? 1);
    }
}

// Logika Link Navigasi dengan Absolute Path
$program_href = $is_home ? '#programs' : '/index.php#programs';
$how_it_works_href = $is_home ? '#how-it-works' : '/index.php#how-it-works';
$donate_href = $is_home ? '#campaigns' : '/index.php#campaigns';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sodakoh Pohon - Sedekah dalam Bentuk Pohon</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7',
                            400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857',
                            800: '#065f46', 900: '#064e3b',
                        },
                        earth: {
                            50: '#faf7f2', 100: '#f0e9df', 200: '#e2d4c2', 300: '#d4bfa5',
                            400: '#c6a988', 500: '#b8946b', 600: '#9e7a54', 700: '#7f6042',
                            800: '#5f4731', 900: '#3f2e20',
                        }
                    },
                    fontFamily: { 'sans': ['Inter', 'sans-serif'] },
                    boxShadow: {
                        'card': '0 20px 35px -8px rgba(0,0,0,0.05), 0 10px 10px -5px rgba(0,0,0,0.02)',
                        'card-hover': '0 30px 45px -12px rgba(5, 150, 105, 0.15), 0 15px 20px -8px rgba(0,0,0,0.05)',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #faf7f2;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .progress-bar {
            height: 8px;
            border-radius: 100px;
            background-color: #e5e7eb;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            border-radius: 100px;
            transition: width 1s ease;
            position: relative;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.2) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, transparent 75%, transparent);
            background-size: 20px 20px;
            animation: move 1s linear infinite;
            border-radius: 100px;
        }

        @keyframes move {
            0% {
                background-position: 0 0;
            }

            100% {
                background-position: 20px 0;
            }
        }

        .campaign-card {
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 35px -8px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        .campaign-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 45px -12px rgba(5, 150, 105, 0.15), 0 15px 20px -8px rgba(0, 0, 0, 0.05);
        }

        .campaign-image {
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .campaign-card:hover .campaign-image {
            transform: scale(1.08);
        }

        .gradient-text {
            background: linear-gradient(135deg, #059669 0%, #10b981 80%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body class="antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-effect border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Desktop Layout -->
            <div class="hidden md:flex justify-between items-center h-20">

                <!-- Logo -->
                <a href="/index.php" class="flex items-center space-x-2 flex-shrink-0">
                    <div class="relative">
                        <div class="absolute inset-0 bg-primary-600 rounded-lg blur-sm opacity-60"></div>
                        <div class="relative bg-gradient-to-br from-primary-600 to-primary-700 rounded-lg p-1.5">
                            <i class="fas fa-tree text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-extrabold">
                            <span class="text-primary-700">Sodakoh</span>
                            <span class="text-earth-700">Pohon</span>
                        </span>
                        <span class="text-xs text-gray-500 -mt-1">Sedekah pohon</span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="flex items-center justify-center gap-1 flex-1">
                    <a href="/index.php"
                        class="<?php echo $is_home ? 'text-primary-700 font-semibold border-b-2 border-primary-600' : 'text-gray-600 hover:text-primary-600 font-medium transition'; ?> py-2 px-3 text-sm">
                        Beranda
                    </a>
                    <a href="/campaign.php"
                        class="<?php echo $is_campaign ? 'text-primary-700 font-semibold border-b-2 border-primary-600' : 'text-gray-600 hover:text-primary-600 font-medium transition'; ?> py-2 px-3 text-sm">
                        Campaign
                    </a>

                    <!-- Programs Dropdown -->
                    <div class="relative" id="programsDropdown">
                        <button type="button" id="programsBtn"
                            class="text-gray-600 hover:text-primary-600 font-medium transition py-2 px-3 text-sm flex items-center cursor-pointer">
                            Program
                            <i class="fas fa-chevron-down ml-1.5 text-xs transition duration-300" id="programsIcon"></i>
                        </button>
                        <div id="programsMenu"
                            class="absolute left-0 mt-1 w-64 bg-white rounded-xl shadow-xl opacity-0 invisible transition-all duration-300 py-2 z-50 border border-gray-100 pointer-events-none">
                            <a href="<?php echo $program_href; ?>"
                                class="block px-4 py-3 hover:bg-primary-50 text-gray-700 hover:text-primary-700 transition">
                                <div class="font-semibold flex items-center text-sm">
                                    <i class="fas fa-building mr-3 text-primary-600 w-5 text-center"></i>Corporation
                                </div>
                                <p class="text-xs text-gray-500 mt-1 ml-8">CSR dengan penanaman pohon</p>
                            </a>
                            <a href="<?php echo $program_href; ?>"
                                class="block px-4 py-3 hover:bg-primary-50 text-gray-700 hover:text-primary-600 transition">
                                <div class="font-semibold flex items-center text-sm">
                                    <i class="fas fa-handshake mr-3 text-primary-600 w-5 text-center"></i>Collaboration
                                </div>
                                <p class="text-xs text-gray-500 mt-1 ml-8">Bundling produk/jasa dengan donasi</p>
                            </a>
                            <a href="<?php echo $program_href; ?>"
                                class="block px-4 py-3 hover:bg-primary-50 text-gray-700 hover:text-primary-600 transition">
                                <div class="font-semibold flex items-center text-sm">
                                    <i class="fas fa-leaf mr-3 text-primary-600 w-5 text-center"></i>Sustainability
                                </div>
                                <p class="text-xs text-gray-500 mt-1 ml-8">Konsultasi & implementasi proyek</p>
                            </a>
                        </div>
                    </div>

                    <!-- Link Cara Kerja -->
                    <a href="<?php echo $how_it_works_href; ?>"
                        class="text-gray-600 hover:text-primary-600 font-medium transition py-2 px-3 text-sm">
                        Cara Kerja
                    </a>

                    <a href="/laporan.php"
                        class="<?php echo $is_laporan ? 'text-primary-700 font-semibold border-b-2 border-primary-600' : 'text-gray-600 hover:text-primary-600 font-medium transition'; ?> py-2 px-3 text-sm">
                        Laporan
                    </a>

                    <!-- Link Tentang -->
                    <a href="/tentang.php"
                        class="<?php echo $is_tentang ? 'text-primary-700 font-semibold border-b-2 border-primary-600' : 'text-gray-600 hover:text-primary-600 font-medium transition'; ?> py-2 px-3 text-sm">
                        Tentang
                    </a>
                </div>

                <!-- Action Buttons (Desktop) -->
                <div class="flex items-center gap-3 flex-shrink-0">
                    <a href="/cart.php" class="relative p-2 text-gray-600 hover:text-primary-600 transition">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cartBadge"
                            class="absolute -top-1 -right-1 bg-primary-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"><?php echo $_cart_count; ?></span>
                    </a>

                    <!-- Login / Account Menu -->
                    <?php if ($is_logged_in): ?>
                        <div class="relative group">
                            <button
                                class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-100 hover:bg-primary-200 hover:shadow-lg hover:scale-110 transition duration-200 cursor-pointer overflow-hidden border-2 border-primary-200"
                                title="<?php echo htmlspecialchars($userData['name']); ?>">

                                <?php
                                $avatarSrc = htmlspecialchars($userData['avatar']);
                                $isDefault = ($avatarSrc === 'assets/images/default-avatar.png' || empty($avatarSrc));
                                ?>

                                <?php if (!$isDefault): ?>
                                    <img src="/<?php echo $avatarSrc; ?>" alt="Profile" class="w-full h-full object-cover"
                                        onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-user text-lg text-primary-600\'></i>'">
                                <?php else: ?>
                                    <i class="fas fa-user text-lg text-primary-600"></i>
                                <?php endif; ?>
                            </button>

                            <div
                                class="absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 py-2 z-50 border border-gray-100">

                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($userData['name']); ?>
                                    </p>
                                    <p class="text-xs text-gray-500 truncate">
                                        <?php echo htmlspecialchars($userData['email']); ?>
                                    </p>
                                </div>

                                <a href="/users/riwayat.php"
                                    class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition duration-150 text-sm">
                                    <i class="fas fa-history mr-2 w-4"></i>Histori Donasi
                                </a>
                                <a href="/users/sertifikat.php"
                                    class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition duration-150 text-sm">
                                    <i class="fas fa-certificate mr-2 w-4"></i>Sertifikat
                                </a>
                                <a href="/users/profile.php"
                                    class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition duration-150 text-sm">
                                    <i class="fas fa-user-circle mr-2 w-4"></i>Profile
                                </a>

                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="/auth.php?action=logout"
                                    class="block px-4 py-2 text-red-600 hover:bg-red-50 transition duration-150 text-sm font-semibold">
                                    <i class="fas fa-sign-out-alt mr-2 w-4"></i>Logout
                                </a>
                            </div>
                        </div>

                    <?php else: ?>
                        <a href="/login.php"
                            class="inline-flex items-center px-4 py-2 border-2 border-primary-600 text-primary-700 font-semibold rounded-lg hover:bg-primary-50 transition text-sm">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                    <?php endif; ?>

                    <!-- Tombol Mulai Sedekah -->
                    <a href="<?php echo $donate_href; ?>"
                        class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25 text-sm whitespace-nowrap">
                        <i class="fas fa-hand-holding-heart mr-2"></i>Mulai Sedekah
                    </a>
                </div>
            </div>

            <!-- Mobile Layout -->
            <div class="md:hidden flex justify-between items-center h-20">

                <!-- Logo -->
                <a href="/index.php" class="flex items-center space-x-2 flex-shrink-0">
                    <div class="relative">
                        <div class="absolute inset-0 bg-primary-600 rounded-lg blur-sm opacity-60"></div>
                        <div class="relative bg-gradient-to-br from-primary-600 to-primary-700 rounded-lg p-1.5">
                            <i class="fas fa-tree text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-extrabold">
                            <span class="text-primary-700">Sodakoh</span>
                            <span class="text-earth-700">Pohon</span>
                        </span>
                        <span class="text-xs text-gray-500 -mt-1">Sedekah pohon</span>
                    </div>
                </a>

                <!-- Mobile Menu Trigger & Profile -->
                <div class="flex items-center gap-3">
                    <!-- Hamburger Button -->
                    <button id="mobileMenuBtn" class="p-2 text-gray-600 hover:text-primary-600 transition">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <!-- Profile / Login (Mobile) -->
                    <?php if ($is_logged_in): ?>
                        <div class="relative group">
                            <button
                                class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-100 hover:bg-primary-200 hover:shadow-lg hover:scale-110 transition duration-200 cursor-pointer overflow-hidden border-2 border-primary-200"
                                title="<?php echo htmlspecialchars($userData['name']); ?>">

                                <?php
                                $avatarSrc = htmlspecialchars($userData['avatar']);
                                $isDefault = ($avatarSrc === 'assets/images/default-avatar.png' || empty($avatarSrc));
                                ?>

                                <?php if (!$isDefault): ?>
                                    <img src="/<?php echo $avatarSrc; ?>" alt="Profile" class="w-full h-full object-cover"
                                        onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-user text-lg text-primary-600\'></i>'">
                                <?php else: ?>
                                    <i class="fas fa-user text-lg text-primary-600"></i>
                                <?php endif; ?>
                            </button>

                            <div
                                class="absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 py-2 z-50 border border-gray-100">

                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($userData['name']); ?>
                                    </p>
                                    <p class="text-xs text-gray-500 truncate">
                                        <?php echo htmlspecialchars($userData['email']); ?>
                                    </p>
                                </div>

                                <a href="/users/riwayat.php"
                                    class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition duration-150 text-sm">
                                    <i class="fas fa-history mr-2 w-4"></i>Histori Donasi
                                </a>
                                <a href="/users/sertifikat.php"
                                    class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition duration-150 text-sm">
                                    <i class="fas fa-certificate mr-2 w-4"></i>Sertifikat
                                </a>
                                <a href="/users/profile.php"
                                    class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition duration-150 text-sm">
                                    <i class="fas fa-user-circle mr-2 w-4"></i>Profile
                                </a>

                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="/auth.php?action=logout"
                                    class="block px-4 py-2 text-red-600 hover:bg-red-50 transition duration-150 text-sm font-semibold">
                                    <i class="fas fa-sign-out-alt mr-2 w-4"></i>Logout
                                </a>
                            </div>
                        </div>

                    <?php else: ?>
                        <a href="/login.php"
                            class="inline-flex items-center px-3 py-2 border-2 border-primary-600 text-primary-700 font-semibold rounded-lg hover:bg-primary-50 transition text-sm">
                            <i class="fas fa-sign-in-alt mr-1"></i>Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mobile Menu Dropdown -->
            <div id="mobileMenu"
                class="hidden md:hidden overflow-y-auto bg-white border-t border-gray-100 max-h-0 transition-all duration-300 ease-in-out">
                <div class="px-3 py-2 space-y-1">
                    <a href="/index.php"
                        class="<?php echo $is_home ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-700 hover:text-primary-600'; ?> block px-3 py-2 rounded-lg transition text-sm">
                        <i class="fas fa-home mr-2 w-4"></i><span class="align-middle">Beranda</span>
                    </a>
                    <a href="/campaign.php"
                        class="<?php echo $is_campaign ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-700 hover:text-primary-600'; ?> block px-3 py-2 rounded-lg transition text-sm">
                        <i class="fas fa-handshake mr-2 w-4"></i><span class="align-middle">Campaign</span>
                    </a>

                    <!-- Mobile Programs -->
                    <div>
                        <button id="mobilePrograms"
                            class="w-full text-left px-3 py-2 text-gray-700 hover:text-primary-600 rounded-lg transition flex items-center justify-between text-sm">
                            <span><i class="fas fa-leaf mr-2 w-4"></i><span class="align-middle">Program</span></span>
                            <i class="fas fa-chevron-down text-xs transition duration-300" id="mobileProgramsIcon"></i>
                        </button>
                        <div id="mobileProgramsMenu" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                            <div class="bg-gray-50 rounded-lg mx-2 my-1 overflow-hidden">
                                <a href="<?php echo $program_href; ?>"
                                    class="block px-3 py-1.5 text-gray-700 hover:bg-primary-100 hover:text-primary-700 transition border-l-2 border-transparent hover:border-primary-600 text-xs">
                                    <i class="fas fa-building mr-2 w-3"></i><span class="align-middle">Corporation</span>
                                </a>
                                <a href="<?php echo $program_href; ?>"
                                    class="block px-3 py-1.5 text-gray-700 hover:bg-primary-100 hover:text-primary-700 transition border-l-2 border-transparent hover:border-primary-600 text-xs">
                                    <i class="fas fa-handshake mr-2 w-3"></i><span class="align-middle">Collaboration</span>
                                </a>
                                <a href="<?php echo $program_href; ?>"
                                    class="block px-3 py-1.5 text-gray-700 hover:bg-primary-100 hover:text-primary-700 transition border-l-2 border-transparent hover:border-primary-600 text-xs">
                                    <i class="fas fa-leaf mr-2 w-3"></i><span class="align-middle">Sustainability</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="<?php echo $how_it_works_href; ?>"
                        class="text-gray-700 hover:text-primary-600 block px-3 py-2 rounded-lg transition text-sm">
                        <i class="fas fa-cogs mr-2 w-4"></i><span class="align-middle">Cara Kerja</span>
                    </a>

                    <a href="/laporan.php"
                        class="<?php echo $is_laporan ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-700 hover:text-primary-600'; ?> block px-3 py-2 rounded-lg transition text-sm">
                        <i class="fas fa-chart-bar mr-2 w-4"></i><span class="align-middle">Laporan</span>
                    </a>

                    <a href="/tentang.php"
                        class="<?php echo $is_tentang ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-700 hover:text-primary-600'; ?> block px-3 py-2 rounded-lg transition text-sm">
                        <i class="fas fa-info-circle mr-2 w-4"></i><span class="align-middle">Tentang</span>
                    </a>

                    <a href="/cart.php"
                        class="text-gray-700 hover:text-primary-600 block px-3 py-2 rounded-lg transition text-sm">
                        <i class="fas fa-shopping-cart mr-2 w-4"></i><span class="align-middle">Keranjang</span>
                        <span class="ml-2 bg-primary-600 text-white text-xs font-bold rounded-full px-2 py-0.5"><?php echo $_cart_count; ?></span>
                    </a>

                    <a href="<?php echo $donate_href; ?>"
                        class="block px-3 py-2 mt-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-lg hover:from-primary-700 hover:to-primary-800 transition text-sm">
                        <i class="fas fa-hand-holding-heart mr-2"></i><span class="align-middle">Mulai Sedekah</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Spacer untuk menghindari konten tertutup navbar -->
    <div class="h-20"></div>

    <!-- Script untuk Menangani Dropdown Klik -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('programsBtn');
            const menu = document.getElementById('programsMenu');
            const icon = document.getElementById('programsIcon');

            if (btn && menu && icon) {
                const toggleMenu = (show) => {
                    if (show) {
                        menu.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
                        menu.classList.add('opacity-100', 'visible', 'pointer-events-auto');
                        icon.style.transform = 'rotate(180deg)';
                    } else {
                        menu.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                        menu.classList.remove('opacity-100', 'visible', 'pointer-events-auto');
                        icon.style.transform = 'rotate(0deg)';
                    }
                };

                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = !menu.classList.contains('opacity-0');
                    toggleMenu(!isOpen);
                });

                document.addEventListener('click', (e) => {
                    if (!btn.contains(e.target) && !menu.contains(e.target)) {
                        toggleMenu(false);
                    }
                });
            }
        });

        // Mobile Menu Handler
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobilePrograms = document.getElementById('mobilePrograms');
        const mobileProgramsMenu = document.getElementById('mobileProgramsMenu');
        const mobileProgramsIcon = document.getElementById('mobileProgramsIcon');

        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                const isHidden = mobileMenu.classList.contains('hidden');
                if (isHidden) {
                    mobileMenu.classList.remove('hidden', 'max-h-0');
                    mobileMenu.classList.add('max-h-screen');
                } else {
                    mobileMenu.classList.add('hidden', 'max-h-0');
                    mobileMenu.classList.remove('max-h-screen');
                    // Close programs submenu when closing main menu
                    if (mobileProgramsMenu) {
                        mobileProgramsMenu.classList.add('max-h-0');
                        mobileProgramsMenu.classList.remove('max-h-32');
                        if (mobileProgramsIcon) {
                            mobileProgramsIcon.style.transform = 'rotate(0deg)';
                        }
                    }
                }
            });
        }

        if (mobilePrograms && mobileProgramsMenu) {
            mobilePrograms.addEventListener('click', () => {
                const isHidden = mobileProgramsMenu.classList.contains('max-h-0');
                if (isHidden) {
                    mobileProgramsMenu.classList.remove('max-h-0');
                    mobileProgramsMenu.classList.add('max-h-32');
                    if (mobileProgramsIcon) {
                        mobileProgramsIcon.style.transform = 'rotate(180deg)';
                    }
                } else {
                    mobileProgramsMenu.classList.add('max-h-0');
                    mobileProgramsMenu.classList.remove('max-h-32');
                    if (mobileProgramsIcon) {
                        mobileProgramsIcon.style.transform = 'rotate(0deg)';
                    }
                }
            });
        }

        // Close mobile menu when clicking on links
        const mobileMenuLinks = mobileMenu ? mobileMenu.querySelectorAll('a:not(button + *)') : [];
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (mobileMenu && mobileMenuBtn) {
                    mobileMenu.classList.add('hidden', 'max-h-0');
                    mobileMenu.classList.remove('max-h-96');
                }
            });
        });
    </script>
</body>

</html>