<?php
/**
 * Admin - Detail Pendaftar PPDB
 */
$page_title = 'Detail Pendaftar';
require_once __DIR__ . '/../includes/admin-header.php';

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) { redirect('ppdb-admin.php'); }

$reg = db_fetch("SELECT * FROM ppdb_registrations WHERE id = :id", [':id' => $id]);
if (!$reg) { redirect('ppdb-admin.php'); }

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error','Token tidak valid.'); }
    else {
        $new_status = $_POST['new_status'] ?? $reg['status'];
        $keterangan = trim($_POST['keterangan'] ?? '');
        db_update('ppdb_registrations', ['status'=>$new_status, 'keterangan'=>$keterangan], 'id=:id', [':id'=>$id]);
        set_flash('success','Status berhasil diperbarui.');
        $reg = db_fetch("SELECT * FROM ppdb_registrations WHERE id = :id", [':id' => $id]);
    }
}
?>

<a href="ppdb-admin.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;"><i class="fas fa-arrow-left"></i> Kembali ke Daftar PPDB</a>

<?= flash_html_admin(null, null) ?>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">
    <!-- Data Pendaftar -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3><i class="fas fa-user" style="color:var(--admin-primary);margin-right:8px;"></i>Data Pendaftar</h3>
            <span class="badge badge-<?=strtolower($reg['status'])?>" style="font-size:0.9rem;padding:6px 16px;"><?=e($reg['status'])?></span>
        </div>
        <div class="admin-card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">Nomor Pendaftaran</small>
                    <strong style="color:var(--admin-primary);font-size:1.1rem;"><?=e($reg['nomor_pendaftaran'])?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">Tanggal Daftar</small>
                    <strong><?=format_datetime_indo($reg['tanggal_daftar'])?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">Nama Lengkap</small>
                    <strong><?=e($reg['nama_lengkap'])?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">NIK</small>
                    <strong><?=e($reg['nik'])?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">NISN</small>
                    <strong><?=e($reg['nisn'] ?? '-')?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">Tempat, Tanggal Lahir</small>
                    <strong><?=e($reg['tempat_lahir'] ?? '-')?>, <?= $reg['tanggal_lahir'] ? format_date_indo($reg['tanggal_lahir']) : '-' ?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">Jenis Kelamin</small>
                    <strong><?=e($reg['jenis_kelamin'])?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">Agama</small>
                    <strong><?=e($reg['agama'] ?? '-')?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;grid-column:1/-1;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">Alamat</small>
                    <strong><?=nl2br(e($reg['alamat']))?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">No. HP</small>
                    <strong><?=e($reg['no_hp'])?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">Email</small>
                    <strong><?=e($reg['email'] ?? '-')?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">Nama Orang Tua/Wali</small>
                    <strong><?=e($reg['nama_ortu'])?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">No. HP Ortu</small>
                    <strong><?=e($reg['no_hp_ortu'] ?? '-')?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">Asal Sekolah</small>
                    <strong><?=e($reg['asal_sekolah'])?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">Pilihan Jurusan</small>
                    <strong style="color:var(--admin-primary);"><?=e($reg['pilihan_jurusan'])?></strong>
                </div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;">
                    <small style="color:var(--admin-gray-400);display:block;margin-bottom:4px;">Tahun Ajaran</small>
                    <strong><?=e($reg['tahun_ajaran'])?></strong>
                </div>
                <?php if ($reg['keterangan']): ?>
                <div style="padding:12px;background:#fffbeb;border-radius:8px;grid-column:1/-1;">
                    <small style="color:#92400e;display:block;margin-bottom:4px;">Keterangan</small>
                    <strong style="color:#92400e;"><?=nl2br(e($reg['keterangan']))?></strong>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div>
        <div class="admin-card">
            <div class="admin-card-header"><h3>Ubah Status</h3></div>
            <div class="admin-card-body">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="update_status" value="1">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="new_status" style="width:100%;">
                            <?php foreach(['Menunggu','Diverifikasi','Diterima','Ditolak'] as $s): ?>
                            <option value="<?=$s?>" <?=($reg['status']===$s)?'selected':''?>><?=$s?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" rows="3"><?=e($reg['keterangan'] ?? '')?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;"><i class="fas fa-save"></i> Update Status</button>
                </form>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-body" style="text-align:center;">
                <a href="ppdb-admin.php?action=delete&id=<?=$reg['id']?>" class="btn btn-danger" style="width:100%;" data-confirm="Hapus data pendaftar ini secara permanen?"><i class="fas fa-trash"></i> Hapus Data</a>
            </div>
        </div>
    </div>
</div>

<style>
@media(max-width:900px){div[style*="grid-template-columns:2fr 1fr"]{grid-template-columns:1fr!important;}div[style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr!important;}}
</style>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>