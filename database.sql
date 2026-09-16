-- ============================================================
-- DATABASE: smk_infokom
-- Website Profil SMK INFOKOM BOGOR
-- ============================================================

CREATE DATABASE IF NOT EXISTS smk_infokom DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smk_infokom;

-- ============================================================
-- TABEL: admins
-- ============================================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO admins (username, password, nama_lengkap, email)
VALUES (
    'admin',
    '$2y$12$S7pt1SNjZ.baG5i0YM1RX.4Gv/ou3h0AsjRZEYbeDSz5dDlJysEpC',
    'Administrator',
    'admin@smkinfokom.sch.id'
) ON DUPLICATE KEY UPDATE
    password = VALUES(password),
    nama_lengkap = VALUES(nama_lengkap),
    email = VALUES(email);

-- Password default akun admin: admin123

-- ============================================================
-- TABEL: school_profile
-- ============================================================
CREATE TABLE IF NOT EXISTS school_profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_sekolah VARCHAR(200) NOT NULL DEFAULT 'SMK INFOKOM BOGOR',
    tagline TEXT,
    deskripsi TEXT,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    jam_operasional VARCHAR(100),
    logo VARCHAR(255) DEFAULT 'assets/images/logo.png',
    tahun_didirikan YEAR,
    npsn VARCHAR(20),
    akreditasi VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO school_profile (nama_sekolah, tagline, deskripsi, alamat, telepon, email, jam_operasional, tahun_didirikan, logo) VALUES
('SMK INFOKOM BOGOR', 'Membangun generasi kompeten, berkarakter, kreatif, dan siap menghadapi dunia kerja serta perkembangan teknologi.', 'SMK INFOKOM BOGOR adalah sekolah menengah kejuruan yang berfokus pada penguatan keterampilan teknologi, inovasi, dan karakter siswa agar siap menghadapi dunia kerja dan pendidikan lanjutan.', 'Jl. Raya Bogor, Kota Bogor, Jawa Barat', '(0251) 123456', 'info@smkinfokom.sch.id', 'Senin - Jumat (07:00 - 16:00)', 2010, 'assets/images/logo.svg')
ON DUPLICATE KEY UPDATE
    nama_sekolah = VALUES(nama_sekolah),
    tagline = VALUES(tagline),
    deskripsi = VALUES(deskripsi),
    alamat = VALUES(alamat),
    telepon = VALUES(telepon),
    email = VALUES(email),
    jam_operasional = VALUES(jam_operasional),
    tahun_didirikan = VALUES(tahun_didirikan),
    logo = VALUES(logo);

CREATE TABLE IF NOT EXISTS principal_message (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kepsek VARCHAR(200) NOT NULL,
    jabatan VARCHAR(200) NOT NULL,
    sambutan TEXT NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO principal_message (nama_kepsek, jabatan, sambutan, foto, is_active)
VALUES (
    'Drs. H. Mulyadi, M.Pd.',
    'Kepala Sekolah',
    'Puji syukur kami panjatkan kehadirat Allah SWT atas terselenggaranya proses pendidikan di SMK INFOKOM BOGOR. Kami berkomitmen untuk menciptakan lulusan yang kompeten, berkarakter, dan siap bersaing di era teknologi.',
    NULL,
    1
) ON DUPLICATE KEY UPDATE
    nama_kepsek = VALUES(nama_kepsek),
    jabatan = VALUES(jabatan),
    sambutan = VALUES(sambutan),
    foto = VALUES(foto),
    is_active = VALUES(is_active);

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (setting_key, setting_value) VALUES
('site_title', 'SMK INFOKOM BOGOR'),
('site_description', 'Website Resmi SMK INFOKOM BOGOR'),
('ppdb_open', '1'),
('ppdb_tahun_ajaran', '2025/2026'),
('items_per_page', '10')
ON DUPLICATE KEY UPDATE
    setting_value = VALUES(setting_value);

-- ============================================================
-- TABEL: school_statistics
-- ============================================================
CREATE TABLE IF NOT EXISTS school_statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    value VARCHAR(20) NOT NULL,
    icon VARCHAR(50) DEFAULT 'users',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO school_statistics (label, value, icon, sort_order) VALUES
('Siswa Aktif', '0', 'users', 1),
('Guru & Staff', '0', 'user-tie', 2),
('Program Keahlian', '0', 'graduation-cap', 3),
('Prestasi', '0', 'trophy', 4),
('Alumni', '0', 'people-group', 5);

-- ============================================================
-- TABEL: banners
-- ============================================================
CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255) NOT NULL,
    link_url VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: news_categories
