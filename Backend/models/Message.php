<?php

// Include Database utility
require_once __DIR__ . '/../utils/Database.php';

class Message {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get all messages for a user (received)
    public function getMessagesByUser($userId) {
        $sql = "SELECT m.*, 
                       u.name as sender_name,
                       f.flight_name
                FROM messages m
                JOIN users u ON m.sender_id = u.id
                LEFT JOIN flights f ON m.flight_id = f.id
                WHERE m.receiver_id = :user_id
                ORDER BY m.sent_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get unread message count
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = :user_id AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    // Send a message
    public function sendMessage($senderId, $receiverId, $message, $flightId = null) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message, flight_id) 
                VALUES (:sender_id, :receiver_id, :message, :flight_id)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $message,
            'flight_id' => $flightId
        ]);
    }

    // Mark message as read
    public function markAsRead($messageId) {
        $sql = "UPDATE messages SET is_read = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $messageId]);
    }

    // Get single message
    public function getMessageById($messageId) {
        $sql = "SELECT m.*, 
                       u.name as sender_name,
                       f.flight_name
                FROM messages m
                JOIN users u ON m.sender_id = u.id
                LEFT JOIN flights f ON m.flight_id = f.id
                WHERE m.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $messageId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}