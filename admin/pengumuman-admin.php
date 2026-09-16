<?php
/**
 * Admin - Kelola Pengumuman
 */
$page_title = 'Kelola Pengumuman';
require_once __DIR__ . '/../includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    db_delete('announcements', 'id=:id', [':id'=>$id]);
    set_flash('success','Pengumuman berhasil dihapus.');
    header("Location: pengumuman-admin.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action,['create','edit'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error','Token tidak valid.'); header("Location: pengumuman-admin.php?action=$action".($id?"&id=$id":"")); exit; }

    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    $tipe = $_POST['tipe'] ?? 'info';
    $tanggal = $_POST['tanggal_pengumuman'] ?? date('Y-m-d');

    if (empty($judul) || empty($isi)) { set_flash('error','Judul dan isi wajib diisi.'); header("Location: pengumuman-admin.php?action=$action".($id?"&id=$id":"")); exit; }

    $data = ['judul'=>$judul,'isi'=>$isi,'tipe'=>$tipe,'tanggal_pengumuman'=>$tanggal];
    if ($action === 'create') { db_insert('announcements',$data); set_flash('success','Pengumuman berhasil ditambahkan.'); }
    else { db_update('announcements',$data,'id=:id',[':id'=>$id]); set_flash('success','Pengumuman berhasil diperbarui.'); }
    header("Location: pengumuman-admin.php"); exit;
}

if ($action === 'list') {
    $announcements = db_fetch_all("SELECT * FROM announcements ORDER BY tanggal_pengumuman DESC");
    ?>
    <div class="toolbar">
        <span style="color:var(--admin-gray-500);font-size:0.9rem;">Total: <?= count($announcements) ?> pengumuman</span>
        <a href="?action=create" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Pengumuman</a>
    </div>
    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <?php if (!empty($announcements)): ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead><tr><th>Judul</th><th>Tipe</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($announcements as $a): ?>
                        <tr>
                            <td><strong><?=e($a['judul'])?></strong><br><small style="color:var(--admin-gray-400);"><?=e(truncate(strip_tags($a['isi']),60))?></small></td>
                            <td><span class="badge badge-<?= $a['tipe'] ?>"><?= ucfirst($a['tipe']) ?></span></td>
                            <td><?=format_date_indo($a['tanggal_pengumuman'])?></td>
                            <td><div class="action-btns"><a href="?action=edit&id=<?=$a['id']?>" class="action-btn edit"><i class="fas fa-pen"></i></a><a href="?action=delete&id=<?=$a['id']?>" class="action-btn delete" data-confirm="Hapus?"><i class="fas fa-trash"></i></a></div></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?><div class="empty-state"><i class="fas fa-bullhorn"></i><h3>Belum ada pengumuman</h3></div><?php endif; ?>
        </div>
    </div>
    <?php
} else {
    $item = ($action==='edit' && $id>0) ? db_fetch("SELECT * FROM announcements WHERE id=:id",[':id'=>$id]) : null;
    if ($action==='edit' && !$item) { redirect('pengumuman-admin.php'); }
    ?>
    <a href="pengumuman-admin.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;"><i class="fas fa-arrow-left"></i> Kembali</a>
    <div class="admin-card">
        <div class="admin-card-header"><h3><?= $item ? 'Edit Pengumuman' : 'Tambah Pengumuman Baru' ?></h3></div>
        <div class="admin-card-body">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="form-row">
                    <div class="form-group full"><label>Judul *</label><input type="text" name="judul" required value="<?=e($item['judul']??'')?>"></div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tipe</label>
                        <select name="tipe">
                            <?php foreach(['info','warning','success','danger'] as $t): ?>
                            <option value="<?=$t?>" <?= (($item['tipe']??'info')===$t)?'selected':'' ?>><?=ucfirst($t)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Tanggal</label><input type="date" name="tanggal_pengumuman" value="<?= $item['tanggal_pengumuman'] ?? date('Y-m-d') ?>"></div>
                </div>
                <div class="form-group"><label>Isi Pengumuman *</label><textarea name="isi" rows="6" required><?=e($item['isi']??'')?></textarea></div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
    <?php
}
require_once __DIR__ . '/../includes/admin-footer.php'; ?>