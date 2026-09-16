<?php
/**
 * Admin - Kelola Fasilitas
 */
$page_title = 'Kelola Fasilitas';
require_once __DIR__ . '/../includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $f = db_fetch("SELECT gambar FROM facilities WHERE id=:id", [':id'=>$id]);
    if ($f && $f['gambar']) delete_file($f['gambar']);
    db_delete('facilities', 'id=:id', [':id'=>$id]);
    set_flash('success','Fasilitas berhasil dihapus.');
    header("Location: fasilitas-admin.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action,['create','edit'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error','Token tidak valid.'); header("Location: fasilitas-admin.php?action=$action".($id?"&id=$id":"")); exit; }

    $nama = trim($_POST['nama_fasilitas'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($nama)) { set_flash('error','Nama fasilitas wajib diisi.'); header("Location: fasilitas-admin.php?action=$action".($id?"&id=$id":"")); exit; }

    $gambar = null;
    if ($action === 'edit') { $ex = db_fetch("SELECT gambar FROM facilities WHERE id=:id", [':id'=>$id]); $gambar = $ex['gambar'] ?? null; }
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $r = upload_image($_FILES['gambar'], 'facilities');
        if ($r['success']) { if ($gambar) delete_file($gambar); $gambar = $r['path']; }
    }

    $data = ['nama_fasilitas'=>$nama,'deskripsi'=>$deskripsi,'gambar'=>$gambar,'sort_order'=>$sort_order,'is_active'=>$is_active];
    if ($action === 'create') { db_insert('facilities',$data); set_flash('success','Fasilitas berhasil ditambahkan.'); }
    else { db_update('facilities',$data,'id=:id',[':id'=>$id]); set_flash('success','Fasilitas berhasil diperbarui.'); }
    header("Location: fasilitas-admin.php"); exit;
}

if ($action === 'list') {
    $facilities = db_fetch_all("SELECT * FROM facilities ORDER BY sort_order ASC");
    ?>
    <div class="toolbar">
        <span style="color:var(--admin-gray-500);font-size:0.9rem;">Total: <?= count($facilities) ?> fasilitas</span>
        <a href="?action=create" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Fasilitas</a>
    </div>
    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <?php if (!empty($facilities)): ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead><tr><th>Gambar</th><th>Nama Fasilitas</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($facilities as $f): ?>
                        <tr>
                            <td><?php if($f['gambar']):?><img src="../assets/uploads/<?=e($f['gambar'])?>" style="width:60px;height:40px;object-fit:cover;border-radius:6px;"><?php else:?><div style="width:60px;height:40px;background:var(--admin-gray-100);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--admin-gray-400);"><i class="fas fa-building"></i></div><?php endif;?></td>
                            <td><strong><?=e($f['nama_fasilitas'])?></strong><br><small style="color:var(--admin-gray-400);"><?=e(truncate($f['deskripsi']??'',50))?></small></td>
                            <td><?= $f['sort_order'] ?></td>
                            <td><span class="badge <?= $f['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $f['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td><div class="action-btns"><a href="?action=edit&id=<?=$f['id']?>" class="action-btn edit"><i class="fas fa-pen"></i></a><a href="?action=delete&id=<?=$f['id']?>" class="action-btn delete" data-confirm="Hapus?"><i class="fas fa-trash"></i></a></div></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?><div class="empty-state"><i class="fas fa-building"></i><h3>Belum ada fasilitas</h3></div><?php endif; ?>
        </div>
    </div>
    <?php
} else {
    $item = ($action==='edit' && $id>0) ? db_fetch("SELECT * FROM facilities WHERE id=:id",[':id'=>$id]) : null;
    if ($action==='edit' && !$item) { redirect('fasilitas-admin.php'); }
    ?>
    <a href="fasilitas-admin.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;"><i class="fas fa-arrow-left"></i> Kembali</a>
    <div class="admin-card">
        <div class="admin-card-header"><h3><?= $item ? 'Edit Fasilitas' : 'Tambah Fasilitas Baru' ?></h3></div>
        <div class="admin-card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="form-group"><label>Nama Fasilitas *</label><input type="text" name="nama_fasilitas" required value="<?=e($item['nama_fasilitas']??'')?>"></div>
                <div class="form-row">
                    <div class="form-group"><label>Urutan</label><input type="number" name="sort_order" min="0" value="<?= $item['sort_order'] ?? 0 ?>"></div>
                    <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:16px;"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="checkbox" name="is_active" <?= ($item['is_active']??1)?'checked':'' ?> style="width:18px;height:18px;"> Aktif</label></div>
                </div>
                <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="3"><?=e($item['deskripsi']??'')?></textarea></div>
                <div class="form-group">
                    <label>Foto</label>
                    <?php if(!empty($item['gambar'])):?><div class="image-preview" style="margin-bottom:12px;"><img src="../assets/uploads/<?=e($item['gambar'])?>"></div><?php endif;?>
                    <input type="file" name="gambar" accept="image/*" data-preview="fasPreview">
                    <div id="fasPreview" class="image-preview" style="display:none;margin-top:12px;"></div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
    <?php
}
require_once __DIR__ . '/../includes/admin-footer.php'; ?>