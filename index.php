<?php
session_start();
if(isset($_SESSION['user_id'])) {
    if($_SESSION['user_type'] == 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: student/dashboard.php');
    }
    exit();
} else {
    header('Location: login.php');
    exit();
}
?>