-- ============================================================
CREATE TABLE IF NOT EXISTS news_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO news_categories (nama_kategori, slug) VALUES
('Pengumuman', 'pengumuman'),
('Agenda', 'agenda'),
('Prestasi', 'prestasi'),
('Kegiatan', 'kegiatan'),
('Akademik', 'akademik');

-- ============================================================
-- TABEL: news
-- ============================================================
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT,
    judul VARCHAR(250) NOT NULL,
    slug VARCHAR(250) NOT NULL UNIQUE,
    ringkasan TEXT,
    isi TEXT NOT NULL,
    thumbnail VARCHAR(255),
    penulis VARCHAR(100) DEFAULT 'Admin',
    status ENUM('draft','publish') DEFAULT 'draft',
    views INT DEFAULT 0,
    tanggal_terbit DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES news_categories(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_tanggal (tanggal_terbit)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: programs (Program Keahlian / Jurusan)
-- ============================================================
CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_program VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    deskripsi TEXT,
    keunggulan TEXT,
    gambar VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: facilities
-- ============================================================
CREATE TABLE IF NOT EXISTS facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_fasilitas VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: gallery_categories
-- ============================================================
CREATE TABLE IF NOT EXISTS gallery_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO gallery_categories (nama_kategori, slug) VALUES
('Kegiatan Sekolah', 'kegiatan-sekolah'),
('Pembelajaran', 'pembelajaran'),
('Praktik', 'praktik'),
('Lomba', 'lomba'),
('Prestasi', 'prestasi'),
('Event', 'event'),
('Ekstrakurikuler', 'ekstrakurikuler');

-- ============================================================
-- TABEL: gallery
-- ============================================================
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT,
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES gallery_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: achievements
-- ============================================================
CREATE TABLE IF NOT EXISTS achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(250) NOT NULL,
    deskripsi TEXT,
    tingkat ENUM('Kabupaten/Kota','Provinsi','Nasional','Internasional') DEFAULT 'Kabupaten/Kota',
    tahun YEAR NOT NULL,
    gambar VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: agendas
-- ============================================================
CREATE TABLE IF NOT EXISTS agendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(250) NOT NULL,
    deskripsi TEXT,
    tanggal_mulai DATETIME NOT NULL,
    tanggal_selesai DATETIME,
    lokasi VARCHAR(200),
    gambar VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: announcements
-- ============================================================
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(250) NOT NULL,
    isi TEXT NOT NULL,
    tipe ENUM('info','warning','success','danger') DEFAULT 'info',
    tanggal_pengumuman DATE NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: ppdb_registrations
-- ============================================================
CREATE TABLE IF NOT EXISTS ppdb_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_pendaftaran VARCHAR(20) NOT NULL UNIQUE,
    nama_lengkap VARCHAR(200) NOT NULL,
    nik VARCHAR(16) NOT NULL,
    nisn VARCHAR(10),
    tempat_lahir VARCHAR(100),
    tanggal_lahir DATE,
    jenis_kelamin ENUM('Laki-laki','Perempuan') NOT NULL,
    agama VARCHAR(50),
    alamat TEXT,
    no_hp VARCHAR(20),
    email VARCHAR(100),
    nama_ortu VARCHAR(200),
    no_hp_ortu VARCHAR(20),
    asal_sekolah VARCHAR(200),
    pilihan_jurusan VARCHAR(200),
    tahun_ajaran VARCHAR(20) NOT NULL,
    status ENUM('Menunggu','Diverifikasi','Diterima','Ditolak') DEFAULT 'Menunggu',
    keterangan TEXT,
    tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nomor (nomor_pendaftaran),
    INDEX idx_status (status),
    INDEX idx_tahun (tahun_ajaran)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: ppdb_documents
-- ============================================================
CREATE TABLE IF NOT EXISTS ppdb_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    jenis_dokumen VARCHAR(100) NOT NULL COMMENT 'ijazah, skhun, akta, kk, foto, dll',
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registration_id) REFERENCES ppdb_registrations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: contact_messages
-- ============================================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(200) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subjek VARCHAR(250),
    pesan TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: social_media
