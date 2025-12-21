<?php
session_start();

require_once __DIR__ . '/../../../Backend/utils/Database.php';

// Get the test company user
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT u.*, c.id as company_id FROM users u JOIN companies c ON u.id = c.user_id WHERE u.email = 'testcompany@test.com'");
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_type'] = $user['type'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['company_id'] = $user['company_id'];
    
    echo "✅ Test login successful!<br>";
    echo "User ID: " . $user['id'] . "<br>";
    echo "Company ID: " . $user['company_id'] . "<br>";
    echo "<br><a href='index.php'>Go to Dashboard</a>";
} else {
    echo "❌ Test user not found. Please run the SQL first.";
}
?>