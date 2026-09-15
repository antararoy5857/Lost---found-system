<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'campus_lost_found');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check user type
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin';
}

// Function to check if user is student
function isStudent() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'student';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit();
    }
}

// Redirect admin to admin area
function redirectAdmin() {
    if (isAdmin()) {
        header('Location: admin/dashboard.php');
        exit();
    }
}

// Redirect student to student area
function redirectStudent() {
    if (isStudent()) {
        header('Location: student/dashboard.php');
        exit();
    }
}

// Sanitize input
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($input)));
}

// File upload function
function uploadFile($file, $type) {
    $target_dir = "uploads/" . $type . "/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_name = time() . '_' . basename($file["name"]);
    $target_file = $target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return false;
    }
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return false;
    }
    
    // Allow certain file formats
    $allowed = array("jpg", "jpeg", "png", "gif");
    if(!in_array($imageFileType, $allowed)) {
        return false;
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    }
    
    return false;
}
?>