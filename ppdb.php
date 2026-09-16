<?php
/**
 * PPDB Online - Formulir Pendaftaran
 */
$page_title = 'PPDB Online | SMK INFOKOM BOGOR';
$meta_desc = 'Pendaftaran Peserta Didik Baru SMK INFOKOM BOGOR Tahun Ajaran ' . get_setting('ppdb_tahun_ajaran');
$body_class = 'page-ppdb';

require_once __DIR__ . '/includes/functions.php';

$errors = [];
$success_data = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token keamanan tidak valid. Silakan coba lagi.');
        redirect(BASE_URL . 'ppdb.php');
    }

    // Validate
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $nik = trim($_POST['nik'] ?? '');
    $nisn = trim($_POST['nisn'] ?? '');
    $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $agama = trim($_POST['agama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nama_ortu = trim($_POST['nama_ortu'] ?? '');
    $no_hp_ortu = trim($_POST['no_hp_ortu'] ?? '');
    $asal_sekolah = trim($_POST['asal_sekolah'] ?? '');
    $pilihan_jurusan = trim($_POST['pilihan_jurusan'] ?? '');
    $tahun_ajaran = get_setting('ppdb_tahun_ajaran') ?: '2025/2026';

    if (empty($nama)) $errors[] = 'Nama lengkap wajib diisi';
    if (empty($nik) || strlen($nik) !== 16) $errors[] = 'NIK harus 16 digit';
    if (empty($jenis_kelamin)) $errors[] = 'Jenis kelamin wajib dipilih';
    if (empty($alamat)) $errors[] = 'Alamat wajib diisi';
    if (empty($no_hp)) $errors[] = 'Nomor HP wajib diisi';
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid';
    if (empty($nama_ortu)) $errors[] = 'Nama orang tua/wali wajib diisi';
    if (empty($asal_sekolah)) $errors[] = 'Asal sekolah wajib diisi';
    if (empty($pilihan_jurusan)) $errors[] = 'Pilihan jurusan wajib dipilih';

    if (empty($errors)) {
        try {
            $nomor_pendaftaran = generate_ppdb_number($tahun_ajaran);

            $id = db_insert('ppdb_registrations', [
                'nomor_pendaftaran' => $nomor_pendaftaran,
                'nama_lengkap'      => $nama,
                'nik'               => $nik,
                'nisn'              => $nisn,
                'tempat_lahir'      => $tempat_lahir,
                'tanggal_lahir'     => $tanggal_lahir ?: null,
                'jenis_kelamin'     => $jenis_kelamin,
                'agama'             => $agama,
                'alamat'            => $alamat,
                'no_hp'             => $no_hp,
                'email'             => $email,
                'nama_ortu'         => $nama_ortu,
                'no_hp_ortu'        => $no_hp_ortu,
                'asal_sekolah'      => $asal_sekolah,
                'pilihan_jurusan'   => $pilihan_jurusan,
                'tahun_ajaran'      => $tahun_ajaran,
                'status'            => 'Menunggu',
            ]);

            $success_data = [
                'nomor' => $nomor_pendaftaran,
                'nama'  => $nama,
                'jurusan' => $pilihan_jurusan,
            ];

            set_flash('success', 'Pendaftaran berhasil! Nomor pendaftaran Anda: ' . $nomor_pendaftaran);
        } catch (Exception $e) {
            $errors[] = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
            error_log("PPDB Error: " . $e->getMessage());
        }
    }
}

$programs = db_fetch_all("SELECT * FROM programs WHERE is_active = 1 ORDER BY sort_order ASC");

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main id="main-content">
    <div class="breadcrumb-bar">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Beranda</a>
                <span class="separator">/</span>
                <span class="current">PPDB Online</span>
            </nav>
        </div>
    </div>

    <?php if ($success_data): ?>
    <!-- Success State -->
    <section class="ppdb-success">
        <div class="container">
            <div class="success-box">
                <div class="success-icon"><i class="fas fa-check-circle"></i></div>
                <h2>Pendaftaran Berhasil!</h2>
                <p>Data pendaftaran Anda telah berhasil disimpan.</p>
                <div class="success-details">
                    <div class="detail-row">
                        <span class="detail-label">Nomor Pendaftaran:</span>
                        <span class="detail-value highlight"><?= e($success_data['nomor']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Nama:</span>
                        <span class="detail-value"><?= e($success_data['nama']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Jurusan:</span>
                        <span class="detail-value"><?= e($success_data['jurusan']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value status-menunggu">Menunggu Verifikasi</span>
                    </div>
                </div>
                <p class="success-note">Simpan nomor pendaftaran Anda untuk mengecek status selanjutnya.</p>
                <div class="success-actions">
                    <a href="<?= BASE_URL ?>cek-status.php" class="btn btn-primary">Cek Status Pendaftaran</a>
                    <a href="<?= BASE_URL ?>" class="btn btn-outline">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </section>
    <?php else: ?>
    <!-- PPDB Hero -->
    <section class="ppdb-hero">
        <div class="container">
            <h1>Penerimaan Peserta Didik Baru</h1>
            <p>Tahun Ajaran <?= e(get_setting('ppdb_tahun_ajaran')) ?></p>
        </div>
    </section>

    <section class="ppdb-form-section">
        <div class="container">
            <div class="ppdb-layout">
                <!-- Info Sidebar -->
                <aside class="ppdb-info">
                    <div class="ppdb-info-card">
                        <h3><i class="fas fa-info-circle"></i> Informasi PPDB</h3>
                        <ul class="ppdb-info-list">
                            <li><i class="fas fa-check"></i> Pendaftaran gratis / tanpa biaya</li>
                            <li><i class="fas fa-check"></i> Isi formulir dengan data yang benar</li>
                            <li><i class="fas fa-check"></i> Simpan nomor pendaftaran</li>
                            <li><i class="fas fa-check"></i> Cek status secara berkala</li>
                        </ul>
                    </div>

                    <div class="ppdb-info-card">
                        <h3><i class="fas fa-file-alt"></i> Persyaratan</h3>
                        <ul class="ppdb-info-list">
                            <li><i class="fas fa-circle"></i> Lulusan SMP/MTs sederajat</li>
                            <li><i class="fas fa-circle"></i> Fotocopy Ijazah/SKHUN</li>
                            <li><i class="fas fa-circle"></i> Fotocopy Akta Kelahiran</li>
                            <li><i class="fas fa-circle"></i> Fotocopy Kartu Keluarga</li>
                            <li><i class="fas fa-circle"></i> Pas foto 3x4 (2 lembar)</li>
                        </ul>
                    </div>

                    <div class="ppdb-info-card">
                        <h3><i class="fas fa-route"></i> Alur Pendaftaran</h3>
                        <ol class="ppdb-alur">
                            <li>Isi formulir online</li>
                            <li>Dapatkan nomor pendaftaran</li>
                            <li>Verifikasi data oleh admin</li>
                            <li>Cek status pendaftaran</li>
                            <li>Pengumuman hasil seleksi</li>
                        </ol>
                    </div>
                </aside>

                <!-- Form -->
                <div class="ppdb-form-wrapper">
                    <?= flash_html() ?>

                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <strong>️ Terdapat kesalahan:</strong>
                        <ul style="margin-top:8px;padding-left:20px;">
                            <?php foreach ($errors as $err): ?>
                            <li><?= e($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="ppdb-form" data-validate>
                        <?= csrf_field() ?>

                        <h2 class="form-section-title">Data Pribadi</h2>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="nama_lengkap">Nama Lengkap <span class="required">*</span></label>
                                <input type="text" id="nama_lengkap" name="nama_lengkap" required value="<?= e($_POST['nama_lengkap'] ?? '') ?>" placeholder="Masukkan nama lengkap">
                            </div>
                            <div class="form-group">
                                <label for="nik">NIK <span class="required">*</span></label>
                                <input type="text" id="nik" name="nik" maxlength="16" required value="<?= e($_POST['nik'] ?? '') ?>" placeholder="16 digit NIK">
                            </div>
                            <div class="form-group">
                                <label for="nisn">NISN</label>
                                <input type="text" id="nisn" name="nisn" maxlength="10" value="<?= e($_POST['nisn'] ?? '') ?>" placeholder="Nomor NISN">
                            </div>
                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input type="text" id="tempat_lahir" name="tempat_lahir" value="<?= e($_POST['tempat_lahir'] ?? '') ?>" placeholder="Kota/Kabupaten">
                            </div>
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= e($_POST['tanggal_lahir'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin <span class="required">*</span></label>
                                <select id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki" <?= (($_POST['jenis_kelamin'] ?? '') === 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= (($_POST['jenis_kelamin'] ?? '') === 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="agama">Agama</label>
                                <select id="agama" name="agama">
                                    <option value="">-- Pilih --</option>
                                    <option value="Islam" <?= (($_POST['agama'] ?? '') === 'Islam') ? 'selected' : '' ?>>Islam</option>
                                    <option value="Kristen" <?= (($_POST['agama'] ?? '') === 'Kristen') ? 'selected' : '' ?>>Kristen</option>
                                    <option value="Katolik" <?= (($_POST['agama'] ?? '') === 'Katolik') ? 'selected' : '' ?>>Katolik</option>
                                    <option value="Hindu" <?= (($_POST['agama'] ?? '') === 'Hindu') ? 'selected' : '' ?>>Hindu</option>
                                    <option value="Buddha" <?= (($_POST['agama'] ?? '') === 'Buddha') ? 'selected' : '' ?>>Buddha</option>
                                    <option value="Konghucu" <?= (($_POST['agama'] ?? '') === 'Konghucu') ? 'selected' : '' ?>>Konghucu</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="alamat">Alamat Lengkap <span class="required">*</span></label>
                            <textarea id="alamat" name="alamat" rows="3" required placeholder="RT/RW, Kelurahan, Kecamatan, Kota"><?= e($_POST['alamat'] ?? '') ?></textarea>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="no_hp">Nomor HP <span class="required">*</span></label>
                                <input type="tel" id="no_hp" name="no_hp" required value="<?= e($_POST['no_hp'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" placeholder="email@contoh.com">
                            </div>
                        </div>

                        <h2 class="form-section-title">Data Orang Tua/Wali</h2>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="nama_ortu">Nama Orang Tua/Wali <span class="required">*</span></label>
                                <input type="text" id="nama_ortu" name="nama_ortu" required value="<?= e($_POST['nama_ortu'] ?? '') ?>" placeholder="Nama lengkap orang tua/wali">
                            </div>
                            <div class="form-group">
                                <label for="no_hp_ortu">No. HP Orang Tua/Wali</label>
                                <input type="tel" id="no_hp_ortu" name="no_hp_ortu" value="<?= e($_POST['no_hp_ortu'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>

                        <h2 class="form-section-title">Data Pendidikan</h2>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="asal_sekolah">Asal Sekolah <span class="required">*</span></label>
                                <input type="text" id="asal_sekolah" name="asal_sekolah" required value="<?= e($_POST['asal_sekolah'] ?? '') ?>" placeholder="Nama SMP/MTs asal">
                            </div>
                            <div class="form-group">
                                <label for="pilihan_jurusan">Pilihan Jurusan <span class="required">*</span></label>
                                <select id="pilihan_jurusan" name="pilihan_jurusan" required>
                                    <option value="">-- Pilih Jurusan --</option>
                                    <?php foreach ($programs as $prog): ?>
                                    <option value="<?= e($prog['nama_program']) ?>" <?= (($_POST['pilihan_jurusan'] ?? '') === $prog['nama_program']) ? 'selected' : '' ?>><?= e($prog['nama_program']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-submit">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Kirim Pendaftaran
                            </button>
                            <p class="form-note">Dengan mengirim formulir ini, Anda menyetujui bahwa data yang diisi adalah benar.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<style>
.ppdb-success { padding: 80px 0; text-align: center; }
.success-box {
    max-width: 600px;
    margin: 0 auto;
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 50px 40px;
    box-shadow: var(--shadow-xl);
    border: 2px solid #10b981;
}
.success-icon { font-size: 4rem; color: #10b981; margin-bottom: 20px; }
.success-box h2 { font-size: 1.8rem; margin-bottom: 10px; }
.success-box > p { color: var(--gray-500); margin-bottom: 30px; }
.success-details { text-align: left; background: var(--gray-50); border-radius: var(--radius-md); padding: 24px; margin-bottom: 24px; }
.detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--gray-200); }
.detail-row:last-child { border-bottom: none; }
.detail-label { font-weight: 600; color: var(--gray-600); }
.detail-value { font-weight: 500; }
.detail-value.highlight { color: var(--primary); font-size: 1.1rem; font-weight: 700; }
.status-menunggu { color: #d97706; font-weight: 600; }
.success-note { font-size: 0.9rem; color: var(--gray-500); margin-bottom: 24px; }
.success-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

.ppdb-hero {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    padding: 60px 0;
    text-align: center;
    color: var(--white);
}
.ppdb-hero h1 { font-size: 2.4rem; margin-bottom: 10px; }
.ppdb-hero p { font-size: 1.2rem; color: var(--secondary-light); }

.ppdb-form-section { padding: var(--section-padding); }
.ppdb-layout { display: grid; grid-template-columns: 300px 1fr; gap: 40px; }

.ppdb-info {}
.ppdb-info-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    padding: 24px;
    margin-bottom: 20px;
}
.ppdb-info-card h3 { font-size: 1rem; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
.ppdb-info-card h3 i { color: var(--primary); }
.ppdb-info-list li { display: flex; align-items: flex-start; gap: 8px; margin-bottom: 8px; font-size: 0.9rem; color: var(--gray-600); }
.ppdb-info-list li i { color: var(--secondary); margin-top: 4px; font-size: 0.7rem; }
.ppdb-alur { padding-left: 20px; list-style: decimal; }
.ppdb-alur li { margin-bottom: 8px; font-size: 0.9rem; color: var(--gray-600); }

.ppdb-form-wrapper {
    background: var(--white);
    border-radius: var(--radius-md);
    padding: 36px;
    border: 1px solid var(--gray-200);
}
.form-section-title {
    font-size: 1.2rem;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--gray-200);
    color: var(--primary);
}
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.form-group { margin-bottom: 4px; }
.form-group.full-width { grid-column: 1 / -1; }
.form-group label { display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; color: var(--gray-700); }
.required { color: #dc2626; }
.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-size: 0.95rem;
    transition: var(--transition-fast);
    background: var(--white);
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(26,58,92,0.1);
}
.form-group input.field-error,
.form-group select.field-error,
.form-group textarea.field-error {
    border-color: #dc2626;
    background: #fef2f2;
}
.form-group textarea { resize: vertical; min-height: 80px; }

.form-submit { margin-top: 30px; text-align: center; }
.form-note { font-size: 0.82rem; color: var(--gray-400); margin-top: 12px; }

@media (max-width: 900px) {
    .ppdb-layout { grid-template-columns: 1fr; }
    .ppdb-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; }
    .form-grid { grid-template-columns: 1fr; }
    .ppdb-form-wrapper { padding: 24px; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>