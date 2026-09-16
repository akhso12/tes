<?php
/**
 * Kontak
 */
$page_title = 'Kontak | SMK INFOKOM BOGOR';
$meta_desc = 'Hubungi SMK INFOKOM BOGOR. Alamat, telepon, email, dan peta lokasi sekolah.';
$body_class = 'page-kontak';

require_once __DIR__ . '/includes/functions.php';

$sent = false;
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error_msg = 'Token keamanan tidak valid.';
    } else {
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subjek = trim($_POST['subjek'] ?? '');
        $pesan = trim($_POST['pesan'] ?? '');

        if (empty($nama) || empty($email) || empty($pesan)) {
            $error_msg = 'Nama, email, dan pesan wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = 'Format email tidak valid.';
        } else {
            try {
                db_insert('contact_messages', [
                    'nama'    => $nama,
                    'email'   => $email,
                    'subjek'  => $subjek,
                    'pesan'   => $pesan,
                ]);
                $sent = true;
                set_flash('success', 'Pesan Anda berhasil dikirim. Kami akan segera merespons.');
            } catch (Exception $e) {
                $error_msg = 'Terjadi kesalahan. Silakan coba lagi.';
                error_log("Contact Error: " . $e->getMessage());
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main id="main-content">
    <div class="breadcrumb-bar">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Beranda</a>
                <span class="separator">/</span>
                <span class="current">Kontak</span>
            </nav>
        </div>
    </div>

    <section class="kontak-section">
        <div class="container">
            <div class="section-title">
                <h2>Hubungi Kami</h2>
                <p>Jangan ragu untuk menghubungi kami jika ada pertanyaan</p>
            </div>

            <div class="kontak-layout">
                <!-- Contact Info -->
                <div class="kontak-info">
                    <div class="kontak-info-card">
                        <h3>Informasi Kontak</h3>
                        <ul class="kontak-list">
                            <li>
                                <div class="kontak-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <div>
                                    <strong>Alamat</strong>
                                    <p><?= e($school['alamat'] ?? '[ALAMAT SEKOLAH]') ?></p>
                                </div>
                            </li>
                            <li>
                                <div class="kontak-icon"><i class="fas fa-phone"></i></div>
                                <div>
                                    <strong>Telepon</strong>
                                    <p><?= e($school['telepon'] ?? '[NOMOR TELEPON]') ?></p>
                                </div>
                            </li>
                            <li>
                                <div class="kontak-icon"><i class="fas fa-envelope"></i></div>
                                <div>
                                    <strong>Email</strong>
                                    <p><?= e($school['email'] ?? '[EMAIL SEKOLAH]') ?></p>
                                </div>
                            </li>
                            <li>
                                <div class="kontak-icon"><i class="fas fa-clock"></i></div>
                                <div>
                                    <strong>Jam Operasional</strong>
                                    <p><?= e($school['jam_operasional'] ?? 'Senin - Jumat (07:00 - 16:00)') ?></p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Social Media -->
                    <div class="kontak-info-card">
                        <h3>Ikuti Kami</h3>
                        <div class="footer-social" style="margin-top:12px;">
                            <?php
                            $socials = db_fetch_all("SELECT * FROM social_media WHERE is_active = 1 ORDER BY sort_order ASC");
                            foreach ($socials as $social):
                            ?>
                            <a href="<?= e($social['url']) ?>" target="_blank" rel="noopener" class="social-icon">
                                <i class="fab fa-<?= e($social['icon'] ?? $social['platform']) ?>"></i>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Contact Form + Map -->
                <div class="kontak-form-area">
                    <?php if ($sent): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Pesan Anda berhasil dikirim! Kami akan segera merespons.
                    </div>
                    <?php endif; ?>

                    <?php if ($error_msg): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?= e($error_msg) ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="kontak-form" data-validate>
                        <?= csrf_field() ?>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="kontak_nama">Nama <span class="required">*</span></label>
                                <input type="text" id="kontak_nama" name="nama" required value="<?= e($_POST['nama'] ?? '') ?>" placeholder="Nama lengkap">
                            </div>
                            <div class="form-group">
                                <label for="kontak_email">Email <span class="required">*</span></label>
                                <input type="email" id="kontak_email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="email@contoh.com">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="kontak_subjek">Subjek</label>
                            <input type="text" id="kontak_subjek" name="subjek" value="<?= e($_POST['subjek'] ?? '') ?>" placeholder="Subjek pesan">
                        </div>
                        <div class="form-group">
                            <label for="kontak_pesan">Pesan <span class="required">*</span></label>
                            <textarea id="kontak_pesan" name="pesan" rows="5" required placeholder="Tulis pesan Anda..."><?= e($_POST['pesan'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane"></i> Kirim Pesan
                        </button>
                    </form>

                    <!-- Google Maps -->
                    <div class="kontak-map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126907.06578914948!2d106.7297724!3d-6.5950345!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c5d2e602b5b5%3A0x25a12f0f0e8bead2!2sBogor%2C%20West%20Java!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid"
                            width="100%"
                            height="350"
                            style="border:0;border-radius:var(--radius-md);"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Lokasi SMK INFOKOM BOGOR">
                        </iframe>
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
.kontak-section { padding: var(--section-padding); }
.kontak-layout { display: grid; grid-template-columns: 380px 1fr; gap: 40px; }

.kontak-info-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    padding: 28px;
    margin-bottom: 20px;
}
.kontak-info-card h3 { font-size: 1.1rem; margin-bottom: 20px; color: var(--primary); }
.kontak-list li {
    display: flex;
    gap: 16px;
    margin-bottom: 20px;
    align-items: flex-start;
}
.kontak-list li:last-child { margin-bottom: 0; }
.kontak-icon {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-full);
    background: var(--primary);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1rem;
}
.kontak-list strong { display: block; font-size: 0.9rem; color: var(--gray-700); margin-bottom: 2px; }
.kontak-list p { font-size: 0.9rem; color: var(--gray-500); }

.kontak-form-area {}
.kontak-form {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    padding: 28px;
    margin-bottom: 24px;
}
.kontak-map { border-radius: var(--radius-md); overflow: hidden; }

@media (max-width: 900px) {
    .kontak-layout { grid-template-columns: 1fr; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>