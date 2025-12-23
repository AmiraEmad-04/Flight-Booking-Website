<!DOCTYPE html>
<html>
<head>
    <title>Messages - SKYBOOK</title>
    <?php 
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }
    include '../../navbar/navbar_css.php'; 
    ?>
    <style>
        /* Reset body positioning to account for fixed navbar */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('../../images/photo-1436491865332-7a61a109cc05.avif');
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            padding-top: 70px; /* Space for fixed navbar */
        }
        
        /* Main container - adjusted for navbar */
        .messages-container {
            width: 100%;
            max-width: 1200px;
            height: calc(85vh - 70px); /* Account for navbar height */
            margin: 0 auto;
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            display: flex;
        }
        
        /* Sidebar */
        .sidebar {
            width: 300px;
            background: white;
            border-right: 1px solid #e0e0e0;
        }
        
        .sidebar-header {
            padding: 25px;
            background: #1a237e;
            color: white;
            font-size: 20px;
            font-weight: 600;
        }
        
        .conversations {
            height: calc(100% - 70px);
            overflow-y: auto;
            padding: 0;
        }
        
        .conv {
            padding: 18px 20px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .conv:hover {
            background: #f8f9fa;
        }
        
        .conv.active {
            background: #e8eaf6;
            border-left: 4px solid #3949ab;
        }
        
        .conv-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .conv-preview {
            color: #666;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .conv-time {
            color: #888;
            font-size: 12px;
            margin-top: 5px;
        }
        
        /* Chat Area */
        .chat {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .chat-header {
            padding: 20px 25px;
            background: white;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .chat-name {
            font-size: 18px;
            font-weight: 600;
            color: #1a237e;
        }
        
        .chat-type {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .messages {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
            background: #f8f9fa;
        }
        
        .message {
            max-width: 70%;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 15px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
        }
        
        .message.sent {
            margin-left: auto;
            background: #1a237e;
            color: white;
            border: none;
        }
        
        .message-text {
            line-height: 1.5;
        }
        
        .message-time {
            text-align: right;
            font-size: 12px;
            color: #888;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        
        .message.sent .message-time {
            color: rgba(255,255,255,0.8);
            border-top-color: rgba(255,255,255,0.1);
        }
        
        /* Input Area */
        .input-area {
            padding: 20px 25px;
            background: white;
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: 15px;
        }
        
        textarea {
            flex: 1;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-family: inherit;
            font-size: 15px;
            resize: none;
            height: 60px;
            outline: none;
            transition: border 0.2s;
        }
        
        textarea:focus {
            border-color: #3949ab;
        }
        
        button {
            padding: 15px 30px;
            background: #1a237e;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: background 0.2s;
        }
        
        button:hover {
            background: #3949ab;
        }
        
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        /* Scrollbar */
        .conversations::-webkit-scrollbar {
            width: 6px;
        }
        
        .conversations::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .conversations::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        .messages::-webkit-scrollbar {
            width: 6px;
        }
        
        .messages::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .messages::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        /* Empty States */
        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #666;
        }
        
        .empty h3 {
            color: #333;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include '../../navbar/navbar.php'; ?>
    
    <div style="display: flex; justify-content: center; padding: 20px;">
        <div class="messages-container">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-header">
                    <i class="fas fa-comments"></i> Messages
                </div>
                <div id="conversations" class="conversations">
                    <div class="empty">Loading conversations...</div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="chat">
                <div class="chat-header">
                    <div class="chat-name" id="chatName">Select a conversation</div>
                    <div class="chat-type" id="chatType"></div>
                </div>
                
                <div id="messages" class="messages">
                    <div class="empty">
                        <h3>SkyBook Messages</h3>
                        <p>Select a conversation to start messaging</p>
                    </div>
                </div>
                
                <div class="input-area">
                    <textarea id="messageInput" placeholder="Type your message..."></textarea>
                    <button id="sendBtn">Send</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentChat = null;
        const userId = <?php echo $_SESSION['user_id']; ?>;
        const apiBase = '../../../Backend/services/message.php';
        
        // Load conversations
        async function loadChats() {
            try {
                const res = await fetch(apiBase + '?action=get_chats');
                const data = await res.json();
                
                if(data.chats && data.chats.length > 0) {
                    const html = data.chats.map(chat => `
                        <div class="conv" onclick="openChat(${chat.chat_with_user_id}, '${chat.name}', '${chat.type}')">
                            <div class="conv-name">${chat.name}</div>
                            <div class="conv-preview">${chat.last_msg || 'Start conversation'}</div>
                            <div class="conv-time">${formatTime(chat.last_time)}</div>
                        </div>
                    `).join('');
                    document.getElementById('conversations').innerHTML = html;
                } else {
                    document.getElementById('conversations').innerHTML = '<div class="empty">No conversations yet</div>';
                }
            } catch(e) {
                document.getElementById('conversations').innerHTML = '<div class="empty">Error loading</div>';
            }
        }
        
        // Open chat
        async function openChat(otherId, name, type) {
            currentChat = {id: otherId, name: name, type: type};
            
            document.getElementById('chatName').textContent = name;
            document.getElementById('chatType').textContent = type === 'company' ? 'Airline Company' : 'Passenger';
            
            try {
                const res = await fetch(apiBase + `?action=get_messages&other_id=${otherId}`);
                const data = await res.json();
                
                if(data.messages && data.messages.length > 0) {
                    const html = data.messages.map(msg => {
                        const isSent = msg.sender_id == userId;
                        const time = msg.time || formatTime(msg.sent_at);
                        return `
                            <div class="message ${isSent ? 'sent' : ''}">
                                <div class="message-text">${escapeHtml(msg.message)}</div>
                                <div class="message-time">${time}</div>
                            </div>
                        `;
                    }).join('');
                    document.getElementById('messages').innerHTML = html;
                } else {
                    document.getElementById('messages').innerHTML = '<div class="empty">No messages yet</div>';
                }
                
                const messagesDiv = document.getElementById('messages');
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
                
            } catch(e) {
                document.getElementById('messages').innerHTML = '<div class="empty">Error loading messages</div>';
            }
            
            document.querySelectorAll('.conv').forEach(conv => {
                conv.classList.remove('active');
            });
            event.target.closest('.conv').classList.add('active');
        }
        
        // Send message
        async function sendMessage() {
            const input = document.getElementById('messageInput');
            const text = input.value.trim();
            
            if(!text || !currentChat) return;
            
            const btn = document.getElementById('sendBtn');
            btn.disabled = true;
            
            try {
                const form = new FormData();
                form.append('to_id', currentChat.id);
                form.append('message', text);
                
                const res = await fetch(apiBase + '?action=send', {
                    method: 'POST',
                    body: form
                });
                
                const data = await res.json();
                if(data.status === 'success') {
                    const messagesDiv = document.getElementById('messages');
                    const now = new Date();
                    const timeStr = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    
                    messagesDiv.innerHTML += `
                        <div class="message sent">
                            <div class="message-text">${escapeHtml(text)}</div>
                            <div class="message-time">${timeStr}</div>
                        </div>
                    `;
                    
                    input.value = '';
                    loadChats();
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }
            } catch(e) {
                alert('Failed to send message');
            }
            
            btn.disabled = false;
        }
        
        function formatTime(dateString) {
            if(!dateString) return '';
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            const hours = Math.floor(diff / (1000 * 60 * 60));
            
            if(hours < 24) return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            if(hours < 48) return 'Yesterday';
            return date.toLocaleDateString([], {month: 'short', day: 'numeric'});
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        loadChats();
        document.getElementById('sendBtn').onclick = sendMessage;
        document.getElementById('messageInput').onkeydown = (e) => {
            if(e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        };
    </script>
</body>
</html>