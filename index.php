<?php
/**
 * Beranda - SMK INFOKOM BOGOR
 */
$page_title = 'SMK INFOKOM BOGOR | Sekolah Menengah Kejuruan Unggulan';
$meta_desc = 'Website Resmi SMK INFOKOM BOGOR. Membangun generasi kompeten, berkarakter, kreatif, dan siap menghadapi dunia kerja.';
$body_class = 'page-home';

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Ambil data untuk beranda
$banners = db_fetch_all("SELECT * FROM banners WHERE is_active = 1 ORDER BY sort_order ASC");
$stats = db_fetch_all("SELECT * FROM school_statistics WHERE is_active = 1 ORDER BY sort_order ASC");
$programs = db_fetch_all("SELECT * FROM programs WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 4");
$news = db_fetch_all("SELECT n.*, nc.nama_kategori, nc.slug as kategori_slug FROM news n LEFT JOIN news_categories nc ON n.kategori_id = nc.id WHERE n.status = 'publish' ORDER BY n.tanggal_terbit DESC, n.created_at DESC LIMIT 4");
$achievements = db_fetch_all("SELECT * FROM achievements WHERE is_active = 1 ORDER BY tahun DESC, sort_order ASC LIMIT 6");
$gallery_items = db_fetch_all("SELECT g.*, gc.nama_kategori as kategori_nama FROM gallery g LEFT JOIN gallery_categories gc ON g.kategori_id = gc.id WHERE g.is_active = 1 ORDER BY g.created_at DESC LIMIT 8");
$announcements = db_fetch_all("SELECT * FROM announcements WHERE is_active = 1 ORDER BY tanggal_pengumuman DESC LIMIT 3");
$agendas = db_fetch_all("SELECT * FROM agendas WHERE is_active = 1 AND tanggal_mulai >= NOW() ORDER BY tanggal_mulai ASC LIMIT 3");
?>

