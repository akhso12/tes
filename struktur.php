<?php
$page_title = 'Struktur Organisasi | SMK INFOKOM BOGOR';
$meta_desc = 'Struktur organisasi SMK INFOKOM BOGOR beserta komposisi tim kepemimpinan dan unit pendukung sekolah.';
$body_class = 'page-structure';

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
                <span class="current">Struktur Organisasi</span>
            </nav>
        </div>
    </div>

    <section class="inner-page-section">
        <div class="container narrow-content">
            <div class="section-title left-title">
                <h2>Struktur Organisasi</h2>
            </div>

            <div class="content-card">
                <div class="org-tree">
                    <div class="node root-node">
                        <div class="node-icon"><i class="fas fa-user-tie"></i></div>
                        <div class="node-name">Kepala Sekolah</div>
                        <div class="node-role">Drs. H. Mulyadi, M.Pd.</div>
                    </div>

                    <div class="node-group">
                        <div class="node">
                            <div class="node-icon"><i class="fas fa-user"></i></div>
                            <div class="node-name">Wakil Kepala Sekolah</div>
                            <div class="node-role">Bidang Kurikulum</div>
                        </div>
                        <div class="node">
                            <div class="node-icon"><i class="fas fa-user"></i></div>
                            <div class="node-name">Wakil Kepala Sekolah</div>
                            <div class="node-role">Bidang Kesiswaan</div>
                        </div>
                        <div class="node">
                            <div class="node-icon"><i class="fas fa-user"></i></div>
                            <div class="node-name">Wakil Kepala Sekolah</div>
                            <div class="node-role">Bidang Sarana Prasarana</div>
                        </div>
                        <div class="node">
                            <div class="node-icon"><i class="fas fa-user"></i></div>
                            <div class="node-name">Wakil Kepala Sekolah</div>
                            <div class="node-role">Bidang Hubungan Masyarakat</div>
                        </div>
                    </div>
                </div>

                <p class="note">Struktur organisasi ini bersifat umum dan dapat diperbarui sesuai dengan kebijakan sekolah serta data resmi yang dimiliki oleh admin.</p>
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
    padding: 32px;
    box-shadow: var(--shadow-sm);
}
.org-tree {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}
.node-group {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 18px;
    width: 100%;
    padding-top: 20px;
    border-top: 2px dashed var(--gray-300);
}
.node {
    min-width: 200px;
    background: #f9fbff;
    border: 2px solid var(--primary);
    border-radius: var(--radius-md);
    padding: 18px 16px;
    text-align: center;
    box-shadow: var(--shadow-sm);
}
.root-node {
    background: #fffaf0;
    border-color: var(--secondary);
}
.node-icon {
    width: 52px;
    height: 52px;
    margin: 0 auto 10px;
    border-radius: 50%;
    background: var(--primary);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}
.root-node .node-icon { background: var(--secondary); color: var(--gray-900); }
.node-name { font-weight: 700; font-size: 0.92rem; }
.node-role { color: var(--gray-500); font-size: 0.82rem; margin-top: 6px; }
.note { margin-top: 22px; color: var(--gray-500); text-align: center; }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
