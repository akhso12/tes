<?php
/**
 * Halaman Staff - SMK INFOKOM BOGOR
 */
$page_title = 'Staff | SMK INFOKOM BOGOR';
$meta_desc = 'Daftar Staff dan Pegawai SMK INFOKOM BOGOR';
$body_class = 'page-staff';

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Ambil data staff yang aktif
$staffs = db_fetch_all("SELECT * FROM staff WHERE status_aktif = 1 ORDER BY sort_order ASC, nama_lengkap ASC");
?>

<main id="main-content">
    <div class="breadcrumb-bar">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Beranda</a>
                <span class="separator">/</span>
                <span class="current">Staff</span>
            </nav>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <h1>Daftar Staff</h1>
            <p>Tim Pegawai dan Staf Administrasi SMK INFOKOM BOGOR</p>
        </div>
    </section>

    <!-- Staff Grid -->
    <section class="staff-section">
        <div class="container">
            <?php if (!empty($staffs)): ?>
            <div class="staff-grid">
                <?php foreach ($staffs as $staff): ?>
                <div class="staff-card fade-up">
                    <div class="staff-card-img">
                        <?php if ($staff['foto']): ?>
                        <img src="<?= e(asset_url($staff['foto'])) ?>" alt="<?= e($staff['nama_lengkap']) ?>" loading="lazy">
                        <?php else: ?>
                        <div style="width:100%;height:280px;display:flex;align-items:center;justify-content:center;background:var(--gray-100);color:var(--gray-400);font-size:3rem;">
                            <i class="fas fa-user"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="staff-card-body">
                        <h3><?= e($staff['nama_lengkap']) ?></h3>
                        <?php if ($staff['nip']): ?>
                        <p class="nip"><strong>NIP:</strong> <?= e($staff['nip']) ?></p>
                        <?php endif; ?>
                        <?php if ($staff['posisi']): ?>
                        <p class="posisi"><i class="fas fa-briefcase"></i> <?= e($staff['posisi']) ?></p>
                        <?php endif; ?>
                        <?php if ($staff['departemen']): ?>
                        <p class="departemen"><i class="fas fa-building"></i> <?= e($staff['departemen']) ?></p>
                        <?php endif; ?>
                        <?php if ($staff['tanggung_jawab']): ?>
                        <p class="tanggung-jawab"><?= e(truncate($staff['tanggung_jawab'], 100)) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>Belum Ada Data Staff</h3>
                <p>Data staff sedang dalam proses update.</p>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<style>
.page-staff {}
.page-hero {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    padding: 60px 0;
    text-align: center;
    color: var(--white);
}
.page-hero h1 { font-size: 2.4rem; margin-bottom: 10px; }
.page-hero p { font-size: 1.1rem; color: var(--secondary-light); }

.breadcrumb-bar {
    background: var(--gray-50);
    padding: 16px 0;
    border-bottom: 1px solid var(--gray-200);
}
.breadcrumb-bar nav {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
}
.breadcrumb-bar a { color: var(--primary); font-weight: 500; }
.breadcrumb-bar a:hover { color: var(--secondary); }
.breadcrumb-bar .separator { color: var(--gray-300); }
.breadcrumb-bar .current { color: var(--gray-600); }

.staff-section { padding: var(--section-padding); }
.staff-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 28px;
}

.staff-card {
    background: var(--white);
    border-radius: var(--radius-md);
    overflow: hidden;
    border: 1px solid var(--gray-200);
    transition: var(--transition);
}
.staff-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.staff-card-img {
    height: 280px;
    background: var(--gray-100);
    overflow: hidden;
}
.staff-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}
.staff-card:hover .staff-card-img img { transform: scale(1.05); }

.staff-card-body {
    padding: 24px;
}
.staff-card-body h3 {
    font-size: 1.1rem;
    margin-bottom: 10px;
    color: var(--primary);
}
.staff-card-body p {
    font-size: 0.9rem;
    color: var(--gray-600);
    margin-bottom: 8px;
    line-height: 1.5;
}
.staff-card-body .nip {
    font-size: 0.85rem;
    color: var(--gray-500);
}
.staff-card-body .posisi,
.staff-card-body .departemen {
    font-weight: 500;
    color: var(--primary);
}
.staff-card-body i { margin-right: 6px; }

.empty-state {
    text-align: center;
    padding: 60px 20px;
}
.empty-state i {
    font-size: 4rem;
    color: var(--gray-300);
    margin-bottom: 20px;
}
.empty-state h3 {
    font-size: 1.3rem;
    margin-bottom: 10px;
}
.empty-state p { color: var(--gray-500); }

@media (max-width: 768px) {
    .staff-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
    .page-hero h1 { font-size: 1.8rem; }
    .staff-card-img { height: 220px; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>