<?php
/**
 * Admin - Pesan Kontak
 */
$page_title = 'Pesan Kontak';
require_once __DIR__ . '/../includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Mark as read
if ($action === 'read' && $id > 0) {
    db_update('contact_messages', ['is_read' => 1], 'id=:id', [':id' => $id]);
    header("Location: kontak-admin.php?action=view&id=$id");
    exit;
}

// Delete
if ($action === 'delete' && $id > 0) {
    db_delete('contact_messages', 'id=:id', [':id' => $id]);
    set_flash('success', 'Pesan berhasil dihapus.');
    header("Location: kontak-admin.php");
    exit;
}

if ($action === 'list') {
    $messages = db_fetch_all("SELECT * FROM contact_messages ORDER BY created_at DESC");
    $unread_count = db_count('contact_messages', 'is_read = 0');
    ?>
    <div class="toolbar">
        <span style="color:var(--admin-gray-500);font-size:0.9rem;">Total: <?= count($messages) ?> pesan | Belum dibaca: <strong style="color:#dc2626;"><?= $unread_count ?></strong></span>
    </div>
    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <?php if (!empty($messages)): ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead><tr><th></th><th>Nama</th><th>Email</th><th>Subjek</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($messages as $m): ?>
                        <tr style="<?= !$m['is_read'] ? 'background:#eff6ff;' : '' ?>">
                            <td><?php if (!$m['is_read']): ?><span style="width:10px;height:10px;background:#3b82f6;border-radius:50%;display:inline-block;"></span><?php endif; ?></td>
                            <td><strong><?=e($m['nama'])?></strong></td>
                            <td><?=e($m['email'])?></td>
                            <td><?=e($m['subjek'] ?? '-')?></td>
                            <td><?=format_datetime_indo($m['created_at'])?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="?action=view&id=<?=$m['id']?>" class="action-btn view" title="Baca"><i class="fas fa-eye"></i></a>
                                    <a href="mailto:<?=e($m['email'])?>" class="action-btn" style="background:#dbeafe;color:#2563eb;" title="Balas Email"><i class="fas fa-reply"></i></a>
                                    <a href="?action=delete&id=<?=$m['id']?>" class="action-btn delete" title="Hapus" data-confirm="Hapus pesan ini?"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state"><i class="fas fa-envelope-open"></i><h3>Tidak ada pesan</h3></div>
            <?php endif; ?>
        </div>
    </div>
    <?php
} elseif ($action === 'view' && $id > 0) {
    $msg = db_fetch("SELECT * FROM contact_messages WHERE id=:id", [':id' => $id]);
    if (!$msg) { redirect('kontak-admin.php'); }
    if (!$msg['is_read']) db_update('contact_messages', ['is_read' => 1], 'id=:id', [':id' => $id]);
    ?>
    <a href="kontak-admin.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;"><i class="fas fa-arrow-left"></i> Kembali</a>
    <div class="admin-card">
        <div class="admin-card-header">
            <h3><?= e($msg['subjek'] ?? 'Tanpa Subjek') ?></h3>
            <span style="font-size:0.85rem;color:var(--admin-gray-400);"><?=format_datetime_indo($msg['created_at'])?></span>
        </div>
        <div class="admin-card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;"><small style="color:var(--admin-gray-400);">Pengirim</small><br><strong><?=e($msg['nama'])?></strong></div>
                <div style="padding:12px;background:var(--admin-gray-50);border-radius:8px;"><small style="color:var(--admin-gray-400);">Email</small><br><strong><a href="mailto:<?=e($msg['email'])?>"><?=e($msg['email'])?></a></strong></div>
            </div>
            <div style="padding:20px;background:var(--admin-gray-50);border-radius:8px;line-height:1.8;white-space:pre-wrap;"><?=e($msg['pesan'])?></div>
            <div style="margin-top:20px;display:flex;gap:10px;">
                <a href="mailto:<?=e($msg['email'])?>?subject=Re: <?=urlencode($msg['subjek'] ?? 'Pesan dari Website')?>" class="btn btn-primary"><i class="fas fa-reply"></i> Balas via Email</a>
                <a href="?action=delete&id=<?=$msg['id']?>" class="btn btn-danger" data-confirm="Hapus pesan ini?"><i class="fas fa-trash"></i> Hapus</a>
            </div>
        </div>
    </div>
    <?php
}
require_once __DIR__ . '/../includes/admin-footer.php'; ?>