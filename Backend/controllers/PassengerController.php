<?php
require_once __DIR__ . '/../models/Passenger.php';
require_once __DIR__ . '/../models/Flight.php';

class PassengerController
{
    private $passengerModel;
    private $flightModel;
    
    public function __construct()
    {
        $this->passengerModel = new Passenger();
        $this->flightModel    = new Flight();
    }
    
    public function home($userId)
    {
        // get passenger by user_id
        $passenger = $this->passengerModel->getByUserId($userId);
    
        if (!$passenger) {
            return [
                'status' => false,
                'message' => 'Passenger not found'
            ];
        }
    
        $passengerId = $passenger['id'];
    
        $currentFlights = $this->passengerModel->getFlights(
            $passengerId,
            ['pending', 'registered']
        );
        
        $completedFlights = $this->passengerModel->getFlights(
            $passengerId,
            ['completed']
        );
        
    
        return [
            'status' => true,
            'passenger' => $passenger,
            'currentFlights' => $currentFlights,
            'completedFlights' => $completedFlights
        ];
    }
    
    
    public function profile($passengerId)
    {
        // Get passenger data
        $passenger = $this->passengerModel->getById($passengerId);
        
        if ($passenger === false) {
            return ['status' => false, 'message' => 'Passenger not found'];
        }
        
        return [
            'status' => true,
            'passenger' => $passenger
        ];
    }
    
    public function updateProfile($passengerId, $data)
    {
        // Check passenger exists
        $passenger = $this->passengerModel->getById($passengerId);
        if (!$passenger) {
            return [
                'status' => false,
                'message' => 'Passenger not found'
            ];
        }
    
        // At least one field must be provided
        $allowedFields = ['name', 'email', 'tel', 'password', 'photo', 'passport_img'];
        $hasData = false;
    
        foreach ($allowedFields as $field) {
            if (isset($data[$field]) && trim($data[$field]) !== '') {
                $hasData = true;
                break;
            }
        }
    
        if (!$hasData) {
            return [
                'status' => false,
                'message' => 'No data provided to update'
            ];
        }
    
        // Email validation
        if (isset($data['email']) && trim($data['email']) !== '') {
            $email = trim($data['email']);
            
            // Check if email is valid format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'status' => false,
                    'message' => 'Invalid email format'
                ];
            }
            
            // Check if email already exists (excluding current user)
            if ($this->passengerModel->emailExists($email, $passengerId)) {
                return [
                    'status' => false,
                    'message' => 'Email already in use by another user'
                ];
            }
            
            $data['email'] = $email;
        }
    
        // Relaxed telephone validation - allows +, spaces, dashes
        if (isset($data['tel']) && trim($data['tel']) !== '') {
            $cleanTel = preg_replace('/[\s\-\(\)]/', '', $data['tel']); // Remove spaces, dashes, parentheses
            if (!preg_match('/^\+?[0-9]{10,15}$/', $cleanTel)) {
                return [
                    'status' => false,
                    'message' => 'Invalid telephone number format'
                ];
            }
            // Store the cleaned version
            $data['tel'] = $cleanTel;
        }
    
        // Password validation
        if (isset($data['password']) && trim($data['password']) !== '') {
            if (strlen(trim($data['password'])) < 6) {
                return [
                    'status' => false,
                    'message' => 'Password must be at least 6 characters'
                ];
            }
        } else {
            // Remove empty password from data
            unset($data['password']);
        }
    
        // Update via model
        $updated = $this->passengerModel->updateProfile($passengerId, $data);
    
        if ($updated) {
            return [
                'status' => true,
                'message' => 'Profile updated successfully'
            ];
        }
    
        return [
            'status' => false,
            'message' => 'Failed to update profile'
        ];
    }
    
    
}
?>