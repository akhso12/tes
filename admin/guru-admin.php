<?php
/**
 * Admin - Kelola Guru
 */
$page_title = 'Kelola Guru';
require_once __DIR__ . '/../includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Handle Delete
if ($action === 'delete' && $id > 0) {
    $teacher = db_fetch("SELECT * FROM teachers WHERE id = :id", [':id' => $id]);
    if ($teacher) {
        if ($teacher['foto']) delete_file($teacher['foto']);
        db_delete('teachers', 'id = :id', [':id' => $id]);
        set_flash('success', 'Data guru berhasil dihapus.');
    }
    header("Location: guru-admin.php");
    exit;
}

// Handle Save (Create/Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
        header("Location: guru-admin.php?action=" . ($action === 'create' ? 'create' : "edit&id=$id"));
        exit;
    }

    $nama = trim($_POST['nama_lengkap'] ?? '');
    $nip = trim($_POST['nip'] ?? '');
    $mata_pelajaran = trim($_POST['mata_pelajaran'] ?? '');
    $keahlian = trim($_POST['keahlian'] ?? '');
    $pendidikan = trim($_POST['pendidikan_terakhir'] ?? '');
    $universitas = trim($_POST['universitas'] ?? '');
    $spesialisasi = trim($_POST['spesialisasi'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $status = (int)($_POST['status_aktif'] ?? 1);

    if (empty($nama)) {
        set_flash('error', 'Nama lengkap wajib diisi.');
        header("Location: guru-admin.php?action=" . ($action === 'create' ? 'create' : "edit&id=$id"));
        exit;
    }

    // Handle foto
    $foto = null;
    if ($action === 'edit') {
        $existing = db_fetch("SELECT foto FROM teachers WHERE id = :id", [':id' => $id]);
        $foto = $existing['foto'] ?? null;
    }

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $result = upload_image($_FILES['foto'], 'teachers');
        if ($result['success']) {
            if ($foto) delete_file($foto);
            $foto = $result['path'];
        }
    }

    try {
        $data = [
            'nama_lengkap' => $nama,
            'nip' => $nip ?: null,
            'mata_pelajaran' => $mata_pelajaran,
            'keahlian' => $keahlian,
            'pendidikan_terakhir' => $pendidikan,
            'universitas' => $universitas,
            'spesialisasi' => $spesialisasi,
            'foto' => $foto,
            'no_hp' => $no_hp,
            'email' => $email,
            'alamat' => $alamat,
            'jenis_kelamin' => $jenis_kelamin,
            'status_aktif' => $status,
        ];

        if ($action === 'create') {
            db_insert('teachers', $data);
            set_flash('success', 'Data guru berhasil ditambahkan.');
        } else {
            db_update('teachers', $data, 'id = :id', [':id' => $id]);
            set_flash('success', 'Data guru berhasil diperbarui.');
        }
    } catch (Exception $e) {
        set_flash('error', 'Gagal menyimpan: ' . $e->getMessage());
    }

    header("Location: guru-admin.php");
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
        $where .= " AND (nama_lengkap LIKE :search OR mata_pelajaran LIKE :search2 OR nip LIKE :search3)";
        $params[':search'] = "%$search%";
        $params[':search2'] = "%$search%";
        $params[':search3'] = "%$search%";
    }

    $total = db_count('teachers', $where, $params);
    $teachers = db_fetch_all(
        "SELECT * FROM teachers WHERE $where ORDER BY sort_order ASC, nama_lengkap ASC LIMIT $per_page OFFSET $offset",
        $params
    );
    ?>

    <div class="toolbar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Cari guru..." value="<?= htmlspecialchars($search) ?>" onchange="window.location='?search='+encodeURIComponent(this.value)">
        </div>
        <a href="?action=create" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Guru</a>
    </div>

    <?= flash_html_admin() ?>

    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <?php if (!empty($teachers)): ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Mata Pelajaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $t): ?>
                        <tr>
                            <td>
                                <?php if ($t['foto']): ?>
                                <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($t['foto']) ?>" alt="" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                                <?php else: ?>
                                <div style="width:50px;height:50px;background:var(--admin-gray-100);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--admin-gray-400);"><i class="fas fa-user"></i></div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($t['nama_lengkap']) ?></strong></td>
                            <td><?= htmlspecialchars($t['nip'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($t['mata_pelajaran'] ?? '-') ?></td>
                            <td><span class="badge badge-<?= $t['status_aktif'] ? 'publish' : 'draft' ?>"><?= $t['status_aktif'] ? 'Aktif' : 'Tidak Aktif' ?></span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="?action=edit&id=<?= $t['id'] ?>" class="action-btn edit" title="Edit"><i class="fas fa-pen"></i></a>
                                    <a href="?action=delete&id=<?= $t['id'] ?>" class="action-btn delete" title="Hapus" data-confirm="Hapus data guru ini?"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?= paginate($total, $per_page, $page, 'guru-admin.php?' . ($search ? 'search=' . urlencode($search) . '&' : '')) ?>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-user-tie"></i>
                <h3>Belum ada guru</h3>
                <p>Klik tombol "Tambah Guru" untuk menambah data guru.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
// CREATE / EDIT FORM
} else {
    $item = null;
    $form_title = 'Tambah Guru Baru';
    if ($action === 'edit' && $id > 0) {
        $item = db_fetch("SELECT * FROM teachers WHERE id = :id", [':id' => $id]);
        if (!$item) { redirect('guru-admin.php'); }
        $form_title = 'Edit Data Guru';
    }
    ?>

    <a href="guru-admin.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>

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
                        <label>Mata Pelajaran</label>
                        <input type="text" name="mata_pelajaran" value="<?= htmlspecialchars($item['mata_pelajaran'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Spesialisasi</label>
                        <input type="text" name="spesialisasi" value="<?= htmlspecialchars($item['spesialisasi'] ?? '') ?>">
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
                    <label>Keahlian</label>
                    <textarea name="keahlian" rows="3"><?= htmlspecialchars($item['keahlian'] ?? '') ?></textarea>
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