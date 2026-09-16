<?php
/**
 * Header Template
 * SMK INFOKOM BOGOR
 */

$school = get_school_profile();
$page_title = $page_title ?? 'SMK INFOKOM BOGOR';
$meta_desc = $meta_desc ?? ($school['deskripsi'] ?? 'Website Resmi SMK INFOKOM BOGOR');
$body_class = $body_class ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($meta_desc) ?>">
    <meta name="keywords" content="SMK INFOKOM BOGOR, sekolah, SMK, pendidikan, Bogor">
    <meta name="author" content="SMK INFOKOM BOGOR">
    <meta property="og:title" content="<?= e($page_title) ?>">
    <meta property="og:description" content="<?= e($meta_desc) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= BASE_URL ?>">
    <title><?= e($page_title) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body class="<?= e($body_class) ?>">

<!-- Skip to content -->
<a href="#main-content" class="skip-link">Langsung ke konten</a>