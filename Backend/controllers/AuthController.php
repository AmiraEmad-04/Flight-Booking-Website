<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Validator.php';

class AuthController{

    private $user;

    public function __construct(){
        $this->user = new User();
    }

    public function register($data){
        $errors = [];

        if(!Validator::required($data['name'])) $errors[] = "Name is required";
        if(!Validator::email($data['email'])) $errors[] = "Invalid email";
        if(!Validator::minLength($data['password'],6)) $errors[] = "Password must be at least 6 charecters";
        if(!Validator::phone($data['tel'])) $errors[] = "Invalid phone number";
        if($this->user->exists($data['email'])) $errors[] = "Email already exists";

        if(!empty($errors)){
            return ['status' => 'error' , 'errors' => $errors];
        }

        $this->user->register($data);
        return['status' => 'success'];
    }

    public function login($data) {
        $user = $this->user->login($data['email'], $data['password']);
        if($user){
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['type'] = $user['type'];
            return ['status' => 'success' , 'user' => $user];
        }
        return ['status' => 'error' , 'message' => 'Invalid email or password'];
    }

    public function logout(){
        session_start();
        session_destroy();
        return ['status' => 'success'];
    }
}
?>