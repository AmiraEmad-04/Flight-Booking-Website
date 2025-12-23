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

    public function registerDetails($data, $files = null) {
    if (!isset($data['user_id']) || !isset($data['type'])) {
        return ['status' => 'error', 'message' => 'Missing user_id or type'];
    }

    $user_id = $data['user_id'];
    $type = $data['type'];
    $files = $files ?: $_FILES;  // Use passed files or global $_FILES

    require_once __DIR__ . '/../utils/FileUpload.php';  // Make sure path is correct

    if ($type === 'company') {
        $errors = [];
        if (empty($data['bio'])) $errors[] = "Company bio required";
        if (empty($data['address'])) $errors[] = "Company address required";
        if (empty($files['logo_img']['name'])) $errors[] = "Company logo required";

        if (!empty($errors)) {
            return ['status' => 'error', 'errors' => $errors];
        }

        // Upload logo
        $uploader = new FileUpload(__DIR__ . '/../uploads/logos/');
        $result = $uploader->upload($files['logo_img']);

        if (!$result['success']) {
            return ['status' => 'error', 'errors' => [$result['message']]];
        }

        $data['logo_img'] = $result['filename'];

        $company = new Company();
        $company->create($user_id, $data);

    } elseif ($type === 'passenger') {
        $errors = [];
        if (empty($files['photo']['name'])) $errors[] = "Profile photo required";
        if (empty($files['passport_img']['name'])) $errors[] = "Passport image required";

        if (!empty($errors)) {
            return ['status' => 'error', 'errors' => $errors];
        }

        // Upload photo
        $uploaderPhoto = new FileUpload(__DIR__ . '/../uploads/photos/');
        $photoResult = $uploaderPhoto->upload($files['photo']);
        if (!$photoResult['success']) {
            return ['status' => 'error', 'errors' => [$photoResult['message']]];
        }

        // Upload passport
        $uploaderPassport = new FileUpload(__DIR__ . '/../uploads/passports/');
        $passportResult = $uploaderPassport->upload($files['passport_img']);
        if (!$passportResult['success']) {
            return ['status' => 'error', 'errors' => [$passportResult['message']]];
        }

        $data['photo'] = $photoResult['filename'];
        $data['passport_img'] = $passportResult['filename'];

        $passenger = new Passenger();
        $passenger->create($user_id, $data);

    } else {
        return ['status' => 'error', 'message' => 'Invalid type'];
    }

    return ['status' => 'success', 'message' => 'Account created successfully!'];
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