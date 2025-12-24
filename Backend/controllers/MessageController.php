<?php
require_once __DIR__ . '/../models/Message.php';

class MessageController {
    private $messageModel;

    public function __construct() {
        $this->messageModel = new Message();
    }

    // Get list of all chats
    public function getChats() {
        if (!isset($_SESSION['user_id'])) {
            return ['status' => 'error', 'message' => 'Not logged in'];
        }

        $chats = $this->messageModel->getChats($_SESSION['user_id']);
        return ['status' => 'success', 'chats' => $chats];
    }

    // Get messages with specific user
    public function getMessages() {
        if (!isset($_SESSION['user_id'])) {
            return ['status' => 'error', 'message' => 'Not logged in'];
        }

        if (!isset($_GET['other_id']) || !is_numeric($_GET['other_id'])) {
            return ['status' => 'error', 'message' => 'Invalid user ID'];
        }

        $otherId = (int)$_GET['other_id'];

        $messages = $this->messageModel->getChat($_SESSION['user_id'], $otherId);
        return ['status' => 'success', 'messages' => $messages];
    }

    // Send message (FIXED - removed echo)
    public function sendMessage($data) {
        if (!isset($_SESSION['user_id'])) {
            return ['status' => 'error', 'message' => 'Not logged in'];
        }

        if (!isset($data['to_id']) || !isset($data['message'])) {
            return ['status' => 'error', 'message' => 'Missing required fields'];
        }

        $toId = (int)$data['to_id'];
        $msg = trim($data['message']);
        $flightId = isset($data['flight_id']) ? (int)$data['flight_id'] : null;

        if (empty($msg)) {
            return ['status' => 'error', 'message' => 'Message cannot be empty'];
        }

        $sent = $this->messageModel->send($_SESSION['user_id'], $toId, $msg, $flightId);
        
        if ($sent) {
            return ['status' => 'success', 'time' => date('h:i A')];
        }
        
        return ['status' => 'error', 'message' => 'Failed to send message'];
    }

    // Start chat with company (from flight page)
    public function startChat() {
        if (!isset($_SESSION['user_id'])) {
            return ['status' => 'error', 'message' => 'Not logged in'];
        }

        if (!isset($_GET['flight_id']) || !is_numeric($_GET['flight_id'])) {
            return ['status' => 'error', 'message' => 'Invalid flight ID'];
        }

        $flightId = (int)$_GET['flight_id'];

        $companyUserId = $this->messageModel->getCompanyByFlight($flightId);
        
        if (!$companyUserId) {
            return ['status' => 'error', 'message' => 'Company not found'];
        }

        return ['status' => 'success', 'company_id' => $companyUserId, 'flight_id' => $flightId];
    }

    // Get unread count
    public function getUnreadCount() {
        if (!isset($_SESSION['user_id'])) {
            return ['status' => 'error', 'message' => 'Not logged in'];
        }

        $count = $this->messageModel->getUnreadCount($_SESSION['user_id']);
        return ['status' => 'success', 'count' => $count];
    }
}
?>