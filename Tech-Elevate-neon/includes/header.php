<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech-Elevate</title>
    <link rel="stylesheet" href="/Tech-Elevate-neon/assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="//cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header class="site-header">
    <div class="logo-wrap">
        <a href="/Tech-Elevate-neon/index.php" class="logo-link" aria-label="Tech Elevate Home">
            Tech Elevate
        </a>
    </div>
    <nav class="top-nav">
        <?php if (isset($_SESSION['user_id'])) : ?>
            <a href="/Tech-Elevate-neon/dashboard.php">Dashboard</a>
            <a href="/Tech-Elevate-neon/explore.php">Explore</a>
            <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                <a href="/Tech-Elevate-neon/admin/dashboard.php">Admin</a>
            <?php endif; ?>
            <a href="/Tech-Elevate-neon/logout.php">Logout</a>
        <?php else : ?>
            <a href="/Tech-Elevate-neon/index.php">Home</a>
            <a href="/Tech-Elevate-neon/login.php">Login</a>
            <a href="/Tech-Elevate-neon/register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
<div class="main-wrapper">
    <main class="page-body">