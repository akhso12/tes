<?php
/**
 * Admin Layout Header - Modern Sidebar Design
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

$menu_items = [
    ['url' => 'dashboard.php', 'icon' => 'th-large', 'label' => 'Dashboard', 'page' => 'dashboard'],
    ['url' => 'profil-admin.php', 'icon' => 'school', 'label' => 'Profil Sekolah', 'page' => 'profil-admin'],
    ['url' => 'berita-admin.php', 'icon' => 'newspaper', 'label' => 'Berita', 'page' => 'berita-admin'],
    ['url' => 'program-admin.php', 'icon' => 'graduation-cap', 'label' => 'Program Keahlian', 'page' => 'program-admin'],
    ['url' => 'galeri-admin.php', 'icon' => 'images', 'label' => 'Galeri', 'page' => 'galeri-admin'],
    ['url' => 'prestasi-admin.php', 'icon' => 'trophy', 'label' => 'Prestasi', 'page' => 'prestasi-admin'],
    ['url' => 'fasilitas-admin.php', 'icon' => 'building', 'label' => 'Fasilitas', 'page' => 'fasilitas-admin'],
    ['url' => 'agenda-admin.php', 'icon' => 'calendar-alt', 'label' => 'Agenda', 'page' => 'agenda-admin'],
    ['url' => 'pengumuman-admin.php', 'icon' => 'bullhorn', 'label' => 'Pengumuman', 'page' => 'pengumuman-admin'],
    ['url' => 'ppdb-admin.php', 'icon' => 'user-plus', 'label' => 'Data PPDB', 'page' => 'ppdb-admin'],
    ['url' => 'kontak-admin.php', 'icon' => 'envelope', 'label' => 'Pesan Kontak', 'page' => 'kontak-admin'],
    ['url' => 'pengaturan.php', 'icon' => 'cog', 'label' => 'Pengaturan', 'page' => 'pengaturan'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Dashboard' ?> | Admin SMK INFOKOM BOGOR</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">

<!-- Mobile Overlay -->
<div class="admin-overlay" id="adminOverlay"></div>

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div class="brand-text">
            <span class="brand-name">SMK INFOKOM</span>
            <span class="brand-sub">ADMIN PANEL</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($menu_items as $item): ?>
        <a href="<?= $item['url'] ?>" class="nav-item<?= ($current_page === $item['page']) ? ' active' : '' ?>">
            <i class="fas fa-<?= $item['icon'] ?>"></i>
            <span><?= $item['label'] ?></span>
        </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
    </div>
</aside>

<!-- Main Content -->
<div class="admin-main">
    <!-- Top Bar -->
    <header class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <div class="topbar-title">
            <h2><?= $page_title ?? 'Dashboard' ?></h2>
        </div>

        <div class="topbar-right">
            <a href="../index.php" target="_blank" class="topbar-link" title="Lihat Website">
                <i class="fas fa-external-link-alt"></i>
            </a>
            <div class="topbar-user">
                <div class="user-avatar"><?= strtoupper(substr($admin_name, 0, 2)) ?></div>
                <span class="user-name"><?= htmlspecialchars($admin_name) ?></span>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="admin-content">