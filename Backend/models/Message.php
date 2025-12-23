<?php

require_once __DIR__ . '/../utils/Database.php';

class Message {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get all people this user has chatted with
    public function getChats($myUserId) {
        $sql = "SELECT DISTINCT
                CASE 
                    WHEN sender_id = :my_id THEN receiver_id
                    ELSE sender_id
                END as chat_with_user_id,
                u.name,
                u.type,
                (SELECT message FROM messages 
                 WHERE (sender_id = :my_id2 AND receiver_id = chat_with_user_id)
                    OR (receiver_id = :my_id3 AND sender_id = chat_with_user_id)
                 ORDER BY sent_at DESC LIMIT 1) as last_msg,
                (SELECT sent_at FROM messages 
                 WHERE (sender_id = :my_id4 AND receiver_id = chat_with_user_id)
                    OR (receiver_id = :my_id5 AND sender_id = chat_with_user_id)
                 ORDER BY sent_at DESC LIMIT 1) as last_time
                FROM messages m
                JOIN users u ON u.id = CASE 
                    WHEN m.sender_id = :my_id6 THEN m.receiver_id
                    ELSE m.sender_id
                END
                WHERE m.sender_id = :my_id7 OR m.receiver_id = :my_id8
                ORDER BY last_time DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'my_id' => $myUserId,
            'my_id2' => $myUserId,
            'my_id3' => $myUserId,
            'my_id4' => $myUserId,
            'my_id5' => $myUserId,
            'my_id6' => $myUserId,
            'my_id7' => $myUserId,
            'my_id8' => $myUserId
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all messages between me and another user
    public function getChat($myUserId, $otherUserId) {
        $sql = "SELECT sender_id, receiver_id, message, sent_at,
                DATE_FORMAT(sent_at, '%h:%i %p') as time
                FROM messages
                WHERE (sender_id = :my_id AND receiver_id = :other_id)
                   OR (sender_id = :other_id2 AND receiver_id = :my_id2)
                ORDER BY sent_at ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'my_id' => $myUserId,
            'other_id' => $otherUserId,
            'other_id2' => $otherUserId,
            'my_id2' => $myUserId
        ]);

        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mark as read
        $updateSql = "UPDATE messages SET is_read = 1 
                     WHERE receiver_id = :my_id AND sender_id = :other_id AND is_read = 0";
        $updateStmt = $this->db->prepare($updateSql);
        $updateStmt->execute(['my_id' => $myUserId, 'other_id' => $otherUserId]);

        return $messages;
    }

    // Send a message
    public function send($fromUserId, $toUserId, $msg, $flightId = null) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message, flight_id, sent_at) 
                VALUES (:from_id, :to_id, :msg, :flight_id, NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'from_id' => $fromUserId,
            'to_id' => $toUserId,
            'msg' => $msg,
            'flight_id' => $flightId
        ]);
    }

    // Get company user_id from flight_id
    public function getCompanyByFlight($flightId) {
        $sql = "SELECT c.user_id FROM flights f
                JOIN companies c ON f.company_id = c.id
                WHERE f.id = :flight_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['flight_id' => $flightId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['user_id'] : null;
    }

    // Get unread message count
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = :user_id AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}
?>