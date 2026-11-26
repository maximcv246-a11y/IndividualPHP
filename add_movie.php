<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_login();
require_once __DIR__ . '/config/database.php';
$genres = ['Sci-Fi', 'Drama', 'Romance', 'Action', 'Comedy', 'Horror', 'Animation', 'Other'];
$errors = [];
$title = trim($_POST['title'] ?? '');
$year = trim($_POST['year'] ?? '');
$genre = $_POST['genre'] ?? '';
$rating = trim($_POST['rating'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) $errors[] = 'Security check failed. Please try again.';
    if ($title === '' || strlen($title) > 100) $errors[] = 'Title is required (maximum 100 characters).';
    if (!filter_var($year, FILTER_VALIDATE_INT) || (int)$year < 1888 || (int)$year > (int)date('Y') + 5) $errors[] = 'Enter a valid release year.';
    if (!in_array($genre, $genres, true)) $errors[] = 'Select a valid genre.';
    if (!is_numeric($rating) || (float)$rating < 0 || (float)$rating > 10) $errors[] = 'Rating must be from 0 to 10.';
    if ($description === '' || strlen($description) > 1000) $errors[] = 'Description is required (maximum 1000 characters).';
    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO movies(title, year, genre, rating, description, created_by) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$title, (int)$year, $genre, (float)$rating, $description, $_SESSION['user_id']]);
        $_SESSION['message'] = 'Movie was added.';
        header('Location: index.php');
        exit;
    }
}
$pageTitle = 'Add movie';
require __DIR__ . '/includes/header.php';
?>
<h2>Add movie</h2>
<?php foreach ($errors as $error): ?><p class="error"><?= e($error) ?></p><?php endforeach; ?>
<form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Title <input name="title" required maxlength="100" value="<?= e($title) ?>"></label>
    <label>Release year <input type="number" name="year" required min="1888" max="<?= date('Y') + 5 ?>" value="<?= e($year) ?>"></label>
    <label>Genre
        <select name="genre" required>
            <option value="">Select genre</option>
            <?php foreach ($genres as $g): ?><option value="<?= e($g) ?>" <?= $genre === $g ? 'selected' : '' ?>><?= e($g) ?></option><?php endforeach; ?>
        </select>
    </label>
    <label>Rating (0–10) <input type="number" name="rating" required min="0" max="10" step="0.1" value="<?= e($rating) ?>"></label>
    <label>Description <textarea name="description" required maxlength="1000" rows="4"><?= e($description) ?></textarea></label>
    <button type="submit">Add movie</button>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
