<?php
// ================================================================
// MODEL: Messaging
// ================================================================
require_once __DIR__ . '/model.php';

    /**
     * Get all messages received by a user.
     */
function message_receivedByUser(int $userId): array
    {
        $sql = "
            SELECT m.*, u.name AS sender_name
            FROM messages m
            JOIN users u ON u.id = m.sender_id
            WHERE m.receiver_id = ?
            ORDER BY m.id DESC
        ";
        $stmt = mysqli_prepare(db(), $sql);
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }
    /**
     * Get active users who can receive a message.
     */
function message_recipients(int $currentUserId): array
    {
        $sql = "
            SELECT id, name, role
            FROM users
            WHERE status = 'active'
              AND id <> ?
            ORDER BY name
        ";
        $stmt = mysqli_prepare(db(), $sql);
        mysqli_stmt_bind_param($stmt, 'i', $currentUserId);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }
    /**
     * Check whether a recipient is active.
     */
function message_activeUserExists(int $userId): bool
    {
        $stmt = mysqli_prepare(db(), 
        "SELECT id FROM users WHERE id = ? AND status = 'active'"
        );
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        return (bool) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }
    /**
     * Create a new message.
     */
function message_send(int $senderId, int $receiverId, string $message): bool
    {
        $stmt = mysqli_prepare(db(), 
        'INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)'
        );
        mysqli_stmt_bind_param($stmt, 'iis', $senderId, $receiverId, $message);
        return mysqli_stmt_execute($stmt);
    }

