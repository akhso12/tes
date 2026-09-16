<?php
/**
 * Footer Template
 * SMK INFOKOM BOGOR
 */

$socials = db_fetch_all("SELECT * FROM social_media WHERE is_active = 1 ORDER BY sort_order ASC");
?>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Tentang Sekolah -->
            <div class="footer-col">
                <div class="footer-brand">
                    <img src="<?= e(asset_url($school['logo'] ?? 'assets/images/logo.svg')) ?>" alt="Logo" class="footer-logo" onerror="this.style.display='none'">
                    <h3 class="footer-title">SMK INFOKOM BOGOR</h3>
                </div>
                <p class="footer-desc"><?= e(truncate($school['tagline'] ?? 'Sekolah Menengah Kejuruan unggulan di Bogor.', 120)) ?></p>
                <div class="footer-social">
                    <?php foreach ($socials as $social): ?>
                        <?php if (!empty($social['url']) && $social['url'] !== "#"): ?><a href="<?= e($social['url']) ?>" target="_blank" rel="noopener" aria-label="<?= e($social['platform']) ?>" title="<?= e($social['platform']) ?>">
                            <i class="fab fa-<?= e($social['icon'] ?? $social['platform']) ?>"></i>
                        </a><?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Navigasi -->
            <div class="footer-col">
                <h4 class="footer-heading">Navigasi</h4>
                <ul class="footer-links">
                    <li><a href="<?= BASE_URL ?>">Beranda</a></li>
                    <li><a href="<?= BASE_URL ?>profil.php">Profil Sekolah</a></li>
                    <li><a href="<?= BASE_URL ?>sejarah.php">Sejarah</a></li>
                    <li><a href="<?= BASE_URL ?>visi-misi.php">Visi & Misi</a></li>
                    <li><a href="<?= BASE_URL ?>sambutan.php">Sambutan</a></li>
                    <li><a href="<?= BASE_URL ?>akademik.php">Akademik</a></li>
                </ul>
            </div>

            <!-- Informasi -->
            <div class="footer-col">
                <h4 class="footer-heading">Informasi</h4>
                <ul class="footer-links">
                    <li><a href="<?= BASE_URL ?>guru.php">Guru</a></li>
                    <li><a href="<?= BASE_URL ?>staff.php">Staff</a></li>
                    <li><a href="<?= BASE_URL ?>ppdb.php">PPDB Online</a></li>
                    <li><a href="<?= BASE_URL ?>cek-status.php">Cek Status PPDB</a></li>
                    <li><a href="<?= BASE_URL ?>berita.php">Berita</a></li>
                    <li><a href="<?= BASE_URL ?>galeri.php">Galeri</a></li>
                </ul>
            </div>

            <!-- Kontak -->
            <div class="footer-col">
                <h4 class="footer-heading">Kontak</h4>
                <ul class="footer-contact">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= e($school['alamat'] ?? '[ALAMAT SEKOLAH]') ?></span>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span><?= e($school['telepon'] ?? '[NOMOR TELEPON]') ?></span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span><?= e($school['email'] ?? '[EMAIL SEKOLAH]') ?></span>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <span><?= e($school['jam_operasional'] ?? 'Senin - Jumat (07:00 - 16:00)') ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> SMK INFOKOM BOGOR. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- JavaScript -->
<script src="<?= BASE_URL ?>assets/js/script.js"></script>
<script src="<?= BASE_URL ?>assets/js/admin.js"></script>
</body>
</html>