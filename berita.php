<?php
/**
 * Berita - Daftar Semua Berita
 */
$page_title = 'Berita | SMK INFOKOM BOGOR';
$meta_desc = 'Berita dan informasi terbaru dari SMK INFOKOM BOGOR.';
$body_class = 'page-berita';

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Pagination
$per_page = (int)get_setting('items_per_page') ?: 10;
$current_page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($current_page - 1) * $per_page;

// Filter kategori
$kategori_filter = $_GET['kategori'] ?? '';
$where = "n.status = 'publish'";
$params = [];

if ($kategori_filter) {
    $where .= " AND nc.slug = :kategori";
    $params[':kategori'] = $kategori_filter;
}

$total = db_count("news n LEFT JOIN news_categories nc ON n.kategori_id = nc.id", $where, $params);
$news = db_fetch_all(
    "SELECT n.*, nc.nama_kategori, nc.slug as kategori_slug 
     FROM news n 
     LEFT JOIN news_categories nc ON n.kategori_id = nc.id 
     WHERE $where 
     ORDER BY n.tanggal_terbit DESC, n.created_at DESC 
     LIMIT $per_page OFFSET $offset",
    $params
);

$categories = db_fetch_all("SELECT * FROM news_categories ORDER BY nama_kategori ASC");
$base_url = BASE_URL . 'berita.php?' . ($kategori_filter ? 'kategori=' . e($kategori_filter) : '');
?>

<main id="main-content">
    <div class="breadcrumb-bar">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Beranda</a>
                <span class="separator">/</span>
                <span class="current">Berita</span>
            </nav>
        </div>
    </div>

    <section class="berita-page">
        <div class="container">
            <div class="section-title">
                <h2>Berita Terbaru</h2>
                <p>Informasi dan kegiatan terkini dari SMK INFOKOM BOGOR</p>
            </div>

            <!-- Filter Kategori -->
            <div class="filter-bar">
                <a href="<?= BASE_URL ?>berita.php" class="filter-btn<?= !$kategori_filter ? ' active' : '' ?>">Semua</a>
                <?php foreach ($categories as $cat): ?>
                <a href="<?= BASE_URL ?>berita.php?kategori=<?= e($cat['slug']) ?>" class="filter-btn<?= ($kategori_filter === $cat['slug']) ? ' active' : '' ?>"><?= e($cat['nama_kategori']) ?></a>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($news)): ?>
            <div class="news-grid berita-page-grid">
                <?php foreach ($news as $item): ?>
                <article class="news-card fade-up">
                    <div class="news-card-img">
                        <?php if ($item['thumbnail']): ?>
                        <img src="<?= BASE_URL . e($item['thumbnail']) ?>" alt="<?= e($item['judul']) ?>" loading="lazy">
                        <?php else: ?>
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--gray-100);color:var(--gray-400);font-size:2.5rem;">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="news-card-body">
                        <div class="news-card-meta">
                            <?php if ($item['nama_kategori']): ?>
                            <span><i class="fas fa-tag"></i> <?= e($item['nama_kategori']) ?></span>
                            <?php endif; ?>
                            <span><i class="far fa-calendar"></i> <?= format_date_indo($item['tanggal_terbit'] ?? $item['created_at']) ?></span>
                            <span><i class="far fa-user"></i> <?= e($item['penulis']) ?></span>
                        </div>
                        <h3><a href="<?= BASE_URL ?>detail-berita.php?slug=<?= e($item['slug']) ?>"><?= e($item['judul']) ?></a></h3>
                        <p><?= e(truncate(strip_tags($item['ringkasan'] ?? $item['isi']), 120)) ?></p>
                        <a href="<?= BASE_URL ?>detail-berita.php?slug=<?= e($item['slug']) ?>" class="read-more">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <?= paginate($total, $per_page, $current_page, $base_url) ?>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-newspaper"></i>
                <h3>Belum ada berita</h3>
                <p>Berita akan ditampilkan setelah administrator menambahkan konten.</p>
            </div>
            <?php endif; ?>

            <div class="back-link">
                <a href="<?= BASE_URL ?>"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
            </div>
        </div>
    </section>
</main>

<style>
.berita-page { padding: var(--section-padding); }
.filter-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 40px;
    justify-content: center;
}
.filter-btn {
    padding: 8px 20px;
    border-radius: 30px;
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--gray-600);
    background: var(--white);
    border: 1px solid var(--gray-200);
    transition: var(--transition-fast);
}
.filter-btn:hover { border-color: var(--primary); color: var(--primary); }
.filter-btn.active { background: var(--primary); color: var(--white); border-color: var(--primary); }
.berita-page-grid { grid-template-columns: repeat(3, 1fr); }
@media (max-width: 1024px) { .berita-page-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .berita-page-grid { grid-template-columns: 1fr; } }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>