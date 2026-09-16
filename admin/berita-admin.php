<?php
/**
 * Admin - Kelola Berita
 */
$page_title = 'Kelola Berita';
require_once __DIR__ . '/../includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Handle Delete
if ($action === 'delete' && $id > 0) {
    $news = db_fetch("SELECT * FROM news WHERE id = :id", [':id' => $id]);
    if ($news) {
        if ($news['thumbnail']) delete_file($news['thumbnail']);
        db_delete('news', 'id = :id', [':id' => $id]);
        set_flash('success', 'Berita berhasil dihapus.');
    }
    header("Location: berita-admin.php");
    exit;
}

// Handle Save (Create/Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
        header("Location: berita-admin.php?action=" . ($action === 'create' ? 'create' : "edit&id=$id"));
        exit;
    }

    $judul = trim($_POST['judul'] ?? '');
    $kategori_id = (int)($_POST['kategori_id'] ?? 0);
    $ringkasan = trim($_POST['ringkasan'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    $penulis = trim($_POST['penulis'] ?? 'Admin');
    $status = $_POST['status'] ?? 'draft';
    $tanggal_terbit = $_POST['tanggal_terbit'] ?? date('Y-m-d');

    if (empty($judul) || empty($isi)) {
        set_flash('error', 'Judul dan isi berita wajib diisi.');
        header("Location: berita-admin.php?action=" . ($action === 'create' ? 'create' : "edit&id=$id"));
        exit;
    }

    // Generate slug
    $slug = strtolower(preg_replace('/[^a-z0-9\-]/', '-', preg_replace('/\s+/', '-', $judul)));
    $slug .= '-' . time();

    // Handle thumbnail
    $thumbnail = null;
    if ($action === 'edit') {
        $existing = db_fetch("SELECT thumbnail FROM news WHERE id = :id", [':id' => $id]);
        $thumbnail = $existing['thumbnail'] ?? null;
    }

    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $result = upload_image($_FILES['thumbnail'], 'news');
        if ($result['success']) {
            if ($thumbnail) delete_file($thumbnail);
            $thumbnail = $result['path'];
        }
    }

    try {
        if ($action === 'create') {
            db_insert('news', [
                'kategori_id'    => $kategori_id ?: null,
                'judul'          => $judul,
                'slug'           => $slug,
                'ringkasan'      => $ringkasan,
                'isi'            => $isi,
                'thumbnail'      => $thumbnail,
                'penulis'        => $penulis,
                'status'         => $status,
                'tanggal_terbit' => $tanggal_terbit,
            ]);
            set_flash('success', 'Berita berhasil ditambahkan.');
        } else {
            db_update('news', [
                'kategori_id'    => $kategori_id ?: null,
                'judul'          => $judul,
                'ringkasan'      => $ringkasan,
                'isi'            => $isi,
                'thumbnail'      => $thumbnail,
                'penulis'        => $penulis,
                'status'         => $status,
                'tanggal_terbit' => $tanggal_terbit,
            ], 'id = :id', [':id' => $id]);
            set_flash('success', 'Berita berhasil diperbarui.');
        }
    } catch (Exception $e) {
        set_flash('error', 'Gagal menyimpan: ' . $e->getMessage());
    }

    header("Location: berita-admin.php");
    exit;
}

