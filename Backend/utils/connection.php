<?php

class connection {
    
    private $conn;
    private $host = "localhost";
    private $user = "root";
    private $password = "root";
    private $database = "flight-booking-system";

    // Connect to database
    public function connect() {
        $this->conn = mysqli_connect($this->host, $this->user, $this->password, $this->database);
        
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
        mysqli_set_charset($this->conn, "utf8mb4");
    }

    // Return connection
    public function return_connect() {
        return $this->conn;
    }

    // Disconnect from database
    public function disconnect() {
        if ($this->conn) {
            mysqli_close($this->conn);
        }
    }
}