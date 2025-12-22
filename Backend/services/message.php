<?php

require_once __DIR__ . '/../utils/Database.php';
require_once __DIR__ . '/../models/Message.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../Frontend/views/auth/login.php');
    exit();
}

$messageModel = new Message();

// Handle different actions
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'markread':
        handleMarkAsRead($messageModel);
        break;
    
    default:
        header('Location: ../../Frontend/views/company/message.php');
        exit();
}

// Function to mark message as read
function handleMarkAsRead($messageModel) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
        $messageModel->markAsRead($_POST['message_id']);
    }
    
    // Redirect based on user type
    if ($_SESSION['user_type'] === 'company') {
        header('Location: ../../Frontend/views/company/message.php');
    } else {
        header('Location: ../../Frontend/views/passenger/message.php');
    }
    exit();
}