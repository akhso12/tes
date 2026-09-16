<?php
/**
 * Akademik
 */
$page_title = 'Akademik | SMK INFOKOM BOGOR';
$meta_desc = 'Informasi akademik, program keahlian, kurikulum, dan kegiatan pembelajaran di SMK INFOKOM BOGOR.';
$body_class = 'page-akademik';

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$programs = db_fetch_all("SELECT * FROM programs WHERE is_active = 1 ORDER BY sort_order ASC");
?>

<main id="main-content">
    <div class="breadcrumb-bar">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Beranda</a>
                <span class="separator">/</span>
                <span class="current">Akademik</span>
            </nav>
        </div>
    </div>

    <section class="akademik-page">
        <div class="container">
            <div class="section-title">
                <h2>Program Keahlian</h2>
                <p>Kompetensi keahlian yang ditawarkan di SMK INFOKOM BOGOR</p>
            </div>

            <?php if (!empty($programs)): ?>
            <div class="programs-grid akademik-grid">
                <?php foreach ($programs as $prog): ?>
                <div class="program-card fade-up">
                    <div class="program-card-img">
                        <?php if ($prog['gambar']): ?>
                        <img src="<?= e(asset_url($prog['gambar'])) ?>" alt="<?= e($prog['nama_program']) ?>" loading="lazy">
                        <?php else: ?>
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--gray-100);color:var(--primary);font-size:2.5rem;">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="program-card-body">
                        <h3><?= e($prog['nama_program']) ?></h3>
                        <p><?= e($prog['deskripsi'] ?? '') ?></p>
                        <?php if ($prog['keunggulan']): ?>
                        <div class="program-keunggulan">
                            <strong>Keunggulan:</strong>
                            <p><?= e($prog['keunggulan']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-graduation-cap"></i>
                <h3>Belum ada data program keahlian</h3>
                <p>Data program keahlian akan ditampilkan setelah administrator menambahkan.</p>
            </div>
            <?php endif; ?>

            <!-- Info Akademik Tambahan -->
            <div class="akademik-info">
                <h2 style="text-align:center;margin-bottom:30px;font-size:1.6rem;">Informasi Akademik</h2>
                <div class="akademik-cards">
                    <div class="akademik-card">
                        <i class="fas fa-book-open"></i>
                        <h3>Kurikulum</h3>
                        <p>Kurikulum Merdeka dengan pendekatan pembelajaran berbasis projek dan kompetensi industri.</p>
                    </div>
                    <div class="akademik-card">
                        <i class="fas fa-industry"></i>
                        <h3>Praktik Kerja Lapangan</h3>
                        <p>Program PKL/Magang di perusahaan mitra untuk pengalaman kerja nyata.</p>
                    </div>
                    <div class="akademik-card">
                        <i class="fas fa-certificate"></i>
                        <h3>Sertifikasi Kompetensi</h3>
                        <p>Uji kompetensi dan sertifikasi profesi sesuai standar industri nasional.</p>
                    </div>
                    <div class="akademik-card">
                        <i class="fas fa-users"></i>
                        <h3>Ekstrakurikuler</h3>
                        <p>Berbagai kegiatan ekstrakurikuler untuk mengembangkan bakat dan minat siswa.</p>
                    </div>
                </div>
            </div>

            <div class="back-link">
                <a href="<?= BASE_URL ?>"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
            </div>
        </div>
    </section>
</main>

<style>
.akademik-page { padding: var(--section-padding); }
.akademik-grid { grid-template-columns: repeat(3, 1fr); }
.program-keunggulan { margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--gray-200); font-size: 0.85rem; }
.program-keunggulan strong { color: var(--primary); }
.program-keunggulan p { margin-top: 4px; color: var(--gray-600); }

.akademik-info { margin-top: 60px; }
.akademik-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.akademik-card {
    text-align: center;
    padding: 30px 20px;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    transition: var(--transition);
}
.akademik-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
.akademik-card i { font-size: 2.2rem; color: var(--primary); margin-bottom: 14px; }
.akademik-card h3 { font-size: 1rem; margin-bottom: 10px; }
.akademik-card p { font-size: 0.88rem; color: var(--gray-500); }

@media (max-width: 1024px) { .akademik-grid { grid-template-columns: repeat(2, 1fr); } .akademik-cards { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .akademik-grid { grid-template-columns: 1fr; } .akademik-cards { grid-template-columns: 1fr; } }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>