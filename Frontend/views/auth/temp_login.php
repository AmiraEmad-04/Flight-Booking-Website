<?php
session_start();

// ⚠️ TEMPORARY LOGIN - FOR TESTING ONLY
// Your teammates will replace this with real login

// IMPORTANT: Change these IDs to match your database
$_SESSION['user_id'] = 1;        // The user ID from users table
$_SESSION['user_type'] = 'company';
$_SESSION['company_id'] = 1;     // The company ID from companies table

// Redirect to company home
header('Location: index.php');
exit();
?>