// LIST VIEW
if ($action === 'list') {
    $per_page = 15;
    $page = max(1, (int)($_GET['page'] ?? 1));
    $offset = ($page - 1) * $per_page;
    $search = $_GET['search'] ?? '';

    $where = "1=1";
    $params = [];
    if ($search) {
        $where .= " AND (n.judul LIKE :search OR n.isi LIKE :search2)";
        $params[':search'] = "%$search%";
        $params[':search2'] = "%$search%";
    }

    $total = db_count("news n", $where, $params);
    $news_list = db_fetch_all(
        "SELECT n.*, nc.nama_kategori FROM news n LEFT JOIN news_categories nc ON n.kategori_id = nc.id 
         WHERE $where ORDER BY n.created_at DESC LIMIT $per_page OFFSET $offset",
        $params
    );
    $categories = db_fetch_all("SELECT * FROM news_categories ORDER BY nama_kategori ASC");
    ?>

    <div class="toolbar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Cari berita..." value="<?= htmlspecialchars($search) ?>" onchange="window.location='?search='+encodeURIComponent(this.value)">
        </div>
        <a href="?action=create" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Berita</a>
    </div>

    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <?php if (!empty($news_list)): ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Thumbnail</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($news_list as $n): ?>
                        <tr>
                            <td>
                                <?php if ($n['thumbnail']): ?>
                                <img src="../assets/uploads/<?= htmlspecialchars($n['thumbnail']) ?>" alt="" style="width:60px;height:40px;object-fit:cover;border-radius:6px;">
                                <?php else: ?>
                                <div style="width:60px;height:40px;background:var(--admin-gray-100);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--admin-gray-400);"><i class="fas fa-image"></i></div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($n['judul']) ?></strong></td>
                            <td><?= htmlspecialchars($n['nama_kategori'] ?? '-') ?></td>
                            <td><span class="badge badge-<?= $n['status'] ?>"><?= ucfirst($n['status']) ?></span></td>
                            <td><?= format_date_indo($n['tanggal_terbit'] ?? $n['created_at']) ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="../detail-berita.php?slug=<?= e($n['slug']) ?>" target="_blank" class="action-btn view" title="Lihat"><i class="fas fa-eye"></i></a>
                                    <a href="?action=edit&id=<?= $n['id'] ?>" class="action-btn edit" title="Edit"><i class="fas fa-pen"></i></a>
                                    <a href="?action=delete&id=<?= $n['id'] ?>" class="action-btn delete" title="Hapus" data-confirm="Hapus berita ini?"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?= paginate($total, $per_page, $page, 'berita-admin.php?' . ($search ? 'search=' . urlencode($search) . '&' : '')) ?>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-newspaper"></i>
                <h3>Belum ada berita</h3>
                <p>Klik tombol "Tambah Berita" untuk membuat berita pertama.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
// CREATE / EDIT FORM
} else {
    $item = null;
    $form_title = 'Tambah Berita Baru';
    if ($action === 'edit' && $id > 0) {
        $item = db_fetch("SELECT * FROM news WHERE id = :id", [':id' => $id]);
        if (!$item) { redirect('berita-admin.php'); }
        $form_title = 'Edit Berita';
    }
    $categories = db_fetch_all("SELECT * FROM news_categories ORDER BY nama_kategori ASC");
    ?>

    <a href="berita-admin.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3><i class="fas fa-pen" style="color:var(--admin-primary);margin-right:8px;"></i><?= $form_title ?></h3>
        </div>
        <div class="admin-card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <div class="form-row">
                    <div class="form-group full">
                        <label>Judul Berita *</label>
                        <input type="text" name="judul" required value="<?= htmlspecialchars($item['judul'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori_id">
                            <option value="">-- Tanpa Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (($item['kategori_id'] ?? 0) == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="draft" <?= (($item['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>>Draft</option>
                            <option value="publish" <?= (($item['status'] ?? '') === 'publish') ? 'selected' : '' ?>>Publish</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Penulis</label>
                        <input type="text" name="penulis" value="<?= htmlspecialchars($item['penulis'] ?? 'Admin') ?>">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Terbit</label>
                        <input type="date" name="tanggal_terbit" value="<?= $item['tanggal_terbit'] ?? date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Ringkasan</label>
                    <textarea name="ringkasan" rows="3"><?= htmlspecialchars($item['ringkasan'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Isi Berita *</label>
                    <textarea name="isi" rows="12" required><?= htmlspecialchars($item['isi'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Thumbnail</label>
                    <?php if (!empty($item['thumbnail'])): ?>
                    <div class="image-preview" style="margin-bottom:12px;">
                        <img src="../assets/uploads/<?= htmlspecialchars($item['thumbnail']) ?>" alt="Current Thumbnail">
                    </div>
                    <?php endif; ?>
                    <input type="file" name="thumbnail" accept="image/*" data-preview="thumbPreview">
                    <div id="thumbPreview" class="image-preview" style="display:none;margin-top:12px;"></div>
                    <small style="color:var(--admin-gray-400);display:block;margin-top:4px;">JPG, PNG, GIF, WEBP. Maks 5MB.</small>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
    <?php
}
?>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>