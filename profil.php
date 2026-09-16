<?php
/**
 * Profil Sekolah - Halaman Utama dengan Sidebar
 */
$page_title = 'Profil Sekolah | SMK INFOKOM BOGOR';
$meta_desc = 'Profil lengkap SMK INFOKOM BOGOR termasuk sejarah, visi misi, sambutan kepala sekolah, struktur organisasi, dan fasilitas.';
$body_class = 'page-profil';

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Default aktif: Sejarah
$active_menu = $_GET['menu'] ?? 'sejarah';

$principal = db_fetch("SELECT * FROM principal_message WHERE is_active = 1 LIMIT 1");
?>

<main id="main-content">
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Beranda</a>
                <span class="separator">/</span>
                <span class="current">Profil Sekolah</span>
            </nav>
        </div>
    </div>

    <section class="profil-section">
        <div class="container">
            <div class="profil-layout">
                <!-- Sidebar -->
                <aside class="profil-sidebar">
                    <h3 class="sidebar-title">Menu Profil</h3>
                    <ul class="sidebar-menu">
                        <li class="<?= ($active_menu === 'sejarah') ? 'active' : '' ?>">
                            <a href="?menu=sejarah"><i class="fas fa-history"></i> Sejarah Sekolah</a>
                        </li>
                        <li class="<?= ($active_menu === 'visi-misi') ? 'active' : '' ?>">
                            <a href="?menu=visi-misi"><i class="fas fa-bullseye"></i> Visi & Misi</a>
                        </li>
                        <li class="<?= ($active_menu === 'sambutan') ? 'active' : '' ?>">
                            <a href="?menu=sambutan"><i class="fas fa-user-tie"></i> Sambutan Kepala Sekolah</a>
                        </li>
                        <li class="<?= ($active_menu === 'struktur') ? 'active' : '' ?>">
                            <a href="?menu=struktur"><i class="fas fa-sitemap"></i> Struktur Organisasi</a>
                        </li>
                        <li class="<?= ($active_menu === 'fasilitas') ? 'active' : '' ?>">
                            <a href="?menu=fasilitas"><i class="fas fa-building"></i> Fasilitas Sekolah</a>
                        </li>
                    </ul>
                </aside>

                <!-- Content Area -->
                <div class="profil-content">
                    <?php switch ($active_menu): case 'sejarah': ?>
                        <h2 class="content-title">Sejarah Sekolah</h2>
                        <div class="content-body">
                            <p>[ISI SEJARAH SEKOLAH]</p>
                            <p>Silakan lengkapi informasi sejarah berdirinya SMK INFOKOM BOGOR melalui panel admin atau langsung mengedit database.</p>
                            <div class="profil-image-placeholder">
                                <i class="fas fa-school"></i>
                                <p>Foto Sekolah</p>
                            </div>
                        </div>
                    <?php break; case 'visi-misi': ?>
                        <h2 class="content-title">Visi & Misi</h2>
                        <div class="content-body">
                            <div class="visi-card">
                                <div class="visi-icon"><i class="fas fa-eye"></i></div>
                                <h3>Visi</h3>
                                <p>[ISI VISI SEKOLAH]</p>
                            </div>
                            <div class="misi-card">
                                <div class="misi-icon"><i class="fas fa-list-ol"></i></div>
                                <h3>Misi</h3>
                                <ol class="misi-list">
                                    <li>[ISI MISI 1]</li>
                                    <li>[ISI MISI 2]</li>
                                    <li>[ISI MISI 3]</li>
                                    <li>[ISI MISI 4]</li>
                                    <li>[ISI MISI 5]</li>
                                </ol>
                            </div>
                        </div>
                    <?php break; case 'sambutan': ?>
                        <h2 class="content-title">Sambutan Kepala Sekolah</h2>
                        <div class="content-body">
                            <?php if ($principal): ?>
                            <div class="sambutan-layout">
                                <div class="sambutan-foto">
                                    <?php if ($principal['foto']): ?>
                                    <img src="<?= e(asset_url($principal['foto'])) ?>" alt="<?= e($principal['nama_kepsek']) ?>">
                                    <?php else: ?>
                                    <div class="foto-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="sambutan-text">
                                    <h3><?= e($principal['nama_kepsek']) ?></h3>
                                    <p class="jabatan"><?= e($principal['jabatan']) ?></p>
                                    <div class="sambutan-is"><?= nl2br(e($principal['sambutan'])) ?></div>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-user-tie"></i>
                                <h3>Belum ada sambutan kepala sekolah</h3>
                                <p>Data akan ditampilkan setelah diisi oleh administrator.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php break; case 'struktur': ?>
                        <h2 class="content-title">Struktur Organisasi</h2>
                        <div class="content-body">
                            <div class="struktur-tree">
                                <div class="struktur-node level-0">
                                    <div class="node-photo"><i class="fas fa-user-tie"></i></div>
                                    <div class="node-name">[NAMA KEPALA SEKOLAH]</div>
                                    <div class="node-jabatan">Kepala Sekolah</div>
                                </div>
                                <div class="struktur-children">
                                    <div class="struktur-node level-1">
                                        <div class="node-photo"><i class="fas fa-user"></i></div>
                                        <div class="node-name">[NAMA WAKASEK]</div>
                                        <div class="node-jabatan">Waka Kurikulum</div>
                                    </div>
                                    <div class="struktur-node level-1">
                                        <div class="node-photo"><i class="fas fa-user"></i></div>
                                        <div class="node-name">[NAMA WAKASEK]</div>
                                        <div class="node-jabatan">Waka Kesiswaan</div>
                                    </div>
                                    <div class="struktur-node level-1">
                                        <div class="node-photo"><i class="fas fa-user"></i></div>
                                        <div class="node-name">[NAMA WAKASEK]</div>
                                        <div class="node-jabatan">Waka Sarpras</div>
                                    </div>
                                    <div class="struktur-node level-1">
                                        <div class="node-photo"><i class="fas fa-user"></i></div>
                                        <div class="node-name">[NAMA WAKASEK]</div>
                                        <div class="node-jabatan">Waka Humas</div>
                                    </div>
                                </div>
                            </div>
                            <p class="struktur-note">* Struktur organisasi dapat diperbarui melalui panel admin.</p>
                        </div>
                    <?php break; case 'fasilitas': ?>
                        <h2 class="content-title">Fasilitas Sekolah</h2>
                        <div class="content-body">
                            <?php
                            $facilities = db_fetch_all("SELECT * FROM facilities WHERE is_active = 1 ORDER BY sort_order ASC");
                            if (!empty($facilities)):
                            ?>
                            <div class="fasilitas-grid">
                                <?php foreach ($facilities as $fac): ?>
                                <div class="fasilitas-card">
                                    <div class="fasilitas-img">
                                        <?php if ($fac['gambar']): ?>
                                        <img src="<?= e(asset_url($fac['gambar'])) ?>" alt="<?= e($fac['nama_fasilitas']) ?>" loading="lazy">
                                        <?php else: ?>
                                        <div class="fasilitas-img-placeholder"><i class="fas fa-building"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="fasilitas-info">
                                        <h4><?= e($fac['nama_fasilitas']) ?></h4>
                                        <p><?= e($fac['deskripsi'] ?? '') ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-building"></i>
                                <h3>Belum ada data fasilitas</h3>
                                <p>Data fasilitas akan ditampilkan setelah diisi oleh administrator.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php break; default: ?>
                        <div class="empty-state">
                            <i class="fas fa-exclamation-circle"></i>
                            <h3>Halaman tidak ditemukan</h3>
                        </div>
                    <?php endswitch; ?>

                    <div class="back-link">
                        <a href="<?= BASE_URL ?>"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
