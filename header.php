<?php 
require_once __DIR__.'/auth.php';
require_once __DIR__.'/db.php';
$pageTitle = $pageTitle ?? 'DKAP';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> · DKAP</title>
    <link rel="stylesheet" href="/dkap/assets/css/style.css">
</head>
<body>
<header class="nav">
    <a class="brand" href="/dkap/index.php"><span class="logo">◆</span> DKAP</a>
    <nav class="nav-links">
        <a href="/dkap/index.php">Discover</a>
        <a href="/dkap/library.php">Library</a>
        <?php if (is_logged_in()): ?>
            <a href="/dkap/bookmarks.php">Saved</a>
            <a href="/dkap/dashboard.php">Dashboard</a>
            <?php if (current_user()['role'] === 'admin'): ?>
                <a href="/dkap/admin/index.php">Admin</a>
            <?php endif; ?>
            <a class="btn-ghost" href="/dkap/logout.php">Logout</a>
        <?php else: ?>
            <a href="/dkap/login.php">Login</a>
            <a class="btn-primary" href="/dkap/register.php">Join Free</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">