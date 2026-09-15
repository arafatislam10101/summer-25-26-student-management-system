<?php
// ================================================================
// CONTROLLER: Messaging
// ================================================================
require_once __DIR__ . '/../models/message_model.php';

    /**
     * Show received messages and the active recipient list.
     */
function message_controller_index(): void
    {
        $user = role('admin', 'teacher', 'student', 'parent');
        $messages = message_receivedByUser((int) $user['id']);
        $users = message_recipients((int) $user['id']);
        $title = 'Messaging';
        $view = __DIR__ . '/../views/partials/messages.php';
        require __DIR__ . '/../views/partials/header.php';
        require $view;
        require __DIR__ . '/../views/partials/footer.php';
    }
    /**
     * Send a message to another active user.
     */
function message_controller_send(): void
    {
        $user = role('admin', 'teacher', 'student', 'parent');
        check_csrf();
        $receiverId = (int) ($_POST['receiver_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');
        if ($receiverId <= 0 || $message === '') {
            message_controller_bad('Please select a recipient and enter a message.');
        }
        if (!message_activeUserExists($receiverId)) {
            message_controller_bad('Invalid recipient.');
        }
        $success = message_send(
        (int) $user['id'],
        $receiverId,
        $message
        );
        flash($success ? 'Message sent.' : 'Unable to send message.');
        redirect('messages');
    }
    /**
     * Show an error and return to the messaging page.
     */
function message_controller_bad(string $message): never
    {
        flash($message);
        redirect('messages');
    }

