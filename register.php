<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';
$errors = [];
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) $errors[] = 'Form security check failed. Please try again.';
    if ($username === '' || strlen($username) < 3 || strlen($username) > 30) $errors[] = 'Username must be 3–30 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (strlen($password) < 8) $errors[] = 'Password must contain at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';
    if (!$errors) {
        $check = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $check->execute([$username, $email]);
        if ($check->fetch()) $errors[] = 'Username or email is already registered.';
    }
    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO users(username, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
        $_SESSION['message'] = 'Registration successful. Please log in.';
        header('Location: login.php');
        exit;
    }
}
$pageTitle = 'Register';
require __DIR__ . '/includes/header.php';
?>
<h2>Registration</h2>
<?php foreach ($errors as $error): ?><p class="error"><?= e($error) ?></p><?php endforeach; ?>
<form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Username <input name="username" required minlength="3" maxlength="30" value="<?= e($username) ?>"></label>
    <label>Email <input type="email" name="email" required maxlength="150" value="<?= e($email) ?>"></label>
    <label>Password <input type="password" name="password" required minlength="8"></label>
    <label>Confirm password <input type="password" name="confirm_password" required minlength="8"></label>
    <button type="submit">Register</button>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