/* Profil Page Specific Styles */
.breadcrumb-bar {
    background: var(--gray-50);
    padding: 14px 0;
    border-bottom: 1px solid var(--gray-200);
}
.breadcrumb-bar nav {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
}
.breadcrumb-bar a { color: var(--primary); }
.breadcrumb-bar .separator { color: var(--gray-400); }
.breadcrumb-bar .current { color: var(--gray-500); }

.profil-section { padding: var(--section-padding); }
.profil-layout { display: grid; grid-template-columns: 280px 1fr; gap: 40px; }

.profil-sidebar {
    background: var(--white);
    border-radius: var(--radius-md);
    border: 1px solid var(--gray-200);
    padding: 24px;
    height: fit-content;
    position: sticky;
    top: 92px;
}
.sidebar-title {
    font-size: 1.1rem;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--primary);
}
.sidebar-menu li a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: var(--radius-sm);
    font-weight: 500;
    color: var(--gray-600);
    transition: var(--transition-fast);
}
.sidebar-menu li a:hover { background: var(--gray-50); color: var(--primary); }
.sidebar-menu li.active a {
    background: var(--primary);
    color: var(--white);
}
.sidebar-menu li.active a i { color: var(--secondary-light); }

.content-title {
    font-size: 1.8rem;
    margin-bottom: 24px;
    padding-bottom: 12px;
    border-bottom: 3px solid var(--secondary);
    display: inline-block;
}
.content-body { line-height: 1.8; }
.content-body p { margin-bottom: 16px; }

