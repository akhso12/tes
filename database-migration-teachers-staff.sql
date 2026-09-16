-- ============================================================
-- MIGRATION: Add Teachers & Staff Tables
-- SMK INFOKOM BOGOR
-- ============================================================

USE smk_infokom;

-- ============================================================
-- TABEL: teachers (Guru)
-- ============================================================
CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(200) NOT NULL,
    nip VARCHAR(20) UNIQUE,
    mata_pelajaran VARCHAR(200),
    keahlian TEXT,
    pendidikan_terakhir VARCHAR(100),
    universitas VARCHAR(200),
    spesialisasi VARCHAR(200),
    foto VARCHAR(255),
    no_hp VARCHAR(20),
    email VARCHAR(100),
    alamat TEXT,
    jenis_kelamin ENUM('Laki-laki','Perempuan'),
    status_aktif TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status_aktif),
    INDEX idx_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: staff (Staf/Pegawai)
-- ============================================================
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(200) NOT NULL,
    nip VARCHAR(20) UNIQUE,
    posisi VARCHAR(200) NOT NULL,
    departemen VARCHAR(200),
    tanggung_jawab TEXT,
    pendidikan_terakhir VARCHAR(100),
    universitas VARCHAR(200),
    foto VARCHAR(255),
    no_hp VARCHAR(20),
    email VARCHAR(100),
    alamat TEXT,
    jenis_kelamin ENUM('Laki-laki','Perempuan'),
    status_aktif TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status_aktif),
    INDEX idx_sort (sort_order),
    INDEX idx_posisi (posisi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;