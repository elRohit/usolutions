<?php
require_once 'functions.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/pages/<?php echo $current_page; ?>.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="/pages/home.php">
                    <h1><?php echo SITE_NAME; ?></h1>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="../pages/home.php" class="<?php echo $current_page === 'home' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="../pages/about.php" class="<?php echo $current_page === 'about' ? 'active' : ''; ?>">About Us</a></li>
                    <li><a href="../pages/servers.php" class="<?php echo $current_page === 'servers' ? 'active' : ''; ?>">Servers</a></li>
                    <li><a href="../pages/contact.php" class="<?php echo $current_page === 'contact' ? 'active' : ''; ?>">Contact Us</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if (isLoggedIn()): ?>
                    <a href="../pages/dashboard.php" class="btn btn-primary">Dashboard</a>
                    <a href="../handlers/logout.php" class="btn btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="../pages/login.php" class="btn btn-primary">Login</a>
                    <a href="../pages/register.php" class="btn btn-secondary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