-- ============================================================
CREATE TABLE IF NOT EXISTS social_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(50) NOT NULL UNIQUE COMMENT 'facebook, instagram, youtube, tiktok, twitter',
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(50),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO social_media (platform, url, icon, sort_order) VALUES
('Facebook', '#', 'facebook', 1),
('Instagram', '#', 'instagram', 2),
('YouTube', '#', 'youtube', 3),
('TikTok', '#', 'tiktok', 4)
ON DUPLICATE KEY UPDATE
    url = VALUES(url), icon = VALUES(icon), sort_order = VALUES(sort_order);

-- ============================================================
-- SEED DATA: Admin Default
-- Username: admin | Password: admin123
-- ============================================================
-- Hash dari "admin123" menggunakan password_hash PASSWORD_DEFAULT
INSERT INTO admins (username, password, nama_lengkap, email) VALUES
('admin', '$2y$12$S7pt1SNjZ.baG5i0YM1RX.4Gv/ou3h0AsjRZEYbeDSz5dDlJysEpC', 'Administrator', 'admin@smkinfokom.sch.id')
ON DUPLICATE KEY UPDATE
    password = VALUES(password),
    nama_lengkap = VALUES(nama_lengkap),
    email = VALUES(email);

-- ============================================================
-- SEED DATA: Banners (placeholder)
-- ============================================================
INSERT INTO banners (judul, deskripsi, gambar, sort_order) VALUES
('Selamat Datang di SMK INFOKOM BOGOR', 'Membangun generasi kompeten, berkarakter, kreatif, dan siap menghadapi dunia kerja serta perkembangan teknologi.', 'assets/images/banner1.svg', 1),
('PPDB Online Tahun Ajaran 2025/2026', 'Pendaftaran Peserta Didik Baru telah dibuka. Segera daftarkan diri Anda!', 'assets/images/banner2.svg', 2),
('Prestasi Gemilang Siswa SMK INFOKOM BOGOR', 'Meraih juara di berbagai kompetisi tingkat nasional dan internasional.', 'assets/images/banner3.svg', 3);

-- ============================================================
-- SEED DATA: Programs (placeholder - sesuaikan dengan jurusan asli)
-- ============================================================
INSERT INTO programs (nama_program, slug, deskripsi, keunggulan, sort_order) VALUES
('Teknik Komputer dan Jaringan', 'teknik-komputer-jaringan', '[ISI DESKRIPSI JURUSAN TKJ]', '[ISI KEUNGGULAN JURUSAN TKJ]', 1),
('Rekayasa Perangkat Lunak', 'rekayasa-perangkat-lunak', '[ISI DESKRIPSI JURUSAN RPL]', '[ISI KEUNGGULAN JURUSAN RPL]', 2),
('Multimedia', 'multimedia', '[ISI DESKRIPSI JURUSAN MM]', '[ISI KEUNGGULAN JURUSAN MM]', 3),
('Teknik Elektronika Industri', 'teknik-elektronika-industri', '[ISI DESKRIPSI JURUSAN TEI]', '[ISI KEUNGGULAN JURUSAN TEI]', 4);

-- ============================================================
-- SEED DATA: Facilities (placeholder)
-- ============================================================
INSERT INTO facilities (nama_fasilitas, deskripsi, sort_order) VALUES
('Laboratorium Komputer', '[ISI DESKRIPSI LAB KOMPUTER]', 1),
('Ruang Kelas', '[ISI DESKRIPSI RUANG KELAS]', 2),
('Perpustakaan', '[ISI DESKRIPSI PERPUSTAKAAN]', 3),
('Lapangan Olahraga', '[ISI DESKRIPSI LAPANGAN]', 4),
('Ruang Praktik', '[ISI DESKRIPSI RUANG PRAKTIK]', 5),
('Aula', '[ISI DESKRIPSI AULA]', 6),
('Ruang Guru', '[ISI DESKRIPSI RUANG GURU]', 7),
('Masjid/Musholla', '[ISI DESKRIPSI MASJID]', 8);

COMMIT;