<?php
/**
 * Cek Status PPDB
 */
$page_title = 'Cek Status PPDB | SMK INFOKOM BOGOR';
$meta_desc = 'Cek status pendaftaran PPDB SMK INFOKOM BOGOR menggunakan nomor pendaftaran.';
$body_class = 'page-cek-status';

require_once __DIR__ . '/includes/functions.php';

$result = null;
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error_msg = 'Token keamanan tidak valid.';
    } else {
        $nomor = trim($_POST['nomor_pendaftaran'] ?? '');
        if (empty($nomor)) {
            $error_msg = 'Masukkan nomor pendaftaran Anda.';
        } else {
            $result = db_fetch(
                "SELECT * FROM ppdb_registrations WHERE nomor_pendaftaran = :nomor",
                [':nomor' => $nomor]
            );
            if (!$result) {
                $error_msg = 'Nomor pendaftaran tidak ditemukan. Periksa kembali nomor Anda.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main id="main-content">
    <div class="breadcrumb-bar">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Beranda</a>
                <span class="separator">/</span>
                <span class="current">Cek Status PPDB</span>
            </nav>
        </div>
    </div>

    <section class="cek-status-section">
        <div class="container">
            <div class="section-title">
                <h2>Cek Status Pendaftaran</h2>
                <p>Masukkan nomor pendaftaran untuk melihat status PPDB Anda</p>
            </div>

            <div class="cek-status-box">
                <form method="POST" action="" class="cek-status-form">
                    <?= csrf_field() ?>
                    <div class="search-input-group">
                        <input type="text" name="nomor_pendaftaran" placeholder="Contoh: PPDB-20252026-0001" value="<?= e($_POST['nomor_pendaftaran'] ?? '') ?>" required>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                    </div>
                </form>

                <?php if ($error_msg): ?>
                <div class="alert alert-error" style="margin-top:20px;">
                    <i class="fas fa-exclamation-circle"></i> <?= e($error_msg) ?>
                </div>
                <?php endif; ?>

                <?php if ($result): ?>
                <div class="status-result">
                    <div class="status-header">
                        <div class="status-badge status-<?= strtolower($result['status']) ?>">
                            <?= e($result['status']) ?>
                        </div>
                        <span class="status-nomor">No: <?= e($result['nomor_pendaftaran']) ?></span>
                    </div>

                    <div class="status-details">
                        <div class="status-row">
                            <span class="status-label">Nama Lengkap</span>
                            <span class="status-value"><?= e($result['nama_lengkap']) ?></span>
                        </div>
                        <div class="status-row">
                            <span class="status-label">NIK</span>
                            <span class="status-value"><?= e(substr($result['nik'], 0, 6) . '**********') ?></span>
                        </div>
                        <div class="status-row">
                            <span class="status-label">Pilihan Jurusan</span>
                            <span class="status-value"><?= e($result['pilihan_jurusan']) ?></span>
                        </div>
                        <div class="status-row">
                            <span class="status-label">Tahun Ajaran</span>
                            <span class="status-value"><?= e($result['tahun_ajaran']) ?></span>
                        </div>
                        <div class="status-row">
                            <span class="status-label">Tanggal Daftar</span>
                            <span class="status-value"><?= format_datetime_indo($result['tanggal_daftar']) ?></span>
                        </div>
                        <?php if ($result['keterangan']): ?>
                        <div class="status-row">
                            <span class="status-label">Keterangan</span>
                            <span class="status-value"><?= e($result['keterangan']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="back-link">
                <a href="<?= BASE_URL ?>ppdb.php"><i class="fas fa-arrow-left"></i> Kembali ke Halaman PPDB</a>
            </div>
        </div>
    </section>
</main>

<style>
.cek-status-section { padding: var(--section-padding); }
.cek-status-box {
    max-width: 700px;
    margin: 0 auto;
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 40px;
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow-md);
}
.search-input-group { display: flex; gap: 12px; }
.search-input-group input {
    flex: 1;
    padding: 14px 20px;
    border: 2px solid var(--gray-300);
    border-radius: var(--radius-sm);
    font-size: 1rem;
    font-family: var(--font-body);
}
.search-input-group input:focus { outline: none; border-color: var(--primary); }

.status-result { margin-top: 30px; animation: slideDown 0.4s ease; }
.status-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid var(--gray-200); }
.status-badge {
    padding: 8px 24px;
    border-radius: 30px;
    font-weight: 700;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.status-menunggu { background: #fef3c7; color: #92400e; }
.status-diverifikasi { background: #dbeafe; color: #1e40af; }
.status-diterima { background: #d1fae5; color: #065f46; }
.status-ditolak { background: #fee2e2; color: #991b1b; }
.status-nomor { font-weight: 700; color: var(--primary); font-size: 1.1rem; }

.status-details {}
.status-row { display: flex; justify-content: space-between; padding: 14px 0; border-bottom: 1px solid var(--gray-100); }
.status-row:last-child { border-bottom: none; }
.status-label { font-weight: 600; color: var(--gray-500); font-size: 0.9rem; }
.status-value { font-weight: 500; color: var(--gray-800); text-align: right; }

@media (max-width: 600px) {
    .search-input-group { flex-direction: column; }
    .cek-status-box { padding: 24px; }
    .status-header { flex-direction: column; gap: 12px; align-items: flex-start; }
    .status-row { flex-direction: column; gap: 4px; }
    .status-value { text-align: left; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>