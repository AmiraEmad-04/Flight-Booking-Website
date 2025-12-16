<?php
require_once __DIR__ . '/../controllers/AuthController.php';

$auth = new AuthController();
$action = $_GET['action'] ?? '';
header('Content-Type: application/json');


if($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $result = $auth->register($_POST);
    echo json_encode($result);
}


if($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $result = $auth->login($_POST);
    echo json_encode($result);
}


if($action === 'logout'){
    $result = $auth->logout();
    echo json_encode($result);
}
  ?>