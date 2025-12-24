<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Build base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$baseUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/Flight-Booking-Website/frontend";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - SKYBOOK</title>
    <?php include '../../navbar/navbar_css.php'; ?>
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/Messages/messages.css">
</head>
<body>
    <!-- Header Section -->
    <?php include '../../navbar/navbar.php'; ?>

    <div class="messages-container">
        <div class="messages-wrapper">
            <div class="chats-sidebar">
                <div class="sidebar-header">
                    <h2>Messages</h2>
                    <p class="subtitle">Select a conversation</p>
                </div>
                <div class="conversations" id="chatsList">
                    <!-- Conversations will be loaded here -->
                </div>
                <div class="empty-state" id="emptyChats">
                    <div class="empty-icon">💬</div>
                    <p>No conversations yet</p>
                </div>
            </div>

            <div class="chat-main">
                <div class="chat-header" id="chatHeader">
                    <div class="header-content">
                        <h3 id="chatName">SkyBook Messages</h3>
                        <p id="chatType">Select a conversation to start messaging</p>
                    </div>
                </div>

                <div class="messages-area" id="messagesArea">
                    <div class="empty-state" id="welcomeScreen">
                        <div class="empty-icon">💬</div>
                        <div class="empty-title">SkyBook Messages</div>
                        <div class="empty-subtitle">Select a conversation to start messaging</div>
                    </div>
                    <div class="messages-list" id="messagesContent"></div>
                </div>

                <div class="input-area" id="inputArea">
                    <input type="text" id="messageInput" placeholder="Type your message..." autocomplete="off" disabled>
                    <button id="sendButton" disabled>Send</button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= $baseUrl ?>/js/jquery.min.js"></script>
    <script src="<?= $baseUrl ?>/js/Messages/messages.js"></script>
    <script>
        const myUserId = <?php echo $_SESSION['user_id']; ?>;
        const baseUrl = '<?= $baseUrl ?>';
    </script>
</body>
</html>