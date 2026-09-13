<?php
// ================================================================
// MODEL: Messaging
// ================================================================
require_once __DIR__ . '/model.php';
class Message extends Model
{
    /**
     * Get all messages received by a user.
     */
    public function receivedByUser(int $userId): array
    {
        $sql = "
            SELECT m.*, u.name AS sender_name
            FROM messages m
            JOIN users u ON u.id = m.sender_id
            WHERE m.receiver_id = ?
            ORDER BY m.id DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    /**
     * Get active users who can receive a message.
     */
    public function recipients(int $currentUserId): array
    {
        $sql = "
            SELECT id, name, role
            FROM users
            WHERE status = 'active'
              AND id <> ?
            ORDER BY name
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $currentUserId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    /**
     * Check whether a recipient is active.
     */
    public function activeUserExists(int $userId): bool
    {
        $stmt = $this->db->prepare(
        "SELECT id FROM users WHERE id = ? AND status = 'active'"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return (bool) $stmt->get_result()->fetch_assoc();
    }
    /**
     * Create a new message.
     */
    public function send(int $senderId, int $receiverId, string $message): bool
    {
        $stmt = $this->db->prepare(
        'INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)'
        );
        $stmt->bind_param('iis', $senderId, $receiverId, $message);
        return $stmt->execute();
    }
}
