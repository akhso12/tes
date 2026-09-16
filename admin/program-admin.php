<?php
/**
 * Admin - Kelola Program Keahlian
 */
$page_title = 'Program Keahlian';
require_once __DIR__ . '/../includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $prog = db_fetch("SELECT gambar FROM programs WHERE id = :id", [':id' => $id]);
    if ($prog && $prog['gambar']) delete_file($prog['gambar']);
    db_delete('programs', 'id = :id', [':id' => $id]);
    set_flash('success', 'Program berhasil dihapus.');
    header("Location: program-admin.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error', 'Token tidak valid.'); header("Location: program-admin.php?action=$action" . ($id ? "&id=$id" : "")); exit; }

    $nama = trim($_POST['nama_program'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $keunggulan = trim($_POST['keunggulan'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($nama)) { set_flash('error', 'Nama program wajib diisi.'); header("Location: program-admin.php?action=$action" . ($id ? "&id=$id" : "")); exit; }

    $slug = strtolower(preg_replace('/[^a-z0-9\-]/', '-', preg_replace('/\s+/', '-', $nama)));
    $gambar = null;
    if ($action === 'edit') { $ex = db_fetch("SELECT gambar FROM programs WHERE id=:id", [':id'=>$id]); $gambar = $ex['gambar'] ?? null; }
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $r = upload_image($_FILES['gambar'], 'programs');
        if ($r['success']) { if ($gambar) delete_file($gambar); $gambar = $r['path']; }
    }

    $data = ['nama_program'=>$nama, 'slug'=>$slug, 'deskripsi'=>$deskripsi, 'keunggulan'=>$keunggulan, 'gambar'=>$gambar, 'sort_order'=>$sort_order, 'is_active'=>$is_active];
    if ($action === 'create') { db_insert('programs', $data); set_flash('success','Program berhasil ditambahkan.'); }
    else { db_update('programs', $data, 'id=:id', [':id'=>$id]); set_flash('success','Program berhasil diperbarui.'); }
    header("Location: program-admin.php"); exit;
}

if ($action === 'list') {
    $programs = db_fetch_all("SELECT * FROM programs ORDER BY sort_order ASC");
    ?>
    <div class="toolbar">
        <span style="color:var(--admin-gray-500);font-size:0.9rem;">Total: <?= count($programs) ?> program keahlian</span>
        <a href="?action=create" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Program</a>
    </div>

    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <?php if (!empty($programs)): ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead><tr><th>Gambar</th><th>Nama Program</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($programs as $p): ?>
                        <tr>
                            <td>
                                <?php if ($p['gambar']): ?>
                                <img src="../assets/uploads/<?= e($p['gambar']) ?>" style="width:60px;height:40px;object-fit:cover;border-radius:6px;">
                                <?php else: ?><div style="width:60px;height:40px;background:var(--admin-gray-100);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--admin-gray-400);"><i class="fas fa-image"></i></div><?php endif; ?>
                            </td>
                            <td><strong><?= e($p['nama_program']) ?></strong><br><small style="color:var(--admin-gray-400);"><?= e(truncate($p['deskripsi']??'',60)) ?></small></td>
                            <td><?= $p['sort_order'] ?></td>
                            <td><span class="badge <?= $p['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="?action=edit&id=<?= $p['id'] ?>" class="action-btn edit"><i class="fas fa-pen"></i></a>
                                    <a href="?action=delete&id=<?= $p['id'] ?>" class="action-btn delete" data-confirm="Hapus program ini?"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state"><i class="fas fa-graduation-cap"></i><h3>Belum ada program keahlian</h3></div>
            <?php endif; ?>
        </div>
    </div>
    <?php
} else {
    $item = ($action === 'edit' && $id > 0) ? db_fetch("SELECT * FROM programs WHERE id=:id", [':id'=>$id]) : null;
    if ($action === 'edit' && !$item) { redirect('program-admin.php'); }
    ?>
    <a href="program-admin.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;"><i class="fas fa-arrow-left"></i> Kembali</a>
    <div class="admin-card">
        <div class="admin-card-header"><h3><?= $item ? 'Edit Program' : 'Tambah Program Baru' ?></h3></div>
        <div class="admin-card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="form-group"><label>Nama Program *</label><input type="text" name="nama_program" required value="<?= e($item['nama_program'] ?? '') ?>"></div>
                <div class="form-row">
                    <div class="form-group"><label>Urutan Tampil</label><input type="number" name="sort_order" min="0" value="<?= $item['sort_order'] ?? 0 ?>"></div>
                    <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:16px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="checkbox" name="is_active" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?> style="width:18px;height:18px;"> Aktif</label>
                    </div>
                </div>
                <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="3"><?= e($item['deskripsi'] ?? '') ?></textarea></div>
                <div class="form-group"><label>Keunggulan</label><textarea name="keunggulan" rows="3"><?= e($item['keunggulan'] ?? '') ?></textarea></div>
                <div class="form-group">
                    <label>Gambar</label>
                    <?php if (!empty($item['gambar'])): ?><div class="image-preview" style="margin-bottom:12px;"><img src="../assets/uploads/<?= e($item['gambar']) ?>"></div><?php endif; ?>
                    <input type="file" name="gambar" accept="image/*" data-preview="progPreview">
                    <div id="progPreview" class="image-preview" style="display:none;margin-top:12px;"></div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
    <?php
}
require_once __DIR__ . '/../includes/admin-footer.php'; ?>