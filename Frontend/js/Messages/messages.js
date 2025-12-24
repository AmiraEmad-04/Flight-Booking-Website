let currentChatUserId = null;

function loadChats() {
    $.get('../../../Backend/services/message.php?action=get_chats', function(data) {
        if (data.status === 'error') {
            $('#chatsList').html('<div class="error">' + data.message + '</div>');
            return;
        }

        $('#chatsList').empty();
        
        if (!data.chats || data.chats.length === 0) {
            $('#emptyChats').show();
            return;
        }
        
        $('#emptyChats').hide();
        
        data.chats.forEach(function(chat) {
            const item = `
                <div class="conversation-item" data-id="${chat.chat_with_user_id}" data-name="${chat.name}" data-type="${chat.type}">
                    <div class="conversation-header">
                        <div class="conversation-name">${chat.name}</div>
                        <span class="conversation-type">${chat.type === 'company' ? 'Company' : 'Passenger'}</span>
                    </div>
                    <div class="conversation-preview">${chat.last_msg || 'No messages yet'}</div>
                    ${chat.last_time ? `<div class="conversation-time">${chat.last_time}</div>` : ''}
                </div>
            `;
            $('#chatsList').append(item);
        });
    }).fail(function() {
        $('#chatsList').html('<div class="error">Failed to load conversations</div>');
    });
}

function loadMessages(userId) {
    $('#welcomeScreen').hide();
    $('#messagesContent').show().html('<div class="loading">Loading messages...</div>');
    
    $.get('../../../Backend/services/message.php?action=get_messages&other_id=' + userId, function(data) {
        if (data.status === 'error') {
            $('#messagesContent').html('<div class="error">' + data.message + '</div>');
            return;
        }

        $('#messagesContent').empty();

        if (!data.messages || data.messages.length === 0) {
            $('#messagesContent').html('<div class="empty-state"><div class="empty-icon">💬</div><div class="empty-subtitle">No messages yet. Start the conversation!</div></div>');
        } else {
            data.messages.forEach(function(msg) {
                const isSent = msg.sender_id == myUserId;
                const msgHtml = `
                    <div class="message-item ${isSent ? 'sent' : 'received'}">
                        <div class="message-text">${msg.message}</div>
                        <div class="message-time">${msg.time}</div>
                    </div>
                `;
                $('#messagesContent').append(msgHtml);
            });
            
            // Scroll to bottom
            const messagesArea = document.getElementById('messagesArea');
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }
    }).fail(function() {
        $('#messagesContent').html('<div class="error">Failed to load messages</div>');
    });
}

function openChat(userId, name, type) {
    currentChatUserId = userId;

    $('.conversation-item').removeClass('active');
    $(`.conversation-item[data-id="${userId}"]`).addClass('active');

    $('#chatName').text(name);
    $('#chatType').text(type === 'company' ? 'Company' : 'Passenger');
    
    $('#messageInput').prop('disabled', false);
    $('#sendButton').prop('disabled', false);
    $('#messageInput').attr('placeholder', 'Type your message...');

    loadMessages(userId);
}



function sendMessage() {
    const text = $('#messageInput').val().trim();
    if (!text || !currentChatUserId) return;

    $('#sendButton').prop('disabled', true);

    $.post('../../../Backend/services/message.php?action=send', {
        to_id: currentChatUserId,
        message: text
    }, function(data) {
        if (data.status === 'success') {
            const currentTime = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            const msgHtml = `
                <div class="message-item sent">
                    <div class="message-text">${text}</div>
                    <div class="message-time">${currentTime}</div>
                </div>
            `;
            
            if ($('#messagesContent .empty-state').length) {
                $('#messagesContent').empty();
            }
            
            $('#messagesContent').append(msgHtml);
            
            // Scroll to bottom
            const messagesArea = document.getElementById('messagesArea');
            messagesArea.scrollTop = messagesArea.scrollHeight;
            
            $('#messageInput').val('');
            loadChats();
        } else {
            alert('Failed to send message: ' + data.message);
        }
    }).always(function() {
        $('#sendButton').prop('disabled', false);
    }).fail(function() {
        alert('Network error. Please try again.');
        $('#sendButton').prop('disabled', false);
    });
}

// Initialize
$(document).ready(function() {
    loadChats();

    const urlParams = new URLSearchParams(window.location.search);
    const companyId = urlParams.get('company_id');

    if (companyId) {
        setTimeout(function() {
            const chatItem = $(`.conversation-item[data-id="${companyId}"]`);
            if (chatItem.length) {
                chatItem.click();
            } else {
                openChat(companyId, 'Company', 'company');
            }
        }, 500);
    }

    $(document).on('click', '.conversation-item', function() {
        openChat($(this).data('id'), $(this).data('name'), $(this).data('type'));
    });

    $('#sendButton').click(sendMessage);
    $('#messageInput').keypress(function(e) {
        if (e.which === 13) sendMessage();
    });

    // Auto-refresh messages
    setInterval(function() {
        if (currentChatUserId) {
            loadMessages(currentChatUserId);
        }
    }, 5000);
});