<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$base = '/campus-resolve/public';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusResolve</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/styles.css">
</head>
<body>
    <header class="top-bar">
        <div class="top-bar-inner">
            <img src="<?= $base ?>/assets/images/icon.png" alt="" class="site-icon">
            <a href="<?= $base ?>/index.php" class="site-name">CampusResolve</a>
        </div>
    </header>

    <?php if (isset($breadcrumb)): ?>
        <nav class="breadcrumb">
            <?php foreach ($breadcrumb as $i => $crumb): ?>
                <?php if ($i > 0): ?><span class="crumb-sep">&gt;</span><?php endif; ?>
                <?php if (isset($crumb['url'])): ?>
                    <a href="<?= $crumb['url'] ?>"><?= htmlspecialchars($crumb['label']) ?></a>
                <?php else: ?>
                    <span class="crumb-current"><?= htmlspecialchars($crumb['label']) ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>

    <nav class="secondary-nav">
        <a href="submit.php" class="<?= $currentPage === 'submit.php' ? 'active' : '' ?>">Submit a complaint</a>
        <a href="track.php" class="<?= $currentPage === 'track.php' ? 'active' : '' ?>">Track a complaint</a>
        <a href="departments.php" class="<?= $currentPage === 'departments.php' ? 'active' : '' ?>">Departments</a>
        <a href="faq.php" class="<?= $currentPage === 'faq.php' ? 'active' : '' ?>">FAQ</a>
        <a href="admin.php" class="<?= $currentPage === 'admin.php' ? 'active' : '' ?>">Admin dashboard</a>
    </nav>

    <main class="page-content">