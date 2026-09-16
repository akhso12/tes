<?php
/**
 * Admin - Data PPDB
 */
$page_title = 'Data PPDB';
require_once __DIR__ . '/../includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Handle status update via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { set_flash('error','Token tidak valid.'); header("Location: ppdb-admin.php"); exit; }
    $reg_id = (int)($_POST['registration_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? 'Menunggu';
    $keterangan = trim($_POST['keterangan'] ?? '');
    db_update('ppdb_registrations', ['status'=>$new_status, 'keterangan'=>$keterangan], 'id=:id', [':id'=>$reg_id]);
    set_flash('success', 'Status pendaftaran berhasil diperbarui.');
    header("Location: ppdb-admin.php"); exit;
}

// Handle delete
if ($action === 'delete' && $id > 0) {
    db_delete('ppdb_registrations', 'id=:id', [':id'=>$id]);
    set_flash('success','Data pendaftar berhasil dihapus.');
    header("Location: ppdb-admin.php"); exit;
}

if ($action === 'list') {
    $per_page = 20;
    $page = max(1, (int)($_GET['page'] ?? 1));
    $offset = ($page - 1) * $per_page;
    $search = $_GET['search'] ?? '';
    $filter_status = $_GET['status'] ?? '';
    $filter_jurusan = $_GET['jurusan'] ?? '';

    $where = "1=1";
    $params = [];
    if ($search) { $where .= " AND (nama_lengkap LIKE :s1 OR nomor_pendaftaran LIKE :s2 OR asal_sekolah LIKE :s3)"; $params[':s1']=$params[':s2']=$params[':s3']="%" . $search . "%"; }
    if ($filter_status) { $where .= " AND status = :fs"; $params[':fs'] = $filter_status; }
    if ($filter_jurusan) { $where .= " AND pilihan_jurusan = :fj"; $params[':fj'] = $filter_jurusan; }

    $total = db_count('ppdb_registrations', $where, $params);
    $registrations = db_fetch_all("SELECT * FROM ppdb_registrations WHERE $where ORDER BY tanggal_daftar DESC LIMIT $per_page OFFSET $offset", $params);
    $programs = db_fetch_all("SELECT DISTINCT pilihan_jurusan FROM ppdb_registrations WHERE pilihan_jurusan IS NOT NULL ORDER BY pilihan_jurusan");
    ?>

    <div class="stats-row" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
        <div class="stat-card"><div class="stat-card-icon blue"><i class="fas fa-users"></i></div><div class="stat-card-info"><div class="stat-card-number"><?= number_format(db_count('ppdb_registrations')) ?></div><div class="stat-card-label">Total Pendaftar</div></div></div>
        <div class="stat-card"><div class="stat-card-icon amber"><i class="fas fa-clock"></i></div><div class="stat-card-info"><div class="stat-card-number"><?= number_format(db_count('ppdb_registrations',"status='Menunggu'")) ?></div><div class="stat-card-label">Menunggu</div></div></div>
        <div class="stat-card"><div class="stat-card-icon green"><i class="fas fa-check-circle"></i></div><div class="stat-card-info"><div class="stat-card-number"><?= number_format(db_count('ppdb_registrations',"status='Diterima'")) ?></div><div class="stat-card-label">Diterima</div></div></div>
        <div class="stat-card"><div class="stat-card-icon red"><i class="fas fa-times-circle"></i></div><div class="stat-card-info"><div class="stat-card-number"><?= number_format(db_count('ppdb_registrations',"status='Ditolak'")) ?></div><div class="stat-card-label">Ditolak</div></div></div>
    </div>

    <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Cari nama/nomor/asal sekolah..." value="<?=htmlspecialchars($search)?>" onchange="updateFilter()"></div>
        <select class="filter-select" onchange="updateFilter()" id="filterStatus">
            <option value="">Semua Status</option>
            <?php foreach(['Menunggu','Diverifikasi','Diterima','Ditolak'] as $s): ?><option value="<?=$s?>" <?=($filter_status===$s)?'selected':''?>><?=$s?></option><?php endforeach; ?>
        </select>
        <select class="filter-select" onchange="updateFilter()" id="filterJurusan">
            <option value="">Semua Jurusan</option>
            <?php foreach($programs as $p): ?><option value="<?=e($p['pilihan_jurusan'])?>" <?=($filter_jurusan===$p['pilihan_jurusan'])?'selected':''?>><?=e($p['pilihan_jurusan'])?></option><?php endforeach; ?>
        </select>
    </div>

    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <?php if (!empty($registrations)): ?>
            <div class="admin-table-wrapper">
                <table class="admin-table" id="ppdbTable">
                    <thead><tr><th>No. Pendaftaran</th><th>Nama</th><th>Asal Sekolah</th><th>Jurusan</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($registrations as $r): ?>
                        <tr>
                            <td><strong style="color:var(--admin-primary);"><?=e($r['nomor_pendaftaran'])?></strong></td>
                            <td><?=e($r['nama_lengkap'])?></td>
                            <td><?=e($r['asal_sekolah'])?></td>
                            <td><?=e($r['pilihan_jurusan'])?></td>
                            <td><?=format_date_indo($r['tanggal_daftar'])?></td>
                            <td><span class="badge badge-<?=strtolower($r['status'])?>"><?=e($r['status'])?></span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="ppdb-detail.php?id=<?=$r['id']?>" class="action-btn view" title="Detail"><i class="fas fa-eye"></i></a>
                                    <button class="action-btn status" title="Ubah Status" onclick="openStatusModal(<?=$r['id']?>,'<?=e($r['status'])?>')"><i class="fas fa-exchange-alt"></i></button>
                                    <a href="ppdb-admin.php?action=delete&id=<?=$r['id']?>" class="action-btn delete" title="Hapus" data-confirm="Hapus data pendaftar ini?"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?= paginate($total, $per_page, $page, 'ppdb-admin.php?') ?>
            <?php else: ?>
            <div class="empty-state"><i class="fas fa-user-plus"></i><h3>Belum ada pendaftar</h3></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Change Modal -->
    <div class="modal-overlay" id="statusModal">
        <div class="modal">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" name="registration_id" id="statusRegId">
                <div class="modal-header">
                    <h3>Ubah Status Pendaftaran</h3>
                    <button type="button" class="modal-close" onclick="closeStatusModal()"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status Baru</label>
                        <select name="new_status" id="statusSelect" style="width:100%;">
                            <option value="Menunggu">Menunggu</option>
                            <option value="Diverifikasi">Diverifikasi</option>
                            <option value="Diterima">Diterima</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" rows="3" placeholder="Tambahkan keterangan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeStatusModal()">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function updateFilter() {
        const search = document.querySelector('.search-box input').value;
        const status = document.getElementById('filterStatus').value;
        const jurusan = document.getElementById('filterJurusan').value;
        let url = 'ppdb-admin.php?';
        if (search) url += 'search=' + encodeURIComponent(search) + '&';
        if (status) url += 'status=' + encodeURIComponent(status) + '&';
        if (jurusan) url += 'jurusan=' + encodeURIComponent(jurusan) + '&';
        window.location = url;
    }
    </script>
    <?php
}
require_once __DIR__ . '/../includes/admin-footer.php'; ?>