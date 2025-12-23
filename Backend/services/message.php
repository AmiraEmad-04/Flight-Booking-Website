<?php

header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controllers/MessageController.php';

$controller = new MessageController();

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_chats':
        echo json_encode($controller->getChats());
        break;
    
    case 'get_messages':
        echo json_encode($controller->getMessages());
        break;
    
    case 'send':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo json_encode($controller->sendMessage($_POST));
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        }
        break;
    
    case 'start_chat':
        echo json_encode($controller->startChat());
        break;

    case 'unread_count':
        echo json_encode($controller->getUnreadCount());
        break;
    
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>