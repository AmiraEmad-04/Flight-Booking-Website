
// Load conversations
async function loadChats() {
    try {
        const res = await fetch('../../Backend/services/message.php?action=get_chats');
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
    
    // Update header
    document.getElementById('chatName').textContent = name;
    document.getElementById('chatType').textContent = type === 'company' ? 'Airline Company' : 'Passenger';
    
    // Load messages
    try {
        const res = await fetch(`../../Backend/services/message.php?action=get_messages&other_id=${otherId}`);
        const data = await res.json();
        
        if(data.messages && data.messages.length > 0) {
            const html = data.messages.map(msg => {
                const isSent = msg.sender_id == userId;
                const time = msg.time || formatTime(msg.sent_at);
                return `
                    <div class="message ${isSent ? 'sent' : ''}">
                        <div class="message-text">${msg.message}</div>
                        <div class="message-time">${time}</div>
                    </div>
                `;
            }).join('');
            document.getElementById('messages').innerHTML = html;
        } else {
            document.getElementById('messages').innerHTML = '<div class="empty">No messages yet</div>';
        }
        
        // Scroll to bottom
        const messagesDiv = document.getElementById('messages');
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
        
    } catch(e) {
        document.getElementById('messages').innerHTML = '<div class="empty">Error loading messages</div>';
    }
    
    // Update active conversation
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
        
        const res = await fetch('../../Backend/services/message.php?action=send', {
            method: 'POST',
            body: form
        });
        
        const data = await res.json();
        if(data.status === 'success') {
            // Add message to chat
            const messagesDiv = document.getElementById('messages');
            const now = new Date();
            const timeStr = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            messagesDiv.innerHTML += `
                <div class="message sent">
                    <div class="message-text">${text}</div>
                    <div class="message-time">${timeStr}</div>
                </div>
            `;
            
            input.value = '';
            
            // Refresh conversations and scroll
            loadChats();
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
    } catch(e) {
        alert('Failed to send message');
    }
    
    btn.disabled = false;
}

// Helper: Format time
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

// Initialize
loadChats();

document.getElementById('sendBtn').onclick = sendMessage;
document.getElementById('messageInput').onkeydown = (e) => {
    if(e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
};