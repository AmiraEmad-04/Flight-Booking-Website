<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../../Backend/utils/Database.php';
require_once __DIR__ . '/../../../Backend/models/Message.php';

$messageModel = new Message();

// Get all messages for this user
$messages = $messageModel->getMessagesByUser($_SESSION['user_id']);

// Get user info for header
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT name FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        
        .header {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #333;
            margin: 0 15px;
            padding: 8px 15px;
            border-radius: 5px;
        }
        
        .nav-links a:hover {
            background: #f0f0f0;
        }
        
        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .page-title {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 25px;
        }
        
        .messages-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .message-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
            transition: background 0.2s;
        }
        
        .message-item:hover {
            background: #f8f9fa;
        }
        
        .message-item:last-child {
            border-bottom: none;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .sender-name {
            font-weight: bold;
            color: #2c3e50;
            font-size: 16px;
        }
        
        .message-time {
            color: #7f8c8d;
            font-size: 13px;
        }
        
        .message-content {
            color: #555;
            line-height: 1.6;
            margin-bottom: 8px;
        }
        
        .flight-ref {
            display: inline-block;
            background: #e8f4f8;
            color: #3498db;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .unread {
            background: #e8f4f8;
        }
        
        .unread .sender-name {
            color: #3498db;
        }
        
        .mark-read-form {
            display: inline;
        }
        
        .mark-read-btn {
            background: none;
            border: none;
            color: #3498db;
            cursor: pointer;
            text-decoration: underline;
            font-size: 13px;
            padding: 0;
        }
        
        .mark-read-btn:hover {
            color: #2980b9;
        }
        
        .no-messages {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .no-messages-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background: #95a5a6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .back-button:hover {
            background: #7f8c8d;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Messages</h2>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="message.php">Messages</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="container">
    
    <a href="index.php" class="back-button">← Back to Home</a>
    
    <h1 class="page-title">Your Messages</h1>
    
    <div class="messages-container">
        <?php if (empty($messages)): ?>
            <div class="no-messages">
                <div class="no-messages-icon">📭</div>
                <p>No messages yet</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $message): ?>
                <div class="message-item <?php echo $message['is_read'] ? '' : 'unread'; ?>">
                    <div class="message-header">
                        <span class="sender-name">
                            <?php echo htmlspecialchars($message['sender_name']); ?>
                            <?php if (!$message['is_read']): ?>
                                <span style="color: #e74c3c; font-size: 12px;">(New)</span>
                            <?php endif; ?>
                        </span>
                        <span class="message-time">
                            <?php echo date('M d, Y - H:i', strtotime($message['sent_at'])); ?>
                        </span>
                    </div>
                    
                    <div class="message-content">
                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                    </div>
                    
                    <?php if ($message['flight_name']): ?>
                        <span class="flight-ref">
                            ✈️ <?php echo htmlspecialchars($message['flight_name']); ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if (!$message['is_read']): ?>
                        <form action="../../../Backend/services/message.php?action=markread" method="POST" class="mark-read-form">
                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                            <button type="submit" class="mark-read-btn">Mark as read</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
</div>

</body>
</html>