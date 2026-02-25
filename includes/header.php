<?php
// Deteksi halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);
$is_home = $current_page === 'index.php' || $current_page === 'header.php';
$is_campaign = $current_page === 'campaign.php' || $current_page === 'campaign-detail.php';
$is_laporan = $current_page === 'laporan.php';
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        },
                        earth: {
                            50: '#faf7f2',
                            100: '#f0e9df',
                            200: '#e2d4c2',
                            300: '#d4bfa5',
                            400: '#c6a988',
                            500: '#b8946b',
                            600: '#9e7a54',
                            700: '#7f6042',
                            800: '#5f4731',
                            900: '#3f2e20',
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'counter': 'counter 2s ease-out forwards',
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        counter: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    },
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
            background: linear-gradient(90deg, rgba(255,255,255,0.2) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.2) 75%, transparent 75%, transparent);
            background-size: 20px 20px;
            animation: move 1s linear infinite;
            border-radius: 100px;
        }
        
        @keyframes move {
            0% { background-position: 0 0; }
            100% { background-position: 20px 0; }
        }
        
        .campaign-card {
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 35px -8px rgba(0,0,0,0.05), 0 10px 10px -5px rgba(0,0,0,0.02);
        }
        
        .campaign-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 45px -12px rgba(5, 150, 105, 0.15), 0 15px 20px -8px rgba(0,0,0,0.05);
        }
        
        .campaign-image {
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        
        .campaign-card:hover .campaign-image {
            transform: scale(1.08);
        }
        
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
            border: 1px solid rgba(5, 150, 105, 0.1);
        }
        
        .stat-card:hover {
            border-color: rgba(5, 150, 105, 0.3);
            transform: translateY(-4px);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #059669 0%, #10b981 80%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .badge-new {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        }
        
        .badge-urgent {
            background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
        }
        
        .badge-popular {
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
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
            <div class="flex justify-between items-center h-20">
                
                <!-- Logo -->
                <div class="flex items-center space-x-3 flex-shrink-0">
                    <div class="relative">
                        <div class="absolute inset-0 bg-primary-600 rounded-xl blur-sm opacity-60"></div>
                        <div class="relative bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl p-2.5">
                            <i class="fas fa-tree text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-extrabold">
                            <span class="text-primary-700">Sodakoh</span>
                            <span class="text-earth-700">Pohon</span>
                        </span>
                        <span class="text-xs text-gray-500 -mt-1">Sedekah dalam bentuk pohon</span>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center justify-center gap-1 flex-1">
                    <a href="index.php" class="<?php echo $is_home ? 'text-primary-700 font-semibold border-b-2 border-primary-600' : 'text-gray-600 hover:text-primary-600 font-medium transition'; ?> py-2 px-3 text-sm">
                        Beranda
                    </a>
                    <a href="campaign.php" class="<?php echo $is_campaign ? 'text-primary-700 font-semibold border-b-2 border-primary-600' : 'text-gray-600 hover:text-primary-600 font-medium transition'; ?> py-2 px-3 text-sm">
                        Campaign
                    </a>
                    
                    <!-- Programs Dropdown -->
                    <div class="relative group" id="programsDropdown">
                        <button id="programsBtn" class="text-gray-600 hover:text-primary-600 font-medium transition py-2 px-3 text-sm flex items-center cursor-pointer">
                            Program
                            <i class="fas fa-chevron-down ml-1.5 text-xs transition duration-300" id="programsIcon"></i>
                        </button>
                        <div id="programsMenu" class="absolute left-0 mt-1 w-64 bg-white rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-300 py-2 z-50 border border-gray-100">
                            <a href="#programs" class="block px-4 py-3 hover:bg-primary-50 text-gray-700 hover:text-primary-700 transition">
                                <div class="font-semibold flex items-center text-sm">
                                    <i class="fas fa-building mr-3 text-primary-600 w-5 text-center"></i>Corporation
                                </div>
                                <p class="text-xs text-gray-500 mt-1 ml-8">CSR dengan penanaman pohon</p>
                            </a>
                            <a href="#programs" class="block px-4 py-3 hover:bg-primary-50 text-gray-700 hover:text-primary-700 transition">
                                <div class="font-semibold flex items-center text-sm">
                                    <i class="fas fa-handshake mr-3 text-primary-600 w-5 text-center"></i>Collaboration
                                </div>
                                <p class="text-xs text-gray-500 mt-1 ml-8">Bundling produk/jasa dengan donasi</p>
                            </a>
                            <a href="#programs" class="block px-4 py-3 hover:bg-primary-50 text-gray-700 hover:text-primary-700 transition">
                                <div class="font-semibold flex items-center text-sm">
                                    <i class="fas fa-leaf mr-3 text-primary-600 w-5 text-center"></i>Sustainability
                                </div>
                                <p class="text-xs text-gray-500 mt-1 ml-8">Konsultasi & implementasi proyek</p>
                            </a>
                        </div>
                    </div>

                    <a href="#how-it-works" class="text-gray-600 hover:text-primary-600 font-medium transition py-2 px-3 text-sm">
                        Cara Kerja
                    </a>
                    <a href="laporan.php" class="<?php echo $is_laporan ? 'text-primary-700 font-semibold border-b-2 border-primary-600' : 'text-gray-600 hover:text-primary-600 font-medium transition'; ?> py-2 px-3 text-sm">
                        Laporan
                    </a>
                    <a href="#about" class="text-gray-600 hover:text-primary-600 font-medium transition py-2 px-3 text-sm">
                        Tentang
                    </a>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3 flex-shrink-0">
                    <a href="cart.php" class="relative p-2 text-gray-600 hover:text-primary-600 transition">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="absolute -top-1 -right-1 bg-primary-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">0</span>
                    </a>
                    <a href="admin/login.php" class="hidden md:inline-flex items-center px-4 py-2 border-2 border-primary-600 text-primary-700 font-semibold rounded-lg hover:bg-primary-50 transition text-sm">
                        <i class="fas fa-user-shield mr-2"></i>Admin
                    </a>
                    <a href="#campaigns" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25 text-sm whitespace-nowrap">
                        <i class="fas fa-hand-holding-heart mr-2"></i>Mulai Sedekah
                    </a>
                </div>

            </div>
        </div>
    </nav>
