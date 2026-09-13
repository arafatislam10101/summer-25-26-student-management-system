<?php
// ================================================================
// HELPERS - common application functions
// Pattern follows the reference Library Management System.
// ================================================================
/* ================= Output / URL helpers ================= */
function esc($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
function e($value): string
{
    return esc($value);
}
function base_url(): string
{
    return 'index.php?page=';
}
function redirect(string $url): never
{
    // Internal application routes are passed as short page names.
    // Full URLs are also supported for compatibility.
    if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')
    && !str_starts_with($url, 'index.php')) {
        $url = base_url() . $url;
    }
    header('Location: ' . $url);
    exit;
}
function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}
/* ================= CSRF protection ================= */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
// Backward-compatible name used by the existing EduManage views.
function csrf(): string
{
    return csrf_token();
}
function csrf_field(): void
{
    echo '<input type="hidden" name="csrf" value="' . esc(csrf_token()) . '">';
}
function csrf_url(string $url): string
{
    $separator = str_contains($url, '?') ? '&' : '?';
    return $url . $separator . 'csrf_token=' . urlencode(csrf_token());
}
function csrf_check(): void
{
    $token = $_POST['csrf_token'] ?? $_POST['csrf'] ?? $_GET['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('Security check failed (invalid CSRF token). Please go back and try again.');
    }
}
// Existing controller alias.
function check_csrf(): void
{
    csrf_check();
}
/* ================= Authentication / roles ================= */
function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}
function current_role(): string
{
    return $_SESSION['user']['role'] ?? '';
}
function auth(): array
{
    if (!is_logged_in()) {
        set_flash('error', 'Please log in to continue.');
        redirect('login');
    }
    if (isset($_SESSION['last_active'])
    && time() - (int)$_SESSION['last_active'] > SESSION_TIMEOUT) {
        $_SESSION = [];
        session_regenerate_id(true);
        set_flash('error', 'Your session expired. Please log in again.');
        redirect('login');
    }
    $_SESSION['last_active'] = time();
    // Keep the old key in sync so existing code remains compatible.
    $_SESSION['last_activity'] = $_SESSION['last_active'];
    return $_SESSION['user'];
}
function require_role(string ...$roles): array
{
    $user = auth();
    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('Access denied.');
    }
    return $user;
}
// Existing controller alias.
function role(string ...$roles): array
{
    return require_role(...$roles);
}
function check_session_timeout(): void
{
    if (is_logged_in()) {
        auth();
    }
}
function role_label(string $role): string
{
    $labels = [
    'admin'   => 'Administrator',
    'teacher' => 'Teacher',
    'student' => 'Student',
    'parent'  => 'Parent',
    ];
    return $labels[$role] ?? ucfirst($role);
}
/* ================= Flash messages ================= */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'][] = [
    'type' => $type,
    'message' => $message,
    ];
}
function get_flash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}
// Existing single-message API used by EduManage.
function flash(?string $message = null): ?string
{
    if ($message !== null) {
        set_flash('success', $message);
        return null;
    }
    $messages = get_flash();
    return $messages[0]['message'] ?? null;
}
/* ================= AJAX / JSON ================= */
function json_out(array $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}
function json_response(bool $ok, string $message, array $data = []): never
{
    json_out([
    'ok' => $ok,
    'message' => $message,
    'data' => $data,
    ]);
}
/* ================= Validation / utilities ================= */
function is_blank($value): bool
{
    return trim((string)$value) === '';
}
function valid_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
function valid_contact(string $contact): bool
{
    return preg_match('/^[0-9+\-\s()]{6,20}$/', $contact) === 1;
}
function valid_date(string $date): bool
{
    $parts = explode('-', $date);
    return count($parts) === 3
    && checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0]);
}
function old(string $key): string
{
    return esc($_POST[$key] ?? '');
}
