-- database/schema.sql
-- Struktur database untuk Sodakoh Pohon

CREATE DATABASE IF NOT EXISTS sodakoh_pohon;
USE sodakoh_pohon;

-- =====================================================
-- TABLE: campaigns
-- Menyimpan data campaign penanaman pohon
-- =====================================================
CREATE TABLE IF NOT EXISTS campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    long_description TEXT,
    location VARCHAR(255) NOT NULL,
    tree_type VARCHAR(100) NOT NULL,
    price_per_tree DECIMAL(12,2) NOT NULL,
    target_trees INT NOT NULL,
    current_trees INT DEFAULT 0,
    planted_trees INT DEFAULT 0,
    image VARCHAR(255),
    status ENUM('active', 'pending', 'completed', 'cancelled') DEFAULT 'active',
    partner VARCHAR(255),
    created_at DATETIME,
    deadline DATE,
    updated_at DATETIME,
    INDEX idx_status (status),
    INDEX idx_location (location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: campaign_gallery
-- Menyimpan foto dokumentasi campaign
-- =====================================================
CREATE TABLE IF NOT EXISTS campaign_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    caption TEXT,
    created_at DATETIME,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: campaign_benefits
-- Menyimpan manfaat/keunggulan campaign
-- =====================================================
CREATE TABLE IF NOT EXISTS campaign_benefits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,
    benefit VARCHAR(255) NOT NULL,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: donations
-- Menyimpan data donasi
-- =====================================================
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donation_number VARCHAR(50) UNIQUE NOT NULL,
    donor_name VARCHAR(255) NOT NULL,
    donor_email VARCHAR(255),
    donor_phone VARCHAR(20),
    anonymous BOOLEAN DEFAULT FALSE,
    campaign_id INT NOT NULL,
    trees_count INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'paid', 'failed', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_proof VARCHAR(255),
    message TEXT,
    certificate_number VARCHAR(100) UNIQUE,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE RESTRICT,
    INDEX idx_status (status),
    INDEX idx_donation_number (donation_number),
    INDEX idx_email (donor_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: plantings
-- Menyimpan data realisasi penanaman
-- =====================================================
CREATE TABLE IF NOT EXISTS plantings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,
    trees_planted INT NOT NULL,
    planting_date DATE NOT NULL,
    location VARCHAR(255) NOT NULL,
    volunteers INT DEFAULT 0,
    coordinator VARCHAR(255),
    description TEXT,
    image VARCHAR(255),
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    INDEX idx_campaign (campaign_id),
    INDEX idx_date (planting_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: carts
-- Menyimpan keranjang belanja (opsional untuk user login)
-- =====================================================
CREATE TABLE IF NOT EXISTS carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    user_id INT DEFAULT NULL,
    cart_data JSON NOT NULL,
    subtotal DECIMAL(12,2) DEFAULT 0,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_session (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: users (untuk pengembangan ke depan)
-- Menyimpan data user/akun
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at DATETIME,
    updated_at DATETIME,
    last_login DATETIME,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: admin_logs
-- Log aktivitas admin untuk audit
-- =====================================================
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at DATETIME,
    INDEX idx_admin (admin_id),
    INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- INSERT SAMPLE DATA
-- =====================================================

-- Sample campaigns
INSERT INTO campaigns (title, description, location, tree_type, price_per_tree, target_trees, current_trees, planted_trees, image, status, partner, deadline, created_at) VALUES
('Restorasi Mangrove Demak', 'Kawasan pesisir Demak mengalami abrasi yang cukup parah. Program ini bertujuan membangun sabuk hijau mangrove.', 'Demak, Jawa Tengah', 'Mangrove Rhizophora', 10000, 5000, 1450, 890, 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09', 'active', 'Kelompok Tani Hutan Mangrove', '2026-03-30', NOW()),
('Reboisasi Lereng Merapi', 'Menyelamatkan hutan di lereng Merapi dari ancaman longsor dengan penanaman pohon keras.', 'Magelang, Jawa Tengah', 'Sengon & Mahoni', 12000, 4000, 2300, 1650, 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e', 'active', 'Komunitas Pecinta Alam', '2026-03-15', NOW()),
('Penghijauan Hutan Lombok', 'Memulihkan ekosistem hutan yang rusak akibat kebakaran di Lombok.', 'Lombok, NTB', 'Mahoni', 15000, 3000, 780, 450, 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e', 'active', 'Green Lombok Foundation', '2026-04-20', NOW()),
('Hutan Pangan Kalimantan', 'Membangun hutan pangan untuk ketahanan pangan masyarakat sekitar hutan.', 'Kutai, Kaltim', 'Durian & Petai', 25000, 2000, 450, 120, 'https://images.unsplash.com/photo-1518531933037-91b2f5f229cc', 'active', 'Komunitas Adat Dayak', '2026-05-10', NOW()),
('Konservasi Hutan Papua', 'Melindungi keanekaragaman hayati Papua melalui konservasi hutan adat.', 'Jayapura, Papua', 'Merbau', 30000, 1500, 320, 100, 'https://images.unsplash.com/photo-1425913397330-cf8af2ff40a1', 'pending', 'Lembaga Adat Papua', '2026-06-30', NOW()),
('Mangrove Pesisir Jakarta', 'Mencegah banjir rob dan abrasi di pesisir Jakarta Utara.', 'Jakarta Utara', 'Mangrove', 10000, 3500, 1250, 840, 'https://images.unsplash.com/photo-1621451498295-af1ea68616ee', 'active', 'Forum Komunitas Hijau', '2026-03-25', NOW());

-- Sample campaign benefits
INSERT INTO campaign_benefits (campaign_id, benefit) VALUES
(1, 'Melindungi garis pantai dari abrasi'),
(1, 'Menciptakan habitat baru bagi biota laut'),
(1, 'Menyerap karbon hingga 4x lebih banyak'),
(1, 'Memberdayakan masyarakat lokal'),
(2, 'Mencegah tanah longsor'),
(2, 'Menjaga sumber mata air'),
(2, 'Habitat satwa liar'),
(2, 'Ekowisata'),
(3, 'Restorasi lahan kritis'),
(3, 'Mengurangi risiko banjir'),
(3, 'Kesuburan tanah'),
(4, 'Ketahanan pangan masyarakat'),
(4, 'Sumber penghasilan tambahan'),
(4, 'Konservasi plasma nutfah'),
(6, 'Pengendalian banjir rob'),
(6, 'Pariwisata edukasi'),
(6, 'Habitat burung migran');

-- Sample campaign gallery
INSERT INTO campaign_gallery (campaign_id, image_url, caption, created_at) VALUES
(1, 'https://images.unsplash.com/photo-1621451498295-af1ea68616ee', 'Penanaman mangrove tahap 1', NOW()),
(1, 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09', 'Bibit mangrove siap tanam', NOW()),
(1, 'https://images.unsplash.com/photo-1624535168245-0f9d5d773c2e', 'Relawan menanam mangrove', NOW()),
(2, 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e', 'Penanaman di lereng Merapi', NOW()),
(6, 'https://images.unsplash.com/photo-1621451498295-af1ea68616ee', 'Edukasi mangrove di Jakarta', NOW());

-- Sample users (admin)
INSERT INTO users (name, email, password, role, created_at) VALUES
('Admin Sodakoh', 'admin@sodakohpohon.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW());

-- Password untuk admin: password