<?php
/**
 * Безопасно выводит текст в HTML.
 * @param mixed $value Значение для вывода.
 * @return string Экранированная строка.
 */
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Создаёт токен защиты CSRF для сессии. */
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/**
 * Проверяет токен CSRF из POST.
 * @return bool Результат проверки.
 */
function csrf_valid(): bool {
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

/** Проверяет, вошёл ли пользователь. */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

/** Проверяет наличие роли администратора. */
function is_admin(): bool {
    return ($_SESSION['role'] ?? '') === 'admin';
}

/** Перенаправляет гостя на страницу входа. */
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/** Перенаправляет пользователя без роли admin на главную. */
function require_admin(): void {
    if (!is_admin()) {
        http_response_code(403);
        exit('Access denied. Administrator role required.');
    }
}

/** Отображает сообщения из сессии и удаляет их после вывода. */
function show_message(): void {
    if (!empty($_SESSION['message'])) {
        echo '<p class="message">' . e($_SESSION['message']) . '</p>';
        unset($_SESSION['message']);
    }
}
