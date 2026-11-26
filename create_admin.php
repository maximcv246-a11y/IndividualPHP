<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_admin();
require_once __DIR__ . '/config/database.php';
$errors = [];
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) $errors[] = 'Security check failed.';
    if ($username === '' || strlen($username) < 3 || strlen($username) > 30) $errors[] = 'Username must be 3–30 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
    $password = $_POST['password'] ?? '';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if (!$errors) {
        $check = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $check->execute([$username, $email]);
        if ($check->fetch()) $errors[] = 'Username or email already exists.';
    }
    if (!$errors) {
        $insert = $pdo->prepare('INSERT INTO users(username, email, password, role) VALUES (?, ?, ?, "admin")');
        $insert->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
        $_SESSION['message'] = 'Administrator account created.';
        header('Location: admin.php');
        exit;
    }
}
$pageTitle = 'Create administrator';
require __DIR__ . '/includes/header.php';
?>
<h2>Create administrator account</h2>
<?php foreach ($errors as $error): ?><p class="error"><?= e($error) ?></p><?php endforeach; ?>
<form method="post">
<input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<label>Username <input name="username" required minlength="3" maxlength="30" value="<?= e($username) ?>"></label>
<label>Email <input type="email" name="email" required value="<?= e($email) ?>"></label>
<label>Password <input type="password" name="password" required minlength="8"></label>
<button type="submit">Create admin</button>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
