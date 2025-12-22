<?php

require_once __DIR__ . '/../utils/Database.php';
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../utils/Validator.php';

class MessageController {
    
    private $messageModel;

    public function __construct() {
        $this->messageModel = new Message();
    }

    // Get all messages for logged in user
    public function getMessages() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            return [];
        }

        return $this->messageModel->getMessagesByUser($_SESSION['user_id']);
    }

    // Get unread message count
    public function getUnreadCount() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            return 0;
        }

        return $this->messageModel->getUnreadCount($_SESSION['user_id']);
    }

    // Mark message as read
    public function markAsRead() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../login.php');
            exit();
        }

        if (isset($_POST['message_id'])) {
            $this->messageModel->markAsRead($_POST['message_id']);
        }

        header('Location: ../message.php');
        exit();
    }
}

// Handle the request
if (isset($_GET['action']) && $_GET['action'] === 'markread') {
    $controller = new MessageController();
    $controller->markAsRead();
}