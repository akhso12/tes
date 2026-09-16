<?php
/**
 * Halaman Guru - SMK INFOKOM BOGOR
 */
$page_title = 'Guru | SMK INFOKOM BOGOR';
$meta_desc = 'Daftar Guru SMK INFOKOM BOGOR - Pendidik Profesional dan Bersertifikat';
$body_class = 'page-guru';

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Ambil data guru yang aktif
$teachers = db_fetch_all("SELECT * FROM teachers WHERE status_aktif = 1 ORDER BY sort_order ASC, nama_lengkap ASC");
?>

<main id="main-content">
    <div class="breadcrumb-bar">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Beranda</a>
                <span class="separator">/</span>
                <span class="current">Guru</span>
            </nav>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <h1>Daftar Guru</h1>
            <p>Tim Pendidik Profesional SMK INFOKOM BOGOR</p>
        </div>
    </section>

    <!-- Teachers Grid -->
    <section class="teachers-section">
        <div class="container">
            <?php if (!empty($teachers)): ?>
            <div class="teachers-grid">
                <?php foreach ($teachers as $teacher): ?>
                <div class="teacher-card fade-up">
                    <div class="teacher-card-img">
                        <?php if ($teacher['foto']): ?>
                        <img src="<?= e(asset_url($teacher['foto'])) ?>" alt="<?= e($teacher['nama_lengkap']) ?>" loading="lazy">
                        <?php else: ?>
                        <div style="width:100%;height:280px;display:flex;align-items:center;justify-content:center;background:var(--gray-100);color:var(--gray-400);font-size:3rem;">
                            <i class="fas fa-user"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="teacher-card-body">
                        <h3><?= e($teacher['nama_lengkap']) ?></h3>
                        <?php if ($teacher['nip']): ?>
                        <p class="nip"><strong>NIP:</strong> <?= e($teacher['nip']) ?></p>
                        <?php endif; ?>
                        <?php if ($teacher['mata_pelajaran']): ?>
                        <p class="subject"><i class="fas fa-book"></i> <?= e($teacher['mata_pelajaran']) ?></p>
                        <?php endif; ?>
                        <?php if ($teacher['spesialisasi']): ?>
                        <p class="specialization"><i class="fas fa-graduation-cap"></i> <?= e($teacher['spesialisasi']) ?></p>
                        <?php endif; ?>
                        <?php if ($teacher['keahlian']): ?>
                        <p class="keahlian"><?= e(truncate($teacher['keahlian'], 100)) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-user-tie"></i>
                <h3>Belum Ada Data Guru</h3>
                <p>Data guru sedang dalam proses update.</p>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<style>
.page-guru {}
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

.teachers-section { padding: var(--section-padding); }
.teachers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 28px;
}

.teacher-card {
    background: var(--white);
    border-radius: var(--radius-md);
    overflow: hidden;
    border: 1px solid var(--gray-200);
    transition: var(--transition);
}
.teacher-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.teacher-card-img {
    height: 280px;
    background: var(--gray-100);
    overflow: hidden;
}
.teacher-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}
.teacher-card:hover .teacher-card-img img { transform: scale(1.05); }

.teacher-card-body {
    padding: 24px;
}
.teacher-card-body h3 {
    font-size: 1.1rem;
    margin-bottom: 10px;
    color: var(--primary);
}
.teacher-card-body p {
    font-size: 0.9rem;
    color: var(--gray-600);
    margin-bottom: 8px;
    line-height: 1.5;
}
.teacher-card-body .nip {
    font-size: 0.85rem;
    color: var(--gray-500);
}
.teacher-card-body .subject,
.teacher-card-body .specialization {
    font-weight: 500;
    color: var(--primary);
}
.teacher-card-body i { margin-right: 6px; }

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
    .teachers-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
    .page-hero h1 { font-size: 1.8rem; }
    .teacher-card-img { height: 220px; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>