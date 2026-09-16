<?php
/**
 * Prestasi
 */
$page_title = 'Prestasi | SMK INFOKOM BOGOR';
$meta_desc = 'Daftar prestasi siswa, guru, dan sekolah SMK INFOKOM BOGOR di berbagai tingkat kompetisi.';
$body_class = 'page-prestasi';

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$achievements = db_fetch_all("SELECT * FROM achievements WHERE is_active = 1 ORDER BY tahun DESC, sort_order ASC");
?>

<main id="main-content">
    <div class="breadcrumb-bar">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Beranda</a>
                <span class="separator">/</span>
                <span class="current">Prestasi</span>
            </nav>
        </div>
    </div>

    <section class="prestasi-page">
        <div class="container">
            <div class="section-title">
                <h2>Prestasi SMK INFOKOM BOGOR</h2>
                <p>Pencapaian membanggakan di berbagai bidang dan tingkat kompetisi</p>
            </div>

            <?php if (!empty($achievements)): ?>
            <div class="prestasi-page-grid">
                <?php foreach ($achievements as $ach): ?>
                <div class="prestasi-page-card fade-up">
                    <div class="prestasi-page-img">
                        <?php if ($ach['gambar']): ?>
                        <img src="<?= e(asset_url($ach['gambar'])) ?>" alt="<?= e($ach['judul']) ?>" loading="lazy">
                        <?php else: ?>
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--gray-100);color:var(--secondary);font-size:2.5rem;">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="prestasi-page-body">
                        <span class="badge-level badge-<?= strtolower(str_replace(['/',' '], '-', $ach['tingkat'])) ?>"><?= e($ach['tingkat']) ?></span>
                        <h3><?= e($ach['judul']) ?></h3>
                        <?php if ($ach['deskripsi']): ?>
                        <p><?= e($ach['deskripsi']) ?></p>
                        <?php endif; ?>
                        <span class="prestasi-year"><i class="far fa-calendar"></i> Tahun <?= $ach['tahun'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-trophy"></i>
                <h3>Belum ada data prestasi</h3>
                <p>Data prestasi akan ditampilkan setelah administrator menambahkan.</p>
            </div>
            <?php endif; ?>

            <div class="back-link">
                <a href="<?= BASE_URL ?>"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
            </div>
        </div>
    </section>
</main>

<style>
.prestasi-page { padding: var(--section-padding); }
.prestasi-page-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.prestasi-page-card {
    background: var(--white);
    border-radius: var(--radius-md);
    overflow: hidden;
    border: 1px solid var(--gray-200);
    transition: var(--transition);
}
.prestasi-page-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
.prestasi-page-img { height: 200px; overflow: hidden; background: var(--gray-100); }
.prestasi-page-img img { width: 100%; height: 100%; object-fit: cover; }
.prestasi-page-body { padding: 20px; }
.prestasi-page-body h3 { font-size: 1rem; margin: 10px 0; }
.prestasi-page-body p { font-size: 0.88rem; color: var(--gray-500); margin-bottom: 12px; }
.prestasi-year { font-size: 0.82rem; color: var(--gray-400); }
@media (max-width: 1024px) { .prestasi-page-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .prestasi-page-grid { grid-template-columns: 1fr; } }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>