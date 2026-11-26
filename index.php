<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';

$titleSearch = trim($_GET['title'] ?? '');
$genreSearch = trim($_GET['genre'] ?? '');
$yearSearch = trim($_GET['year'] ?? '');

$sql = 'SELECT * FROM movies WHERE 1=1';
$params = [];
if ($titleSearch !== '') {
    $sql .= ' AND title LIKE :title';
    $params[':title'] = '%' . $titleSearch . '%';
}
$genres = ['Sci-Fi', 'Drama', 'Romance', 'Action', 'Comedy', 'Horror', 'Animation', 'Other'];
if ($genreSearch !== '' && in_array($genreSearch, $genres, true)) {
    $sql .= ' AND genre = :genre';
    $params[':genre'] = $genreSearch;
}
if ($yearSearch !== '' && filter_var($yearSearch, FILTER_VALIDATE_INT)) {
    $sql .= ' AND year = :year';
    $params[':year'] = (int)$yearSearch;
}
$sql .= ' ORDER BY title';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movies = $stmt->fetchAll();

$pageTitle = 'Movie Catalog';
require __DIR__ . '/includes/header.php';
?>
<h2>Movie list</h2>
<form method="get" class="search-form">
    <label>Title
        <input type="text" name="title" maxlength="100" value="<?= e($titleSearch) ?>">
    </label>
    <label>Genre
        <select name="genre">
            <option value="">All genres</option>
            <?php foreach ($genres as $genre): ?>
                <option value="<?= e($genre) ?>" <?= $genreSearch === $genre ? 'selected' : '' ?>><?= e($genre) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Year
        <input type="number" name="year" min="1888" max="<?= date('Y') + 5 ?>" value="<?= e($yearSearch) ?>">
    </label>
    <button type="submit">Search</button>
    <a href="index.php">Reset</a>
</form>

<?php if ($movies): ?>
<table>
    <thead><tr><th>Title</th><th>Genre</th><th>Year</th><th>Rating</th><th>Description</th></tr></thead>
    <tbody>
    <?php foreach ($movies as $movie): ?>
        <tr>
            <td><?= e($movie['title']) ?></td>
            <td><?= e($movie['genre']) ?></td>
            <td><?= e($movie['year']) ?></td>
            <td><?= e($movie['rating']) ?>/10</td>
            <td><?= e($movie['description']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>No movies found.</p>
<?php endif; ?>
<p>Movies in results: <?= count($movies) ?></p>
<?php require __DIR__ . '/includes/footer.php'; ?>
