<?php
/**
 * Admin - Kelola Staff
 */
$page_title = 'Kelola Staff';
require_once __DIR__ . '/../includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Handle Delete
if ($action === 'delete' && $id > 0) {
    $staff = db_fetch("SELECT * FROM staff WHERE id = :id", [':id' => $id]);
    if ($staff) {
        if ($staff['foto']) delete_file($staff['foto']);
        db_delete('staff', 'id = :id', [':id' => $id]);
        set_flash('success', 'Data staff berhasil dihapus.');
    }
    header("Location: staff-admin.php");
    exit;
}

// Handle Save (Create/Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
        header("Location: staff-admin.php?action=" . ($action === 'create' ? 'create' : "edit&id=$id"));
        exit;
    }

    $nama = trim($_POST['nama_lengkap'] ?? '');
    $nip = trim($_POST['nip'] ?? '');
    $posisi = trim($_POST['posisi'] ?? '');
    $departemen = trim($_POST['departemen'] ?? '');
    $tanggung_jawab = trim($_POST['tanggung_jawab'] ?? '');
    $pendidikan = trim($_POST['pendidikan_terakhir'] ?? '');
    $universitas = trim($_POST['universitas'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $status = (int)($_POST['status_aktif'] ?? 1);

    if (empty($nama) || empty($posisi)) {
        set_flash('error', 'Nama lengkap dan posisi wajib diisi.');
        header("Location: staff-admin.php?action=" . ($action === 'create' ? 'create' : "edit&id=$id"));
        exit;
    }

    // Handle foto
    $foto = null;
    if ($action === 'edit') {
        $existing = db_fetch("SELECT foto FROM staff WHERE id = :id", [':id' => $id]);
        $foto = $existing['foto'] ?? null;
    }

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $result = upload_image($_FILES['foto'], 'staff');
        if ($result['success']) {
            if ($foto) delete_file($foto);
            $foto = $result['path'];
        }
    }

    try {
        $data = [
            'nama_lengkap' => $nama,
            'nip' => $nip ?: null,
            'posisi' => $posisi,
            'departemen' => $departemen,
            'tanggung_jawab' => $tanggung_jawab,
            'pendidikan_terakhir' => $pendidikan,
            'universitas' => $universitas,
            'foto' => $foto,
            'no_hp' => $no_hp,
            'email' => $email,
            'alamat' => $alamat,
            'jenis_kelamin' => $jenis_kelamin,
            'status_aktif' => $status,
        ];

        if ($action === 'create') {
            db_insert('staff', $data);
            set_flash('success', 'Data staff berhasil ditambahkan.');
        } else {
            db_update('staff', $data, 'id = :id', [':id' => $id]);
            set_flash('success', 'Data staff berhasil diperbarui.');
        }
    } catch (Exception $e) {
        set_flash('error', 'Gagal menyimpan: ' . $e->getMessage());
    }

    header("Location: staff-admin.php");
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
        $where .= " AND (nama_lengkap LIKE :search OR posisi LIKE :search2 OR departemen LIKE :search3)";
        $params[':search'] = "%$search%";
        $params[':search2'] = "%$search%";
        $params[':search3'] = "%$search%";
    }

    $total = db_count('staff', $where, $params);
    $staffs = db_fetch_all(
        "SELECT * FROM staff WHERE $where ORDER BY sort_order ASC, nama_lengkap ASC LIMIT $per_page OFFSET $offset",
        $params
    );
    ?>

    <div class="toolbar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Cari staff..." value="<?= htmlspecialchars($search) ?>" onchange="window.location='?search='+encodeURIComponent(this.value)">
        </div>
        <a href="?action=create" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Staff</a>
    </div>

    <?= flash_html_admin() ?>

    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <?php if (!empty($staffs)): ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Posisi</th>
                            <th>Departemen</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($staffs as $s): ?>
                        <tr>
                            <td>
                                <?php if ($s['foto']): ?>
                                <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($s['foto']) ?>" alt="" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                                <?php else: ?>
                                <div style="width:50px;height:50px;background:var(--admin-gray-100);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--admin-gray-400);"><i class="fas fa-user"></i></div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($s['nama_lengkap']) ?></strong></td>
                            <td><?= htmlspecialchars($s['nip'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($s['posisi'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($s['departemen'] ?? '-') ?></td>
                            <td><span class="badge badge-<?= $s['status_aktif'] ? 'publish' : 'draft' ?>"><?= $s['status_aktif'] ? 'Aktif' : 'Tidak Aktif' ?></span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="?action=edit&id=<?= $s['id'] ?>" class="action-btn edit" title="Edit"><i class="fas fa-pen"></i></a>
                                    <a href="?action=delete&id=<?= $s['id'] ?>" class="action-btn delete" title="Hapus" data-confirm="Hapus data staff ini?"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?= paginate($total, $per_page, $page, 'staff-admin.php?' . ($search ? 'search=' . urlencode($search) . '&' : '')) ?>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>Belum ada staff</h3>
                <p>Klik tombol "Tambah Staff" untuk menambah data staff.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
// CREATE / EDIT FORM
} else {
    $item = null;
    $form_title = 'Tambah Staff Baru';
    if ($action === 'edit' && $id > 0) {
        $item = db_fetch("SELECT * FROM staff WHERE id = :id", [':id' => $id]);
        if (!$item) { redirect('staff-admin.php'); }
        $form_title = 'Edit Data Staff';
    }
    ?>

    <a href="staff-admin.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>

    <?= flash_html_admin() ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3><i class="fas fa-pen" style="color:var(--admin-primary);margin-right:8px;"></i><?= $form_title ?></h3>
        </div>
        <div class="admin-card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <div class="form-row">
                    <div class="form-group full">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap" required value="<?= htmlspecialchars($item['nama_lengkap'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Posisi *</label>
                        <input type="text" name="posisi" required value="<?= htmlspecialchars($item['posisi'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Departemen</label>
                        <input type="text" name="departemen" value="<?= htmlspecialchars($item['departemen'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>NIP</label>
                        <input type="text" name="nip" value="<?= htmlspecialchars($item['nip'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin">
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki" <?= (($item['jenis_kelamin'] ?? '') === 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= (($item['jenis_kelamin'] ?? '') === 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Pendidikan Terakhir</label>
                        <input type="text" name="pendidikan_terakhir" value="<?= htmlspecialchars($item['pendidikan_terakhir'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Universitas</label>
                        <input type="text" name="universitas" value="<?= htmlspecialchars($item['universitas'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>No. HP</label>
                        <input type="tel" name="no_hp" value="<?= htmlspecialchars($item['no_hp'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($item['email'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Tanggung Jawab</label>
                    <textarea name="tanggung_jawab" rows="3"><?= htmlspecialchars($item['tanggung_jawab'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" rows="3"><?= htmlspecialchars($item['alamat'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Foto</label>
                    <?php if (!empty($item['foto'])): ?>
                    <div class="image-preview" style="margin-bottom:12px;">
                        <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($item['foto']) ?>" alt="Current Photo">
                    </div>
                    <?php endif; ?>
                    <input type="file" name="foto" accept="image/*" data-preview="fotoPreview">
                    <div id="fotoPreview" class="image-preview" style="display:none;margin-top:12px;"></div>
                    <small style="color:var(--admin-gray-400);display:block;margin-top:4px;">JPG, PNG, GIF. Maks 5MB.</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="status_aktif" value="1" <?= (($item['status_aktif'] ?? 1) == 1) ? 'checked' : '' ?>>
                            Aktif
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
    <?php
}
?>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>