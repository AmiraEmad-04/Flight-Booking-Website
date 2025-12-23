<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = "Messages - SKYBOOK";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../../css/Home/style.css">
    <?php include '../../navbar/navbar_css.php'; ?>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .messages-container {
            max-width: 1400px;
            margin: 100px auto 40px;
            padding: 0 20px;
        }

        .messages-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            height: 80vh;
            display: flex;
        }

        /* Chats List */
        .chats-sidebar {
            width: 350px;
            border-right: 2px solid #e0e0e0;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
        }

        .chats-header {
            padding: 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .chats-list {
            flex: 1;
            overflow-y: auto;
        }

        .chat-item {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .chat-item:hover {
            background: #e9ecef;
        }

        .chat-item.active {
            background: linear-gradient(90deg, #e3f2fd 0%, #f3e5f5 100%);
            border-left: 5px solid #667eea;
        }

        .chat-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .chat-name {
            font-weight: bold;
            font-size: 16px;
            color: #333;
        }

        .chat-type-badge {
            padding: 3px 10px;
            background: #667eea;
            color: white;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .chat-preview {
            font-size: 14px;
            color: #666;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Chat Area */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .chat-header {
            padding: 25px 30px;
            background: #f8f9fa;
            border-bottom: 2px solid #e0e0e0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .chat-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 20px;
        }

        .chat-header-info h3 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }

        .chat-header-info p {
            margin: 5px 0 0;
            font-size: 13px;
            color: #666;
        }

        .messages-area {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            background: linear-gradient(to bottom, #fafbfc 0%, #f5f6f8 100%);
        }

        .message-item {
            margin-bottom: 20px;
            display: flex;
            animation: messageSlide 0.3s ease;
        }

        @keyframes messageSlide {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-item.sent {
            justify-content: flex-end;
        }

        .message-bubble {
            max-width: 70%;
            padding: 14px 18px;
            border-radius: 18px;
            word-wrap: break-word;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .message-item.sent .message-bubble {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message-item.received .message-bubble {
            background: white;
            color: #333;
            border-bottom-left-radius: 4px;
        }

        .message-time {
            font-size: 11px;
            margin-top: 6px;
            opacity: 0.8;
        }

        .message-input-area {
            padding: 25px 30px;
            background: white;
            border-top: 2px solid #e0e0e0;
            display: flex;
            gap: 15px;
        }

        .message-input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 30px;
            font-size: 15px;
            outline: none;
            transition: border 0.3s;
        }

        .message-input:focus {
            border-color: #667eea;
        }

        .send-button {
            padding: 15px 35px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: bold;
            font-size: 15px;
            transition: all 0.3s;
        }

        .send-button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .send-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: scale(1);
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #999;
        }

        .empty-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .empty-text {
            font-size: 18px;
            text-align: center;
        }

        .loading {
            text-align: center;
            padding: 30px;
            color: #666;
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            margin: 15px;
            border-radius: 8px;
            border-left: 4px solid #c62828;
        }

        /* Scrollbar styling */
        .chats-list::-webkit-scrollbar,
        .messages-area::-webkit-scrollbar {
            width: 8px;
        }

        .chats-list::-webkit-scrollbar-track,
        .messages-area::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .chats-list::-webkit-scrollbar-thumb,
        .messages-area::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .chats-list::-webkit-scrollbar-thumb:hover,
        .messages-area::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        @media (max-width: 768px) {
            .messages-wrapper {
                flex-direction: column;
                height: auto;
                min-height: 80vh;
            }

            .chats-sidebar {
                width: 100%;
                max-height: 300px;
            }

            .chat-main {
                min-height: 500px;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="messages-container">
        <div class="messages-wrapper">
            <!-- Chats Sidebar -->
            <div class="chats-sidebar">
                <div class="chats-header">
                    💬 Messages
                </div>
                <div class="chats-list" id="chatsList">
                    <div class="loading">Loading conversations...</div>
                </div>
            </div>

            <!-- Chat Main Area -->
            <div class="chat-main">
                <div class="chat-header" id="chatHeader" style="display:none;">
                    <div class="chat-avatar" id="chatAvatar"></div>
                    <div class="chat-header-info">
                        <h3 id="chatName"></h3>
                        <p id="chatType"></p>
                    </div>
                </div>

                <div class="messages-area" id="messagesArea">
                    <div class="empty-state">
                        <div class="empty-icon">💬</div>
                        <div class="empty-text">Select a conversation to start messaging</div>
                    </div>
                </div>

                <div class="message-input-area" id="inputArea" style="display:none;">
                    <input type="text" class="message-input" id="messageInput" placeholder="Type your message..." autocomplete="off">
                    <button class="send-button" id="sendButton">Send ➤</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        let currentChatUserId = null;
        const myUserId = <?php echo $_SESSION['user_id']; ?>;

        function getInitials(name) {
            if (!name) return '?';
            const parts = name.trim().split(' ');
            if (parts.length >= 2) {
                return (parts[0][0] + parts[1][0]).toUpperCase();
            }
            return name.substring(0, 2).toUpperCase();
        }

        function loadChats() {
            $.get('../../Backend/services/message.php?action=get_chats', function(data) {
                if (data.status === 'error') {
                    $('#chatsList').html('<div class="error-message">' + data.message + '</div>');
                    return;
                }

                $('#chatsList').empty();
                
                if (!data.chats || data.chats.length === 0) {
                    $('#chatsList').html('<div class="empty-state" style="padding:40px; text-align:center;">No conversations yet</div>');
                    return;
                }

                data.chats.forEach(function(chat) {
                    const item = `
                        <div class="chat-item" data-id="${chat.chat_with_user_id}" data-name="${chat.name}" data-type="${chat.type}">
                            <div class="chat-item-header">
                                <span class="chat-name">${chat.name}</span>
                                <span class="chat-type-badge">${chat.type === 'company' ? 'Company' : 'Passenger'}</span>
                            </div>
                            <div class="chat-preview">${chat.last_msg || 'No messages yet'}</div>
                        </div>
                    `;
                    $('#chatsList').append(item);
                });
            });
        }

        function loadMessages(userId) {
            $('#messagesArea').html('<div class="loading">Loading...</div>');
            
            $.get('../../Backend/services/message.php?action=get_messages&other_id=' + userId, function(data) {
                if (data.status === 'error') {
                    $('#messagesArea').html('<div class="error-message">' + data.message + '</div>');
                    return;
                }

                $('#messagesArea').empty();

                if (!data.messages || data.messages.length === 0) {
                    $('#messagesArea').html('<div class="empty-state"><div class="empty-icon">💬</div><div class="empty-text">No messages yet<br>Start the conversation!</div></div>');
                } else {
                    data.messages.forEach(function(msg) {
                        const isSent = msg.sender_id != userId;
                        const msgHtml = `
                            <div class="message-item ${isSent ? 'sent' : 'received'}">
                                <div class="message-bubble">
                                    ${msg.message}
                                    <div class="message-time">${msg.time}</div>
                                </div>
                            </div>
                        `;
                        $('#messagesArea').append(msgHtml);
                    });
                }

                $('#messagesArea').scrollTop($('#messagesArea')[0].scrollHeight);
            });
        }

        function openChat(userId, name, type) {
            currentChatUserId = userId;

            $('.chat-item').removeClass('active');
            $(`.chat-item[data-id="${userId}"]`).addClass('active');

            $('#chatAvatar').text(getInitials(name));
            $('#chatName').text(name);
            $('#chatType').text(type === 'company' ? 'Company' : 'Passenger');
            $('#chatHeader').show();
            $('#inputArea').show();

            loadMessages(userId);
        }

        function sendMessage() {
            const text = $('#messageInput').val().trim();
            if (!text || !currentChatUserId) return;

            $('#sendButton').prop('disabled', true);

            $.post('../../Backend/services/message.php?action=send', {
                to_id: currentChatUserId,
                message: text,
                flight_id: null
            }, function(data) {
                if (data.status === 'success') {
                    const msgHtml = `
                        <div class="message-item sent">
                            <div class="message-bubble">
                                ${text}
                                <div class="message-time">${data.time}</div>
                            </div>
                        </div>
                    `;
                    
                    if ($('#messagesArea .empty-state').length) {
                        $('#messagesArea').empty();
                    }
                    
                    $('#messagesArea').append(msgHtml);
                    $('#messagesArea').scrollTop($('#messagesArea')[0].scrollHeight);
                    $('#messageInput').val('');
                    loadChats();
                } else {
                    alert('Failed: ' + data.message);
                }
            }).always(function() {
                $('#sendButton').prop('disabled', false);
            });
        }

        $(document).ready(function() {
            loadChats();

            const urlParams = new URLSearchParams(window.location.search);
            const companyId = urlParams.get('company_id');

            if (companyId) {
                setTimeout(function() {
                    const chatItem = $(`.chat-item[data-id="${companyId}"]`);
                    if (chatItem.length) {
                        chatItem.click();
                    }
                }, 500);
            }

            $(document).on('click', '.chat-item', function() {
                openChat($(this).data('id'), $(this).data('name'), $(this).data('type'));
            });

            $('#sendButton').click(sendMessage);
            $('#messageInput').keypress(function(e) {
                if (e.which === 13) sendMessage();
            });

            setInterval(function() {
                if (currentChatUserId) {
                    loadMessages(currentChatUserId);
                }
            }, 5000);
        });
    </script>
</body>
</html>