<?php
// index.php - Front Controller
session_start();

// ================= KONEKSI DATABASE =================
require_once 'config/koneksi.php';
require_once 'helpers/campaign.php';

// ================= DATA STATISTIK =================
$stats = getCampaignStats();
$total_trees = $stats['total_trees'];
$total_planted = $stats['total_planted'];
$total_donors = $stats['total_donors'];
$total_locations = $stats['total_locations'];

// ================= AMBIL DATA CAMPAIGN =================
$campaigns = getCampaignsForHome(6); // Ambil 6 campaign terbaru untuk home page
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
                    <a href="#" class="text-primary-700 font-semibold border-b-2 border-primary-600 py-2 px-3 text-sm">
                        Beranda
                    </a>
                    <a href="campaign.php" class="text-gray-600 hover:text-primary-600 font-medium transition py-2 px-3 text-sm">
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
                    <a href="laporan.php" class="text-gray-600 hover:text-primary-600 font-medium transition py-2 px-3 text-sm">
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

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-0 left-0 w-96 h-96 bg-primary-500 rounded-full filter blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-primary-400 rounded-full filter blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Column -->
                <div data-aos="fade-right" data-aos-duration="1000">
                    <div class="inline-flex items-center bg-primary-100 rounded-full px-4 py-2 mb-6">
                        <div class="w-2 h-2 bg-primary-600 rounded-full mr-2 animate-pulse"></div>
                        <span class="text-sm font-semibold text-primary-800">#SodakohPohon</span>
                    </div>
                    
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                        Sedekah Bukan<br>dalam Bentuk Uang,<br>
                        <span class="gradient-text">Tapi dalam Bentuk</span>
                        <span class="relative">
                            <span class="text-primary-700">Pohon</span>
                            <svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 200 8" fill="none">
                                <path d="M1 5C50 2 150 2 199 5" stroke="#10b981" stroke-width="2" stroke-dasharray="4 4"/>
                            </svg>
                        </span>
                    </h1>
                    
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Bayangkan, setiap pohon yang Anda sedekahkan akan tumbuh dan memberikan manfaat 
                        berlipat untuk bumi, untuk manusia, dan untuk keberkahan hidup.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#campaigns" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-2xl hover:from-primary-700 hover:to-primary-800 transition shadow-xl shadow-primary-600/30 text-lg">
                            <i class="fas fa-tree mr-3"></i>
                            Mulai Sodakoh Pohon
                        </a>
                        <a href="#how-it-works" class="inline-flex items-center justify-center px-8 py-4 bg-white text-gray-700 font-bold rounded-2xl border-2 border-gray-200 hover:border-primary-600 hover:text-primary-600 transition text-lg">
                            <i class="fas fa-play-circle mr-3"></i>
                            Lihat Cara Kerja
                        </a>
                    </div>
                    
                    <!-- Trust Indicators -->
                    <div class="flex items-center gap-6 mt-8 pt-8 border-t border-gray-200">
                        <div class="flex -space-x-2">
                            <img class="w-10 h-10 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Donatur">
                            <img class="w-10 h-10 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Donatur">
                            <img class="w-10 h-10 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Donatur">
                            <div class="w-10 h-10 rounded-full bg-primary-100 border-2 border-white flex items-center justify-center">
                                <span class="text-xs font-bold text-primary-700">2k+</span>
                            </div>
                        </div>
                        <span class="text-gray-600">Telah dipercaya <span class="font-bold text-primary-700">3.241+</span> donatur</span>
                    </div>
                </div>
                
                <!-- Right Column - Visual Stats -->
                <div data-aos="fade-left" data-aos-duration="1000">
                    <div class="relative">
                        <!-- Main Illustration -->
                        <div class="relative z-10 bg-white rounded-3xl shadow-card p-8 border border-gray-100">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="col-span-2 text-center mb-4">
                                    <div class="inline-flex items-center bg-primary-50 rounded-2xl px-6 py-3">
                                        <i class="fas fa-leaf text-primary-600 text-2xl mr-3"></i>
                                        <span class="text-lg font-bold text-primary-800">Total Dampak Sodakoh Pohon</span>
                                    </div>
                                </div>
                                
                                <div class="stat-card">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center">
                                            <i class="fas fa-tree text-primary-600 text-2xl"></i>
                                        </div>
                                        <span class="text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full">Terkumpul</span>
                                    </div>
                                    <div class="text-3xl font-extrabold text-gray-900 mb-1"><?php echo number_format($total_trees); ?></div>
                                    <div class="text-sm text-gray-500">Pohon terkumpul</div>
                                </div>
                                
                                <div class="stat-card">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="w-12 h-12 rounded-xl bg-earth-100 flex items-center justify-center">
                                            <i class="fas fa-seedling text-earth-600 text-2xl"></i>
                                        </div>
                                        <span class="text-xs font-semibold text-earth-600 bg-earth-50 px-3 py-1 rounded-full">Tertanam</span>
                                    </div>
                                    <div class="text-3xl font-extrabold text-gray-900 mb-1"><?php echo number_format($total_planted); ?></div>
                                    <div class="text-sm text-gray-500">Pohon tertanam</div>
                                </div>
                                
                                <div class="stat-card">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                                        </div>
                                    </div>
                                    <div class="text-3xl font-extrabold text-gray-900 mb-1"><?php echo number_format($total_donors); ?></div>
                                    <div class="text-sm text-gray-500">Donatur</div>
                                </div>
                                
                                <div class="stat-card">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                                            <i class="fas fa-map-marker-alt text-purple-600 text-2xl"></i>
                                        </div>
                                    </div>
                                    <div class="text-3xl font-extrabold text-gray-900 mb-1"><?php echo $total_locations; ?></div>
                                    <div class="text-sm text-gray-500">Lokasi tanam</div>
                                </div>
                            </div>
                            
                            <!-- Live Update Indicator -->
                            <div class="mt-6 flex items-center justify-center text-sm text-gray-500 border-t border-gray-100 pt-6">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                <span>Update real-time • Angka terus bertambah</span>
                            </div>
                        </div>
                        
                        <!-- Decorative Elements -->
                        <div class="absolute -top-6 -right-6 w-32 h-32 bg-primary-200 rounded-full blur-2xl opacity-60"></div>
                        <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-earth-200 rounded-full blur-2xl opacity-60"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section id="programs" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <span class="inline-block px-4 py-2 bg-primary-100 rounded-full text-primary-700 font-semibold text-sm mb-4">
                    <i class="fas fa-star mr-2"></i>Solusi Kami
                </span>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Program Penanaman Pohon untuk Semua
                </h2>
                <p class="text-xl text-gray-600">
                    Kami menyediakan berbagai program yang dapat disesuaikan dengan kebutuhan Anda:
                </p>
            </div>

            <!-- Programs Grid -->
            <div class="grid md:grid-cols-3 gap-8 mb-12">
                <!-- Corporatree Card -->
                <div class="group relative bg-white rounded-3xl overflow-hidden border-2 border-gray-100 hover:border-primary-300 transition duration-300" data-aos="fade-up" data-aos-delay="0">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-primary-50 opacity-0 group-hover:opacity-100 transition duration-300"></div>
                    <div class="relative p-8">
                        <!-- Icon -->
                        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                            <i class="fas fa-building text-blue-600 text-3xl"></i>
                        </div>
                        <!-- Title & Subtitle -->
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Corporation</h3>
                        <p class="text-primary-600 font-semibold mb-4 text-sm">Program CSR Perusahaan</p>
                        <!-- Description -->
                        <p class="text-gray-600 text-sm leading-relaxed mb-6">
                            Program CSR dengan penanaman pohon yang dapat disesuaikan dengan target dan misi perusahaan Anda.
                        </p>
                        <!-- Features -->
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start text-sm text-gray-700">
                                <i class="fas fa-check text-primary-600 mr-3 mt-0.5 flex-shrink-0"></i>
                                <span>Target penanaman pohon sesuai kebutuhan</span>
                            </li>
                            <li class="flex items-start text-sm text-gray-700">
                                <i class="fas fa-check text-primary-600 mr-3 mt-0.5 flex-shrink-0"></i>
                                <span>Dokumentasi & laporan dampak</span>
                            </li>
                            <li class="flex items-start text-sm text-gray-700">
                                <i class="fas fa-check text-primary-600 mr-3 mt-0.5 flex-shrink-0"></i>
                                <span>Kustomisasi lokasi penanaman</span>
                            </li>
                        </ul>
                        <!-- CTA Button -->
                        <a href="#" class="inline-flex items-center justify-center w-full px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:bg-primary-800 transition group/btn">
                            Pelajari Lebih Lanjut
                            <i class="fas fa-arrow-right ml-2 group-hover/btn:translate-x-1 transition"></i>
                        </a>
                    </div>
                </div>

                <!-- Collaboratree Card -->
                <div class="group relative bg-white rounded-3xl overflow-hidden border-2 border-gray-100 hover:border-primary-300 transition duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-primary-50 opacity-0 group-hover:opacity-100 transition duration-300"></div>
                    <div class="relative p-8">
                        <!-- Icon -->
                        <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                            <i class="fas fa-handshake text-purple-600 text-3xl"></i>
                        </div>
                        <!-- Title & Subtitle -->
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Collaboration</h3>
                        <p class="text-primary-600 font-semibold mb-4 text-sm">Bundling Produk/Jasa</p>
                        <!-- Description -->
                        <p class="text-gray-600 text-sm leading-relaxed mb-6">
                            Kolaborasikan brand Anda dengan program donasi pohon untuk membangun citra yang peduli dan berkelanjutan.
                        </p>
                        <!-- Features -->
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start text-sm text-gray-700">
                                <i class="fas fa-check text-primary-600 mr-3 mt-0.5 flex-shrink-0"></i>
                                <span>Integrasi dengan produk/layanan Anda</span>
                            </li>
                            <li class="flex items-start text-sm text-gray-700">
                                <i class="fas fa-check text-primary-600 mr-3 mt-0.5 flex-shrink-0"></i>
                                <span>Meningkatkan brand awareness</span>
                            </li>
                            <li class="flex items-start text-sm text-gray-700">
                                <i class="fas fa-check text-primary-600 mr-3 mt-0.5 flex-shrink-0"></i>
                                <span>Pelanggan berkontribusi untuk lingkungan</span>
                            </li>
                        </ul>
                        <!-- CTA Button -->
                        <a href="#" class="inline-flex items-center justify-center w-full px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:bg-purple-700 transition group/btn">
                            Pelajari Lebih Lanjut
                            <i class="fas fa-arrow-right ml-2 group-hover/btn:translate-x-1 transition"></i>
                        </a>
                    </div>
                </div>

                <!-- SustainabiliTree Card -->
                <div class="group relative bg-white rounded-3xl overflow-hidden border-2 border-gray-100 hover:border-primary-300 transition duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-primary-50 opacity-0 group-hover:opacity-100 transition duration-300"></div>
                    <div class="relative p-8">
                        <!-- Icon -->
                        <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                            <i class="fas fa-leaf text-green-600 text-3xl"></i>
                        </div>
                        <!-- Title & Subtitle -->
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">SustainabiliTy</h3>
                        <p class="text-primary-600 font-semibold mb-4 text-sm">Proyek Berkelanjutan</p>
                        <!-- Description -->
                        <p class="text-gray-600 text-sm leading-relaxed mb-6">
                            Konsultasi dan implementasi proyek penanaman pohon skala besar dengan strategi keberlanjutan jangka panjang.
                        </p>
                        <!-- Features -->
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start text-sm text-gray-700">
                                <i class="fas fa-check text-primary-600 mr-3 mt-0.5 flex-shrink-0"></i>
                                <span>Konsultasi mendalam dengan ahli</span>
                            </li>
                            <li class="flex items-start text-sm text-gray-700">
                                <i class="fas fa-check text-primary-600 mr-3 mt-0.5 flex-shrink-0"></i>
                                <span>Implementasi di berbagai lokasi</span>
                            </li>
                            <li class="flex items-start text-sm text-gray-700">
                                <i class="fas fa-check text-primary-600 mr-3 mt-0.5 flex-shrink-0"></i>
                                <span>Monitoring jangka panjang & laporan</span>
                            </li>
                        </ul>
                        <!-- CTA Button -->
                        <a href="#" class="inline-flex items-center justify-center w-full px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:bg-green-700 transition group/btn">
                            Pelajari Lebih Lanjut
                            <i class="fas fa-arrow-right ml-2 group-hover/btn:translate-x-1 transition"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-3xl p-12 text-center text-white" data-aos="fade-up">
                <h3 class="text-3xl font-bold mb-4">Tertarik dengan Salah Satu Program?</h3>
                <p class="text-white/90 mb-8 text-lg">Hubungi tim kami untuk mendiskusikan program yang paling sesuai untuk Anda</p>
                <a href="mailto:info@sodakohpohon.com" class="inline-flex items-center px-8 py-4 bg-white text-primary-700 font-bold rounded-2xl hover:bg-gray-100 transition">
                    <i class="fas fa-envelope mr-3"></i>
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    <!-- Campaigns Section -->
    <section id="campaigns" class="py-20 bg-gray-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-12" data-aos="fade-up">
                <span class="inline-block px-4 py-2 bg-primary-100 rounded-full text-primary-700 font-semibold text-sm mb-4">
                    <i class="fas fa-tree mr-2"></i>Campaign Aktif
                </span>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Pilih Program Penanaman
                </h2>
                <p class="text-xl text-gray-600">
                    Setiap pohon yang Anda sedekahkan akan ditanam dan dirawat hingga memberikan manfaat untuk lingkungan.
                </p>
            </div>

            <!-- Filter & Sort -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8" data-aos="fade-up">
                <div class="flex items-center gap-3 overflow-x-auto pb-2">
                    <button class="px-5 py-2 bg-primary-600 text-white rounded-full text-sm font-semibold hover:bg-primary-700 transition whitespace-nowrap">
                        Semua Campaign
                    </button>
                    <button class="px-5 py-2 bg-white text-gray-700 rounded-full text-sm font-medium hover:bg-primary-50 hover:text-primary-700 transition border border-gray-200 whitespace-nowrap">
                        Mangrove
                    </button>
                    <button class="px-5 py-2 bg-white text-gray-700 rounded-full text-sm font-medium hover:bg-primary-50 hover:text-primary-700 transition border border-gray-200 whitespace-nowrap">
                        Hutan Kota
                    </button>
                    <button class="px-5 py-2 bg-white text-gray-700 rounded-full text-sm font-medium hover:bg-primary-50 hover:text-primary-700 transition border border-gray-200 whitespace-nowrap">
                        Reboisasi
                    </button>
                    <button class="px-5 py-2 bg-white text-gray-700 rounded-full text-sm font-medium hover:bg-primary-50 hover:text-primary-700 transition border border-gray-200 whitespace-nowrap">
                        Hutan Pangan
                    </button>
                </div>
                
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Urutkan:</span>
                    <select class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-600">
                        <option>Paling Populer</option>
                        <option>Target Terbesar</option>
                        <option>Paling Dekat</option>
                        <option>Terbaru</option>
                    </select>
                </div>
            </div>

            <!-- Campaign Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php 
                if (count($campaigns) > 0):
                    foreach ($campaigns as $index => $campaign): 
                    $progress = ($campaign['target_trees'] > 0) ? round(($campaign['current_trees'] / $campaign['target_trees']) * 100, 1) : 0;
                ?>
                <div class="campaign-card group" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <!-- Image Container -->
                    <div class="relative h-56 overflow-hidden bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                        <img src="<?php echo htmlspecialchars($campaign['image']); ?>" 
                             alt="<?php echo htmlspecialchars($campaign['title']); ?>"
                             class="campaign-image w-full h-full object-cover"
                             loading="lazy">
                        
                        <!-- Overlay Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        
                        <!-- Badges -->
                        <div class="absolute top-4 left-4 flex gap-2">
                            <?php if ($index == 0): ?>
                            <span class="badge-new px-3 py-1 rounded-full text-white text-xs font-bold">
                                <i class="fas fa-star mr-1"></i>BARU
                            </span>
                            <?php endif; ?>
                            <?php if ($campaign['days_left'] <= 15 && $campaign['days_left'] > 0): ?>
                            <span class="badge-urgent px-3 py-1 rounded-full text-white text-xs font-bold">
                                <i class="fas fa-clock mr-1"></i>URGENT
                            </span>
                            <?php endif; ?>
                            <?php if ($campaign['donors'] > 200): ?>
                            <span class="badge-popular px-3 py-1 rounded-full text-white text-xs font-bold">
                                <i class="fas fa-fire mr-1"></i>POPULER
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Price Tag -->
                        <div class="absolute bottom-4 left-4">
                            <div class="bg-white/90 backdrop-blur-sm rounded-xl px-4 py-2">
                                <span class="text-xs text-gray-600 block">Mulai dari</span>
                                <span class="text-xl font-bold text-primary-700">Rp <?php echo number_format($campaign['price_per_tree']); ?></span>
                                <span class="text-xs text-gray-500">/pohon</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6">
                        <!-- Title & Location -->
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-primary-700 transition">
                                <?php echo htmlspecialchars($campaign['title']); ?>
                            </h3>
                            <div class="flex items-center text-gray-500 text-sm">
                                <i class="fas fa-map-marker-alt mr-2 text-primary-600"></i>
                                <?php echo htmlspecialchars($campaign['location']); ?>
                                <span class="mx-2">•</span>
                                <i class="fas fa-leaf mr-2 text-primary-600"></i>
                                <?php echo htmlspecialchars($campaign['tree_type']); ?>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            <?php echo htmlspecialchars($campaign['description']); ?>
                        </p>
                        
                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="font-semibold text-gray-900"><?php echo number_format($campaign['current_trees']); ?> pohon</span>
                                <span class="text-gray-500">Target <?php echo number_format($campaign['target_trees']); ?> pohon</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                            <div class="flex justify-between mt-2">
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-users mr-1"></i><?php echo number_format($campaign['donors']); ?> donatur
                                </span>
                                <span class="text-xs font-semibold text-primary-700">
                                    <?php echo $progress; ?>% terkumpul
                                </span>
                            </div>
                        </div>
                        
                        <!-- Footer Stats -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center text-sm">
                                <i class="far fa-clock mr-2 text-gray-400"></i>
                                <span class="text-gray-600">
                                    <?php 
                                    if($campaign['days_left'] > 0) {
                                        echo $campaign['days_left'] . ' hari lagi';
                                    } else {
                                        echo "Selesai";
                                    }
                                    ?>
                                </span>
                            </div>
                            <a href="campaign-detail.php?id=<?php echo $campaign['id']; ?>" 
                               class="inline-flex items-center text-primary-700 font-semibold hover:text-primary-800 transition group">
                                Lihat Detail
                                <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php 
                    endforeach; 
                else:
                ?>
                    <div class="col-span-3 text-center py-12">
                        <i class="fas fa-folder-open text-5xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Campaign</h3>
                        <p class="text-gray-500">Saat ini belum ada campaign yang tersedia di database.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- View All Button -->
            <div class="text-center mt-12" data-aos="fade-up">
                <a href="campaign.php" class="inline-flex items-center px-8 py-4 bg-white border-2 border-primary-600 text-primary-700 font-bold rounded-2xl hover:bg-primary-50 transition shadow-lg">
                    <i class="fas fa-tree mr-2"></i>
                    Lihat Semua Campaign
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <span class="inline-block px-4 py-2 bg-primary-100 rounded-full text-primary-700 font-semibold text-sm mb-4">
                    <i class="fas fa-leaf mr-2"></i>Cara Kerja
                </span>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Sedekah Pohon Semudah 1-2-3
                </h2>
                <p class="text-xl text-gray-600">
                    Tidak perlu ribet, cukup pilih jumlah pohon dan kami yang akan menanamnya.
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="text-center relative" data-aos="fade-up" data-aos-delay="100">
                    <div class="relative inline-block mb-6">
                        <div class="w-24 h-24 mx-auto bg-primary-100 rounded-3xl rotate-45 flex items-center justify-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-primary-600 to-primary-700 rounded-2xl rotate-45 flex items-center justify-center">
                                <i class="fas fa-hand-pointer text-white text-3xl -rotate-45"></i>
                            </div>
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                            1
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Pilih Campaign</h3>
                    <p class="text-gray-600">
                        Pilih program penanaman pohon yang ingin kamu dukung sesuai lokasi dan jenis pohon.
                    </p>
                </div>
                
                <!-- Step 2 -->
                <div class="text-center relative" data-aos="fade-up" data-aos-delay="200">
                    <div class="relative inline-block mb-6">
                        <div class="w-24 h-24 mx-auto bg-primary-100 rounded-3xl rotate-45 flex items-center justify-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-primary-600 to-primary-700 rounded-2xl rotate-45 flex items-center justify-center">
                                <i class="fas fa-tree text-white text-3xl -rotate-45"></i>
                            </div>
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                            2
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Tentukan Jumlah Pohon</h3>
                    <p class="text-gray-600">
                        Pilih berapa banyak pohon yang ingin disedekahkan. Semakin banyak, semakin besar manfaatnya.
                    </p>
                </div>
                
                <!-- Step 3 -->
                <div class="text-center relative" data-aos="fade-up" data-aos-delay="300">
                    <div class="relative inline-block mb-6">
                        <div class="w-24 h-24 mx-auto bg-primary-100 rounded-3xl rotate-45 flex items-center justify-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-primary-600 to-primary-700 rounded-2xl rotate-45 flex items-center justify-center">
                                <i class="fas fa-check-circle text-white text-3xl -rotate-45"></i>
                            </div>
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                            3
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Bayar & Pantau</h3>
                    <p class="text-gray-600">
                        Lakukan pembayaran dan pantau terus perkembangan penanaman pohonmu.
                    </p>
                </div>
            </div>
            
            <!-- Impact Statement -->
            <div class="mt-20 p-8 bg-gradient-to-r from-primary-600 to-primary-700 rounded-3xl text-white" data-aos="fade-up">
                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <div>
                        <h3 class="text-3xl font-bold mb-4">Setiap Pohon adalah Sedekah Jariyah</h3>
                        <p class="text-white/90 text-lg mb-6">
                            Rasulullah SAW bersabda: "Tidaklah seorang muslim menanam pohon atau menanam tanaman, 
                            lalu buahnya dimakan oleh burung, manusia, atau hewan, melainkan itu menjadi sedekah baginya." (HR. Bukhari)
                        </p>
                        <div class="flex items-center">
                            <i class="fas fa-quote-right text-4xl text-white/30"></i>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-0 bg-white/10 rounded-2xl transform rotate-3"></div>
                        <div class="relative bg-white/20 backdrop-blur-sm rounded-2xl p-6">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-white/30 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-tree text-3xl"></i>
                                </div>
                                <div>
                                    <div class="text-3xl font-bold"><?php echo number_format($total_trees); ?></div>
                                    <div class="text-white/90">Total pohon disedekahkan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Impact Stories Section -->
    <section class="py-20 bg-gray-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12" data-aos="fade-up">
                <span class="inline-block px-4 py-2 bg-primary-100 rounded-full text-primary-700 font-semibold text-sm mb-4">
                    <i class="fas fa-images mr-2"></i>Dokumentasi
                </span>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Dampak Nyata Sodakoh Pohon
                </h2>
                <p class="text-xl text-gray-600">
                    Lihat bagaimana ribuan pohon telah tumbuh dan memberikan manfaat.
                </p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4" data-aos="fade-up">
                <div class="relative group overflow-hidden rounded-2xl aspect-square">
                    <img src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                         alt="Mangrove" 
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition">
                        <div class="absolute bottom-4 left-4 text-white">
                            <p class="font-semibold">Mangrove Demak</p>
                            <p class="text-sm">1.450 pohon</p>
                        </div>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-2xl aspect-square">
                    <img src="https://images.unsplash.com/photo-1472214103451-9374bd1c798e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                         alt="Hutan" 
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition">
                        <div class="absolute bottom-4 left-4 text-white">
                            <p class="font-semibold">Lereng Merapi</p>
                            <p class="text-sm">2.300 pohon</p>
                        </div>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-2xl aspect-square">
                    <img src="https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                         alt="Hutan" 
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition">
                        <div class="absolute bottom-4 left-4 text-white">
                            <p class="font-semibold">Hutan Lombok</p>
                            <p class="text-sm">780 pohon</p>
                        </div>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-2xl aspect-square">
                    <img src="https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                         alt="Hutan Pangan" 
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition">
                        <div class="absolute bottom-4 left-4 text-white">
                            <p class="font-semibold">Hutan Pangan Kaltim</p>
                            <p class="text-sm">450 pohon</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-8">
                <a href="laporan.php" class="inline-flex items-center text-primary-700 font-semibold hover:text-primary-800 transition">
                    Lihat Seluruh Dokumentasi
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-aos="fade-up">
            <div class="bg-gradient-to-r from-primary-50 to-earth-50 rounded-3xl p-12 border border-primary-100">
                <i class="fas fa-leaf text-primary-600 text-4xl mb-4"></i>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Ikuti Perkembangan Gerakan Sodakoh Pohon
                </h2>
                <p class="text-gray-600 text-lg mb-8">
                    Dapatkan update terbaru tentang program penanaman dan dampak yang telah kita hasilkan bersama.
                </p>
                
                <form class="flex flex-col sm:flex-row gap-4 max-w-xl mx-auto">
                    <input type="email" 
                           placeholder="Masukkan email Anda" 
                           class="flex-1 px-6 py-4 border-2 border-gray-200 rounded-2xl focus:border-primary-600 focus:outline-none text-lg">
                    <button type="submit" 
                            class="px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-2xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg shadow-primary-600/25">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Berlangganan
                    </button>
                </form>
                
                <p class="text-sm text-gray-500 mt-4">
                    Kami tidak akan spam. Berhenti berlangganan kapan saja.
                </p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
                <!-- Brand Column -->
                <div class="lg:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl p-2.5">
                            <i class="fas fa-tree text-white text-2xl"></i>
                        </div>
                        <span class="text-2xl font-extrabold">
                            <span class="text-primary-500">Sodakoh</span>
                            <span class="text-white">Pohon</span>
                        </span>
                    </div>
                    <p class="text-gray-400 mb-6 text-lg max-w-md">
                        Platform sedekah pohon yang memudahkan siapa saja untuk berkontribusi dalam penghijauan dan pelestarian lingkungan.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center hover:bg-primary-600 transition">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center hover:bg-primary-600 transition">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center hover:bg-primary-600 transition">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center hover:bg-primary-600 transition">
                            <i class="fab fa-youtube text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Jelajahi</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Beranda</a></li>
                        <li><a href="#campaigns" class="text-gray-400 hover:text-white transition">Campaign</a></li>
                        <li><a href="laporan.php" class="text-gray-400 hover:text-white transition">Laporan Transparansi</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Dokumentasi</a></li>
                    </ul>
                </div>
                
                <!-- Program -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Program</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Mangrove</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Reboisasi</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Hutan Kota</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Hutan Pangan</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Kontak</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-primary-500"></i>
                            Jl. Sodakoh No. 123, Jakarta
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-3 text-primary-500"></i>
                            hello@sodakohpohon.id
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone mt-1 mr-3 text-primary-500"></i>
                            +62 21 1234 5678
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>© <?php echo date('Y'); ?> Sodakoh Pohon. Sedekah dalam Bentuk Pohon.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });
        
        // Smooth scroll untuk anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('bg-white/95', 'backdrop-blur-md', 'shadow-lg');
                nav.classList.remove('glass-effect');
            } else {
                nav.classList.remove('bg-white/95', 'backdrop-blur-md', 'shadow-lg');
                nav.classList.add('glass-effect');
            }
        });

        // Programs Dropdown Click Handler
        const programsBtn = document.getElementById('programsBtn');
        const programsMenu = document.getElementById('programsMenu');
        const programsIcon = document.getElementById('programsIcon');
        const programsDropdown = document.getElementById('programsDropdown');
        let isDropdownOpen = false;

        programsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            isDropdownOpen = !isDropdownOpen;
            
            if (isDropdownOpen) {
                programsMenu.classList.remove('opacity-0', 'invisible');
                programsMenu.classList.add('opacity-100', 'visible');
                programsIcon.classList.add('rotate-180');
            } else {
                programsMenu.classList.add('opacity-0', 'invisible');
                programsMenu.classList.remove('opacity-100', 'visible');
                programsIcon.classList.remove('rotate-180');
            }
        });

        // Close dropdown when clicking on menu items
        const programsLinks = programsMenu.querySelectorAll('a');
        programsLinks.forEach(link => {
            link.addEventListener('click', function() {
                isDropdownOpen = false;
                programsMenu.classList.add('opacity-0', 'invisible');
                programsMenu.classList.remove('opacity-100', 'visible');
                programsIcon.classList.remove('rotate-180');
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!programsDropdown.contains(e.target)) {
                isDropdownOpen = false;
                programsMenu.classList.add('opacity-0', 'invisible');
                programsMenu.classList.remove('opacity-100', 'visible');
                programsIcon.classList.remove('rotate-180');
            }
        });
    </script>
</body>
</html>