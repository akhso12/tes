<?php
/**
 * Admin Index - Redirect to Dashboard
 */
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
header("Location: dashboard.php");
exit;