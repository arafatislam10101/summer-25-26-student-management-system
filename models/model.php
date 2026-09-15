<?php
require_once __DIR__ . '/../config/config.php';

// Procedural database helpers. No classes or objects are used.
function db_fetch_all(string $sql, string $types = '', array $params = []): array {
    $stmt = mysqli_prepare(db(), $sql);
    if (!$stmt) return [];
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$params);
    if (!mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); return []; }
    $result = mysqli_stmt_get_result($stmt);
    $rows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);
    return $rows;
}
function db_fetch_one(string $sql, string $types = '', array $params = []): ?array {
    $rows = db_fetch_all($sql, $types, $params);
    return $rows[0] ?? null;
}
function db_execute(string $sql, string $types = '', array $params = []): bool {
    $stmt = mysqli_prepare(db(), $sql);
    if (!$stmt) return false;
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$params);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}
?>
