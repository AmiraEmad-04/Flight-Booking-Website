<?php

class FileUpload {
    
    private $uploadDir;
    private $allowedTypes;
    private $maxSize;

    public function __construct($uploadDir = null, $allowedTypes = ['jpg', 'jpeg', 'png'], $maxSize = 5242880) {
        // If uploadDir is provided, use it; otherwise default to logos folder
        $this->uploadDir = $uploadDir ? $uploadDir : __DIR__ . '/../uploads/logos/';
        $this->allowedTypes = $allowedTypes;
        $this->maxSize = $maxSize; // 5MB default
        
        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function upload($file) {
        // Check if file was uploaded
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'No file uploaded or upload error'];
        }

        // Check file size
        if ($file['size'] > $this->maxSize) {
            return ['success' => false, 'message' => 'File size exceeds maximum allowed size (5MB)'];
        }

        // Get file extension
        $fileName = $file['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Check file type
        if (!in_array($fileExt, $this->allowedTypes)) {
            return ['success' => false, 'message' => 'File type not allowed. Only JPG, JPEG, and PNG are allowed.'];
        }

        // Generate unique file name
        $newFileName = uniqid() . '_' . time() . '.' . $fileExt;
        $destination = $this->uploadDir . $newFileName;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $newFileName, 'path' => $destination];
        } else {
            return ['success' => false, 'message' => 'Failed to move uploaded file'];
        }
    }

    public function delete($filename) {
        $filePath = $this->uploadDir . $filename;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}