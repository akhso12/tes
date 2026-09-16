<?php
$page_title = 'Visi & Misi | SMK INFOKOM BOGOR';
$meta_desc = 'Visi dan misi SMK INFOKOM BOGOR dalam membentuk lulusan yang kompeten, berkarakter, dan siap menghadapi tantangan zaman.';
$body_class = 'page-vision';

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main id="main-content">
    <div class="breadcrumb-bar">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Beranda</a>
                <span class="separator">/</span>
                <a href="<?= BASE_URL ?>profil.php">Profil</a>
                <span class="separator">/</span>
                <span class="current">Visi & Misi</span>
            </nav>
        </div>
    </div>

    <section class="inner-page-section">
        <div class="container narrow-content">
            <div class="section-title left-title">
                <h2>Visi & Misi</h2>
            </div>

            <div class="content-card vision-block">
                <div class="icon-wrap blue"><i class="fas fa-eye"></i></div>
                <h3>Visi</h3>
                <p>Menjadi sekolah menengah kejuruan unggulan yang menghasilkan lulusan kompeten, berkarakter, kreatif, inovatif, dan siap bersaing di dunia kerja serta terus berkembang dalam teknologi dan industri.</p>
            </div>

            <div class="content-card mission-block">
                <div class="icon-wrap gold"><i class="fas fa-list-ol"></i></div>
                <h3>Misi</h3>
                <ol class="mission-list">
                    <li>Menyelenggarakan pendidikan dan pembelajaran yang berkualitas, relevan, dan berorientasi pada kebutuhan industri.</li>
                    <li>Menumbuhkan karakter siswa yang beriman, bertakwa, disiplin, jujur, dan berakhlak mulia.</li>
                    <li>Mengembangkan keterampilan teknologi, komunikasi, dan kewirausahaan untuk mendukung kesiapan kerja.</li>
                    <li>Mengoptimalkan kegiatan praktik, magang, dan kerja sama industri agar siswa siap menghadapi dunia kerja.</li>
                    <li>Menciptakan lingkungan sekolah yang aman, nyaman, inovatif, dan kondusif untuk tumbuh kembang siswa.</li>
                </ol>
            </div>

            <div class="back-link">
                <a href="<?= BASE_URL ?>profil.php"><i class="fas fa-arrow-left"></i> Kembali ke Profil</a>
            </div>
        </div>
    </section>
</main>

<style>
.inner-page-section { padding: var(--section-padding); }
.narrow-content { max-width: 960px; }
.left-title { text-align: left; margin-bottom: 32px; }
.left-title h2::after { left: 0; transform: none; }
.content-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    padding: 32px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
}
.icon-wrap {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
    color: var(--white);
    font-size: 1.4rem;
}
.icon-wrap.blue { background: var(--primary); }
.icon-wrap.gold { background: var(--secondary); color: var(--gray-900); }
.mission-list {
    list-style: decimal;
    padding-left: 18px;
    color: var(--gray-600);
    line-height: 1.9;
}
.mission-list li { margin-bottom: 10px; }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
