<?php
/**
 * Admin - Pengaturan
 */
$page_title = 'Pengaturan';
require_once __DIR__ . '/../includes/admin-header.php';

$success = '';
$error = '';

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { $error = 'Token tidak valid.'; }
    else {
        try {
            $settings = [
                'site_title' => trim($_POST['site_title'] ?? ''),
                'site_description' => trim($_POST['site_description'] ?? ''),
                'ppdb_open' => isset($_POST['ppdb_open']) ? '1' : '0',
                'ppdb_tahun_ajaran' => trim($_POST['ppdb_tahun_ajaran'] ?? ''),
                'items_per_page' => max(5, min(50, (int)($_POST['items_per_page'] ?? 10))),
            ];
            foreach ($settings as $key => $value) {
                db_update('settings', ['setting_value' => $value], 'setting_key = :key', [':key' => $key]);
            }
            $success = 'Pengaturan berhasil disimpan.';
        } catch (Exception $e) {
            $error = 'Gagal menyimpan: ' . $e->getMessage();
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { $error = 'Token tidak valid.'; }
    else {
        $old_pass = $_POST['old_password'] ?? '';
        $new_pass = $_POST['new_password'] ?? '';
        $confirm_pass = $_POST['confirm_password'] ?? '';

        $admin = get_admin();
        if (!$admin || !password_verify($old_pass, $admin['password'])) {
            $error = 'Password lama salah.';
        } elseif (strlen($new_pass) < 6) {
            $error = 'Password baru minimal 6 karakter.';
        } elseif ($new_pass !== $confirm_pass) {
            $error = 'Konfirmasi password tidak cocok.';
        } else {
            db_update('admins', ['password' => password_hash($new_pass, PASSWORD_DEFAULT)], 'id=:id', [':id' => $_SESSION['admin_id']]);
            $success = 'Password berhasil diubah.';
        }
    }
}

$current_settings = [];
$rows = db_fetch_all("SELECT * FROM settings");
foreach ($rows as $r) $current_settings[$r['setting_key']] = $r['setting_value'];
?>

<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div><?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <!-- General Settings -->
    <div class="admin-card">
        <div class="admin-card-header"><h3><i class="fas fa-cog" style="color:var(--admin-primary);margin-right:8px;"></i>Pengaturan Umum</h3></div>
        <div class="admin-card-body">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="save_settings" value="1">
                <div class="form-group"><label>Judul Website</label><input type="text" name="site_title" value="<?=e($current_settings['site_title'] ?? '')?>"></div>
                <div class="form-group"><label>Deskripsi Website</label><textarea name="site_description" rows="2"><?=e($current_settings['site_description'] ?? '')?></textarea></div>
                <div class="form-row">
                    <div class="form-group">
                        <label>PPDB Status</label>
                        <select name="ppdb_open">
                            <option value="1" <?= (($current_settings['ppdb_open'] ?? '1') === '1') ? 'selected' : '' ?>>Dibuka</option>
                            <option value="0" <?= (($current_settings['ppdb_open'] ?? '') === '0') ? 'selected' : '' ?>>Ditutup</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Tahun Ajaran PPDB</label><input type="text" name="ppdb_tahun_ajaran" value="<?=e($current_settings['ppdb_tahun_ajaran'] ?? '')?>"></div>
                </div>
                <div class="form-group"><label>Item Per Halaman</label><input type="number" name="items_per_page" min="5" max="50" value="<?= (int)($current_settings['items_per_page'] ?? 10) ?>"></div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Pengaturan</button>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="admin-card">
        <div class="admin-card-header"><h3><i class="fas fa-key" style="color:#d97706;margin-right:8px;"></i>Ubah Password</h3></div>
        <div class="admin-card-body">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="change_password" value="1">
                <div class="form-group"><label>Password Lama</label><input type="password" name="old_password" required></div>
                <div class="form-group"><label>Password Baru</label><input type="password" name="new_password" required minlength="6"></div>
                <div class="form-group"><label>Konfirmasi Password Baru</label><input type="password" name="confirm_password" required></div>
                <button type="submit" class="btn btn-warning"><i class="fas fa-lock"></i> Ubah Password</button>
            </form>
        </div>
    </div>
</div>

<!-- System Info -->
<div class="admin-card" style="margin-top:24px;">
    <div class="admin-card-header"><h3><i class="fas fa-info-circle" style="color:#2563eb;margin-right:8px;"></i>Informasi Sistem</h3></div>
    <div class="admin-card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
            <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;"><small style="color:var(--admin-gray-400);">PHP Version</small><br><strong><?= phpversion() ?></strong></div>
            <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;"><small style="color:var(--admin-gray-400);">Database</small><br><strong>MySQL / MariaDB</strong></div>
            <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;"><small style="color:var(--admin-gray-400);">Server</small><br><strong><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Apache' ?></strong></div>
            <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;"><small style="color:var(--admin-gray-400);">Waktu Server</small><br><strong><?= date('d M Y H:i:s') ?></strong></div>
        </div>
    </div>
</div>

<style>
@media(max-width:900px){div[style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr!important;}}
</style>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>