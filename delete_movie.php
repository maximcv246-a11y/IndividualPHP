<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_admin();
require_once __DIR__ . '/config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) {
    http_response_code(400);
    exit('Invalid request.');
}
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $stmt = $pdo->prepare('DELETE FROM movies WHERE id = ?');
    $stmt->execute([$id]);
    $_SESSION['message'] = 'Movie deleted.';
}
header('Location: admin_movies.php');
exit;
