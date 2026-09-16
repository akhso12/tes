<?php
/**
 * Admin - Kelola Galeri
 */
$page_title = 'Kelola Galeri';
require_once __DIR__ . '/../includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $g = db_fetch("SELECT gambar FROM gallery WHERE id=:id", [':id'=>$id]);
    if ($g && $g['gambar']) delete_file($g['gambar']);
    db_delete('gallery', 'id=:id', [':id'=>$id]);
    set_flash('success', 'Foto berhasil dihapus.');
    header("Location: galeri-admin.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error','Token tidak valid.'); header("Location: galeri-admin.php?action=$action".($id?"&id=$id":"")); exit; }

    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $kategori_id = (int)($_POST['kategori_id'] ?? 0) ?: null;
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    if (empty($judul)) { set_flash('error','Judul wajib diisi.'); header("Location: galeri-admin.php?action=$action".($id?"&id=$id":"")); exit; }

    $gambar = null;
    if ($action === 'edit') { $ex = db_fetch("SELECT gambar FROM gallery WHERE id=:id", [':id'=>$id]); $gambar = $ex['gambar'] ?? null; }

    // Support multiple upload for create
    if ($action === 'create' && isset($_FILES['gambar']) && is_array($_FILES['gambar']['name'])) {
        $count = 0;
        for ($i = 0; $i < count($_FILES['gambar']['name']); $i++) {
            if ($_FILES['gambar']['error'][$i] === UPLOAD_ERR_OK) {
                $singleFile = ['name'=>$_FILES['gambar']['name'][$i], 'type'=>$_FILES['gambar']['type'][$i], 'tmp_name'=>$_FILES['gambar']['tmp_name'][$i], 'error'=>$_FILES['gambar']['error'][$i], 'size'=>$_FILES['gambar']['size'][$i]];
                $r = upload_image($singleFile, 'gallery');
                if ($r['success']) {
                    db_insert('gallery', ['kategori_id'=>$kategori_id, 'judul'=>$judul . ($count > 0 ? ' ('.($count+1).')' : ''), 'deskripsi'=>$deskripsi, 'gambar'=>$r['path'], 'sort_order'=>$sort_order + $count]);
                    $count++;
                }
            }
        }
        set_flash('success', "$count foto berhasil diupload.");
        header("Location: galeri-admin.php"); exit;
    }

    // Single file for edit
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $r = upload_image($_FILES['gambar'], 'gallery');
        if ($r['success']) { if ($gambar) delete_file($gambar); $gambar = $r['path']; }
    }

    $data = ['kategori_id'=>$kategori_id, 'judul'=>$judul, 'deskripsi'=>$deskripsi, 'gambar'=>$gambar, 'sort_order'=>$sort_order];
    if ($action === 'create') { db_insert('gallery', $data); set_flash('success','Foto berhasil ditambahkan.'); }
    else { db_update('gallery', $data, 'id=:id', [':id'=>$id]); set_flash('success','Foto berhasil diperbarui.'); }
    header("Location: galeri-admin.php"); exit;
}

$categories = db_fetch_all("SELECT * FROM gallery_categories ORDER BY nama_kategori ASC");

if ($action === 'list') {
    $gallery = db_fetch_all("SELECT g.*, gc.nama_kategori FROM gallery g LEFT JOIN gallery_categories gc ON g.kategori_id = gc.id ORDER BY g.created_at DESC");
    ?>
    <div class="toolbar">
        <span style="color:var(--admin-gray-500);font-size:0.9rem;">Total: <?= count($gallery) ?> foto</span>
        <a href="?action=create" class="btn btn-primary"><i class="fas fa-plus"></i> Upload Foto</a>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">
            <?php if (!empty($gallery)): ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;">
                <?php foreach ($gallery as $g): ?>
                <div style="position:relative;border-radius:var(--admin-radius-sm);overflow:hidden;border:1px solid var(--admin-gray-200);">
                    <img src="../assets/uploads/<?= e($g['gambar']) ?>" alt="<?= e($g['judul']) ?>" style="width:100%;height:160px;object-fit:cover;">
                    <div style="padding:10px;background:var(--admin-white);">
                        <strong style="font-size:0.85rem;display:block;margin-bottom:4px;"><?= e(truncate($g['judul'],30)) ?></strong>
                        <?php if ($g['nama_kategori']): ?><small style="color:var(--admin-gray-400);"><?= e($g['nama_kategori']) ?></small><?php endif; ?>
                    </div>
                    <div style="position:absolute;top:8px;right:8px;display:flex;gap:4px;">
                        <a href="?action=edit&id=<?= $g['id'] ?>" class="action-btn edit" style="background:white;box-shadow:0 2px 8px rgba(0,0,0,0.15);"><i class="fas fa-pen"></i></a>
                        <a href="?action=delete&id=<?= $g['id'] ?>" class="action-btn delete" style="background:white;box-shadow:0 2px 8px rgba(0,0,0,0.15);" data-confirm="Hapus foto ini?"><i class="fas fa-trash"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state"><i class="fas fa-images"></i><h3>Belum ada foto</h3></div>
            <?php endif; ?>
        </div>
    </div>
    <?php
} else {
    $item = ($action === 'edit' && $id > 0) ? db_fetch("SELECT * FROM gallery WHERE id=:id", [':id'=>$id]) : null;
    if ($action === 'edit' && !$item) { redirect('galeri-admin.php'); }
    ?>
    <a href="galeri-admin.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;"><i class="fas fa-arrow-left"></i> Kembali</a>
    <div class="admin-card">
        <div class="admin-card-header"><h3><?= $item ? 'Edit Foto' : 'Upload Foto Baru' ?></h3></div>
        <div class="admin-card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="form-group"><label>Judul *</label><input type="text" name="judul" required value="<?= e($item['judul'] ?? '') ?>"></div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori_id">
                            <option value="">-- Tanpa Kategori --</option>
                            <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (($item['kategori_id'] ?? 0) == $c['id']) ? 'selected' : '' ?>><?= e($c['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Urutan</label><input type="number" name="sort_order" min="0" value="<?= $item['sort_order'] ?? 0 ?>"></div>
                </div>
                <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="2"><?= e($item['deskripsi'] ?? '') ?></textarea></div>

                <?php if ($action === 'create'): ?>
                <div class="form-group">
                    <label>Foto (bisa pilih banyak)</label>
                    <input type="file" name="gambar[]" accept="image/*" multiple>
                    <small style="color:var(--admin-gray-400);display:block;margin-top:4px;">JPG, PNG, GIF, WEBP. Maks 5MB per file.</small>
                </div>
                <?php else: ?>
                <div class="form-group">
                    <label>Ganti Foto</label>
                    <?php if (!empty($item['gambar'])): ?><div class="image-preview" style="margin-bottom:12px;"><img src="../assets/uploads/<?= e($item['gambar']) ?>"></div><?php endif; ?>
                    <input type="file" name="gambar" accept="image/*" data-preview="galPreview">
                    <div id="galPreview" class="image-preview" style="display:none;margin-top:12px;"></div>
                </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
    <?php
}
require_once __DIR__ . '/../includes/admin-footer.php'; ?>