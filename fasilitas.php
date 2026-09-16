<?php
$page_title = 'Fasilitas | SMK INFOKOM BOGOR';
$meta_desc = 'Fasilitas unggulan SMK INFOKOM BOGOR untuk mendukung proses pembelajaran dan pengembangan bakat siswa.';
$body_class = 'page-facilities';

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$facilities = db_fetch_all("SELECT * FROM facilities WHERE is_active = 1 ORDER BY sort_order ASC");
?>

<main id="main-content">
    <div class="breadcrumb-bar">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Beranda</a>
                <span class="separator">/</span>
                <a href="<?= BASE_URL ?>profil.php">Profil</a>
                <span class="separator">/</span>
                <span class="current">Fasilitas</span>
            </nav>
        </div>
    </div>

    <section class="inner-page-section">
        <div class="container narrow-content">
            <div class="section-title left-title">
                <h2>Fasilitas Sekolah</h2>
            </div>

            <div class="facility-grid">
                <?php if (!empty($facilities)): ?>
                    <?php foreach ($facilities as $facility): ?>
                        <article class="facility-card">
                            <div class="facility-image">
                                <?php if (!empty($facility['gambar'])): ?>
                                    <img src="<?= e(asset_url($facility['gambar'])) ?>" alt="<?= e($facility['nama_fasilitas']) ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="facility-placeholder"><i class="fas fa-building"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="facility-body">
                                <h3><?= e($facility['nama_fasilitas']) ?></h3>
                                <p><?= e($facility['deskripsi'] ?? 'Fasilitas pendukung pembelajaran yang modern dan nyaman untuk siswa.') ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="content-card empty-box">
                        <i class="fas fa-building"></i>
                        <h3>Belum ada data fasilitas</h3>
                        <p>Data fasilitas akan muncul setelah admin menambahkannya melalui panel pengelolaan.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="back-link">
                <a href="<?= BASE_URL ?>profil.php"><i class="fas fa-arrow-left"></i> Kembali ke Profil</a>
            </div>
        </div>
    </section>
</main>

<style>
.inner-page-section { padding: var(--section-padding); }
.narrow-content { max-width: 1100px; }
.left-title { text-align: left; margin-bottom: 32px; }
.left-title h2::after { left: 0; transform: none; }
.facility-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 24px;
}
.facility-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.facility-image {
    height: 230px;
    background: var(--gray-100);
    overflow: hidden;
}
.facility-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.facility-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: var(--gray-300);
}
.facility-body { padding: 22px; }
.facility-body h3 { margin-bottom: 8px; }
.facility-body p { color: var(--gray-600); }
.empty-box {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px 24px;
    color: var(--gray-500);
}
.empty-box i { font-size: 2.5rem; margin-bottom: 12px; color: var(--gray-300); }
@media (max-width: 768px) {
    .facility-grid { grid-template-columns: 1fr; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
