<?php
/**
 * Admin - Profil Sekolah
 */
$page_title = 'Profil Sekolah';
require_once __DIR__ . '/../includes/admin-header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Token tidak valid.';
    } else {
        try {
            // Handle logo upload
            $logo_path = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $filename = uniqid('logo_') . '.' . $ext;
                    $upload_dir = __DIR__ . '/../assets/uploads/logo/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $filename);
                    $logo_path = 'assets/uploads/logo/' . $filename;
                }
            }

            $data = [
                'nama_sekolah' => trim($_POST['nama_sekolah'] ?? ''),
                'tagline'      => trim($_POST['tagline'] ?? ''),
                'deskripsi'    => trim($_POST['deskripsi'] ?? ''),
                'alamat'       => trim($_POST['alamat'] ?? ''),
                'telepon'      => trim($_POST['telepon'] ?? ''),
                'email'        => trim($_POST['email'] ?? ''),
                'jam_operasional' => trim($_POST['jam_operasional'] ?? ''),
                'tahun_didirikan' => $_POST['tahun_didirikan'] ?? null,
                'npsn'         => trim($_POST['npsn'] ?? ''),
                'akreditasi'   => trim($_POST['akreditasi'] ?? ''),
            ];

            if ($logo_path) {
                $data['logo'] = $logo_path;
            }

            db_update('school_profile', $data, 'id = :id', [':id' => 1]);
            $success = 'Data profil sekolah berhasil diperbarui.';
        } catch (Exception $e) {
            $error = 'Gagal menyimpan data: ' . $e->getMessage();
        }
    }
}

$profile = db_fetch("SELECT * FROM school_profile LIMIT 1") ?: [];
?>

<?= flash_html_admin($success, $error) ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3><i class="fas fa-school" style="color:var(--admin-primary);margin-right:8px;"></i>Edit Profil Sekolah</h3>
    </div>
    <div class="admin-card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="form-row">
                <div class="form-group">
                    <label>Nama Sekolah</label>
                    <input type="text" name="nama_sekolah" value="<?= htmlspecialchars($profile['nama_sekolah'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Tahun Didirikan</label>
                    <input type="number" name="tahun_didirikan" min="1900" max="2100" value="<?= htmlspecialchars($profile['tahun_didirikan'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Tagline</label>
                <input type="text" name="tagline" value="<?= htmlspecialchars($profile['tagline'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Deskripsi Sekolah</label>
                <textarea name="deskripsi" rows="4"><?= htmlspecialchars($profile['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" rows="3"><?= htmlspecialchars($profile['alamat'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Jam Operasional</label>
                    <input type="text" name="jam_operasional" value="<?= htmlspecialchars($profile['jam_operasional'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Telepon</label>
                    <input type="text" name="telepon" value="<?= htmlspecialchars($profile['telepon'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>NPSN</label>
                    <input type="text" name="npsn" value="<?= htmlspecialchars($profile['npsn'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Akreditasi</label>
                    <select name="akreditasi">
                        <option value="">-- Pilih --</option>
                        <option value="A" <?= ($profile['akreditasi'] ?? '') === 'A' ? 'selected' : '' ?>>A (Unggul)</option>
                        <option value="B" <?= ($profile['akreditasi'] ?? '') === 'B' ? 'selected' : '' ?>>B (Baik)</option>
                        <option value="C" <?= ($profile['akreditasi'] ?? '') === 'C' ? 'selected' : '' ?>>C (Cukup)</option>
                        <option value="Belum Terakreditasi" <?= ($profile['akreditasi'] ?? '') === 'Belum Terakreditasi' ? 'selected' : '' ?>>Belum Terakreditasi</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Logo Sekolah</label>
                <?php if (!empty($profile['logo'])): ?>
                <div class="image-preview" style="margin-bottom:12px;">
                    <img src="../<?= htmlspecialchars($profile['logo']) ?>" alt="Logo Current">
                </div>
                <?php endif; ?>
                <input type="file" name="logo" accept="image/*">
                <small style="color:var(--admin-gray-400);display:block;margin-top:4px;">Format: JPG, PNG, GIF, WEBP. Maks 2MB.</small>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>