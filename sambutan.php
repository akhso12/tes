<?php
$page_title = 'Sambutan Kepala Sekolah | SMK INFOKOM BOGOR';
$meta_desc = 'Sambutan kepala sekolah SMK INFOKOM BOGOR serta komitmen sekolah dalam membangun generasi kompeten.';
$body_class = 'page-sambutan';

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$principal = db_fetch("SELECT * FROM principal_message WHERE is_active = 1 LIMIT 1");
?>

<main id="main-content">
    <div class="breadcrumb-bar">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Beranda</a>
                <span class="separator">/</span>
                <a href="<?= BASE_URL ?>profil.php">Profil</a>
                <span class="separator">/</span>
                <span class="current">Sambutan</span>
            </nav>
        </div>
    </div>

    <section class="inner-page-section">
        <div class="container narrow-content">
            <div class="section-title left-title">
                <h2>Sambutan Kepala Sekolah</h2>
            </div>

            <div class="content-card">
                <?php if ($principal): ?>
                    <div class="sambutan-layout">
                        <div class="photo-box">
                            <?php if (!empty($principal['foto'])): ?>
                                <img src="<?= e(asset_url($principal['foto'])) ?>" alt="<?= e($principal['nama_kepsek']) ?>">
                            <?php else: ?>
                                <div class="photo-placeholder"><i class="fas fa-user-tie"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="text-box">
                            <h3><?= e($principal['nama_kepsek'] ?? 'Kepala Sekolah') ?></h3>
                            <p class="role"><?= e($principal['jabatan'] ?? 'Kepala Sekolah') ?></p>
                            <div class="message"><?= nl2br(e($principal['sambutan'] ?? 'Belum ada sambutan yang tersedia.')) ?></div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-user-tie"></i>
                        <h3>Belum ada sambutan kepala sekolah</h3>
                        <p>Data akan ditampilkan setelah administrator mengisi dari panel admin.</p>
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
.narrow-content { max-width: 980px; }
.left-title { text-align: left; margin-bottom: 32px; }
.left-title h2::after { left: 0; transform: none; }
.content-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    padding: 30px;
    box-shadow: var(--shadow-sm);
}
.sambutan-layout { display: grid; grid-template-columns: 260px 1fr; gap: 28px; align-items: start; }
.photo-box img, .photo-placeholder {
    width: 100%;
    min-height: 300px;
    border-radius: var(--radius-md);
    background: var(--gray-100);
    object-fit: cover;
}
.photo-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: var(--gray-300);
}
.text-box h3 { margin-bottom: 6px; }
.role { color: var(--secondary); font-weight: 700; margin-bottom: 18px; }
.message { color: var(--gray-600); line-height: 1.9; }
.empty-state {
    text-align: center;
    color: var(--gray-500);
    padding: 24px 0;
}
.empty-state i { font-size: 2.5rem; margin-bottom: 12px; color: var(--gray-300); }
@media (max-width: 768px) {
    .sambutan-layout { grid-template-columns: 1fr; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
