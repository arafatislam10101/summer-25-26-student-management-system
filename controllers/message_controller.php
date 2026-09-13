<?php
// ================================================================
// CONTROLLER: Messaging
// ================================================================
require_once __DIR__ . '/../models/message_model.php';
class MessageController
{
    private Message $model;
    public function __construct()
    {
        $this->model = new Message();
    }
    /**
     * Show received messages and the active recipient list.
     */
    public function index(): void
    {
        $user = role('admin', 'teacher', 'student', 'parent');
        $messages = $this->model->receivedByUser((int) $user['id']);
        $users = $this->model->recipients((int) $user['id']);
        $title = 'Messaging';
        $view = __DIR__ . '/../views/partials/messages.php';
        require __DIR__ . '/../views/partials/header.php';
        require $view;
        require __DIR__ . '/../views/partials/footer.php';
    }
    /**
     * Send a message to another active user.
     */
    public function send(): void
    {
        $user = role('admin', 'teacher', 'student', 'parent');
        check_csrf();
        $receiverId = (int) ($_POST['receiver_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');
        if ($receiverId <= 0 || $message === '') {
            $this->bad('Please select a recipient and enter a message.');
        }
        if (!$this->model->activeUserExists($receiverId)) {
            $this->bad('Invalid recipient.');
        }
        $success = $this->model->send(
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
    private function bad(string $message): never
    {
        flash($message);
        redirect('messages');
    }
}