.profil-image-placeholder {
    width: 100%;
    height: 300px;
    background: var(--gray-100);
    border-radius: var(--radius-md);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--gray-400);
    margin-top: 20px;
}
.profil-image-placeholder i { font-size: 3rem; margin-bottom: 10px; }

/* Visi Misi */
.visi-card, .misi-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    padding: 30px;
    margin-bottom: 24px;
}
.visi-icon, .misi-icon {
    width: 50px;
    height: 50px;
    border-radius: var(--radius-full);
    background: var(--primary);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    margin-bottom: 16px;
}
.visi-card h3, .misi-card h3 { font-size: 1.3rem; margin-bottom: 12px; }
.misi-list { padding-left: 20px; list-style: decimal; }
.misi-list li { margin-bottom: 10px; padding-left: 8px; }

/* Sambutan */
.sambutan-layout { display: grid; grid-template-columns: 250px 1fr; gap: 30px; align-items: start; }
.sambutan-foto img {
    width: 100%;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-md);
}
.foto-placeholder {
    width: 100%;
    aspect-ratio: 3/4;
    background: var(--gray-100);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: var(--gray-300);
}
.sambutan-text h3 { font-size: 1.4rem; margin-bottom: 4px; }
.jabatan { color: var(--secondary); font-weight: 600; margin-bottom: 20px; }
.sambutan-is { line-height: 1.9; color: var(--gray-600); }

/* Struktur Organisasi */
.struktur-tree { text-align: center; }
.struktur-node {
    display: inline-block;
    background: var(--white);
    border: 2px solid var(--primary);
    border-radius: var(--radius-md);
    padding: 20px 30px;
    margin: 8px;
    min-width: 180px;
}
.struktur-node.level-0 { border-color: var(--secondary); background: #fffbeb; }
.node-photo {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-full);
    background: var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 1.5rem;
    color: var(--gray-400);
}
.node-name { font-weight: 700; font-size: 0.95rem; }
.node-jabatan { font-size: 0.8rem; color: var(--gray-500); margin-top: 4px; }
.struktur-children {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 16px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px dashed var(--gray-300);
}
.struktur-note { text-align: center; color: var(--gray-400); font-size: 0.85rem; margin-top: 20px; }

/* Fasilitas Grid */
.fasilitas-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.fasilitas-card {
    display: flex;
    gap: 16px;
    padding: 20px;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    transition: var(--transition);
}
.fasilitas-card:hover { box-shadow: var(--shadow-md); }
.fasilitas-img {
    width: 100px;
    height: 80px;
    border-radius: var(--radius-sm);
    overflow: hidden;
    flex-shrink: 0;
    background: var(--gray-100);
}
.fasilitas-img img { width: 100%; height: 100%; object-fit: cover; }
.fasilitas-img-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-300);
    font-size: 1.5rem;
}
.fasilitas-info h4 { font-size: 1rem; margin-bottom: 6px; }
.fasilitas-info p { font-size: 0.85rem; color: var(--gray-500); }

.back-link { margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--gray-200); }
.back-link a { color: var(--primary); font-weight: 600; }
.back-link a:hover { color: var(--secondary); }

@media (max-width: 768px) {
    .profil-layout { grid-template-columns: 1fr; }
    .profil-sidebar { position: static; }
    .sidebar-menu { display: flex; flex-wrap: wrap; gap: 8px; }
    .sidebar-menu li a { padding: 8px 14px; font-size: 0.85rem; }
    .sambutan-layout { grid-template-columns: 1fr; }
    .fasilitas-grid { grid-template-columns: 1fr; }
    .struktur-children { flex-direction: column; align-items: center; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>