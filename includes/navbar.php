<?php
/**
 * Navbar Template
 * SMK INFOKOM BOGOR
 */

$nav_items = [
    ['url' => BASE_URL, 'label' => 'Beranda', 'page' => 'index'],
    ['url' => BASE_URL . 'profil.php', 'label' => 'Profil', 'page' => 'profil'],
    ['url' => BASE_URL . 'sejarah.php', 'label' => 'Sejarah', 'page' => 'sejarah'],
    ['url' => BASE_URL . 'visi-misi.php', 'label' => 'Visi & Misi', 'page' => 'visi-misi'],
    ['url' => BASE_URL . 'sambutan.php', 'label' => 'Sambutan', 'page' => 'sambutan'],
    ['url' => BASE_URL . 'struktur.php', 'label' => 'Struktur', 'page' => 'struktur'],
    ['url' => BASE_URL . 'akademik.php', 'label' => 'Akademik', 'page' => 'akademik'],
    ['url' => BASE_URL . 'fasilitas.php', 'label' => 'Fasilitas', 'page' => 'fasilitas'],
    ['url' => BASE_URL . 'berita.php', 'label' => 'Berita', 'page' => 'berita'],
    ['url' => BASE_URL . 'prestasi.php', 'label' => 'Prestasi', 'page' => 'prestasi'],
    ['url' => BASE_URL . 'galeri.php', 'label' => 'Galeri', 'page' => 'galeri'],
    ['url' => BASE_URL . 'ppdb.php', 'label' => 'PPDB', 'page' => 'ppdb'],
    ['url' => BASE_URL . 'cek-status.php', 'label' => 'Cek Status', 'page' => 'cek-status'],
    ['url' => BASE_URL . 'kontak.php', 'label' => 'Kontak', 'page' => 'kontak'],
];

$current_page = basename($_SERVER['PHP_SELF'], '.php');
if ($current_page === '') $current_page = 'index';
?>

<nav class="navbar" id="navbar">
    <div class="container navbar-container">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>" class="navbar-brand">
            <div class="brand-logo">
                <img src="<?= e(asset_url($school['logo'] ?? 'assets/images/logo.svg')) ?>" alt="Logo SMK INFOKOM BOGOR" onerror="this.style.display='none'">
                <span class="brand-text">
                    <span class="brand-name">SMK INFOKOM</span>
                    <span class="brand-sub">BOGOR</span>
                </span>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <ul class="navbar-nav desktop-nav">
            <?php foreach ($nav_items as $item): ?>
                <li class="nav-item<?= ($current_page === $item['page']) ? ' active' : '' ?>">
                    <a href="<?= $item['url'] ?>" class="nav-link"><?= e($item['label']) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Mobile Hamburger -->
        <button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
    </div>

    <!-- Mobile Navigation -->
    <div class="mobile-nav" id="mobileNav">
        <ul class="mobile-nav-list">
            <?php foreach ($nav_items as $item): ?>
                <li class="mobile-nav-item<?= ($current_page === $item['page']) ? ' active' : '' ?>">
                    <a href="<?= $item['url'] ?>" class="mobile-nav-link">
                        <?= e($item['label']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<!-- Overlay for mobile nav -->
<div class="nav-overlay" id="navOverlay"></div>