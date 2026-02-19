<?php
// admin/includes/sidebar.php
// Set $current_page sebelum include untuk menentukan menu aktif
// Contoh: $current_page = 'dashboard';
?>
<!-- Sidebar -->
<aside class="w-72 bg-white shadow-xl flex flex-col fixed h-full overflow-y-auto">
    <!-- Logo -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl p-2.5">
                <i class="fas fa-tree text-white text-2xl"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-xl font-extrabold">
                    <span class="text-primary-700">Sodakoh</span>
                    <span class="text-gray-800">Pohon</span>
                </span>
                <span class="text-xs text-gray-500">Administrator Panel</span>
            </div>
        </div>
    </div>

    <!-- Admin Profile -->
    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-white">
        <div class="flex items-center">
            <div class="relative">
                <img src="https://ui-avatars.com/api/?name=Admin+Sodakoh&background=059669&color=fff&size=64"
                    alt="Admin" class="w-12 h-12 rounded-xl border-2 border-white shadow-lg">
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
            </div>
            <div class="ml-4">
                <p class="font-semibold text-gray-900">Admin Sodakoh</p>
                <p class="text-xs text-gray-500">admin@sodakohpohon.id</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4">
        <div class="mb-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2">Main Menu</p>
            <ul class="space-y-1">
                <li>
                    <a href="index.php"
                        class="sidebar-link <?php echo ($current_page == 'dashboard') ? 'active flex items-center px-4 py-3 rounded-xl text-white bg-primary-600' : 'flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50'; ?>">
                        <i
                            class="fas fa-dashboard w-6 <?php echo ($current_page != 'dashboard') ? 'text-gray-500' : ''; ?>"></i>
                        <span class="ml-3 font-medium">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="campaign.php"
                        class="sidebar-link <?php echo ($current_page == 'campaign') ? 'active flex items-center px-4 py-3 rounded-xl text-white bg-primary-600' : 'flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50'; ?>">
                        <i
                            class="fas fa-tree w-6 <?php echo ($current_page != 'campaign') ? 'text-gray-500' : ''; ?>"></i>
                        <span class="ml-3 font-medium">Campaign</span>
                    </a>
                </li>
                <li>
                    <a href="donations.php"
                        class="sidebar-link <?php echo ($current_page == 'donations') ? 'active flex items-center px-4 py-3 rounded-xl text-white bg-primary-600' : 'flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50'; ?>">
                        <i
                            class="fas fa-hand-holding-heart w-6 <?php echo ($current_page != 'donations') ? 'text-gray-500' : ''; ?>"></i>
                        <span class="ml-3 font-medium">Donasi</span>
                    </a>
                </li>
                <li>
                    <a href="planted.php"
                        class="sidebar-link <?php echo ($current_page == 'planted') ? 'active flex items-center px-4 py-3 rounded-xl text-white bg-primary-600' : 'flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50'; ?>">
                        <i
                            class="fas fa-seedling w-6 <?php echo ($current_page != 'planted') ? 'text-gray-500' : ''; ?>"></i>
                        <span class="ml-3 font-medium">Penanaman</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="mb-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2">Laporan</p>
            <ul class="space-y-1">
                <li>
                    <a href="#"
                        class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50">
                        <i class="fas fa-chart-line w-6 text-gray-500"></i>
                        <span class="ml-3 font-medium">Statistik</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50">
                        <i class="fas fa-file-export w-6 text-gray-500"></i>
                        <span class="ml-3 font-medium">Ekspor Data</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="mb-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2">Pengaturan</p>
            <ul class="space-y-1">
                <li>
                    <a href="#"
                        class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50">
                        <i class="fas fa-user-cog w-6 text-gray-500"></i>
                        <span class="ml-3 font-medium">Profil</span>
                    </a>
                </li>
                <li>
                    <a href="../index.php"
                        class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50">
                        <i class="fas fa-sign-out-alt w-6 text-gray-500"></i>
                        <span class="ml-3 font-medium">Keluar</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Version Info -->
    <div class="p-4 border-t border-gray-200">
        <p class="text-xs text-gray-500 text-center">
            Sodakoh Pohon v1.0.0<br>
            &copy; 2026 All rights reserved
        </p>
    </div>
</aside>