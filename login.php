<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';
$error = '';
$login = trim($_POST['login'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $error = 'Security check failed. Please reload the page.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1');
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();
        if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['message'] = 'You are logged in.';
            header('Location: index.php');
            exit;
        }
        $error = 'Incorrect username/email or password.';
    }
}
$pageTitle = 'Login';
require __DIR__ . '/includes/header.php';
?>
<h2>Login</h2>
<?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
<form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Username or Email <input name="login" required value="<?= e($login) ?>"></label>
    <label>Password <input type="password" name="password" required></label>
    <button type="submit">Login</button>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
