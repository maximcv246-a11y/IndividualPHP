<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_admin();
require_once __DIR__ . '/config/database.php';
$genres = ['Sci-Fi', 'Drama', 'Romance', 'Action', 'Comedy', 'Horror', 'Animation', 'Other'];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM movies WHERE id = ?');
$stmt->execute([$id]);
$movie = $stmt->fetch();
if (!$movie) { http_response_code(404); exit('Movie not found.'); }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) $errors[] = 'Security check failed.';
    $title = trim($_POST['title'] ?? '');
    $year = $_POST['year'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $description = trim($_POST['description'] ?? '');
    if ($title === '' || strlen($title) > 100) $errors[] = 'Title is required (maximum 100 characters).';
    if (!filter_var($year, FILTER_VALIDATE_INT) || (int)$year < 1888 || (int)$year > (int)date('Y') + 5) $errors[] = 'Invalid year.';
    if (!in_array($genre, $genres, true)) $errors[] = 'Invalid genre.';
    if (!is_numeric($rating) || (float)$rating < 0 || (float)$rating > 10) $errors[] = 'Rating must be 0–10.';
    if ($description === '' || strlen($description) > 1000) $errors[] = 'Description is required (maximum 1000 characters).';
    if (!$errors) {
        $update = $pdo->prepare('UPDATE movies SET title=?, year=?, genre=?, rating=?, description=? WHERE id=?');
        $update->execute([$title, (int)$year, $genre, (float)$rating, $description, $id]);
        $_SESSION['message'] = 'Movie updated.';
        header('Location: admin_movies.php');
        exit;
    }
    $movie = compact('title', 'year', 'genre', 'rating', 'description') + ['id' => $id];
}
$pageTitle = 'Edit movie';
require __DIR__ . '/includes/header.php';
?>
<h2>Edit movie</h2>
<?php foreach ($errors as $error): ?><p class="error"><?= e($error) ?></p><?php endforeach; ?>
<form method="post">
<input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<label>Title <input name="title" required maxlength="100" value="<?= e($movie['title']) ?>"></label>
<label>Release year <input type="number" name="year" required min="1888" max="<?= date('Y') + 5 ?>" value="<?= e($movie['year']) ?>"></label>
<label>Genre <select name="genre" required><?php foreach ($genres as $g): ?><option value="<?= e($g) ?>" <?= $movie['genre'] === $g ? 'selected' : '' ?>><?= e($g) ?></option><?php endforeach; ?></select></label>
<label>Rating (0–10) <input type="number" name="rating" required min="0" max="10" step="0.1" value="<?= e($movie['rating']) ?>"></label>
<label>Description <textarea name="description" required maxlength="1000" rows="4"><?= e($movie['description']) ?></textarea></label>
<button type="submit">Save changes</button>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
