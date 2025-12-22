<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../models/Company_Register.php';
require_once __DIR__ . '/../models/Passenger_Register.php';

class AuthController{

    private $user;

    public function __construct(){
        $this->user = new User();
    }

    public function registerUser($data){
        $errors = [];

        if(!Validator::required($data['name'])) $errors[] = "Name is required";
        if(!Validator::email($data['email'])) $errors[] = "Invalid email";
        if(!Validator::minLength($data['password'],6)) $errors[] = "Password must be at least 6 charecters";
        if(!Validator::phone($data['tel'])) $errors[] = "Invalid phone number";
        if($this->user->exists($data['email'])) $errors[] = "Email already exists";

        if(!empty($errors)){
            return ['status' => 'error' , 'errors' => $errors];
        }

 // Provide default account balance if not set
        if(!isset($data['account_balance'])) $data['account_balance'] = 0.00;

        $user_id = $this->user->register($data);
        return ['status'=>'success','user_id'=>$user_id,'type'=>$data['type']];
    }

    // Step 2: add company/passenger details after user is created
    public function registerDetails($data){
        if(!isset($data['user_id']) || !isset($data['type'])){
            return ['status'=>'error','message'=>'Missing user_id or type'];
        }

        $user_id = $data['user_id'];
        $type = $data['type'];

        if($type === 'company'){
            $errors = [];
            if(!Validator::required($data['bio'])) $errors[] = "Company bio required";
            if(!Validator::required($data['address'])) $errors[] = "Company address required";
            if(!empty($errors)) return ['status'=>'error','errors'=>$errors];

            $company = new Company();
            $company->create($user_id, $data);

        } elseif($type === 'passenger'){
            $errors = [];
            if(!Validator::required($data['passport_img'])) $errors[] = "Passport image required";
            if(!empty($errors)) return ['status'=>'error','errors'=>$errors];

            $passenger = new Passenger();
            $passenger->create($user_id, $data);
        } else {
            return ['status'=>'error','message'=>'Invalid type'];
        }

        return ['status'=>'success','message'=>'Details saved, redirect to login'];
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