<?php
/**
 * Admin Dashboard
 */
$page_title = 'Dashboard';

require_once __DIR__ . '/../includes/admin-header.php';

// Get statistics
$total_news = db_count('news');
$total_ppdb = db_count('ppdb_registrations');
$total_pending = db_count('ppdb_registrations', "status = 'Menunggu'");
$total_programs = db_count('programs', 'is_active = 1');
$total_gallery = db_count('gallery', 'is_active = 1');
$total_achievements = db_count('achievements', 'is_active = 1');
$total_messages = db_count('contact_messages', 'is_read = 0');

// Recent PPDB registrations
$recent_ppdb = db_fetch_all("SELECT * FROM ppdb_registrations ORDER BY tanggal_daftar DESC LIMIT 5");

// Recent news
$recent_news = db_fetch_all("SELECT * FROM news ORDER BY created_at DESC LIMIT 5");

// PPDB chart data (last 7 days)
$chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $count = db_count('ppdb_registrations', "DATE(tanggal_daftar) = :date", [':date' => $date]);
    $chart_data[] = [
        'date' => date('d M', strtotime($date)),
        'count' => $count
    ];
}

$max_chart = max(array_column($chart_data, 'count'), 1);
?>

<!-- Stats Row -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-card-icon blue"><i class="fas fa-newspaper"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-number"><?= number_format($total_news) ?></div>
            <div class="stat-card-label">Total Berita</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon green"><i class="fas fa-user-plus"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-number"><?= number_format($total_ppdb) ?></div>
            <div class="stat-card-label">Total Pendaftar PPDB</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon amber"><i class="fas fa-clock"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-number"><?= number_format($total_pending) ?></div>
            <div class="stat-card-label">Pendaftar Menunggu</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon purple"><i class="fas fa-trophy"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-number"><?= number_format($total_achievements) ?></div>
            <div class="stat-card-label">Total Prestasi</div>
        </div>
    </div>
</div>

<div class="stats-row" style="grid-template-columns: repeat(3, 1fr);">
    <div class="stat-card">
        <div class="stat-card-icon teal"><i class="fas fa-images"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-number"><?= number_format($total_gallery) ?></div>
            <div class="stat-card-label">Foto Galeri</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon blue"><i class="fas fa-graduation-cap"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-number"><?= number_format($total_programs) ?></div>
            <div class="stat-card-label">Program Keahlian</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon red"><i class="fas fa-envelope"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-number"><?= number_format($total_messages) ?></div>
            <div class="stat-card-label">Pesan Belum Dibaca</div>
        </div>
    </div>
</div>

<!-- Chart + Recent PPDB -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- PPDB Chart -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3><i class="fas fa-chart-bar" style="color:var(--admin-primary);margin-right:8px;"></i>Pendaftaran 7 Hari Terakhir</h3>
        </div>
        <div class="admin-card-body">
            <div style="display:flex;align-items:flex-end;gap:12px;height:180px;padding-top:20px;">
                <?php foreach ($chart_data as $day): 
                    $height = max(4, ($day['count'] / $max_chart) * 140);
                ?>
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
                    <span style="font-size:0.78rem;font-weight:700;color:var(--admin-gray-700);"><?= $day['count'] ?></span>
                    <div style="width:100%;background:linear-gradient(to top,var(--admin-primary),var(--admin-primary-light));border-radius:6px 6px 0 0;height:<?= $height ?>px;transition:height 0.5s ease;"></div>
                    <span style="font-size:0.72rem;color:var(--admin-gray-400);"><?= $day['date'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Recent PPDB -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3><i class="fas fa-user-plus" style="color:#059669;margin-right:8px;"></i>Pendaftar Terbaru</h3>
            <a href="ppdb-admin.php" class="btn btn-sm btn-outline">Lihat Semua</a>
        </div>
        <div class="admin-card-body" style="padding:0;">
            <?php if (!empty($recent_ppdb)): ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>No. Pendaftaran</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_ppdb as $r): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($r['nomor_pendaftaran']) ?></strong></td>
                            <td><?= htmlspecialchars($r['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($r['pilihan_jurusan']) ?></td>
                            <td><span class="badge badge-<?= strtolower($r['status']) ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state" style="padding:40px;">
                <i class="fas fa-inbox"></i>
                <p>Belum ada pendaftar</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent News -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3><i class="fas fa-newspaper" style="color:#2563eb;margin-right:8px;"></i>Berita Terbaru</h3>
        <a href="berita-admin.php" class="btn btn-sm btn-outline">Kelola Berita</a>
    </div>
    <div class="admin-card-body" style="padding:0;">
        <?php if (!empty($recent_news)): ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_news as $n): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($n['judul']) ?></strong></td>
                        <td><?= htmlspecialchars($n['kategori'] ?? '-') ?></td>
                        <td><span class="badge badge-<?= $n['status'] ?>"><?= ucfirst($n['status']) ?></span></td>
                        <td><?= date('d M Y', strtotime($n['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state" style="padding:40px;">
            <i class="fas fa-newspaper"></i>
            <p>Belum ada berita</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
@media (max-width: 900px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>