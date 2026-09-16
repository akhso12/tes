<?php
/**
 * Koneksi Database - PDO
 * SMK INFOKOM BOGOR
 */

// Konfigurasi Database
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'smk_infokom');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Base URL untuk project di /opt/lampp/htdocs/website3
// URL project saat Apache berjalan pada port 8080
define('BASE_URL', 'http://localhost:8080/website3/');
define('ADMIN_URL', BASE_URL . 'admin/');
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', BASE_URL . 'assets/uploads/');

// Allowed upload extensions
define('ALLOWED_IMAGE_EXT', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    die("<div style='padding:40px;text-align:center;font-family:sans-serif;'>
        <h2>⚠️ Koneksi Database Gagal</h2>
        <p>Pastikan MySQL sudah berjalan dan database <strong>smk_infokom</strong> sudah di-import.</p>
        <p><small>Error: " . htmlspecialchars($e->getMessage()) . "</small></p>
    </div>");
}

/**
 * Fungsi helper untuk query sederhana
 */
function db_query($sql, $params = []) {
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function db_fetch($sql, $params = []) {
    $stmt = db_query($sql, $params);
    return $stmt->fetch();
}

function db_fetch_all($sql, $params = []) {
    $stmt = db_query($sql, $params);
    return $stmt->fetchAll();
}

function db_insert($table, $data) {
    global $pdo;
    $fields = array_keys($data);
    $placeholders = array_map(fn($f) => ":$f", $fields);

    $sql = "INSERT INTO `$table` (`" . implode('`,`', $fields) . "`) VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);

    foreach ($data as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }

    $stmt->execute();
    return $pdo->lastInsertId();
}

function db_update($table, $data, $where, $where_params = []) {
    global $pdo;
    $sets = [];
    $params = [];

    foreach ($data as $key => $value) {
        $sets[] = "`$key` = :set_$key";
        $params[":set_$key"] = $value;
    }

    $sql = "UPDATE `$table` SET " . implode(', ', $sets) . " WHERE $where";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    foreach ($where_params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    return $stmt->execute();
}

function db_delete($table, $where, $params = []) {
    global $pdo;
    $sql = "DELETE FROM `$table` WHERE $where";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    return $stmt->execute();
}

function db_count($table, $where = '', $params = []) {
    global $pdo;
    $sql = "SELECT COUNT(*) as total FROM `$table`";
    if ($where) $sql .= " WHERE $where";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $row = $stmt->fetch();
    return (int)$row['total'];
}