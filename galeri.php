<?php
/**
 * Galeri Kegiatan
 */
$page_title = 'Galeri Kegiatan | SMK INFOKOM BOGOR';
$meta_desc = 'Dokumentasi foto kegiatan pembelajaran, praktik, lomba, dan event di SMK INFOKOM BOGOR.';
$body_class = 'page-galeri';

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$filter = $_GET['kategori'] ?? '';
$where = "g.is_active = 1";
$params = [];

if ($filter) {
    $where .= " AND gc.slug = :kategori";
    $params[':kategori'] = $filter;
}

$gallery = db_fetch_all(
    "SELECT g.*, gc.nama_kategori as kategori_nama, gc.slug as kategori_slug 
     FROM gallery g 
     LEFT JOIN gallery_categories gc ON g.kategori_id = gc.id 
     WHERE $where 
     ORDER BY g.created_at DESC",
    $params
);

$categories = db_fetch_all("SELECT * FROM gallery_categories ORDER BY nama_kategori ASC");
?>

<main id="main-content">
    <div class="breadcrumb-bar">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Beranda</a>
                <span class="separator">/</span>
                <span class="current">Galeri</span>
            </nav>
        </div>
    </div>

    <section class="galeri-page">
        <div class="container">
            <div class="section-title">
                <h2>Galeri Kegiatan</h2>
                <p>Dokumentasi berbagai kegiatan di SMK INFOKOM BOGOR</p>
            </div>

            <!-- Filter -->
            <div class="filter-bar">
                <a href="<?= BASE_URL ?>galeri.php" class="filter-btn<?= !$filter ? ' active' : '' ?>">Semua</a>
                <?php foreach ($categories as $cat): ?>
                <a href="<?= BASE_URL ?>galeri.php?kategori=<?= e($cat['slug']) ?>" class="filter-btn<?= ($filter === $cat['slug']) ? ' active' : '' ?>"><?= e($cat['nama_kategori']) ?></a>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($gallery)): ?>
            <div class="galeri-grid">
                <?php foreach ($gallery as $gal): ?>
                <div class="galeri-item" data-src="<?= e(asset_url($gal['gambar'])) ?>" data-title="<?= e($gal['judul']) ?>">
                    <img src="<?= e(asset_url($gal['gambar'])) ?>" alt="<?= e($gal['judul']) ?>" loading="lazy">
                    <div class="galeri-item-overlay">
                        <div class="galeri-item-info">
                            <h4><?= e($gal['judul']) ?></h4>
                            <?php if ($gal['kategori_nama']): ?>
                            <span><?= e($gal['kategori_nama']) ?></span>
                            <?php endif; ?>
                        </div>
                        <i class="fas fa-search-plus galeri-zoom-icon"></i>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-images"></i>
                <h3>Belum ada dokumentasi kegiatan</h3>
                <p>Galeri foto akan ditampilkan setelah administrator mengupload dokumentasi.</p>
            </div>
            <?php endif; ?>

            <div class="back-link">
                <a href="<?= BASE_URL ?>"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
            </div>
        </div>
    </section>
</main>

<style>
.galeri-page { padding: var(--section-padding); }
.galeri-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}
.galeri-item {
    border-radius: var(--radius-md);
    overflow: hidden;
    position: relative;
    cursor: pointer;
    aspect-ratio: 1;
    background: var(--gray-100);
}
.galeri-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.galeri-item:hover img { transform: scale(1.1); }
.galeri-item-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(26,58,92,0.85) 0%, transparent 60%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 20px;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.galeri-item:hover .galeri-item-overlay { opacity: 1; }
.galeri-item-info h4 { color: var(--white); font-size: 0.95rem; margin-bottom: 4px; }
.galeri-item-info span { color: rgba(255,255,255,0.7); font-size: 0.8rem; }
.galeri-zoom-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: var(--white);
    font-size: 2rem;
    opacity: 0.8;
}

/* Lightbox styles */
#lightbox {
    position: fixed;
    inset: 0;
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
}
#lightbox.open { display: flex; }
.lightbox-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.9);
}
.lightbox-content {
    position: relative;
    z-index: 1;
    max-width: 90vw;
    max-height: 90vh;
    text-align: center;
}
.lightbox-content img {
    max-width: 90vw;
    max-height: 80vh;
    object-fit: contain;
    border-radius: var(--radius-md);
}
.lightbox-close {
    position: absolute;
    top: -40px;
    right: 0;
    background: none;
    border: none;
    color: var(--white);
    font-size: 2rem;
    cursor: pointer;
}
.lightbox-caption {
    color: var(--white);
    margin-top: 12px;
    font-size: 1rem;
}

@media (max-width: 1024px) { .galeri-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px) { .galeri-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px) { .galeri-grid { grid-template-columns: 1fr 1fr; gap: 10px; } }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>