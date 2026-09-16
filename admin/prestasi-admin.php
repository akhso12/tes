<?php
/**
 * Admin - Kelola Prestasi
 */
$page_title = 'Kelola Prestasi';
require_once __DIR__ . '/../includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $p = db_fetch("SELECT gambar FROM achievements WHERE id=:id", [':id'=>$id]);
    if ($p && $p['gambar']) delete_file($p['gambar']);
    db_delete('achievements', 'id=:id', [':id'=>$id]);
    set_flash('success','Prestasi berhasil dihapus.');
    header("Location: prestasi-admin.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create','edit'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error','Token tidak valid.'); header("Location: prestasi-admin.php?action=$action".($id?"&id=$id":"")); exit; }

    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $tingkat = $_POST['tingkat'] ?? 'Kabupaten/Kota';
    $tahun = (int)($_POST['tahun'] ?? date('Y'));
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    if (empty($judul)) { set_flash('error','Judul wajib diisi.'); header("Location: prestasi-admin.php?action=$action".($id?"&id=$id":"")); exit; }

    $gambar = null;
    if ($action === 'edit') { $ex = db_fetch("SELECT gambar FROM achievements WHERE id=:id", [':id'=>$id]); $gambar = $ex['gambar'] ?? null; }
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $r = upload_image($_FILES['gambar'], 'achievements');
        if ($r['success']) { if ($gambar) delete_file($gambar); $gambar = $r['path']; }
    }

    $data = ['judul'=>$judul,'deskripsi'=>$deskripsi,'tingkat'=>$tingkat,'tahun'=>$tahun,'gambar'=>$gambar,'sort_order'=>$sort_order];
    if ($action === 'create') { db_insert('achievements',$data); set_flash('success','Prestasi berhasil ditambahkan.'); }
    else { db_update('achievements',$data,'id=:id',[':id'=>$id]); set_flash('success','Prestasi berhasil diperbarui.'); }
    header("Location: prestasi-admin.php"); exit;
}

if ($action === 'list') {
    $prestasi = db_fetch_all("SELECT * FROM achievements ORDER BY tahun DESC, sort_order ASC");
    ?>
    <div class="toolbar">
        <span style="color:var(--admin-gray-500);font-size:0.9rem;">Total: <?= count($prestasi) ?> prestasi</span>
        <a href="?action=create" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Prestasi</a>
    </div>
    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <?php if (!empty($prestasi)): ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead><tr><th>Foto</th><th>Judul</th><th>Tingkat</th><th>Tahun</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($prestasi as $p): ?>
                        <tr>
                            <td><?php if($p['gambar']):?><img src="../assets/uploads/<?=e($p['gambar'])?>" style="width:50px;height:50px;object-fit:cover;border-radius:50%;"><?php else:?><div style="width:50px;height:50px;border-radius:50%;background:var(--admin-gray-100);display:flex;align-items:center;justify-content:center;color:var(--secondary);"><i class="fas fa-trophy"></i></div><?php endif;?></td>
                            <td><strong><?=e($p['judul'])?></strong><?php if($p['deskripsi']):?><br><small style="color:var(--admin-gray-400);"><?=e(truncate($p['deskripsi'],60))?></small><?php endif;?></td>
                            <td><span class="badge badge-<?=strtolower(str_replace(['/',' '],'-',$p['tingkat']))?>"><?=e($p['tingkat'])?></span></td>
                            <td><?= $p['tahun'] ?></td>
                            <td><div class="action-btns"><a href="?action=edit&id=<?=$p['id']?>" class="action-btn edit"><i class="fas fa-pen"></i></a><a href="?action=delete&id=<?=$p['id']?>" class="action-btn delete" data-confirm="Hapus?"><i class="fas fa-trash"></i></a></div></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?><div class="empty-state"><i class="fas fa-trophy"></i><h3>Belum ada prestasi</h3></div><?php endif; ?>
        </div>
    </div>
    <?php
} else {
    $item = ($action==='edit' && $id>0) ? db_fetch("SELECT * FROM achievements WHERE id=:id",[':id'=>$id]) : null;
    if ($action==='edit' && !$item) { redirect('prestasi-admin.php'); }
    ?>
    <a href="prestasi-admin.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;"><i class="fas fa-arrow-left"></i> Kembali</a>
    <div class="admin-card">
        <div class="admin-card-header"><h3><?= $item ? 'Edit Prestasi' : 'Tambah Prestasi Baru' ?></h3></div>
        <div class="admin-card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="form-group"><label>Judul Prestasi *</label><input type="text" name="judul" required value="<?=e($item['judul']??'')?>"></div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tingkat</label>
                        <select name="tingkat">
                            <?php foreach(['Kabupaten/Kota','Provinsi','Nasional','Internasional'] as $t): ?>
                            <option value="<?=$t?>" <?= (($item['tingkat']??'')===$t)?'selected':'' ?>><?=$t?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Tahun</label><input type="number" name="tahun" min="2000" max="2100" value="<?= $item['tahun'] ?? date('Y') ?>"></div>
                </div>
                <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="3"><?=e($item['deskripsi']??'')?></textarea></div>
                <div class="form-group"><label>Urutan</label><input type="number" name="sort_order" min="0" value="<?= $item['sort_order'] ?? 0 ?>"></div>
                <div class="form-group">
                    <label>Foto</label>
                    <?php if(!empty($item['gambar'])):?><div class="image-preview" style="margin-bottom:12px;"><img src="../assets/uploads/<?=e($item['gambar'])?>"></div><?php endif;?>
                    <input type="file" name="gambar" accept="image/*" data-preview="prestasiPreview">
                    <div id="prestasiPreview" class="image-preview" style="display:none;margin-top:12px;"></div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
    <?php
}
require_once __DIR__ . '/../includes/admin-footer.php'; ?>