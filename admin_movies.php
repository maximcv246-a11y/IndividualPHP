<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_admin();
require_once __DIR__ . '/config/database.php';
$movies = $pdo->query('SELECT * FROM movies ORDER BY id')->fetchAll();
$pageTitle = 'Manage movies';
require __DIR__ . '/includes/header.php';
?>
<h2>Manage movies</h2>
<p><a href="add_movie.php">Add a movie</a></p>
<table>
<thead><tr><th>ID</th><th>Title</th><th>Genre</th><th>Year</th><th>Rating</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($movies as $m): ?>
<tr>
<td><?= e($m['id']) ?></td><td><?= e($m['title']) ?></td><td><?= e($m['genre']) ?></td><td><?= e($m['year']) ?></td><td><?= e($m['rating']) ?></td>
<td><a href="edit_movie.php?id=<?= (int)$m['id'] ?>">Edit</a> |
<form class="inline" method="post" action="delete_movie.php" onsubmit="return confirm('Delete this movie?');">
<input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
<button type="submit">Delete</button>
</form></td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php require __DIR__ . '/includes/footer.php'; ?>