<main id="main-content">

    <!-- ===== HERO SLIDER ===== -->
    <?php if (!empty($banners)): ?>
    <section class="hero-slider" aria-label="Slider Utama">
        <?php foreach ($banners as $i => $banner): ?>
        <div class="hero-slide<?= $i === 0 ? ' active' : '' ?>">
            <img src="<?= e(asset_url($banner['gambar'], 'assets/images/banner1.svg')) ?>" alt="<?= e($banner['judul']) ?>" class="hero-slide-bg" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
            <div class="hero-overlay">
                <div class="container">
                    <div class="hero-content">
                        <p class="hero-label">Selamat Datang di</p>
                        <h1><?= e($banner['judul']) ?></h1>
                        <?php if ($banner['deskripsi']): ?>
                        <p><?= e($banner['deskripsi']) ?></p>
                        <?php endif; ?>
                        <div class="hero-buttons">
                            <a href="<?= BASE_URL ?>profil.php" class="btn btn-primary btn-lg">Profil Sekolah</a>
                            <a href="<?= BASE_URL ?>ppdb.php" class="btn btn-secondary btn-lg">PPDB Online</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Slider Controls -->
        <?php if (count($banners) > 1): ?>
        <button class="slider-arrow prev" aria-label="Slide sebelumnya"><i class="fas fa-chevron-left"></i></button>
        <button class="slider-arrow next" aria-label="Slide berikutnya"><i class="fas fa-chevron-right"></i></button>
        <div class="slider-controls">
            <?php foreach ($banners as $i => $banner): ?>
            <button class="slider-dot<?= $i === 0 ? ' active' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>
    <?php else: ?>
    <section class="hero-placeholder">
        <div class="hero-overlay">
            <div class="container">
                <div class="hero-content">
                    <p class="hero-label">Selamat Datang di</p>
                    <h1>SMK INFOKOM BOGOR</h1>
                    <p>Membangun generasi kompeten, berkarakter, kreatif, dan siap menghadapi dunia kerja serta perkembangan teknologi.</p>
                    <div class="hero-buttons">
                        <a href="<?= BASE_URL ?>profil.php" class="btn btn-primary btn-lg">Profil Sekolah</a>
                        <a href="<?= BASE_URL ?>ppdb.php" class="btn btn-secondary btn-lg">PPDB Online</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== INFORMASI TERBARU ===== -->
    <section class="info-section">
        <div class="container">
            <div class="section-title">
                <h2>Informasi Terbaru</h2>
                <p>Berita terkini seputar kegiatan dan pengumuman sekolah</p>
            </div>

            <div class="info-cards">
                <!-- Pengumuman -->
                <div class="info-card fade-up">
                    <div class="info-card-icon blue">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h3>Pengumuman</h3>
                    <?php if (!empty($announcements)): ?>
                        <?php $ann = $announcements[0]; ?>
                        <p><?= e(truncate($ann['judul'], 60)) ?></p>
                        <span class="card-date"><i class="far fa-calendar"></i> <?= format_date_indo($ann['tanggal_pengumuman']) ?></span>
                        <a href="<?= BASE_URL ?>profil.php" class="card-link">Lihat Detail &rarr;</a>
                    <?php else: ?>
                        <p>Belum ada pengumuman terbaru.</p>
                    <?php endif; ?>
                </div>

                <!-- Agenda -->
                <div class="info-card fade-up">
                    <div class="info-card-icon amber">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Agenda</h3>
                    <?php if (!empty($agendas)): ?>
                        <?php $agd = $agendas[0]; ?>
                        <p><?= e(truncate($agd['judul'], 60)) ?></p>
                        <span class="card-date"><i class="far fa-clock"></i> <?= format_datetime_indo($agd['tanggal_mulai']) ?></span>
                        <a href="<?= BASE_URL ?>akademik.php" class="card-link">Lihat Detail &rarr;</a>
                    <?php else: ?>
                        <p>Belum ada agenda mendatang.</p>
                    <?php endif; ?>
                </div>

                <!-- Prestasi -->
                <div class="info-card fade-up">
                    <div class="info-card-icon green">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>Prestasi</h3>
                    <?php if (!empty($achievements)): ?>
                        <?php $ach = $achievements[0]; ?>
                        <p><?= e(truncate($ach['judul'], 60)) ?></p>
                        <span class="card-date"><i class="far fa-calendar"></i> <?= $ach['tahun'] ?></span>
                        <a href="<?= BASE_URL ?>prestasi.php" class="card-link">Lihat Semua &rarr;</a>
                    <?php else: ?>
                        <p>Belum ada data prestasi.</p>
                    <?php endif; ?>
                </div>

                <!-- Galeri -->
                <div class="info-card fade-up">
                    <div class="info-card-icon purple">
                        <i class="fas fa-images"></i>
                    </div>
                    <h3>Galeri</h3>
                    <?php if (!empty($gallery_items)): ?>
                        <?php $gal = $gallery_items[0]; ?>
                        <p><?= e(truncate($gal['judul'], 60)) ?></p>
                        <span class="card-date"><i class="far fa-calendar"></i> <?= format_date_indo($gal['created_at']) ?></span>
                        <a href="<?= BASE_URL ?>galeri.php" class="card-link">Lihat Semua &rarr;</a>
                    <?php else: ?>
                        <p>Belum ada dokumentasi kegiatan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== TENTANG SEKOLAH ===== -->
    <section class="about-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-image fade-up">
                    <img src="<?= e(asset_url('assets/images/about-school.svg')) ?>" alt="Kegiatan Siswa SMK INFOKOM BOGOR" loading="lazy" onerror="this.parentElement.style.background='linear-gradient(135deg,#1a3a5c,#2c5f8a)'; this.style.display='none';">
                </div>
                <div class="about-content fade-up">
                    <h2>Tentang SMK INFOKOM BOGOR</h2>
                    <p><?= e(truncate($school['deskripsi'] ?? '[ISI DESKRIPSI LENGKAP SEKOLAH]', 200)) ?></p>
                    <div class="about-features">
                        <div class="about-feature"><i class="fas fa-check-circle"></i> Kurikulum Merdeka</div>
                        <div class="about-feature"><i class="fas fa-check-circle"></i> Fasilitas Modern</div>
                        <div class="about-feature"><i class="fas fa-check-circle"></i> Guru Bersertifikasi</div>
                        <div class="about-feature"><i class="fas fa-check-circle"></i> Kerjasama Industri</div>
                        <div class="about-feature"><i class="fas fa-check-circle"></i> Program Magang</div>
                        <div class="about-feature"><i class="fas fa-check-circle"></i> Sertifikasi Kompetensi</div>
                    </div>
                    <a href="<?= BASE_URL ?>profil.php" class="btn btn-outline">Selengkapnya <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== STATISTIK ===== -->
    <?php if (!empty($stats)): ?>
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <?php foreach ($stats as $stat): ?>
                <div class="stat-item">
                    <div class="stat-icon"><i class="fas fa-<?= e($stat['icon'] ?? 'star') ?>"></i></div>
                    <div class="stat-number" data-target="<?= (int)$stat['value'] ?>" data-suffix="+"><?= number_format((int)$stat['value']) ?></div>
                    <div class="stat-label"><?= e($stat['label']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== PROGRAM KEAHLIAN ===== -->
    <?php if (!empty($programs)): ?>
    <section class="programs-section">
        <div class="container">
            <div class="section-title">
                <h2>Program Keahlian</h2>
                <p>Pilihan jurusan yang mempersiapkan siswa siap kerja dan berwirausaha</p>
            </div>
            <div class="programs-grid">
                <?php foreach ($programs as $prog): ?>
                <div class="program-card fade-up">
                    <div class="program-card-img">
                        <?php if ($prog['gambar']): ?>
                        <img src="<?= e(asset_url($prog['gambar'])) ?>" alt="<?= e($prog['nama_program']) ?>" loading="lazy">
                        <?php else: ?>
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--gray-100);color:var(--gray-400);font-size:2.5rem;">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="program-card-body">
                        <h3><?= e($prog['nama_program']) ?></h3>
                        <p><?= e(truncate($prog['deskripsi'] ?? '', 100)) ?></p>
                        <a href="<?= BASE_URL ?>akademik.php" class="btn btn-outline btn-sm">Selengkapnya</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== BERITA TERBARU ===== -->
    <?php if (!empty($news)): ?>
    <section class="news-section">
        <div class="container">
            <div class="section-title">
                <h2>Berita Terbaru</h2>
                <p>Informasi dan kegiatan terkini dari SMK INFOKOM BOGOR</p>
            </div>
            <div class="news-grid">
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
                        </div>
                        <h3><a href="<?= BASE_URL ?>detail-berita.php?slug=<?= e($item['slug']) ?>"><?= e($item['judul']) ?></a></h3>
                        <p><?= e(truncate(strip_tags($item['ringkasan'] ?? $item['isi']), 120)) ?></p>
                        <a href="<?= BASE_URL ?>detail-berita.php?slug=<?= e($item['slug']) ?>" class="read-more">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <div class="news-more">
                <a href="<?= BASE_URL ?>berita.php" class="btn btn-outline">Lihat Berita Lainnya <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== PRESTASI ===== -->
    <?php if (!empty($achievements)): ?>
    <section class="prestasi-section">
        <div class="container">
            <div class="section-title">
                <h2>Prestasi Gemilang</h2>
                <p>Pencapaian membanggakan siswa dan guru SMK INFOKOM BOGOR</p>
            </div>
            <div class="prestasi-grid">
                <?php foreach ($achievements as $ach): ?>
                <div class="prestasi-card fade-up">
                    <div class="prestasi-card-img">
                        <?php if ($ach['gambar']): ?>
                        <img src="<?= e(asset_url($ach['gambar'])) ?>" alt="<?= e($ach['judul']) ?>" loading="lazy">
                        <?php else: ?>
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--gray-100);color:var(--secondary);font-size:1.8rem;">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="prestasi-card-content">
                        <span class="badge-level badge-<?= strtolower(str_replace(['/',' '], '-', $ach['tingkat'])) ?>"><?= e($ach['tingkat']) ?></span>
                        <h4><?= e($ach['judul']) ?></h4>
                        <p class="prestasi-year"><i class="far fa-calendar"></i> Tahun <?= $ach['tahun'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="news-more">
                <a href="<?= BASE_URL ?>prestasi.php" class="btn btn-outline">Lihat Semua Prestasi <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== GALERI KEGIATAN ===== -->
    <?php if (!empty($gallery_items)): ?>
    <section class="gallery-section">
        <div class="container">
            <div class="section-title">
                <h2>Galeri Kegiatan</h2>
                <p>Dokumentasi berbagai kegiatan di SMK INFOKOM BOGOR</p>
            </div>
            <div class="gallery-grid">
                <?php foreach ($gallery_items as $gal): ?>
                <div class="gallery-item fade-up" data-src="<?= e(asset_url($gal['gambar'])) ?>" data-title="<?= e($gal['judul']) ?>">
                    <img src="<?= e(asset_url($gal['gambar'])) ?>" alt="<?= e($gal['judul']) ?>" loading="lazy">
                    <div class="gallery-item-overlay">
                        <i class="fas fa-search-plus"></i>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="gallery-more">
                <a href="<?= BASE_URL ?>galeri.php" class="btn btn-outline">Lihat Semua Galeri <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== PPDB CTA ===== -->
    <?php if (get_setting('ppdb_open') == '1'): ?>
    <section class="ppdb-cta-section">
        <div class="container">
            <h2>Penerimaan Peserta Didik Baru</h2>
            <p>Tahun Ajaran <?= e(get_setting('ppdb_tahun_ajaran')) ?> telah dibuka. Segera daftarkan diri Anda dan bergabung bersama SMK INFOKOM BOGOR!</p>
            <div class="ppdb-cta-buttons">
                <a href="<?= BASE_URL ?>ppdb.php" class="btn btn-primary btn-lg">Daftar Sekarang <i class="fas fa-pen"></i></a>
                <a href="<?= BASE_URL ?>cek-status.php" class="btn btn-secondary btn-lg">Cek Status Pendaftaran <i class="fas fa-search"></i></a>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>