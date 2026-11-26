<?php
$pageTitle = $pageTitle ?? 'Movie Catalog';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Movie Catalog</h1>
    <nav>
        <a href="index.php">Home</a>
        <?php if (is_logged_in()): ?>
            <a href="add_movie.php">Add movie</a>
            <?php if (is_admin()): ?><a href="admin.php">Admin panel</a><?php endif; ?>
            <a href="logout.php">Logout (<?= e($_SESSION['username']) ?>)</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main>
<?php show_message(); ?>
