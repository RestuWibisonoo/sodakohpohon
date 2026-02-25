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
