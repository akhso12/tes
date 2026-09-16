<?php
/**
 * Detail Berita
 */
$slug = $_GET['slug'] ?? '';
if (!$slug) redirect(BASE_URL . 'berita.php');

require_once __DIR__ . '/includes/functions.php';

$news = db_fetch(
    "SELECT n.*, nc.nama_kategori, nc.slug as kategori_slug 
     FROM news n 
     LEFT JOIN news_categories nc ON n.kategori_id = nc.id 
     WHERE n.slug = :slug AND n.status = 'publish'",
    [':slug' => $slug]
);

if (!$news) redirect(BASE_URL . 'berita.php');

// Update views
db_query("UPDATE news SET views = views + 1 WHERE id = :id", [':id' => $news['id']]);

$page_title = e($news['judul']) . ' | SMK INFOKOM BOGOR';
$meta_desc = truncate(strip_tags($news['ringkasan'] ?? $news['isi']), 160);
$body_class = 'page-detail-berita';

// Related news
$related = db_fetch_all(
    "SELECT n.*, nc.nama_kategori FROM news n LEFT JOIN news_categories nc ON n.kategori_id = nc.id 
     WHERE n.status = 'publish' AND n.id != :id AND n.kategori_id = :kat 
     ORDER BY n.tanggal_terbit DESC LIMIT 3",
    [':id' => $news['id'], ':kat' => $news['kategori_id']]
);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main id="main-content">
    <div class="breadcrumb-bar">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Beranda</a>
                <span class="separator">/</span>
                <a href="<?= BASE_URL ?>berita.php">Berita</a>
                <span class="separator">/</span>
                <span class="current"><?= e(truncate($news['judul'], 40)) ?></span>
            </nav>
        </div>
    </div>

    <section class="detail-berita-section">
        <div class="container">
            <div class="detail-layout">
                <article class="detail-main">
                    <div class="detail-meta-top">
                        <?php if ($news['nama_kategori']): ?>
                        <span class="detail-kategori"><?= e($news['nama_kategori']) ?></span>
                        <?php endif; ?>
                        <span class="detail-date"><i class="far fa-calendar"></i> <?= format_date_indo($news['tanggal_terbit'] ?? $news['created_at']) ?></span>
                        <span class="detail-author"><i class="far fa-user"></i> <?= e($news['penulis']) ?></span>
                    </div>

                    <h1 class="detail-title"><?= e($news['judul']) ?></h1>

                    <?php if ($news['thumbnail']): ?>
                    <div class="detail-hero-img">
                        <img src="<?= BASE_URL . e($news['thumbnail']) ?>" alt="<?= e($news['judul']) ?>">
                    </div>
                    <?php endif; ?>

                    <div class="detail-content">
                        <?= nl2br(e($news['isi'])) ?>
                    </div>

                    <div class="detail-share">
                        <span>Bagikan:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode(BASE_URL . 'detail-berita.php?id=' . (int)$id) ?>" class="share-btn facebook" target="_blank" rel="noopener" aria-label="Bagikan ke Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com/intent/tweet?url=<?= rawurlencode(BASE_URL . 'detail-berita.php?id=' . (int)$id) ?>&text=<?= rawurlencode($news['judul'] ?? '') ?>" class="share-btn twitter" target="_blank" rel="noopener" aria-label="Bagikan ke X"><i class="fab fa-twitter"></i></a>
                        <a href="https://wa.me/?text=<?= rawurlencode(($news['judul'] ?? '') . ' ' . BASE_URL . 'detail-berita.php?id=' . (int)$id) ?>" class="share-btn whatsapp" target="_blank" rel="noopener" aria-label="Bagikan ke WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </article>

                <!-- Sidebar Related -->
                <?php if (!empty($related)): ?>
                <aside class="detail-sidebar">
                    <h3 class="sidebar-heading">Berita Terkait</h3>
                    <?php foreach ($related as $rel): ?>
                    <div class="related-item">
                        <div class="related-img">
                            <?php if ($rel['thumbnail']): ?>
                            <img src="<?= BASE_URL . e($rel['thumbnail']) ?>" alt="<?= e($rel['judul']) ?>" loading="lazy">
                            <?php else: ?>
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--gray-100);color:var(--gray-400);"><i class="fas fa-newspaper"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="related-info">
                            <h4><a href="<?= BASE_URL ?>detail-berita.php?slug=<?= e($rel['slug']) ?>"><?= e($rel['judul']) ?></a></h4>
                            <span class="related-date"><?= format_date_indo($rel['tanggal_terbit'] ?? $rel['created_at']) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </aside>
                <?php endif; ?>
            </div>

            <div class="back-link">
                <a href="<?= BASE_URL ?>berita.php"><i class="fas fa-arrow-left"></i> Kembali ke Berita</a>
            </div>
        </div>
    </section>
</main>

<style>
.detail-berita-section { padding: var(--section-padding); }
.detail-layout { display: grid; grid-template-columns: 1fr 320px; gap: 40px; }
.detail-meta-top { display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 16px; font-size: 0.9rem; color: var(--gray-500); }
.detail-kategori {
    background: var(--primary);
    color: var(--white);
    padding: 3px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}
.detail-title { font-size: 2rem; margin-bottom: 24px; line-height: 1.3; }
.detail-hero-img {
    border-radius: var(--radius-md);
    overflow: hidden;
    margin-bottom: 30px;
    box-shadow: var(--shadow-md);
}
.detail-hero-img img { width: 100%; height: 400px; object-fit: cover; }
.detail-content { font-size: 1.05rem; line-height: 1.9; color: var(--gray-700); }
.detail-content p { margin-bottom: 16px; }
.detail-share {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 40px;
    padding-top: 24px;
    border-top: 1px solid var(--gray-200);
}
.share-btn {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1rem;
}
.share-btn.facebook { background: #1877f2; }
.share-btn.twitter { background: #1da1f2; }
.share-btn.whatsapp { background: #25d366; }

.detail-sidebar {}
.sidebar-heading { font-size: 1.2rem; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid var(--primary); }
.related-item { display: flex; gap: 14px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--gray-100); }
.related-img { width: 90px; height: 70px; border-radius: var(--radius-sm); overflow: hidden; flex-shrink: 0; background: var(--gray-100); }
.related-img img { width: 100%; height: 100%; object-fit: cover; }
.related-info h4 { font-size: 0.9rem; line-height: 1.4; margin-bottom: 4px; }
.related-info h4 a:hover { color: var(--primary); }
.related-date { font-size: 0.78rem; color: var(--gray-400); }

@media (max-width: 900px) {
    .detail-layout { grid-template-columns: 1fr; }
    .detail-title { font-size: 1.5rem; }
    .detail-hero-img img { height: 250px; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>