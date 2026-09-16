<?php
$page_title = 'Sejarah | SMK INFOKOM BOGOR';
$meta_desc = 'Sejarah berdirinya SMK INFOKOM BOGOR dan perjalanan sekolah dalam menyiapkan lulusan kompeten.';
$body_class = 'page-history';

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
                <span class="current">Sejarah</span>
            </nav>
        </div>
    </div>

    <section class="inner-page-section">
        <div class="container narrow-content">
            <div class="section-title left-title">
                <h2>Sejarah SMK INFOKOM BOGOR</h2>
            </div>

            <div class="content-card">
                <p>SMK INFOKOM BOGOR berdiri sebagai respons terhadap kebutuhan pendidikan vokasi yang berkualitas di wilayah Bogor. Sekolah ini hadir dengan semangat untuk melahirkan generasi muda yang siap menghadapi tantangan dunia kerja, khususnya di bidang teknologi dan industri digital.</p>
                <p>Dengan komitmen yang kuat dalam menumbuhkan karakter, kreativitas, dan kompetensi, sekolah terus mengembangkan kurikulum, fasilitas pembelajaran, serta hubungan dengan dunia industri agar siswa memiliki bekal yang relevan dan siap bersaing di era modern.</p>
                <p>Sejak awal berdiri, SMK INFOKOM BOGOR fokus pada penguatan kemampuan siswa dalam bidang teknologi, komunikasi, dan kewirausahaan. Berbagai program pelatihan, kegiatan ekstrakurikuler, serta praktik industri menjadi bagian dari proses pembelajaran yang menyiapkan siswa menjadi lulusan yang kompeten, disiplin, dan siap berkontribusi di masyarakat.</p>
            </div>

            <div class="milestone-grid">
                <div class="milestone-item">
                    <span class="year">2010</span>
                    <h3>Awal Berdiri</h3>
                    <p>SMK INFOKOM BOGOR mulai berkiprah dengan visi menciptakan sekolah yang unggul dalam teknologi dan karakter.</p>
                </div>
                <div class="milestone-item">
                    <span class="year">2015</span>
                    <h3>Penguatan Program</h3>
                    <p>Program studi dan kegiatan pembelajaran ditingkatkan dengan pendekatan berbasis kompetensi dan industri.</p>
                </div>
                <div class="milestone-item">
                    <span class="year">2020</span>
                    <h3>Digitalisasi</h3>
                    <p>Perkembangan teknologi mendorong sekolah memperkuat pembelajaran berbasis digital dan praktik modern.</p>
                </div>
                <div class="milestone-item">
                    <span class="year">Saat ini</span>
                    <h3>Transformasi</h3>
                    <p>SMK INFOKOM BOGOR terus berkembang menjadi sekolah yang siap menghasilkan lulusan unggul dan berdaya saing.</p>
                </div>
            </div>

            <div class="back-link">
                <a href="<?= BASE_URL ?>profil.php"><i class="fas fa-arrow-left"></i> Kembali ke Profil</a>
            </div>
        </div>
    </section>
</main>

<style>
.inner-page-section {
    padding: var(--section-padding);
}
.narrow-content {
    max-width: 960px;
}
.left-title {
    text-align: left;
    margin-bottom: 32px;
}
.left-title h2::after {
    left: 0;
    transform: none;
}
.content-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    padding: 30px;
    box-shadow: var(--shadow-sm);
}
.content-card p {
    margin-bottom: 18px;
    color: var(--gray-600);
}
.milestone-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 24px;
    margin-top: 36px;
}
.milestone-item {
    background: linear-gradient(135deg, #f8fbff 0%, #fffdf2 100%);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    padding: 24px;
}
.year {
    display: inline-block;
    background: var(--primary);
    color: var(--white);
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 700;
    margin-bottom: 12px;
}
.milestone-item h3 {
    margin-bottom: 10px;
}
.milestone-item p {
    color: var(--gray-600);
}
@media (max-width: 768px) {
    .milestone-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
