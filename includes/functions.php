<?php
/**
 * Helper Functions
 * SMK INFOKOM BOGOR
 */

require_once __DIR__ . '/../config/database.php';

session_start();

/**
 * Sanitize output
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Build a public asset URL. Handles both upload-relative paths and assets/* paths.
 */
function asset_url($path, $fallback = 'assets/images/logo.svg') {
    $path = trim((string)$path);
    if ($path === '') $path = $fallback;
    if (preg_match('#^https?://#i', $path)) return $path;
    return BASE_URL . ltrim($path, '/');
}

/**
 * Generate CSRF Token
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Flash Message
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function flash_html() {
    $flash = get_flash();
    if (!$flash) return '';

    $alert_class = match($flash['type']) {
        'success' => 'alert-success',
        'error'   => 'alert-error',
        'warning' => 'alert-warning',
        default   => 'alert-info'
    };

    return '<div class="alert ' . $alert_class . '" id="flashMessage">' . e($flash['message']) . '</div>';
}

/**
 * Check if user is logged in (admin)
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function require_admin() {
    if (!is_admin_logged_in()) {
        redirect(ADMIN_URL . 'login.php');
    }
}

function get_admin() {
    if (!is_admin_logged_in()) return null;
    return db_fetch("SELECT * FROM admins WHERE id = :id", [':id' => $_SESSION['admin_id']]);
}

/**
 * Format date Indonesia
 */
function format_date_indo($date, $format = 'd F Y') {
    if (!$date) return '-';
    $months = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    if ($format === 'd F Y') {
        $dt = new DateTime($date);
        return $dt->format('d') . ' ' . $months[$dt->format('m')] . ' ' . $dt->format('Y');
    }

    $dt = new DateTime($date);
    return $dt->format($format);
}

function format_datetime_indo($datetime) {
    if (!$datetime) return '-';
    $dt = new DateTime($datetime);
    return format_date_indo($dt->format('Y-m-d')) . ' ' . $dt->format('H:i') . ' WIB';
}

/**
 * Truncate text
 */
function truncate($text, $length = 150, $suffix = '...') {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Upload image
 */
function upload_image($file, $folder = 'general') {
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File tidak valid'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_IMAGE_EXT)) {
        return ['success' => false, 'message' => 'Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP'];
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal 5MB'];
    }

    $upload_dir = UPLOAD_DIR . $folder . '/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $filename = uniqid() . '_' . time() . '.' . $ext;
    $filepath = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => $folder . '/' . $filename, 'full_path' => UPLOAD_URL . $folder . '/' . $filename];
    }

    return ['success' => false, 'message' => 'Gagal mengupload file'];
}

/**
 * Delete file
 */
function delete_file($relative_path) {
    $full_path = UPLOAD_DIR . $relative_path;
    if (file_exists($full_path)) {
        return unlink($full_path);
    }
    return false;
}

/**
 * Generate PPDB Registration Number
 */
function generate_ppdb_number($tahun_ajaran) {
    $prefix = 'PPDB-' . str_replace('/', '', $tahun_ajaran) . '-';
    $count = db_count('ppdb_registrations', "tahun_ajaran = :ta", [':ta' => $tahun_ajaran]);
    $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    return $prefix . $number;
}

/**
 * Get site settings
 */
function get_setting($key) {
    static $settings = null;
    if ($settings === null) {
        try {
            $rows = db_fetch_all("SELECT setting_key, setting_value FROM settings");
            $settings = [];
            foreach ($rows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Throwable $e) {
            error_log('Settings table missing or unavailable: ' . $e->getMessage());
            $settings = [];
        }
    }
    return $settings[$key] ?? '';
}

/**
 * Get school profile
 */
function get_school_profile() {
    static $profile = null;
    if ($profile === null) {
        try {
            $profile = db_fetch("SELECT * FROM school_profile LIMIT 1");
        } catch (Throwable $e) {
            error_log('school_profile table missing or unavailable: ' . $e->getMessage());
            $profile = [];
        }
    }
    return $profile ?: [];
}

/**
 * Pagination helper
 */
function paginate($total, $per_page, $current_page, $base_url) {
    $total_pages = ceil($total / $per_page);
    if ($total_pages <= 1) return '';

    $html = '<div class="pagination">';

    // Previous
    if ($current_page > 1) {
        $html .= '<a href="' . $base_url . '&page=' . ($current_page - 1) . '" class="page-link">&laquo; Prev</a>';
    }

    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);

    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $current_page) ? ' active' : '';
        $html .= '<a href="' . $base_url . '&page=' . $i . '" class="page-link' . $active . '">' . $i . '</a>';
    }

    // Next
    if ($current_page < $total_pages) {
        $html .= '<a href="' . $base_url . '&page=' . ($current_page + 1) . '" class="page-link">Next &raquo;</a>';
    }

    $html .= '</div>';
    return $html;
}

/**
 * Active nav helper
 */
function is_active($page) {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    return ($current === $page) ? ' active' : '';
}

/**
 * Admin Flash Message HTML Helper
 */
function flash_html_admin($success = null, $error = null) {
    $html = '';

    // Check session flash first
    $flash = get_flash();
    if ($flash) {
        $class = match($flash['type']) {
            'success' => 'alert-success',
            'error'   => 'alert-error',
            'warning' => 'alert-warning',
            default   => 'alert-info'
        };
        $icon = match($flash['type']) {
            'success' => 'fa-check-circle',
            'error'   => 'fa-exclamation-circle',
            'warning' => 'fa-exclamation-triangle',
            default   => 'fa-info-circle'
        };
        $html .= '<div class="alert ' . $class . '"><i class="fas ' . $icon . '"></i> ' . e($flash['message']) . '</div>';
    }

    // Direct messages
    if ($success) {
        $html .= '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' . e($success) . '</div>';
    }
    if ($error) {
        $html .= '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> ' . e($error) . '</div>';
    }

    return $html;
}