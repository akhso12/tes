<?php
/**
 * Admin - Kelola Agenda
 */
$page_title = 'Kelola Agenda';
require_once __DIR__ . '/../includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $a = db_fetch("SELECT gambar FROM agendas WHERE id=:id", [':id'=>$id]);
    if ($a && $a['gambar']) delete_file($a['gambar']);
    db_delete('agendas', 'id=:id', [':id'=>$id]);
    set_flash('success','Agenda berhasil dihapus.');
    header("Location: agenda-admin.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action,['create','edit'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error','Token tidak valid.'); header("Location: agenda-admin.php?action=$action".($id?"&id=$id":"")); exit; }

    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;
    $lokasi = trim($_POST['lokasi'] ?? '');

    if (empty($judul) || empty($tanggal_mulai)) { set_flash('error','Judul dan tanggal mulai wajib diisi.'); header("Location: agenda-admin.php?action=$action".($id?"&id=$id":"")); exit; }

    $gambar = null;
    if ($action === 'edit') { $ex = db_fetch("SELECT gambar FROM agendas WHERE id=:id", [':id'=>$id]); $gambar = $ex['gambar'] ?? null; }
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $r = upload_image($_FILES['gambar'], 'agendas');
        if ($r['success']) { if ($gambar) delete_file($gambar); $gambar = $r['path']; }
    }

    $data = ['judul'=>$judul,'deskripsi'=>$deskripsi,'tanggal_mulai'=>$tanggal_mulai,'tanggal_selesai'=>$tanggal_selesai?:null,'lokasi'=>$lokasi,'gambar'=>$gambar];
    if ($action === 'create') { db_insert('agendas',$data); set_flash('success','Agenda berhasil ditambahkan.'); }
    else { db_update('agendas',$data,'id=:id',[':id'=>$id]); set_flash('success','Agenda berhasil diperbarui.'); }
    header("Location: agenda-admin.php"); exit;
}

if ($action === 'list') {
    $agendas = db_fetch_all("SELECT * FROM agendas ORDER BY tanggal_mulai DESC");
    ?>
    <div class="toolbar">
        <span style="color:var(--admin-gray-500);font-size:0.9rem;">Total: <?= count($agendas) ?> agenda</span>
        <a href="?action=create" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Agenda</a>
    </div>
    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <?php if (!empty($agendas)): ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead><tr><th>Judul</th><th>Tanggal Mulai</th><th>Tanggal Selesai</th><th>Lokasi</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($agendas as $a): ?>
                        <tr>
                            <td><strong><?=e($a['judul'])?></strong><?php if($a['deskripsi']):?><br><small style="color:var(--admin-gray-400);"><?=e(truncate($a['deskripsi'],50))?></small><?php endif;?></td>
                            <td><?=format_datetime_indo($a['tanggal_mulai'])?></td>
                            <td><?= $a['tanggal_selesai'] ? format_datetime_indo($a['tanggal_selesai']) : '-' ?></td>
                            <td><?=e($a['lokasi']??'-')?></td>
                            <td><div class="action-btns"><a href="?action=edit&id=<?=$a['id']?>" class="action-btn edit"><i class="fas fa-pen"></i></a><a href="?action=delete&id=<?=$a['id']?>" class="action-btn delete" data-confirm="Hapus?"><i class="fas fa-trash"></i></a></div></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?><div class="empty-state"><i class="fas fa-calendar-alt"></i><h3>Belum ada agenda</h3></div><?php endif; ?>
        </div>
    </div>
    <?php
} else {
    $item = ($action==='edit' && $id>0) ? db_fetch("SELECT * FROM agendas WHERE id=:id",[':id'=>$id]) : null;
    if ($action==='edit' && !$item) { redirect('agenda-admin.php'); }
    ?>
    <a href="agenda-admin.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;"><i class="fas fa-arrow-left"></i> Kembali</a>
    <div class="admin-card">
        <div class="admin-card-header"><h3><?= $item ? 'Edit Agenda' : 'Tambah Agenda Baru' ?></h3></div>
        <div class="admin-card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="form-group"><label>Judul Agenda *</label><input type="text" name="judul" required value="<?=e($item['judul']??'')?>"></div>
                <div class="form-row">
                    <div class="form-group"><label>Tanggal Mulai *</label><input type="datetime-local" name="tanggal_mulai" required value="<?= $item['tanggal_mulai'] ? date('Y-m-d\TH:i', strtotime($item['tanggal_mulai'])) : '' ?>"></div>
                    <div class="form-group"><label>Tanggal Selesai</label><input type="datetime-local" name="tanggal_selesai" value="<?= $item['tanggal_selesai'] ? date('Y-m-d\TH:i', strtotime($item['tanggal_selesai'])) : '' ?>"></div>
                </div>
                <div class="form-group"><label>Lokasi</label><input type="text" name="lokasi" value="<?=e($item['lokasi']??'')?>"></div>
                <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="3"><?=e($item['deskripsi']??'')?></textarea></div>
                <div class="form-group">
                    <label>Foto</label>
                    <?php if(!empty($item['gambar'])):?><div class="image-preview" style="margin-bottom:12px;"><img src="../assets/uploads/<?=e($item['gambar'])?>"></div><?php endif;?>
                    <input type="file" name="gambar" accept="image/*" data-preview="agdPreview">
                    <div id="agdPreview" class="image-preview" style="display:none;margin-top:12px;"></div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
    <?php
}
require_once __DIR__ . '/../includes/admin-footer.php'; ?>