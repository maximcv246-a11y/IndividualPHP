<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_admin();
require_once __DIR__ . '/config/database.php';
$pageTitle = 'Admin panel';
require __DIR__ . '/includes/header.php';
?>
<h2>Admin panel</h2>
<ul>
    <li><a href="admin_movies.php">Manage movies (view, add, edit, delete)</a></li>
    <li><a href="create_admin.php">Create administrator account</a></li>
    <li><a href="index.php">Public movie catalog</a></li>
</ul>
<?php require __DIR__ . '/includes/footer.php'; ?>
